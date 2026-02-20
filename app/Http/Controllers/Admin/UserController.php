<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Notification;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use Notifiable;

    /**
     * Display a listing of users (excluding admins)
     */
        public function index(Request $request)
        {
            $query = User::with('roles');

            // Search filter
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Role filter
            if ($request->filled('role')) {
                $query->whereHas('roles', function($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Date filter
            if ($request->filled('date_range')) {
                switch ($request->date_range) {
                    case 'today':
                        $query->whereDate('created_at', today());
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

            $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();

            // ===== FIX: Add all required stats keys =====
            $stats = [
                'total' => User::count(),
                'active' => User::where('status', 'ACTIVE')->count(),
                'banned' => User::where('status', 'BANNED')->count(),
                'new_today' => User::whereDate('created_at', today())->count(),
                'new_this_week' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)
                                        ->whereYear('created_at', now()->year)
                                        ->count(),
                'verified' => User::whereNotNull('email_verified_at')->count(),
                'unverified' => User::whereNull('email_verified_at')->count(),
            ];

            $roles = Role::all();

            return view('admin.users.index', compact('users', 'stats', 'roles'));
        }

    /**
     * Show user details
     */
    public function show($id)
    {
        $user = User::with(['roles', 'addresses', 'properties', 'bookings.property'])
            ->findOrFail($id);

        // Get user statistics
        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'active_bookings' => $user->bookings()->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])->count(),
            'total_orders' => $user->foodOrders()->count(),
            'total_complaints' => $user->complaints()->count(),
            'total_spent' => $user->payments()->where('status', 'COMPLETED')->sum('amount'),
        ];

        return view('admin.users.show', compact('user', 'stats'));
    }

    /**
     * Update user status (active/banned)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,BANNED'
        ]);

        $user = User::findOrFail($id);

        $oldStatus = $user->status;
        $user->status = $request->status;
        $user->save();

        // Send notification to user
        $this->createNotification(
            $user->id,
            'SYSTEM',
            'Account Status Updated',
            "Your account status has been changed from {$oldStatus} to {$request->status}.",
            'user',
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => "User status updated to {$request->status}"
        ]);
    }

    /**
     * Update user role
     */
    public function updateRole(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|in:USER,OWNER,FOOD,LAUNDRY'
        ]);

        $user = User::findOrFail($id);
        $role = Role::where('name', $request->role)->first();

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        // Remove all existing roles and assign new one
        DB::transaction(function() use ($user, $role) {
            $user->roles()->detach();
            $user->roles()->attach($role->id);
        });

        // Send notification to user
        $this->createNotification(
            $user->id,
            'SYSTEM',
            'Role Updated',
            "Your role has been updated to {$request->role}.",
            'user',
            $user->id
        );

        return response()->json([
            'success' => true,
            'message' => "User role updated to {$request->role}"
        ]);
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Don't allow deleting yourself
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account'
            ], 422);
        }

        DB::transaction(function() use ($user) {
            // Remove roles
            $user->roles()->detach();
            // Delete user (cascade will handle related records based on foreign keys)
            $user->delete();
        });

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::with('roles');

        // Apply filters (same as index)
        if ($request->filled('role')) {
            $query->whereHas('roles', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->get();

        $filename = 'users-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['ID', 'Name', 'Email', 'Phone', 'Role', 'Status', 'Verified', 'Joined Date'];

        $callback = function() use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone ?? 'N/A',
                    $user->roles->pluck('name')->implode(', '),
                    $user->status,
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk action on users
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|in:activate,ban,delete'
        ]);

        $count = 0;
        $users = User::whereIn('id', $request->user_ids)->get();

        foreach ($users as $user) {
            // Don't allow actions on self
            if ($user->id === auth()->id()) {
                continue;
            }

            switch ($request->action) {
                case 'activate':
                    if ($user->status !== 'ACTIVE') {
                        $user->status = 'ACTIVE';
                        $user->save();
                        $count++;
                    }
                    break;
                case 'ban':
                    if ($user->status !== 'BANNED') {
                        $user->status = 'BANNED';
                        $user->save();
                        $count++;
                    }
                    break;
                case 'delete':
                    $user->roles()->detach();
                    $user->delete();
                    $count++;
                    break;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "{$count} users processed successfully"
        ]);
    }
}