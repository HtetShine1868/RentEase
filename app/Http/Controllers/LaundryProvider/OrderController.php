<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\ServiceProvider;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $provider;
    
    /**
     * Get the authenticated laundry provider
     */
    private function getProvider()
    {
        if (!$this->provider) {
            $this->provider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'LAUNDRY')
                ->firstOrFail();
        }
        return $this->provider;
    }
    
    /**
     * Display a listing of orders with tabs
     */
    public function index(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        
        // Get all orders for this provider with pagination
        $allOrders = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        // Normal orders data
        $normalOrders = [
            'pickup_today' => $this->getNormalPickupToday($provider->id, $carbonDate),
            'deliver_today' => $this->getNormalDeliverToday($provider->id, $carbonDate),
            'in_progress' => $this->getNormalInProgress($provider->id),
            'all' => $this->getNormalOrders($provider->id)
        ];
        
        // Rush orders data
        $rushOrders = [
            'rush_pickup_today' => $this->getRushPickupToday($provider->id, $carbonDate),
            'rush_deliver_today' => $this->getRushDeliverToday($provider->id, $carbonDate),
            'rush_in_progress' => $this->getRushInProgress($provider->id),
            'all' => $this->getRushOrders($provider->id)
        ];
        
        // Calculate rush count for badge
        $rushCount = $rushOrders['rush_pickup_today']->count() + 
                     $rushOrders['rush_deliver_today']->count() + 
                     $rushOrders['rush_in_progress']->count();
        
        return view('laundry-provider.orders.index', compact(
            'normalOrders',
            'rushOrders',
            'allOrders',
            'rushCount'
        ));
    }
    
    /**
     * Get rush orders only (for rush tab)
     */
    public function rushOrders(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        
        $rushOrders = [
            'rush_pickup_today' => $this->getRushPickupToday($provider->id, $carbonDate),
            'rush_deliver_today' => $this->getRushDeliverToday($provider->id, $carbonDate),
            'rush_in_progress' => $this->getRushInProgress($provider->id),
            'all' => $this->getRushOrders($provider->id)
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('laundry-provider.orders.partials.rush-tab', ['orders' => $rushOrders])->render()
            ]);
        }
        
        return view('laundry-provider.orders.rush', compact('rushOrders'));
    }
    
    /**
     * Get normal orders only (for normal tab)
     */
    public function normalOrders(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        
        $normalOrders = [
            'pickup_today' => $this->getNormalPickupToday($provider->id, $carbonDate),
            'deliver_today' => $this->getNormalDeliverToday($provider->id, $carbonDate),
            'in_progress' => $this->getNormalInProgress($provider->id),
            'all' => $this->getNormalOrders($provider->id)
        ];
        
        if ($request->ajax()) {
            return response()->json([
                'html' => view('laundry-provider.orders.partials.normal-tab', ['orders' => $normalOrders])->render()
            ]);
        }
        
        return view('laundry-provider.orders.normal', compact('normalOrders'));
    }
    
    /**
     * Filter orders by date and tab (AJAX endpoint)
     */
    public function filter(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $tab = $request->get('tab', 'normal');
        $search = $request->get('search', '');
        $carbonDate = Carbon::parse($date);
        
        $response = [];
        
        // Always prepare all tabs data for faster switching
        // Normal tab data
        $normalOrders = [
            'pickup_today' => $this->getNormalPickupToday($provider->id, $carbonDate),
            'deliver_today' => $this->getNormalDeliverToday($provider->id, $carbonDate),
            'in_progress' => $this->getNormalInProgress($provider->id)
        ];
        
        // Apply search filter if provided
        if ($search) {
            foreach ($normalOrders as $key => $collection) {
                $normalOrders[$key] = $collection->filter(function($order) use ($search) {
                    return str_contains(strtolower($order->order_reference), strtolower($search)) ||
                           str_contains(strtolower($order->user->name), strtolower($search)) ||
                           str_contains(strtolower($order->user->phone ?? ''), strtolower($search));
                });
            }
        }
        $response['normal'] = view('laundry-provider.orders.partials.normal-tab', ['orders' => $normalOrders])->render();
        
        // Rush tab data
        $rushOrders = [
            'rush_pickup_today' => $this->getRushPickupToday($provider->id, $carbonDate),
            'rush_deliver_today' => $this->getRushDeliverToday($provider->id, $carbonDate),
            'rush_in_progress' => $this->getRushInProgress($provider->id)
        ];
        
        // Apply search filter if provided
        if ($search) {
            foreach ($rushOrders as $key => $collection) {
                $rushOrders[$key] = $collection->filter(function($order) use ($search) {
                    return str_contains(strtolower($order->order_reference), strtolower($search)) ||
                           str_contains(strtolower($order->user->name), strtolower($search)) ||
                           str_contains(strtolower($order->user->phone ?? ''), strtolower($search));
                });
            }
        }
        $response['rush'] = view('laundry-provider.orders.partials.rush-tab', ['orders' => $rushOrders])->render();
        
        // All orders tab data
        $allOrders = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $provider->id)
            ->whereDate('created_at', $carbonDate)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Apply search filter if provided
        if ($search) {
            $allOrders = $allOrders->filter(function($order) use ($search) {
                return str_contains(strtolower($order->order_reference), strtolower($search)) ||
                       str_contains(strtolower($order->user->name), strtolower($search)) ||
                       str_contains(strtolower($order->user->phone ?? ''), strtolower($search));
            });
        }
        $response['all'] = view('laundry-provider.orders.partials.all-orders-tab', ['orders' => $allOrders])->render();
        
        return response()->json($response);
    }
    
    /**
     * Search orders (AJAX endpoint)
     */
    public function search(Request $request)
    {
        $provider = $this->getProvider();
        
        $searchTerm = $request->get('q', '');
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        
        $orders = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $provider->id)
            ->whereDate('created_at', $carbonDate)
            ->where(function($query) use ($searchTerm) {
                $query->where('order_reference', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('user', function($q) use ($searchTerm) {
                          $q->where('name', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('phone', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                      });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'html' => view('laundry-provider.orders.partials.search-results', ['orders' => $orders])->render(),
            'count' => $orders->count()
        ]);
    }
    
    /**
     * Display the specified order
     */
    public function show($id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::with(['user', 'items.laundryItem', 'serviceProvider'])
            ->where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        // If it's an AJAX request, return just the modal content
        if (request()->ajax()) {
            return view('laundry-provider.orders.partials.order-details-content', compact('order'));
        }
        
        return view('laundry-provider.orders.show', compact('order'));
    }
    
    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,PICKUP_SCHEDULED,PICKED_UP,IN_PROGRESS,READY,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
        ]);
        
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $oldStatus = $order->status;
        $order->status = $request->status;
        
        // Update timestamps based on status
        if ($request->status == 'PICKED_UP' && $oldStatus != 'PICKED_UP') {
            $order->pickup_time = now();
        } elseif ($request->status == 'DELIVERED' && $oldStatus != 'DELIVERED') {
            $order->actual_return_date = now();
        } elseif ($request->status == 'IN_PROGRESS' && $oldStatus != 'IN_PROGRESS') {
            // Could add started_processing_at timestamp if you have that field
        }
        
        $order->save();
        
        // Create notification for customer if status changes to certain states
        if (in_array($request->status, ['PICKED_UP', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED'])) {
            $this->sendStatusNotification($order);
        }
        
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Order status updated successfully',
                'status' => $order->status,
                'order' => $order
            ]);
        }
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }
    
    /**
     * Accept an order
     */
    public function accept(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'PICKUP_SCHEDULED';
        $order->save();
        
        // Notify customer
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'ORDER',
            'title' => 'Order Accepted',
            'message' => "Your laundry order #{$order->order_reference} has been accepted and pickup is scheduled.",
            'related_entity_type' => 'laundry_order',
            'related_entity_id' => $order->id,
            'channel' => 'IN_APP'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order accepted successfully'
        ]);
    }
    
    /**
     * Schedule pickup for an order
     */
    public function schedulePickup(Request $request, $id)
    {
        $request->validate([
            'pickup_time' => 'required|date',
            'notes' => 'nullable|string|max:255'
        ]);
        
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->pickup_time = Carbon::parse($request->pickup_time);
        $order->status = 'PICKUP_SCHEDULED';
        $order->save();
        
        // Notify customer
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'ORDER',
            'title' => 'Pickup Scheduled',
            'message' => "Your laundry pickup has been scheduled for " . Carbon::parse($request->pickup_time)->format('M d, Y g:i A'),
            'related_entity_type' => 'laundry_order',
            'related_entity_id' => $order->id,
            'channel' => 'IN_APP'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pickup scheduled successfully'
        ]);
    }
    
    /**
     * Mark order as picked up
     */
    public function markPickedUp(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'PICKED_UP';
        $order->pickup_time = now(); // Update actual pickup time
        $order->save();
        
        $this->sendStatusNotification($order);
        
        return response()->json([
            'success' => true,
            'message' => 'Order marked as picked up'
        ]);
    }
    
    /**
     * Start processing order
     */
    public function startProcessing(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'IN_PROGRESS';
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Processing started'
        ]);
    }
    
    /**
     * Mark order as ready
     */
    public function markReady(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'READY';
        $order->save();
        
        $this->sendStatusNotification($order);
        
        return response()->json([
            'success' => true,
            'message' => 'Order marked as ready'
        ]);
    }
    
    /**
     * Mark order as out for delivery
     */
    public function outForDelivery(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'OUT_FOR_DELIVERY';
        $order->save();
        
        $this->sendStatusNotification($order);
        
        return response()->json([
            'success' => true,
            'message' => 'Order marked as out for delivery'
        ]);
    }
    
    /**
     * Mark order as delivered
     */
    public function deliver(Request $request, $id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'DELIVERED';
        $order->actual_return_date = now();
        $order->save();
        
        $this->sendStatusNotification($order);
        
        // Update provider's total orders count
        $provider = ServiceProvider::find($order->service_provider_id);
        $provider->total_orders = LaundryOrder::where('service_provider_id', $provider->id)
            ->where('status', 'DELIVERED')
            ->count();
        $provider->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Order marked as delivered'
        ]);
    }
    
    /**
     * Assign staff to order
     */
    public function assignStaff(Request $request, $id)
    {
        $request->validate([
            'staff_id' => 'required|integer',
            'staff_name' => 'nullable|string|max:100'
        ]);
        
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        // Add assigned_staff_id and assigned_staff_name to your orders table if needed
        // $order->assigned_staff_id = $request->staff_id;
        // $order->assigned_staff_name = $request->staff_name;
        $order->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Staff assigned successfully'
        ]);
    }
    
    /**
     * Reschedule pickup time
     */
    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'pickup_time' => 'required|date',
            'reason' => 'nullable|string|max:500'
        ]);
        
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $oldPickupTime = $order->pickup_time;
        $order->pickup_time = Carbon::parse($request->pickup_time);
        $order->save();
        
        // Notify customer about reschedule
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'ORDER',
            'title' => 'Pickup Rescheduled',
            'message' => "Your pickup time has been changed from " . 
                Carbon::parse($oldPickupTime)->format('M d, g:i A') . " to " . 
                Carbon::parse($request->pickup_time)->format('M d, g:i A'),
            'related_entity_type' => 'laundry_order',
            'related_entity_id' => $order->id,
            'channel' => 'IN_APP'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Pickup time rescheduled successfully'
        ]);
    }
    
    /**
     * Cancel an order
     */
    public function cancel(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);
        
        $provider = $this->getProvider();
        
        $order = LaundryOrder::where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        $order->status = 'CANCELLED';
        $order->cancellation_reason = $request->reason;
        $order->save();
        
        // Notify customer about cancellation
        Notification::create([
            'user_id' => $order->user_id,
            'type' => 'ORDER',
            'title' => 'Order Cancelled',
            'message' => "Your order #{$order->order_reference} has been cancelled. Reason: {$request->reason}",
            'related_entity_type' => 'laundry_order',
            'related_entity_id' => $order->id,
            'channel' => 'IN_APP'
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully'
        ]);
    }
    
    /**
     * Bulk update order status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer',
            'status' => 'required|in:PENDING,PICKUP_SCHEDULED,PICKED_UP,IN_PROGRESS,READY,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
        ]);
        
        $provider = $this->getProvider();
        
        $updated = LaundryOrder::whereIn('id', $request->order_ids)
            ->where('service_provider_id', $provider->id)
            ->update(['status' => $request->status]);
        
        // Notify all affected customers
        $orders = LaundryOrder::whereIn('id', $request->order_ids)
            ->where('service_provider_id', $provider->id)
            ->get();
            
        foreach ($orders as $order) {
            if (in_array($request->status, ['PICKED_UP', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED'])) {
                $this->sendStatusNotification($order);
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => $updated . ' orders updated successfully'
        ]);
    }
    
    /**
     * Bulk assign staff
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'integer',
            'staff_id' => 'required|integer',
            'staff_name' => 'nullable|string|max:100'
        ]);
        
        $provider = $this->getProvider();
        
        $updated = LaundryOrder::whereIn('id', $request->order_ids)
            ->where('service_provider_id', $provider->id)
            ->update([
                'assigned_staff_id' => $request->staff_id,
                'assigned_staff_name' => $request->staff_name
            ]);
        
        return response()->json([
            'success' => true,
            'message' => $updated . ' orders assigned successfully'
        ]);
    }
    
    /**
     * Get calendar view of orders
     */
    public function calendar(Request $request)
    {
        $provider = $this->getProvider();
        
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $pickups = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereBetween('pickup_time', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy(function($order) {
                return Carbon::parse($order->pickup_time)->format('Y-m-d');
            });
        
        $deliveries = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereBetween('expected_return_date', [$startDate, $endDate])
            ->with('user')
            ->get()
            ->groupBy(function($order) {
                return Carbon::parse($order->expected_return_date)->format('Y-m-d');
            });
        
        if ($request->ajax()) {
            return response()->json([
                'pickups' => $pickups,
                'deliveries' => $deliveries
            ]);
        }
        
        return view('laundry-provider.orders.calendar', compact('pickups', 'deliveries', 'month', 'year'));
    }
    
    /**
     * Get timeline view
     */
    public function timeline(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $carbonDate = Carbon::parse($date);
        
        $pickups = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereDate('pickup_time', $carbonDate)
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->with('user')
            ->orderBy('pickup_time')
            ->get();
        
        $deliveries = LaundryOrder::where('service_provider_id', $provider->id)
            ->whereDate('expected_return_date', $carbonDate)
            ->whereIn('status', ['READY', 'OUT_FOR_DELIVERY'])
            ->with('user')
            ->orderBy('expected_return_date')
            ->get();
        
        return view('laundry-provider.orders.timeline', compact('pickups', 'deliveries', 'date'));
    }
    
    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $provider = $this->getProvider();
        
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $type = $request->get('type', 'all'); // all, normal, rush
        $format = $request->get('format', 'csv'); // csv, excel
        
        $query = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $provider->id)
            ->whereDate('created_at', $date);
            
        if ($type != 'all') {
            $query->where('service_mode', strtoupper($type));
        }
        
        $orders = $query->get();
        
        // Generate CSV export
        $filename = "orders-{$date}-{$type}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Order Reference',
                'Customer Name',
                'Customer Phone',
                'Service Mode',
                'Is Rush',
                'Status',
                'Items Count',
                'Base Amount',
                'Rush Surcharge',
                'Pickup Fee',
                'Commission',
                'Total Amount',
                'Pickup Time',
                'Expected Return',
                'Actual Return',
                'Created At'
            ]);
            
            // Data rows
            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_reference,
                    $order->user->name,
                    $order->user->phone ?? '',
                    $order->service_mode,
                    $order->is_rush ? 'Yes' : 'No',
                    $order->status,
                    $order->items->sum('quantity'),
                    number_format($order->base_amount, 2),
                    number_format($order->rush_surcharge ?? 0, 2),
                    number_format($order->pickup_fee ?? 0, 2),
                    number_format($order->commission_amount ?? 0, 2),
                    number_format($order->total_amount, 2),
                    $order->pickup_time ? Carbon::parse($order->pickup_time)->format('Y-m-d H:i') : '',
                    $order->expected_return_date ? Carbon::parse($order->expected_return_date)->format('Y-m-d') : '',
                    $order->actual_return_date ? Carbon::parse($order->actual_return_date)->format('Y-m-d H:i') : '',
                    $order->created_at->format('Y-m-d H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Print invoice for an order
     */
    public function printInvoice($id)
    {
        $provider = $this->getProvider();
        
        $order = LaundryOrder::with(['user', 'items.laundryItem', 'serviceProvider'])
            ->where('service_provider_id', $provider->id)
            ->findOrFail($id);
        
        return view('laundry-provider.orders.print', compact('order'));
    }
    
    /**
     * Get recent orders for dashboard
     */
    public function recentOrders(Request $request)
    {
        $provider = $this->getProvider();
        
        $limit = $request->get('limit', 5);
        
        $orders = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
        
        return response()->json([
            'html' => view('laundry-provider.orders.partials.recent-orders', compact('orders'))->render(),
            'count' => $orders->count()
        ]);
    }
    
    /**
     * Get upcoming pickups for dashboard
     */
    public function upcomingPickups(Request $request)
    {
        $provider = $this->getProvider();
        
        $orders = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->whereDate('pickup_time', '>=', Carbon::today())
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->orderBy('pickup_time')
            ->limit(5)
            ->get();
        
        return response()->json([
            'html' => view('laundry-provider.orders.partials.upcoming-pickups', compact('orders'))->render(),
            'count' => $orders->count()
        ]);
    }
    
    /**
     * Get overdue orders for dashboard
     */
    public function overdueOrders(Request $request)
    {
        $provider = $this->getProvider();
        
        $orders = LaundryOrder::with('user')
            ->where('service_provider_id', $provider->id)
            ->where('pickup_time', '<', Carbon::now())
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->orderBy('pickup_time')
            ->get();
        
        return response()->json([
            'html' => view('laundry-provider.orders.partials.overdue-orders', compact('orders'))->render(),
            'count' => $orders->count()
        ]);
    }
    
    /**
     * Get order statistics
     */
    public function getStatistics()
    {
        $provider = $this->getProvider();
        
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $monthStart = Carbon::now()->startOfMonth();
        
        $stats = [
            'today' => [
                'total' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->count(),
                'pending' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
                    ->count(),
                'completed' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->where('status', 'DELIVERED')
                    ->count(),
                'revenue' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', $today)
                    ->where('status', 'DELIVERED')
                    ->sum('total_amount')
            ],
            'week' => [
                'total' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', '>=', $weekStart)
                    ->count(),
                'revenue' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', '>=', $weekStart)
                    ->where('status', 'DELIVERED')
                    ->sum('total_amount')
            ],
            'month' => [
                'total' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', '>=', $monthStart)
                    ->count(),
                'revenue' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->whereDate('created_at', '>=', $monthStart)
                    ->where('status', 'DELIVERED')
                    ->sum('total_amount')
            ],
            'all_time' => [
                'total' => LaundryOrder::where('service_provider_id', $provider->id)->count(),
                'completed' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->where('status', 'DELIVERED')
                    ->count(),
                'revenue' => LaundryOrder::where('service_provider_id', $provider->id)
                    ->where('status', 'DELIVERED')
                    ->sum('total_amount')
            ]
        ];
        
        return response()->json($stats);
    }
    
    /**
     * Send status notification to customer
     */
    private function sendStatusNotification($order)
    {
        $messages = [
            'PICKED_UP' => 'Your laundry has been picked up and is being processed.',
            'READY' => 'Your laundry is ready for delivery.',
            'OUT_FOR_DELIVERY' => 'Your laundry is out for delivery.',
            'DELIVERED' => 'Your laundry has been delivered. Thank you for choosing us!'
        ];
        
        if (isset($messages[$order->status])) {
            Notification::create([
                'user_id' => $order->user_id,
                'type' => 'ORDER',
                'title' => 'Order Status Update',
                'message' => $messages[$order->status],
                'related_entity_type' => 'laundry_order',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP'
            ]);
        }
    }
    
    // ==================== PRIVATE HELPER METHODS ====================
    
    /**
     * Get all normal orders
     */
    private function getNormalOrders($providerId)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'NORMAL')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
    
    /**
     * Get all rush orders
     */
    private function getRushOrders($providerId)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'RUSH')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
    }
    
    /**
     * Get normal orders that need pickup today
     */
    private function getNormalPickupToday($providerId, $date)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'NORMAL')
            ->whereDate('pickup_time', $date)
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->orderBy('pickup_time', 'asc')
            ->get();
    }
    
    /**
     * Get normal orders that need delivery today
     */
    private function getNormalDeliverToday($providerId, $date)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'NORMAL')
            ->whereDate('expected_return_date', $date)
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date', 'asc')
            ->get();
    }
    
    /**
     * Get normal orders in progress
     */
    private function getNormalInProgress($providerId)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'NORMAL')
            ->whereIn('status', ['PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }
    
    /**
     * Get rush orders that need pickup today
     */
    private function getRushPickupToday($providerId, $date)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'RUSH')
            ->whereDate('pickup_time', $date)
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->orderBy('pickup_time', 'asc')
            ->get();
    }
    
    /**
     * Get rush orders that need delivery today
     */
    private function getRushDeliverToday($providerId, $date)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'RUSH')
            ->whereDate('expected_return_date', $date)
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date', 'asc')
            ->get();
    }
    
    /**
     * Get rush orders in progress
     */
    private function getRushInProgress($providerId)
    {
        return LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $providerId)
            ->where('service_mode', 'RUSH')
            ->whereIn('status', ['PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY'])
            ->orderBy('updated_at', 'desc')
            ->get();
    }
    
    /**
     * Get statistics for an order
     */
    private function getOrderStats($order)
    {
        $stats = [
            'total_items' => $order->items->sum('quantity'),
            'unique_items' => $order->items->count(),
            'processing_days' => $order->actual_return_date ? 
                Carbon::parse($order->pickup_time)->diffInDays(Carbon::parse($order->actual_return_date)) : null,
            'is_on_time' => $order->actual_return_date ? 
                Carbon::parse($order->actual_return_date)->lte(Carbon::parse($order->expected_return_date)) : null
        ];
        
        return $stats;
    }
}