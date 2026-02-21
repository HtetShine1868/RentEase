<?php
// app/Http/Controllers/LaundryProvider/DashboardController.php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use App\Models\ServiceProvider;
use App\Models\ServiceRating;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $provider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->firstOrFail();
            
        $today = Carbon::today();
        
        // Stats
        $stats = [
            'total_orders_today' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('created_at', $today)
                ->count(),
                
            'pending_pickups' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('pickup_time', $today)
                ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
                ->count(),
                
            'ready_for_delivery' => LaundryOrder::where('service_provider_id', $provider->id)
                ->where('status', 'READY')
                ->count(),
                
            'in_progress' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereIn('status', ['PICKED_UP', 'IN_PROGRESS'])
                ->count(),
                
            'completed_today' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('actual_return_date', $today)
                ->where('status', 'DELIVERED')
                ->count(),
                
            'total_earnings_today' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('actual_return_date', $today)
                ->where('status', 'DELIVERED')
                ->sum('total_amount'),
                
            'average_rating' => ServiceRating::where('service_provider_id', $provider->id)
                ->avg('overall_rating') ?? 0,
                
            'total_reviews' => ServiceRating::where('service_provider_id', $provider->id)
                ->count()
        ];
        
        // Rush orders alert
        $rushOrders = LaundryOrder::where('service_provider_id', $provider->id)
            ->where('service_mode', 'RUSH')
            ->where('status', 'PENDING')
            ->where('pickup_time', '<=', Carbon::now()->addHours(2))
            ->count();
        
        // Today's timeline
        $todayPickups = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereDate('pickup_time', $today)
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->orderBy('pickup_time', 'asc')
            ->get();
            
        $todayDeliveries = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereDate('expected_return_date', $today)
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date', 'asc')
            ->get();
        
        // Recent orders
        $recentOrders = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Performance chart data (last 7 days)
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartData['labels'][] = $date->format('D');
            $chartData['orders'][] = LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('created_at', $date)
                ->count();
            $chartData['completed'][] = LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('actual_return_date', $date)
                ->where('status', 'DELIVERED')
                ->count();
        }
        
        return view('laundry-provider.dashboard.index', compact(
            'stats',
            'rushOrders',
            'todayPickups',
            'todayDeliveries',
            'recentOrders',
            'chartData'
        ));
    }
}