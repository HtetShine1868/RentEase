<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $query = FoodOrder::with(['user', 'items.foodItem', 'mealType'])
            ->where('service_provider_id', $serviceProvider->id);
        
        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Apply date range filter
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
        
        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['created_at', 'total_amount', 'status', 'order_reference'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        $query->orderBy($sortField, $sortDirection);
        
        // Get paginated results
        $orders = $query->paginate(15)->withQueryString();
        
        // Calculate statistics
        $stats = $this->getOrderStatistics($serviceProvider);
        
        // Get order status distribution
        $orderStatusDistribution = $this->getOrderStatusDistribution($serviceProvider);
        
        // Get today's delivery schedule
        $todaysSchedule = $this->getTodaysSchedule($serviceProvider);
        
        // Get commission rate
        $commissionRate = $this->getCommissionRate();
        
        return view('food-provider.orders.index', compact(
            'orders',
            'stats',
            'orderStatusDistribution',
            'todaysSchedule',
            'commissionRate'
        ));
    }
    
    /**
     * Display the specified order.
     */
    public function show($id)
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $order = FoodOrder::with([
            'user', 
            'items.foodItem', 
            'mealType',
            'subscription',
            'serviceProvider'
        ])->where('service_provider_id', $serviceProvider->id)
          ->findOrFail($id);
        
        return view('food-provider.orders.show', compact('order'));
    }
    
    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,ACCEPTED,PREPARING,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
        ]);
        
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No food service provider found for this user.'
                ], 403);
            }
            abort(403, 'No food service provider found for this user.');
        }
        
        $order = FoodOrder::where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);
        
        // Validate status transition
        $validTransitions = [
            'PENDING' => ['ACCEPTED', 'CANCELLED'],
            'ACCEPTED' => ['PREPARING', 'CANCELLED'],
            'PREPARING' => ['OUT_FOR_DELIVERY', 'CANCELLED'],
            'OUT_FOR_DELIVERY' => ['DELIVERED'],
            'DELIVERED' => [],
            'CANCELLED' => []
        ];
        
        if (!in_array($request->status, $validTransitions[$order->status] ?? [])) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid status transition from ' . $order->status . ' to ' . $request->status
                ], 422);
            }
            
            return redirect()->back()->with('error', 'Invalid status transition');
        }
        
        $oldStatus = $order->status;
        $order->status = $request->status;
        
        // Set timestamps based on status
        if ($request->status === 'OUT_FOR_DELIVERY') {
            // Get delivery buffer from service config
            $foodServiceConfig = $order->serviceProvider->foodServiceConfig;
            $deliveryBuffer = $foodServiceConfig ? $foodServiceConfig->delivery_buffer_minutes : 15;
            $order->estimated_delivery_time = now()->addMinutes($order->distance_km * $deliveryBuffer);
        } elseif ($request->status === 'DELIVERED') {
            $order->actual_delivery_time = now();
        }
        
        $order->save();
        
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'order' => $order
            ]);
        }
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }
    
    /**
     * Bulk update order status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:food_orders,id',
            'status' => 'required|in:PENDING,ACCEPTED,PREPARING,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
        ]);
        
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json([
                'success' => false,
                'message' => 'No food service provider found for this user.'
            ], 403);
        }
        
        $orders = FoodOrder::whereIn('id', $request->order_ids)
            ->where('service_provider_id', $serviceProvider->id)
            ->get();
        
        $updatedCount = 0;
        foreach ($orders as $order) {
            // Validate each order's status transition
            $validTransitions = [
                'PENDING' => ['ACCEPTED', 'CANCELLED'],
                'ACCEPTED' => ['PREPARING', 'CANCELLED'],
                'PREPARING' => ['OUT_FOR_DELIVERY', 'CANCELLED'],
                'OUT_FOR_DELIVERY' => ['DELIVERED'],
                'DELIVERED' => [],
                'CANCELLED' => []
            ];
            
            if (in_array($request->status, $validTransitions[$order->status] ?? [])) {
                $order->status = $request->status;
                
                if ($request->status === 'DELIVERED') {
                    $order->actual_delivery_time = now();
                }
                
                $order->save();
                $updatedCount++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $updatedCount . ' orders updated successfully'
        ]);
    }
    
    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $query = FoodOrder::with(['user', 'items'])
            ->where('service_provider_id', $serviceProvider->id);
        
        // Apply filters
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
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $orders = $query->get();
        
        $filename = 'orders-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $columns = ['Order Reference', 'Customer', 'Type', 'Items', 'Status', 'Amount', 'Commission', 'Your Earnings', 'Date'];
        
        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_reference,
                    $order->user->name ?? 'N/A',
                    $order->order_type === 'SUBSCRIPTION_MEAL' ? 'Subscription' : 'Pay-per-eat',
                    $order->items->count(),
                    $order->status,
                    number_format($order->total_amount, 2),
                    number_format($order->commission_amount, 2),
                    number_format($order->total_amount - $order->commission_amount, 2),
                    $order->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Print order invoice.
     */
    public function printInvoice($id)
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $order = FoodOrder::with([
            'user', 
            'items.foodItem', 
            'mealType',
            'serviceProvider'
        ])->where('service_provider_id', $serviceProvider->id)
          ->findOrFail($id);
        
        return view('food-provider.orders.print', compact('order'));
    }
    
    /**
     * Get order statistics.
     */
    private function getOrderStatistics($serviceProvider)
    {
        $today = now()->startOfDay();
        $now = now();
        
        return [
            'pending' => FoodOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'PENDING')
                ->count(),
                
            'today' => FoodOrder::where('service_provider_id', $serviceProvider->id)
                ->whereDate('created_at', $today)
                ->count(),
                
            'delayed' => FoodOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', '!=', 'DELIVERED')
                ->where('status', '!=', 'CANCELLED')
                ->where('estimated_delivery_time', '<', $now)
                ->count(),
                
            'revenue_today' => FoodOrder::where('service_provider_id', $serviceProvider->id)
                ->whereDate('created_at', $today)
                ->where('status', 'DELIVERED')
                ->sum('total_amount')
        ];
    }
    
    /**
     * Get order status distribution.
     */
    private function getOrderStatusDistribution($serviceProvider)
    {
        $total = FoodOrder::where('service_provider_id', $serviceProvider->id)->count();
        
        if ($total === 0) {
            return collect([]);
        }
        
        $distribution = FoodOrder::where('service_provider_id', $serviceProvider->id)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        return $distribution->map(function ($item) use ($total) {
            return [
                'status' => $item->status,
                'count' => $item->count,
                'total' => $total,
                'percentage' => round(($item->count / $total) * 100, 1)
            ];
        });
    }
    
    /**
     * Get today's delivery schedule.
     */
    private function getTodaysSchedule($serviceProvider)
    {
        return FoodOrder::with('user')
            ->where('service_provider_id', $serviceProvider->id)
            ->whereDate('meal_date', today())
            ->whereIn('status', ['PREPARING', 'OUT_FOR_DELIVERY', 'ACCEPTED'])
            ->orderBy('estimated_delivery_time')
            ->get();
    }
    
    /**
     * Get commission rate.
     */
    private function getCommissionRate()
    {
        return DB::table('commission_configs')
            ->where('service_type', 'FOOD')
            ->value('rate') ?? 8.00;
    }
}