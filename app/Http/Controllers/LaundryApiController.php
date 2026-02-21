<?php
// app/Http/Controllers/LaundryApiController.php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\LaundryItem;
use App\Models\LaundryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LaundryApiController extends Controller
{
    /**
     * Get nearby laundry providers
     */
    public function getProviders(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:50'
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10;

        $providers = ServiceProvider::select(
                'service_providers.*',
                DB::raw("(6371 * acos(cos(radians($latitude)) 
                    * cos(radians(latitude)) 
                    * cos(radians(longitude) - radians($longitude)) 
                    + sin(radians($latitude)) 
                    * sin(radians(latitude)))) AS distance")
            )
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->with(['laundryConfig'])
            ->get();

        return response()->json([
            'success' => true,
            'providers' => $providers
        ]);
    }

    /**
     * Get provider items
     */
    public function getProviderItems($id)
    {
        $provider = ServiceProvider::where('id', $id)
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->orderBy('item_type')
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'success' => true,
            'items' => $items,
            'provider' => $provider
        ]);
    }

    /**
     * Get user's orders
     */
    public function getOrders(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = LaundryOrder::with('serviceProvider')
            ->where('user_id', Auth::id());

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Get single order details
     */
    public function getOrder($id)
    {
        $order = LaundryOrder::with(['serviceProvider', 'orderItems.laundryItem'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    /**
     * Place an order
     */
    public function placeOrder(Request $request)
    {
        // This will call the same method from LaundryController
        return app(LaundryController::class)->placeOrder($request);
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $id)
    {
        return app(LaundryController::class)->cancelOrder($request, $id);
    }
}