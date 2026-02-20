<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RoleApplication;
use App\Models\User;
use App\Models\Role;
use App\Models\ServiceProvider;
use App\Models\Notification;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RoleApplicationController extends Controller
{
    use Notifiable;
/**
 * Display a listing of role applications
 */
/**
 * Display a listing of role applications
 */
public function index(Request $request)
{
    // Get the active tab from request, default to 'all'
    $activeTab = $request->get('tab', 'all');
    
    $query = RoleApplication::with(['user', 'reviewer']);
    
    // Apply role filter based on active tab
    if ($activeTab !== 'all') {
        $roleType = strtoupper($activeTab);
        if (in_array($roleType, ['OWNER', 'FOOD', 'LAUNDRY'])) {
            $query->where('role_type', $roleType);
        }
    }

    // ===== NEW: Apply status filter =====
    if ($request->filled('status')) {
        $status = $request->status;
        if (in_array($status, ['PENDING', 'APPROVED', 'REJECTED'])) {
            $query->where('status', $status);
        }
    }

    // Filter by date range
    if ($request->filled('date_range')) {
        switch ($request->date_range) {
            case 'today':
                $query->whereDate('created_at', today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', today()->subDay());
                break;
            case 'this_week':
                $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $query->whereMonth('created_at', now()->month)
                      ->whereYear('created_at', now()->year);
                break;
        }
    }

    // Search by applicant name or business name
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->whereHas('user', function($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
            })
            ->orWhere('business_name', 'like', "%{$search}%")
            ->orWhere('contact_email', 'like', "%{$search}%")
            ->orWhere('contact_phone', 'like', "%{$search}%");
        });
    }

    // Get counts for tabs
    $tabCounts = [
        'all' => RoleApplication::count(),
        'owner' => RoleApplication::where('role_type', 'OWNER')->count(),
        'food' => RoleApplication::where('role_type', 'FOOD')->count(),
        'laundry' => RoleApplication::where('role_type', 'LAUNDRY')->count(),
    ];

    // Get status counts for the active tab
    $baseQuery = RoleApplication::query();
    if ($activeTab !== 'all') {
        $baseQuery->where('role_type', strtoupper($activeTab));
    }
    
    $statusCounts = [
        'pending' => (clone $baseQuery)->where('status', 'PENDING')->count(),
        'approved' => (clone $baseQuery)->where('status', 'APPROVED')->count(),
        'rejected' => (clone $baseQuery)->where('status', 'REJECTED')->count(),
        'all' => (clone $baseQuery)->count(),
    ];

    // Get statistics
    $stats = [
        'total' => $tabCounts['all'],
        'pending' => RoleApplication::where('status', 'PENDING')->count(),
        'approved' => RoleApplication::where('status', 'APPROVED')->count(),
        'rejected' => RoleApplication::where('status', 'REJECTED')->count(),
    ];

    $applications = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

    return view('admin.role-applications.index', compact(
        'applications', 
        'stats', 
        'activeTab', 
        'tabCounts',
        'statusCounts'
    ));
}

    /**
     * Display the specified application
     */
    public function show($id)
    {
        $application = RoleApplication::with(['user', 'reviewer'])
            ->findOrFail($id);

        return view('admin.role-applications.show', compact('application'));
    }

    /**
     * Show the review form
     */
    public function review($id)
    {
        $application = RoleApplication::with('user')
            ->findOrFail($id);

        if ($application->status !== 'PENDING') {
            return redirect()->route('admin.role-applications.show', $application)
                ->with('info', 'This application has already been reviewed.');
        }

        return view('admin.role-applications.review', compact('application'));
    }

    /**
     * Approve the application
     */
    public function approve(Request $request, $id)
    {
        $application = RoleApplication::with('user')->findOrFail($id);

        if ($application->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'This application has already been processed.'
            ], 422);
        }

        $request->validate([
            'notes' => 'nullable|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            // Update application status
            $application->status = 'APPROVED';
            $application->reviewed_by = Auth::id();
            $application->reviewed_at = now();
            $application->save();

            // Assign role to user
            $user = $application->user;
            $role = Role::where('name', $application->role_type)->first();
            
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            // Create service provider record based on role type
            if ($application->role_type === 'FOOD' || $application->role_type === 'LAUNDRY') {
                $this->createServiceProvider($application);
            }

            // ============ SEND NOTIFICATIONS ============
            
            // Notify user about approval
            $this->createNotification(
                $application->user_id,
                'SYSTEM',
                'Application Approved',
                "Congratulations! Your application to become a {$this->getRoleDisplayName($application->role_type)} has been approved.",
                'role_application',
                $application->id
            );

            // Send email notification (you can implement this later)
            // Mail::to($user->email)->send(new ApplicationApproved($application));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application approved successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error approving application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve application. Please try again.'
            ], 500);
        }
    }

    /**
     * Reject the application
     */
    public function reject(Request $request, $id)
    {
        $application = RoleApplication::with('user')->findOrFail($id);

        if ($application->status !== 'PENDING') {
            return response()->json([
                'success' => false,
                'message' => 'This application has already been processed.'
            ], 422);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            // Update application status
            $application->status = 'REJECTED';
            $application->rejection_reason = $request->rejection_reason;
            $application->reviewed_by = Auth::id();
            $application->reviewed_at = now();
            $application->save();

            // ============ SEND NOTIFICATIONS ============
            
            // Notify user about rejection
            $this->createNotification(
                $application->user_id,
                'SYSTEM',
                'Application Rejected',
                "Your application to become a {$this->getRoleDisplayName($application->role_type)} has been reviewed. Reason: {$request->rejection_reason}",
                'role_application',
                $application->id
            );

            // Send email notification (you can implement this later)
            // Mail::to($application->user->email)->send(new ApplicationRejected($application, $request->rejection_reason));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application rejected successfully.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error rejecting application: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reject application. Please try again.'
            ], 500);
        }
    }

    /**
     * Download application document
     */
    public function downloadDocument($id)
    {
        $application = RoleApplication::findOrFail($id);

        if (!$application->document_path) {
            return back()->with('error', 'No document found for this application.');
        }

        if (!Storage::disk('public')->exists($application->document_path)) {
            return back()->with('error', 'Document file not found.');
        }

        return Storage::disk('public')->download($application->document_path);
    }

    /**
     * Bulk approve applications
     */
    public function bulkApprove(Request $request)
    {
        $request->validate([
            'application_ids' => 'required|array',
            'application_ids.*' => 'exists:role_applications,id',
        ]);

        $successCount = 0;
        $failCount = 0;

        foreach ($request->application_ids as $id) {
            try {
                $application = RoleApplication::find($id);
                
                if ($application && $application->status === 'PENDING') {
                    $application->status = 'APPROVED';
                    $application->reviewed_by = Auth::id();
                    $application->reviewed_at = now();
                    $application->save();

                    // Assign role
                    $user = $application->user;
                    $role = Role::where('name', $application->role_type)->first();
                    if ($role) {
                        $user->roles()->syncWithoutDetaching([$role->id]);
                    }

                    $successCount++;
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                $failCount++;
                \Log::error('Bulk approve error: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Approved {$successCount} applications. Failed: {$failCount}"
        ]);
    }

    /**
     * Export applications to CSV
     */
    public function export(Request $request)
    {
        $query = RoleApplication::with('user');

        // Apply filters
        if ($request->filled('role_type')) {
            $query->where('role_type', $request->role_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }

        $applications = $query->orderBy('created_at', 'desc')->get();

        $filename = 'role-applications-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Date', 'Applicant', 'Email', 'Role', 'Business Name', 'Contact Person', 'Contact Email', 'Contact Phone', 'Status', 'Reviewed By', 'Reviewed At'];

        $callback = function() use ($applications, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->created_at->format('Y-m-d H:i'),
                    $app->user->name ?? 'N/A',
                    $app->user->email ?? 'N/A',
                    $app->role_type,
                    $app->business_name,
                    $app->contact_person,
                    $app->contact_email,
                    $app->contact_phone,
                    $app->status,
                    $app->reviewer->name ?? 'N/A',
                    $app->reviewed_at ? $app->reviewed_at->format('Y-m-d H:i') : 'N/A',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get dashboard statistics
     */
    public function statistics()
    {
        $stats = [
            'total' => RoleApplication::count(),
            'pending' => RoleApplication::where('status', 'PENDING')->count(),
            'approved' => RoleApplication::where('status', 'APPROVED')->count(),
            'rejected' => RoleApplication::where('status', 'REJECTED')->count(),
            
            'by_role' => [
                'OWNER' => RoleApplication::where('role_type', 'OWNER')->count(),
                'FOOD' => RoleApplication::where('role_type', 'FOOD')->count(),
                'LAUNDRY' => RoleApplication::where('role_type', 'LAUNDRY')->count(),
            ],
            
            'by_role_pending' => [
                'OWNER' => RoleApplication::where('role_type', 'OWNER')->where('status', 'PENDING')->count(),
                'FOOD' => RoleApplication::where('role_type', 'FOOD')->where('status', 'PENDING')->count(),
                'LAUNDRY' => RoleApplication::where('role_type', 'LAUNDRY')->where('status', 'PENDING')->count(),
            ],
            
            'this_week' => RoleApplication::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => RoleApplication::whereMonth('created_at', now()->month)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Create service provider record for approved applications
     */
    private function createServiceProvider($application)
    {
        $serviceProvider = new ServiceProvider();
        $serviceProvider->user_id = $application->user_id;
        $serviceProvider->service_type = $application->role_type;
        $serviceProvider->business_name = $application->business_name;
        $serviceProvider->contact_email = $application->contact_email;
        $serviceProvider->contact_phone = $application->contact_phone;
        $serviceProvider->address = $application->business_address;
        $serviceProvider->city = $this->extractCity($application->business_address);
        $serviceProvider->latitude = $application->latitude;
        $serviceProvider->longitude = $application->longitude;
        $serviceProvider->service_radius_km = $application->service_radius_km ?? 5;
        $serviceProvider->status = 'ACTIVE';
        $serviceProvider->save();

        // Create service-specific configuration
        if ($application->role_type === 'FOOD') {
            $additionalData = $application->additional_data['food_provider'] ?? [];
            
            $serviceProvider->foodServiceConfig()->create([
                'supports_subscription' => in_array('SUBSCRIPTION', $additionalData['meal_types'] ?? []),
                'supports_pay_per_eat' => true,
                'opening_time' => $this->extractOpeningTime($additionalData['delivery_hours'] ?? '09:00-21:00'),
                'closing_time' => $this->extractClosingTime($additionalData['delivery_hours'] ?? '09:00-21:00'),
                'avg_preparation_minutes' => 30,
                'delivery_buffer_minutes' => 15,
                'subscription_discount_percent' => 10,
            ]);
        }

        if ($application->role_type === 'LAUNDRY') {
            $additionalData = $application->additional_data['laundry_provider'] ?? [];
            
            $serviceProvider->laundryServiceConfig()->create([
                'normal_turnaround_hours' => $additionalData['normal_turnaround_hours'] ?? 120,
                'rush_turnaround_hours' => $additionalData['rush_turnaround_hours'] ?? 48,
                'pickup_start_time' => '09:00:00',
                'pickup_end_time' => '18:00:00',
                'provides_pickup_service' => $additionalData['has_pickup_service'] ?? true,
                'pickup_fee' => 0,
            ]);
        }

        return $serviceProvider;
    }

    /**
     * Get role display name
     */
    private function getRoleDisplayName($roleType)
    {
        return match($roleType) {
            'OWNER' => 'Property Owner',
            'FOOD' => 'Food Provider',
            'LAUNDRY' => 'Laundry Provider',
            default => $roleType,
        };
    }

    /**
     * Extract city from address
     */
    private function extractCity($address)
    {
        // Simple extraction - you might want to improve this
        $parts = explode(',', $address);
        return count($parts) > 1 ? trim($parts[1]) : 'Unknown';
    }

    /**
     * Extract opening time from hours string
     */
    private function extractOpeningTime($hours)
    {
        $parts = explode('-', $hours);
        return trim($parts[0] ?? '09:00') . ':00';
    }

    /**
     * Extract closing time from hours string
     */
    private function extractClosingTime($hours)
    {
        $parts = explode('-', $hours);
        return trim($parts[1] ?? '21:00') . ':00';
    }
}