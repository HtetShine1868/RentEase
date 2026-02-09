<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use App\Models\FoodItem;
use App\Models\FoodServiceConfig;
use App\Models\MealType;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->first();

            if (!$foodProvider) {
                // Return empty data if no food provider found
                return $this->returnEmptyData();
            }

            // Start query with relationships
            $query = FoodOrder::with(['user', 'items', 'items.foodItem'])
                ->where('service_provider_id', $foodProvider->id)
                ->orderBy('created_at', 'desc');

            // Apply search filter
            if ($request->has('search') && $request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('order_reference', 'like', "%{$search}%")
                      ->orWhereHas('user', function($q2) use ($search) {
                          $q2->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%")
                             ->orWhere('phone', 'like', "%{$search}%");
                      });
                });
            }

            // Apply status filter
            if ($request->has('status') && $request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Apply order type filter
            if ($request->has('order_type') && $request->filled('order_type')) {
                $query->where('order_type', $request->order_type);
            }

            // Apply date filter
            if ($request->has('date') && $request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            // Apply delivery date filter
            if ($request->has('delivery_date') && $request->filled('delivery_date')) {
                $query->whereDate('meal_date', $request->delivery_date);
            }

            $orders = $query->paginate(15);

            // Calculate stats
            $today = now()->format('Y-m-d');
            
            $pendingOrders = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->where('status', 'PENDING')
                ->count();

            $todayOrders = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->whereDate('created_at', $today)
                ->count();

            // Delayed orders: orders not delivered but past estimated delivery time
            $delayedOrders = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
                ->where('estimated_delivery_time', '<', now())
                ->count();

            $todayRevenue = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->whereDate('created_at', $today)
                ->where('status', 'DELIVERED')
                ->sum('total_amount');

            // Get commission rate from commission_configs table
            $commissionRate = DB::table('commission_configs')
                ->where('service_type', 'FOOD')
                ->value('rate') ?? 8.00;

            // Get order status distribution
            $statusDistribution = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            // Prepare order status distribution for view
            $allStatuses = ['PENDING', 'ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED', 'CANCELLED'];
            $orderStatusDistribution = [];
            $totalOrders = $statusDistribution->sum('count');

            foreach ($allStatuses as $status) {
                $statusData = $statusDistribution->firstWhere('status', $status);
                $count = $statusData ? $statusData->count : 0;
                
                $orderStatusDistribution[] = [
                    'status' => $status,
                    'count' => $count,
                    'total' => $totalOrders
                ];
            }

            // Get today's schedule
            $todaysSchedule = FoodOrder::with('user')
                ->where('service_provider_id', $foodProvider->id)
                ->whereDate('meal_date', $today)
                ->whereIn('status', ['ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY'])
                ->orderBy('estimated_delivery_time')
                ->limit(10)
                ->get();

            // Get meal types for filter
            $mealTypes = MealType::all();

            // Calculate additional stats
            $totalRevenue = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->where('status', 'DELIVERED')
                ->sum('total_amount');

            $totalOrdersCount = FoodOrder::where('service_provider_id', $foodProvider->id)->count();
            $deliveredOrders = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->where('status', 'DELIVERED')
                ->count();

            // Calculate average order value
            $averageOrderValue = $deliveredOrders > 0 ? $totalRevenue / $deliveredOrders : 0;

            return view('food-provider.orders.index', compact(
                'orders',
                'pendingOrders',
                'todayOrders',
                'delayedOrders',
                'todayRevenue',
                'commissionRate',
                'orderStatusDistribution',
                'todaysSchedule',
                'mealTypes',
                'totalRevenue',
                'totalOrdersCount',
                'deliveredOrders',
                'averageOrderValue'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@index: ' . $e->getMessage());
            return $this->returnEmptyData()->with('error', 'Error loading orders: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show($id)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            // Load order with all necessary relationships
            $order = FoodOrder::with([
                'user',
                'items.foodItem',
                'mealType',
                'subscription',
                'booking'
            ])->where('service_provider_id', $foodProvider->id)
              ->findOrFail($id);

            // Load order items with food items
            $order->load(['items' => function($query) {
                $query->with('foodItem');
            }]);

            // Get payment for this order
            $payment = Payment::where('payable_type', 'FOOD_ORDER')
                ->where('payable_id', $order->id)
                ->first();

            // Get previous orders from same customer
            $previousOrders = FoodOrder::where('user_id', $order->user_id)
                ->where('service_provider_id', $foodProvider->id)
                ->where('id', '!=', $order->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // Calculate earnings breakdown
            $providerEarnings = $order->total_amount - $order->commission_amount;
            $commissionPercentage = $order->total_amount > 0 ? 
                ($order->commission_amount / $order->total_amount) * 100 : 0;

            // Prepare order timeline
            $timeline = $this->prepareOrderTimeline($order);

            // Get delivery details
            $deliveryDistance = $order->distance_km ?? 0;
            $deliveryTime = $order->estimated_delivery_time ? 
                $order->estimated_delivery_time->format('h:i A') : 'N/A';

            return view('food-provider.orders.show', compact(
                'order',
                'payment',
                'previousOrders',
                'providerEarnings',
                'commissionPercentage',
                'timeline',
                'deliveryDistance',
                'deliveryTime'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@show: ' . $e->getMessage());
            return redirect()->route('food-provider.orders.index')
                ->with('error', 'Order not found or you don\'t have permission to view it.');
        }
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:ACCEPTED,PREPARING,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
            ]);

            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $order = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            $oldStatus = $order->status;
            $newStatus = $request->status;

            // Validate status transition
            if (!$this->isValidStatusTransition($oldStatus, $newStatus)) {
                return back()->with('error', 'Invalid status transition from ' . $oldStatus . ' to ' . $newStatus);
            }

            $order->status = $newStatus;

            // Update timestamps based on status
            switch ($newStatus) {
                case 'ACCEPTED':
                    $order->accepted_at = now();
                    break;
                case 'PREPARING':
                    $order->preparing_at = now();
                    break;
                case 'OUT_FOR_DELIVERY':
                    $order->out_for_delivery_at = now();
                    break;
                case 'DELIVERED':
                    $order->actual_delivery_time = now();
                    $order->delivered_at = now();
                    
                    // Update provider's order count
                    $foodProvider->increment('total_orders');
                    break;
                case 'CANCELLED':
                    $order->cancelled_at = now();
                    break;
            }

            $order->save();

            // Create notification for user
            $this->createOrderStatusNotification($order, $oldStatus, $newStatus);

            return back()->with('success', 'Order status updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@updateStatus: ' . $e->getMessage());
            return back()->with('error', 'Error updating order status: ' . $e->getMessage());
        }
    }

    /**
     * Export orders to CSV.
     */
    public function export(Request $request)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $query = FoodOrder::with(['user', 'items'])
                ->where('service_provider_id', $foodProvider->id);

            // Apply filters if present
            if ($request->has('start_date') && $request->filled('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->filled('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            if ($request->has('status') && $request->filled('status')) {
                $query->where('status', $request->status);
            }

            $orders = $query->orderBy('created_at', 'desc')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="orders_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($orders) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'Order ID',
                    'Customer',
                    'Email',
                    'Phone',
                    'Order Type',
                    'Status',
                    'Items Count',
                    'Total Amount',
                    'Commission',
                    'Earnings',
                    'Order Date',
                    'Delivery Date',
                    'Delivery Address'
                ]);

                // Add data rows
                foreach ($orders as $order) {
                    fputcsv($file, [
                        $order->order_reference,
                        $order->user->name ?? 'N/A',
                        $order->user->email ?? 'N/A',
                        $order->user->phone ?? 'N/A',
                        $order->order_type,
                        $order->status,
                        $order->items->count(),
                        $order->total_amount,
                        $order->commission_amount,
                        $order->total_amount - $order->commission_amount,
                        $order->created_at->format('Y-m-d H:i:s'),
                        $order->meal_date,
                        $order->delivery_address
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@export: ' . $e->getMessage());
            return back()->with('error', 'Error exporting orders: ' . $e->getMessage());
        }
    }

    /**
     * Print order details.
     */
    public function print($id)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $order = FoodOrder::with([
                'user',
                'items.foodItem',
                'mealType'
            ])->where('service_provider_id', $foodProvider->id)
              ->findOrFail($id);

            $payment = Payment::where('payable_type', 'FOOD_ORDER')
                ->where('payable_id', $order->id)
                ->first();

            return view('food-provider.orders.print', compact('order', 'payment'));

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@print: ' . $e->getMessage());
            return redirect()->route('food-provider.orders.index')
                ->with('error', 'Error printing order: ' . $e->getMessage());
        }
    }

    /**
     * Get order statistics for dashboard.
     */
    public function statistics(Request $request)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
            $endDate = $request->get('end_date', now()->format('Y-m-d'));

            $stats = [
                'total_orders' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                
                'total_revenue' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->where('status', 'DELIVERED')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum('total_amount'),
                
                'total_earnings' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->where('status', 'DELIVERED')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->sum(DB::raw('total_amount - commission_amount')),
                
                'pending_orders' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->where('status', 'PENDING')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                
                'delivered_orders' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->where('status', 'DELIVERED')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
                
                'cancelled_orders' => FoodOrder::where('service_provider_id', $foodProvider->id)
                    ->where('status', 'CANCELLED')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->count(),
            ];

            // Daily revenue for chart
            $dailyRevenue = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->where('status', 'DELIVERED')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(total_amount) as revenue')
                )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Order status distribution
            $statusDistribution = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->get();

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'daily_revenue' => $dailyRevenue,
                'status_distribution' => $statusDistribution
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@statistics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics'
            ], 500);
        }
    }

    /**
     * Bulk update order statuses.
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'order_ids' => 'required|array',
                'order_ids.*' => 'exists:food_orders,id',
                'status' => 'required|in:ACCEPTED,PREPARING,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
            ]);

            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $orderIds = $request->order_ids;
            $newStatus = $request->status;

            // Update orders that belong to this provider
            $updatedCount = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->whereIn('id', $orderIds)
                ->update([
                    'status' => $newStatus,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} order(s) to {$newStatus} status."
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@bulkUpdateStatus: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error updating order statuses'
            ], 500);
        }
    }

    /**
     * Prepare order timeline.
     */
    private function prepareOrderTimeline($order)
    {
        $timeline = [];

        // Order placed
        $timeline[] = [
            'event' => 'Order Placed',
            'time' => $order->created_at,
            'completed' => true,
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue'
        ];

        // Order accepted
        if ($order->status != 'PENDING') {
            $timeline[] = [
                'event' => 'Order Accepted',
                'time' => $order->accepted_at ?? $order->updated_at,
                'completed' => true,
                'icon' => 'fas fa-check',
                'color' => 'green'
            ];
        }

        // Preparing
        if (in_array($order->status, ['PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED'])) {
            $timeline[] = [
                'event' => 'Preparing Food',
                'time' => $order->preparing_at ?? $order->updated_at,
                'completed' => true,
                'icon' => 'fas fa-utensils',
                'color' => 'yellow'
            ];
        }

        // Out for delivery
        if (in_array($order->status, ['OUT_FOR_DELIVERY', 'DELIVERED'])) {
            $timeline[] = [
                'event' => 'Out for Delivery',
                'time' => $order->out_for_delivery_at ?? $order->updated_at,
                'completed' => true,
                'icon' => 'fas fa-shipping-fast',
                'color' => 'purple'
            ];
        }

        // Delivered
        if ($order->status == 'DELIVERED') {
            $timeline[] = [
                'event' => 'Delivered',
                'time' => $order->actual_delivery_time ?? $order->delivered_at,
                'completed' => true,
                'icon' => 'fas fa-flag-checkered',
                'color' => 'green'
            ];
        }

        // Future steps
        if ($order->status == 'PENDING') {
            $timeline[] = [
                'event' => 'Accept Order',
                'time' => null,
                'completed' => false,
                'icon' => 'fas fa-clock',
                'color' => 'gray'
            ];
        }

        if (in_array($order->status, ['PENDING', 'ACCEPTED'])) {
            $timeline[] = [
                'event' => 'Start Preparing',
                'time' => null,
                'completed' => false,
                'icon' => 'fas fa-clock',
                'color' => 'gray'
            ];
        }

        if (in_array($order->status, ['PENDING', 'ACCEPTED', 'PREPARING'])) {
            $timeline[] = [
                'event' => 'Out for Delivery',
                'time' => null,
                'completed' => false,
                'icon' => 'fas fa-clock',
                'color' => 'gray'
            ];
        }

        if (in_array($order->status, ['PENDING', 'ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY'])) {
            $timeline[] = [
                'event' => 'Delivered',
                'time' => null,
                'completed' => false,
                'icon' => 'fas fa-clock',
                'color' => 'gray'
            ];
        }

        return $timeline;
    }

    /**
     * Validate status transition.
     */
    private function isValidStatusTransition($fromStatus, $toStatus)
    {
        $validTransitions = [
            'PENDING' => ['ACCEPTED', 'CANCELLED'],
            'ACCEPTED' => ['PREPARING', 'CANCELLED'],
            'PREPARING' => ['OUT_FOR_DELIVERY', 'CANCELLED'],
            'OUT_FOR_DELIVERY' => ['DELIVERED', 'CANCELLED'],
            'DELIVERED' => [],
            'CANCELLED' => []
        ];

        return in_array($toStatus, $validTransitions[$fromStatus] ?? []);
    }

    /**
     * Create notification for order status change.
     */
    private function createOrderStatusNotification($order, $oldStatus, $newStatus)
    {
        try {
            DB::table('notifications')->insert([
                'user_id' => $order->user_id,
                'type' => 'ORDER',
                'title' => 'Order Status Updated',
                'message' => "Your order #{$order->order_reference} status changed from {$oldStatus} to {$newStatus}.",
                'related_entity_type' => 'FOOD_ORDER',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating notification: ' . $e->getMessage());
        }
    }

    /**
     * Return empty data for views.
     */
    private function returnEmptyData()
    {
        return view('food-provider.orders.index', [
            'orders' => collect([]),
            'pendingOrders' => 0,
            'todayOrders' => 0,
            'delayedOrders' => 0,
            'todayRevenue' => 0,
            'commissionRate' => 8.00,
            'orderStatusDistribution' => [],
            'todaysSchedule' => collect([]),
            'mealTypes' => collect([]),
            'totalRevenue' => 0,
            'totalOrdersCount' => 0,
            'deliveredOrders' => 0,
            'averageOrderValue' => 0
        ]);
    }

    /**
     * Get order counts by status.
     */
    public function getOrderCounts()
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $counts = FoodOrder::where('service_provider_id', $foodProvider->id)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status');

            return response()->json([
                'success' => true,
                'counts' => $counts
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in OrderController@getOrderCounts: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading order counts'
            ], 500);
        }
    }
}