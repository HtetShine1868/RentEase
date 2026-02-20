<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\LaundryOrder;
use App\Models\LaundryItem;
use App\Models\ServiceProvider;
use App\Models\LaundryServiceConfig;
use App\Models\User;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderController extends Controller
{
    use Notifiable;

    /**
     * Display orders with filters
     */
    public function index(Request $request)
    {
        // Get service provider directly in the method
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $config = LaundryServiceConfig::where('service_provider_id', $serviceProvider->id)->first();

        $query = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $serviceProvider->id);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('service_mode')) {
            $query->where('service_mode', $request->service_mode);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', Carbon::yesterday());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month)
                          ->whereYear('created_at', Carbon::now()->year);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        // Validate sort field
        $allowedSortFields = ['created_at', 'total_amount', 'status', 'expected_return_date', 'order_reference'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'created_at';
        }
        
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(15)->withQueryString();

        // Get statistics for the filtered results
        $stats = [
            'total' => LaundryOrder::where('service_provider_id', $serviceProvider->id)->count(),
            'pending' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'PENDING')->count(),
            'pickup_scheduled' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'PICKUP_SCHEDULED')->count(),
            'picked_up' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'PICKED_UP')->count(),
            'in_progress' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->whereIn('status', ['PICKED_UP', 'IN_PROGRESS'])->count(),
            'ready' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'READY')->count(),
            'out_for_delivery' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'OUT_FOR_DELIVERY')->count(),
            'delivered' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'DELIVERED')->count(),
            'cancelled' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'CANCELLED')->count(),
            'rush' => LaundryOrder::where('service_provider_id', $serviceProvider->id)
                ->where('service_mode', 'RUSH')
                ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])->count(),
        ];

        return view('laundry-provider.orders.index', compact('orders', 'stats', 'config'));
    }

    /**
     * Show single order details
     */
    public function show($id)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $order = LaundryOrder::with(['user', 'items.laundryItem', 'booking'])
            ->where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);

        // Calculate progress
        $statuses = ['PENDING', 'PICKUP_SCHEDULED', 'PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED'];
        $currentIndex = array_search($order->status, $statuses);
        $progress = $currentIndex !== false ? round(($currentIndex + 1) / count($statuses) * 100) : 0;

        // Check if rush order is on track
        $rushDeadline = Carbon::parse($order->expected_return_date);
        $isRushOnTrack = $order->service_mode === 'RUSH' && !Carbon::today()->gt($rushDeadline);

        return view('laundry-provider.orders.show', compact('order', 'progress', 'isRushOnTrack'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:PENDING,PICKUP_SCHEDULED,PICKED_UP,IN_PROGRESS,READY,OUT_FOR_DELIVERY,DELIVERED,CANCELLED'
        ]);

        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json(['success' => false, 'message' => 'Service provider not found'], 403);
        }

        $order = LaundryOrder::where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);

        $oldStatus = $order->status;
        $order->status = $request->status;

        // Set actual return date when delivered
        if ($request->status === 'DELIVERED') {
            $order->actual_return_date = Carbon::now();
        }

        // If cancelled, add cancellation reason
        if ($request->status === 'CANCELLED' && $request->filled('cancellation_reason')) {
            $order->cancellation_reason = $request->cancellation_reason;
        }

        $order->save();

        // ============ SEND NOTIFICATIONS ============
        
        $statusMessages = [
            'PICKUP_SCHEDULED' => 'Your laundry pickup has been scheduled',
            'PICKED_UP' => 'Your laundry items have been picked up',
            'IN_PROGRESS' => 'Your laundry is now being processed',
            'READY' => 'Your laundry is ready for delivery',
            'OUT_FOR_DELIVERY' => 'Your laundry is out for delivery',
            'DELIVERED' => 'Your laundry has been delivered',
            'CANCELLED' => 'Your laundry order has been cancelled'
        ];

        if (isset($statusMessages[$request->status])) {
            DB::table('notifications')->insert([
                'user_id' => $order->user_id,
                'type' => 'ORDER',
                'title' => 'Laundry Order Updated',
                'message' => $statusMessages[$request->status] . " for order #{$order->order_reference}",
                'related_entity_type' => 'laundry_order',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        // Special notifications for rush orders
        if ($order->service_mode === 'RUSH' && $request->status === 'READY') {
            DB::table('notifications')->insert([
                'user_id' => $order->user_id,
                'type' => 'ORDER',
                'title' => '🎯 Rush Order Ready Early!',
                'message' => "Your rush order #{$order->order_reference} is ready earlier than expected!",
                'related_entity_type' => 'laundry_order',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

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
     * Get rush orders
     */
    public function rushOrders(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $rushOrders = LaundryOrder::with(['user', 'items.laundryItem'])
            ->where('service_provider_id', $serviceProvider->id)
            ->where('service_mode', 'RUSH')
            ->whereNotIn('status', ['DELIVERED', 'CANCELLED'])
            ->orderBy('expected_return_date')
            ->get();

        // Calculate urgency
        foreach ($rushOrders as $order) {
            $daysLeft = Carbon::today()->diffInDays(Carbon::parse($order->expected_return_date), false);
            $order->urgency = $daysLeft <= 1 ? 'critical' : ($daysLeft <= 2 ? 'warning' : 'normal');
            $order->days_left = $daysLeft;
        }

        return view('laundry-provider.orders.rush', compact('rushOrders'));
    }

    /**
     * Bulk update order status
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:laundry_orders,id',
            'status' => 'required|in:PICKUP_SCHEDULED,PICKED_UP,IN_PROGRESS,READY,OUT_FOR_DELIVERY,DELIVERED'
        ]);

        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json(['success' => false, 'message' => 'Service provider not found'], 403);
        }

        $orders = LaundryOrder::whereIn('id', $request->order_ids)
            ->where('service_provider_id', $serviceProvider->id)
            ->get();

        $updated = 0;
        foreach ($orders as $order) {
            $order->status = $request->status;
            
            if ($request->status === 'DELIVERED') {
                $order->actual_return_date = Carbon::now();
            }
            
            $order->save();
            $updated++;
        }

        return response()->json([
            'success' => true,
            'message' => "{$updated} orders updated successfully"
        ]);
    }

    /**
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $query = LaundryOrder::with('user')
            ->where('service_provider_id', $serviceProvider->id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        $orders = $query->get();

        $filename = 'laundry-orders-' . Carbon::now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = [
            'Order Reference', 'Customer', 'Phone', 'Service Mode', 
            'Items Count', 'Status', 'Pickup Time', 'Expected Return',
            'Actual Return', 'Base Amount', 'Rush Surcharge', 'Total Amount'
        ];

        $callback = function() use ($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_reference,
                    $order->user->name ?? 'N/A',
                    $order->user->phone ?? 'N/A',
                    $order->service_mode,
                    $order->items->count(),
                    $order->status,
                    $order->pickup_time ? Carbon::parse($order->pickup_time)->format('Y-m-d H:i') : 'N/A',
                    $order->expected_return_date ? Carbon::parse($order->expected_return_date)->format('Y-m-d') : 'N/A',
                    $order->actual_return_date ? Carbon::parse($order->actual_return_date)->format('Y-m-d') : 'N/A',
                    number_format($order->base_amount, 2),
                    number_format($order->rush_surcharge, 2),
                    number_format($order->total_amount, 2)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Print invoice
     */
    public function printInvoice($id)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No laundry service provider found for this user.');
        }

        $order = LaundryOrder::with(['user', 'items.laundryItem', 'serviceProvider'])
            ->where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);

        return view('laundry-provider.orders.print', compact('order'));
    }

    /**
     * Update expected return date (for rush orders)
     */
    public function updateReturnDate(Request $request, $id)
    {
        $request->validate([
            'expected_return_date' => 'required|date|after:today'
        ]);

        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'LAUNDRY')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json(['success' => false, 'message' => 'Service provider not found'], 403);
        }

        $order = LaundryOrder::where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);

        $oldDate = $order->expected_return_date;
        $order->expected_return_date = $request->expected_return_date;
        $order->save();

        // Notify user about date change
        if ($order->service_mode === 'RUSH') {
            DB::table('notifications')->insert([
                'user_id' => $order->user_id,
                'type' => 'ORDER',
                'title' => 'Rush Order Update',
                'message' => "Your rush order #{$order->order_reference} expected return date has been changed to " . Carbon::parse($request->expected_return_date)->format('M d, Y'),
                'related_entity_type' => 'laundry_order',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Return date updated successfully'
        ]);
    }
}