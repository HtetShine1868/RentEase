<?php

namespace App\Http\Controllers;

use App\Models\RoleApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoleApplicationController extends Controller
{
    /**
     * Show role application form
     */
    public function create($role = null)
    {
        $user = Auth::user();
        
        // Check if role is valid
        if (!in_array($role, ['OWNER', 'FOOD', 'LAUNDRY'])) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid role selected.');
        }

        if (!$user->canApplyForRole($role)) {
            return redirect()->route('dashboard')
                ->with('error', 'You cannot apply for this role at this time.');
        }

        $roleDetails = $this->getRoleDetails($role);

        return view('user.role-application.create', [
            'role' => $role,
            'roleDetails' => $roleDetails
        ]);
    }

    /**
     * Store role application
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $role = $request->input('role_type');

        // Base validation for all roles
        $baseValidation = [
            'role_type' => 'required|in:OWNER,FOOD,LAUNDRY',
            'business_name' => 'required|string|max:200',
            'business_registration' => 'nullable|string|max:100',
            'contact_person' => 'required|string|max:100',
            'contact_email' => 'required|email|max:150',
            'contact_phone' => 'required|string|max:20',
            'document' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ];

        // Role-specific validation rules
        $roleSpecificRules = [];

        switch ($role) {
            case 'OWNER':
                $roleSpecificRules = [
                    'property_type' => 'required|in:HOSTEL,APARTMENT',
                    'property_count' => 'nullable|integer|min:1',
                    'years_experience' => 'nullable|integer|min:0',
                ];
                break;

            case 'FOOD':
                $roleSpecificRules = [
                    'business_address' => 'required|string',
                    'service_radius_km' => 'required|numeric|min:1|max:50',
                    'food_license' => 'required|string|max:100',
                    'service_types' => 'required|array',
                    'service_types.*' => 'in:subscription,pay_per_eat',
                    'opening_time' => 'required|date_format:H:i',
                    'closing_time' => 'required|date_format:H:i|after:opening_time',
                    'avg_preparation_time' => 'required|integer|min:15|max:120',
                ];
                break;

            case 'LAUNDRY':
                $roleSpecificRules = [
                    'business_address' => 'required|string',
                    'service_radius_km' => 'required|numeric|min:1|max:50',
                    'service_license' => 'required|string|max:100',
                    'provides_pickup' => 'required|boolean',
                    'normal_turnaround_hours' => 'required|integer|min:24|max:168',
                    'rush_turnaround_hours' => 'required|integer|min:12|max:48',
                    'laundry_items' => 'required|array|min:1',
                ];
                break;
        }

        // Merge validation rules
        $validationRules = array_merge($baseValidation, $roleSpecificRules);
        $request->validate($validationRules);

        // Check if user can apply
        if (!$user->canApplyForRole($role)) {
            return redirect()->route('dashboard')
                ->with('error', 'You cannot apply for this role at this time.');
        }

        // Handle document upload
        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('documents', 'public');
        }

        // Prepare application data
        $applicationData = [
            'user_id' => $user->id,
            'role_type' => $role,
            'status' => 'PENDING',
            'business_name' => $request->business_name,
            'business_registration' => $request->business_registration,
            'document_path' => $documentPath,
            'contact_person' => $request->contact_person,
            'contact_email' => $request->contact_email,
            'contact_phone' => $request->contact_phone,
        ];

        // Prepare additional_info JSON based on role
        $additionalInfo = [];

        switch ($role) {
            case 'OWNER':
                $additionalInfo = [
                    'property_type' => $request->property_type,
                    'property_count' => $request->property_count,
                    'years_experience' => $request->years_experience,
                ];
                break;

            case 'FOOD':
                $applicationData['business_address'] = $request->business_address;
                $applicationData['service_radius_km'] = $request->service_radius_km;
                
                $additionalInfo = [
                    'food_license' => $request->food_license,
                    'service_types' => $request->service_types,
                    'opening_time' => $request->opening_time,
                    'closing_time' => $request->closing_time,
                    'avg_preparation_time' => $request->avg_preparation_time,
                ];
                break;

            case 'LAUNDRY':
                $applicationData['business_address'] = $request->business_address;
                $applicationData['service_radius_km'] = $request->service_radius_km;
                
                $additionalInfo = [
                    'service_license' => $request->service_license,
                    'provides_pickup' => $request->provides_pickup,
                    'normal_turnaround_hours' => $request->normal_turnaround_hours,
                    'rush_turnaround_hours' => $request->rush_turnaround_hours,
                    'laundry_items' => $request->laundry_items,
                ];
                break;
        }

        // Add additional_info to application data
        $applicationData['additional_info'] = json_encode($additionalInfo);

        // Create application
        $application = RoleApplication::create($applicationData);

        // If location provided for service providers, set dummy coordinates
        if (in_array($role, ['FOOD', 'LAUNDRY']) && $request->filled('business_address')) {
            $application->update([
                'latitude' => 23.8103, // Dummy Dhaka coordinates
                'longitude' => 90.4125,
            ]);
        }

        return redirect()->route('dashboard')
            ->with('success', 'Your application has been submitted successfully! It will be reviewed within 2-3 business days.');
    }

    /**
     * Show user's applications
     */
    public function index()
    {
        $applications = Auth::user()->roleApplications()->latest()->get();
        
        return view('user.role-application.index', compact('applications'));
    }

    /**
     * Show application details
     */
    public function show(RoleApplication $application)
    {
        // Check if user owns this application
        if ($application->user_id !== Auth::id()) {
            abort(403);
        }

        return view('user.role-application.show', compact('application'));
    }

    /**
     * Get role details for display
     */
    private function getRoleDetails($role)
    {
        $roles = [
            'OWNER' => [
                'title' => 'Property Owner',
                'description' => 'List and manage rental properties',
                'commission' => '3-5%',
                'requirements' => ['Property documents', 'Tax registration'],
                'icon' => 'home',
            ],
            'FOOD' => [
                'title' => 'Food Service Provider',
                'description' => 'Provide food subscription and pay-per-eat services',
                'commission' => '8%',
                'requirements' => ['Business license', 'Food safety certificate'],
                'icon' => 'utensils',
            ],
            'LAUNDRY' => [
                'title' => 'Laundry Service Provider',
                'description' => 'Provide laundry services with normal/rush options',
                'commission' => '10%',
                'requirements' => ['Business license', 'Service location'],
                'icon' => 'tshirt',
            ],
        ];

        return $roles[$role] ?? null;
    }
}