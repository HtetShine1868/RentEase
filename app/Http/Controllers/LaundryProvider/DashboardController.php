<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use App\Models\LaundryItem;
use App\Models\ServiceProvider;
use App\Models\LaundryServiceConfig;
use App\Models\ServiceRating;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    use Notifiable;

    /**
     * Show the dashboard
     */
    public function index()
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $provider = $serviceProvider;
        $config = LaundryServiceConfig::where('service_provider_id', $provider->id)->first();

        // Today's statistics
        $today = Carbon::today();
        
        $stats = [
            'total_orders' => LaundryOrder::where('service_provider_id', $provider->id)->count(),
            'pending_orders' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
                ->count(),
            'in_progress' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereIn('status', ['PICKED_UP', 'IN_PROGRESS'])
                ->count(),
            'ready_for_delivery' => LaundryOrder::where('service_provider_id', $provider->id)
                ->where('status', 'READY')
                ->count(),
            'delivered_today' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('actual_return_date', $today)
                ->where('status', 'DELIVERED')
                ->count(),
            'revenue_today' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereDate('actual_return_date', $today)
                ->where('status', 'DELIVERED')
                ->sum('total_amount'),
            'rush_orders' => LaundryOrder::where('service_provider_id', $provider->id)
                ->where('service_mode', 'RUSH')
                ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
                ->count(),
            'delayed_orders' => LaundryOrder::where('service_provider_id', $provider->id)
                ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
                ->whereDate('expected_return_date', '<', $today)
                ->count(),
            'average_rating' => $provider->rating ?? 0,
            'total_reviews' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->count(),
        ];

        // Recent orders
        $recentOrders = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Orders ready for pickup/delivery today
        $todaysSchedule = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->whereDate('pickup_time', $today)
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('pickup_time')
            ->get();

        // Orders due for return in next 3 days
        $upcomingReturns = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->whereBetween('expected_return_date', [$today, $today->copy()->addDays(3)])
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date')
            ->get();

        // Rush orders needing attention
        $rushOrders = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->where('service_mode', 'RUSH')
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date')
            ->get();

        // Chart data for last 7 days
        $chartData = $this->getChartData($provider->id);

        return view('laundry-provider.dashboard', compact(
            'stats',
            'recentOrders',
            'todaysSchedule',
            'upcomingReturns',
            'rushOrders',
            'chartData',
            'config'
        ));
    }

    /**
     * Get chart data for last 7 days
     */
    private function getChartData($providerId)
    {
        $dates = [];
        $orders = [];
        $revenue = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('D');
            
            $orderCount = LaundryOrder::where('service_provider_id', $providerId)
                ->whereDate('created_at', $date)
                ->count();
            $orders[] = $orderCount;
            
            $revenueAmount = LaundryOrder::where('service_provider_id', $providerId)
                ->whereDate('actual_return_date', $date)
                ->where('status', 'DELIVERED')
                ->sum('total_amount');
            $revenue[] = $revenueAmount;
        }

        return [
            'dates' => $dates,
            'orders' => $orders,
            'revenue' => $revenue
        ];
    }
}