<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\RoleApplication;
use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use App\Models\CommissionConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_users' => User::count(),
            'total_properties' => Property::count(),
            'total_bookings' => Booking::count(),
            'active_bookings' => Booking::whereIn('status', ['CONFIRMED', 'CHECKED_IN'])->count(),
            'total_revenue' => Payment::where('status', 'COMPLETED')->sum('amount'),
            'pending_applications' => RoleApplication::where('status', 'PENDING')->count(),
            'total_orders' => FoodOrder::count(),
            'active_providers' => ServiceProvider::where('status', 'ACTIVE')->count(),
        ];

        // Get recent applications
        $recentApplications = RoleApplication::with('user')
            ->latest()
            ->limit(5)
            ->get();

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'property'])
            ->latest()
            ->limit(5)
            ->get();

        // Get recent orders
        $recentOrders = FoodOrder::with(['user', 'serviceProvider'])
            ->latest()
            ->limit(5)
            ->get();

        // Get user growth data (last 7 days)
        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Get revenue data (last 7 days)
        $revenueData = Payment::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(amount) as total'))
            ->where('status', 'COMPLETED')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date')
            ->toArray();

        // Get commission rates
        $commissions = CommissionConfig::all()->keyBy('service_type');

        // Get role distribution
        $roleDistribution = [
            'owners' => User::whereHas('roles', function($q) { $q->where('name', 'OWNER'); })->count(),
            'food' => User::whereHas('roles', function($q) { $q->where('name', 'FOOD'); })->count(),
            'laundry' => User::whereHas('roles', function($q) { $q->where('name', 'LAUNDRY'); })->count(),
            'users' => User::whereHas('roles', function($q) { $q->where('name', 'USER'); })->count(),
        ];

        return view('admin.dashboard', compact(
            'stats',
            'recentApplications',
            'recentBookings',
            'recentOrders',
            'userGrowth',
            'revenueData',
            'commissions',
            'roleDistribution'
        ));
    }

    /**
     * Get dashboard statistics for AJAX
     */
    public function getStats()
    {
        $stats = [
            'total_users' => User::count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'ACTIVE')->count(),
            'total_bookings' => Booking::count(),
            'today_bookings' => Booking::whereDate('created_at', today())->count(),
            'total_revenue' => Payment::where('status', 'COMPLETED')->sum('amount'),
            'today_revenue' => Payment::where('status', 'COMPLETED')->whereDate('created_at', today())->sum('amount'),
            'pending_applications' => RoleApplication::where('status', 'PENDING')->count(),
            'active_providers' => ServiceProvider::where('status', 'ACTIVE')->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get chart data for AJAX
     */
    public function getChartData()
    {
        // User growth for last 30 days
        $userGrowth = User::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Revenue for last 30 days
        $revenue = Payment::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(amount) as total'))
            ->where('status', 'COMPLETED')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Bookings by status
        $bookingsByStatus = Booking::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        return response()->json([
            'user_growth' => $userGrowth,
            'revenue' => $revenue,
            'bookings_by_status' => $bookingsByStatus
        ]);
    }
}