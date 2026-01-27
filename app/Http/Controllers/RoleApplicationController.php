<?php

namespace App\Http\Controllers;

use App\Models\RoleApplication;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoleApplicationController extends Controller
{
    /**
     * Show the role selection page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get existing applications
        $applications = RoleApplication::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Check which roles user can apply for
        $availableRoles = [];
        $roleTypes = ['OWNER', 'FOOD', 'LAUNDRY'];
        
        foreach ($roleTypes as $roleType) {
            if (RoleApplication::canApply($user->id, $roleType)) {
                $availableRoles[] = $roleType;
            }
        }
        
        return view('role-applications.index', [
            'title' => 'Apply for Role',
            'applications' => $applications,
            'availableRoles' => $availableRoles,
            'user' => $user
        ]);
    }

    /**
     * Show the role application form based on role type.
     */
    public function create($roleType)
    {
        $user = Auth::user();
        
        // Validate role type
        if (!in_array($roleType, ['OWNER', 'FOOD', 'LAUNDRY'])) {
            return redirect()->route('role.apply.index')->withErrors(['Invalid role type']);
        }
        
        // Check if user can apply for this role
        if (!RoleApplication::canApply($user->id, $roleType)) {
            return redirect()->route('role.apply.index')
                ->withErrors(['You cannot apply for this role at this time.']);
        }
        
        $roleName = match($roleType) {
            'OWNER' => 'Property Owner',
            'FOOD' => 'Food Provider',
            'LAUNDRY' => 'Laundry Provider',
            default => $roleType,
        };
        
        return view("role-applications.create-{$roleType}", [
            'title' => "Apply as {$roleName}",
            'roleType' => $roleType,
            'roleName' => $roleName,
            'requirements' => RoleApplication::getRoleRequirements($roleType)
        ]);
    }

    /**
     * Store a new role application.
     */
    public function store(Request $request, $roleType)
    {
        $user = Auth::user();
        
        // Validate role type
        if (!in_array($roleType, ['OWNER', 'FOOD', 'LAUNDRY'])) {
            return redirect()->route('role.apply.index')->withErrors(['Invalid role type']);
        }
        
        // Check if user can apply
        if (!RoleApplication::canApply($user->id, $roleType)) {
            return redirect()->route('role.apply.index')
                ->withErrors(['You cannot apply for this role at this time.']);
        }
        
        // Common validation for all roles
        $commonRules = [
            'business_name' => ['required', 'string', 'max:200'],
            'contact_person' => ['required', 'string', 'max:100'],
            'contact_email' => ['required', 'email', 'max:150'],
            'contact_phone' => ['required', 'string', 'max:20'],
            'business_address' => ['required', 'string'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
        ];
        
        // Role-specific validation
        $roleSpecificRules = match($roleType) {
            'OWNER' => [
                'property_type' => ['required', 'in:HOSTEL,APARTMENT'],
                'property_count' => ['required', 'integer', 'min:1'],
                'years_experience' => ['required', 'integer', 'min:0'],
            ],
            'FOOD' => [
                'service_radius_km' => ['required', 'numeric', 'min:1', 'max:50'],
                'cuisine_type' => ['required', 'string'],
                'meal_types' => ['required', 'array'],
                'meal_types.*' => ['in:BREAKFAST,LUNCH,DINNER,SNACKS'],
                'delivery_hours' => ['required', 'string'],
                'max_daily_orders' => ['required', 'integer', 'min:1'],
            ],
            'LAUNDRY' => [
                'service_radius_km' => ['required', 'numeric', 'min:1', 'max:50'],
                'has_pickup_service' => ['required', 'boolean'],
                'normal_turnaround_hours' => ['required', 'integer', 'min:24'],
                'rush_turnaround_hours' => ['required', 'integer', 'min:12'],
                'max_daily_orders' => ['required', 'integer', 'min:1'],
            ],
        };
        
        $validationRules = array_merge($commonRules, $roleSpecificRules);
        
        $validated = $request->validate($validationRules);
        
        DB::beginTransaction();
        try {
            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('documents/role-applications', 'public');
            }
            
            // Prepare additional data based on role type
            $additionalData = match($roleType) {
                'OWNER' => [
                    'owner' => [
                        'property_type' => $validated['property_type'],
                        'property_count' => $validated['property_count'],
                        'years_experience' => $validated['years_experience'],
                    ]
                ],
                'FOOD' => [
                    'food_provider' => [
                        'service_radius_km' => $validated['service_radius_km'],
                        'cuisine_type' => $validated['cuisine_type'],
                        'meal_types' => $validated['meal_types'],
                        'delivery_hours' => $validated['delivery_hours'],
                        'max_daily_orders' => $validated['max_daily_orders'],
                    ]
                ],
                'LAUNDRY' => [
                    'laundry_provider' => [
                        'service_radius_km' => $validated['service_radius_km'],
                        'has_pickup_service' => $validated['has_pickup_service'],
                        'normal_turnaround_hours' => $validated['normal_turnaround_hours'],
                        'rush_turnaround_hours' => $validated['rush_turnaround_hours'],
                        'max_daily_orders' => $validated['max_daily_orders'],
                    ]
                ],
            };
            
            // Create role application
            $application = RoleApplication::create([
                'user_id' => $user->id,
                'role_type' => $roleType,
                'business_name' => $validated['business_name'],
                'contact_person' => $validated['contact_person'],
                'contact_email' => $validated['contact_email'],
                'contact_phone' => $validated['contact_phone'],
                'business_address' => $validated['business_address'],
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'service_radius_km' => $roleType !== 'OWNER' ? $validated['service_radius_km'] : null,
                'document_path' => $documentPath,
                'additional_data' => $additionalData,
                'status' => 'PENDING',
            ]);
            
            DB::commit();
            
            return redirect()->route('role.apply.index')
                ->with('success', 'Your application has been submitted successfully! It will be reviewed by our admin team.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            // Delete uploaded file if error occurs
            if (isset($documentPath) && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            return back()->withErrors(['error' => 'Failed to submit application. Please try again.']);
        }
    }

    /**
     * Show a specific application.
     */
    public function show($id)
    {
        $application = RoleApplication::findOrFail($id);
        
        // Check if user owns this application or is admin
        if ($application->user_id !== Auth::id() && !Auth::user()->isSuperAdmin()) {
            abort(403);
        }
        
        return view('role-applications.show', [
            'title' => 'Application Details',
            'application' => $application
        ]);
    }

    /**
     * Cancel a pending application.
     */
    public function destroy($id)
    {
        $application = RoleApplication::findOrFail($id);
        
        // Check if user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Only allow cancellation of pending applications
        if ($application->status !== 'PENDING') {
            return back()->withErrors(['error' => 'Only pending applications can be cancelled.']);
        }
        
        DB::beginTransaction();
        try {
            // Delete document file
            if ($application->document_path && Storage::disk('public')->exists($application->document_path)) {
                Storage::disk('public')->delete($application->document_path);
            }
            
            $application->delete();
            
            DB::commit();
            
            return redirect()->route('role.apply.index')
                ->with('success', 'Application cancelled successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to cancel application. Please try again.']);
        }
    }
}