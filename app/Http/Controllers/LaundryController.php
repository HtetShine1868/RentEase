<?php

namespace App\Http\Controllers;

use App\Models\ServiceProvider;
use App\Models\LaundryItem;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\UserAddress;
use App\Models\CommissionConfig;
use App\Models\ServiceRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class LaundryController extends Controller
{
    /**
     * Show laundry providers listing page
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user's default address or first address
        $userAddress = null;
        if ($user) {
            try {
                $userAddress = UserAddress::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->first();
                    
                if (!$userAddress) {
                    $userAddress = UserAddress::where('user_id', $user->id)->first();
                }
            } catch (\Exception $e) {
                Log::error('Error fetching user address: ' . $e->getMessage());
                $userAddress = null;
            }
        }
        
        // Get initial providers (first page)
        try {
            $providers = ServiceProvider::where('service_type', 'LAUNDRY')
                ->where('status', 'ACTIVE')
                ->with(['laundryConfig', 'laundryItems' => function($q) {
                    $q->where('is_active', true);
                }])
                ->orderBy('rating', 'desc')
                ->paginate(10);
            
            // Calculate distance for each provider if user has address
            if ($userAddress && $userAddress->latitude && $userAddress->longitude) {
                foreach ($providers as $provider) {
                    if ($provider->latitude && $provider->longitude) {
                        $provider->distance = $this->calculateDistance(
                            $userAddress->latitude,
                            $userAddress->longitude,
                            $provider->latitude,
                            $provider->longitude
                        );
                    } else {
                        $provider->distance = null;
                    }
                }
            }
            
        } catch (\Exception $e) {
            Log::error('Error fetching providers: ' . $e->getMessage());
            $providers = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10);
        }
        
        return view('laundry.index', compact('userAddress', 'providers'));
    }

    /**
     * Get nearby laundry providers (API endpoint)
     */
    public function getProviders(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'nullable|integer|min:1|max:50',
            'search' => 'nullable|string',
            'type' => 'nullable|string',
            'sort' => 'nullable|in:rating,distance,orders'
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius ?? 10; // Default 10km radius

        // Build query with distance calculation
        $query = ServiceProvider::select(
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
            ->with(['laundryConfig', 'laundryItems' => function($q) {
                $q->where('is_active', true);
            }]);

        // Apply search filter
        if ($request->filled('search')) {
            $query->where('business_name', 'like', '%' . $request->search . '%');
        }

        // Apply service type filter
        if ($request->filled('type') && $request->type !== 'all') {
            $query->whereHas('laundryItems', function($q) use ($request) {
                $q->where('item_type', $request->type);
            });
        }

        // Apply sorting
        $sortBy = $request->get('sort', 'rating');
        if ($sortBy === 'distance') {
            $query->orderBy('distance');
        } elseif ($sortBy === 'orders') {
            $query->orderBy('total_orders', 'desc');
        } else {
            $query->orderBy('rating', 'desc');
        }

        $providers = $query->get()->map(function($provider) {
            return [
                'id' => $provider->id,
                'business_name' => $provider->business_name,
                'description' => $provider->description,
                'address' => $provider->address,
                'city' => $provider->city,
                'latitude' => $provider->latitude,
                'longitude' => $provider->longitude,
                'distance' => round($provider->distance, 1),
                'rating' => $provider->rating,
                'total_orders' => $provider->total_orders,
                'opening_time' => $provider->laundryConfig->pickup_start_time ?? '09:00',
                'closing_time' => $provider->laundryConfig->pickup_end_time ?? '18:00',
                'pickup_fee' => $provider->laundryConfig->pickup_fee ?? 0,
                'normal_turnaround' => $provider->laundryConfig->normal_turnaround_hours ?? 120,
                'rush_turnaround' => $provider->laundryConfig->rush_turnaround_hours ?? 48,
                'min_price' => $provider->laundryItems->min('base_price') ?? 0,
                'image' => $provider->avatar_url ? Storage::url($provider->avatar_url) : asset('images/laundry-placeholder.jpg')
            ];
        });

        return response()->json([
            'success' => true,
            'providers' => $providers
        ]);
    }

    /**
     * Show provider details and items
     */
    public function showProvider($id)
    {
        $provider = ServiceProvider::with(['laundryConfig', 'user'])
            ->where('id', $id)
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        // Get provider's laundry items
        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->where('is_active', true)
            ->orderBy('item_type')
            ->orderBy('item_name')
            ->get();

        // Get recent ratings
        $ratings = ServiceRating::where('service_provider_id', $provider->id)
            ->where('order_type', 'LAUNDRY')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate average ratings
        $avgRatings = [
            'overall' => $provider->rating,
            'quality' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('quality_rating') ?? 0,
            'delivery' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('delivery_rating') ?? 0,
            'value' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('value_rating') ?? 0,
        ];

        return view('laundry.provider-show', compact('provider', 'items', 'ratings', 'avgRatings'));
    }

    /**
     * Get provider items (API endpoint)
     */
    public function getProviderItems($id)
    {
        $provider = ServiceProvider::where('id', $id)
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->where('is_active', true)
            ->orderBy('item_type')
            ->orderBy('item_name')
            ->get()
            ->groupBy('item_type')
            ->map(function($group, $type) {
                return [
                    'type' => $type,
                    'items' => $group->map(function($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->item_name,
                            'description' => $item->description,
                            'base_price' => $item->base_price,
                            'total_price' => $item->total_price,
                            'rush_surcharge_percent' => $item->rush_surcharge_percent,
                            'type' => $item->item_type,
                            'is_active' => $item->is_active
                        ];
                    })
                ];
            })->values();

        return response()->json([
            'success' => true,
            'items' => $items,
            'provider' => [
                'id' => $provider->id,
                'business_name' => $provider->business_name,
                'pickup_fee' => $provider->laundryConfig->pickup_fee ?? 0,
                'normal_turnaround' => $provider->laundryConfig->normal_turnaround_hours ?? 120,
                'rush_turnaround' => $provider->laundryConfig->rush_turnaround_hours ?? 48
            ]
        ]);
    }

    /**
     * Show order placement page
     */
    public function createOrder($providerId)
    {
        $provider = ServiceProvider::with('laundryConfig')
            ->where('id', $providerId)
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->firstOrFail();

        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->where('is_active', true)
            ->orderBy('item_type')
            ->orderBy('item_name')
            ->get();

        $addresses = UserAddress::where('user_id', Auth::id())
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate distance if user has default address
        $userAddress = $addresses->where('is_default', true)->first() ?? $addresses->first();
        $distance = null;
        
        if ($userAddress && $userAddress->latitude && $userAddress->longitude && 
            $provider->latitude && $provider->longitude) {
            $distance = $this->calculateDistance(
                $userAddress->latitude,
                $userAddress->longitude,
                $provider->latitude,
                $provider->longitude
            );
        }

        return view('laundry.create-order', compact('provider', 'items', 'addresses', 'distance'));
    }

    /**
     * Place a new laundry order
     */
    public function placeOrder(Request $request)
    {
        try {
            // Log the incoming request for debugging
            \Log::info('Laundry order request received', $request->all());
        
        // Validate the request
        $validated = $request->validate([
            'provider_id' => 'required|exists:service_providers,id',
            'service_mode' => 'required|in:NORMAL,RUSH',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'pickup_address' => 'required|string',
            'pickup_date' => 'required|date|after:today',
            'pickup_time' => 'required',
            'pickup_instructions' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:laundry_items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        // Find the provider
        $provider = ServiceProvider::with('laundryConfig')
            ->where('id', $request->provider_id)
            ->where('service_type', 'LAUNDRY')
            ->where('status', 'ACTIVE')
            ->first();

        if (!$provider) {
            \Log::error('Provider not found: ' . $request->provider_id);
            return response()->json([
                'success' => false,
                'message' => 'Provider not found or inactive'
            ], 404);
        }

        // Calculate distance from provider to pickup location
        $distance = $this->calculateDistance(
            $request->pickup_latitude,
            $request->pickup_longitude,
            $provider->latitude,
            $provider->longitude
        );

        // Get commission rate
        $commissionConfig = CommissionConfig::where('service_type', 'LAUNDRY')->first();
        $commissionRate = $commissionConfig ? $commissionConfig->rate : 10.00;

        // Calculate order totals
        $baseAmount = 0;
        $rushSurcharge = 0;
        $rushSurchargePercent = 0;
        $orderItems = [];

        foreach ($request->items as $itemData) {
            $item = LaundryItem::find($itemData['id']);
            if (!$item) {
                \Log::error('Item not found: ' . $itemData['id']);
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found: ' . $itemData['id']
                ], 400);
            }
            
            $quantity = (int)$itemData['quantity'];
            if ($quantity < 1) {
                continue;
            }
            
            $itemBasePrice = $item->base_price * $quantity;
            $baseAmount += $itemBasePrice;
            
            if ($request->service_mode === 'RUSH' && $item->rush_surcharge_percent > 0) {
                $itemSurcharge = $itemBasePrice * ($item->rush_surcharge_percent / 100);
                $rushSurcharge += $itemSurcharge;
                // Use the highest surcharge percent for the order (or you could calculate average)
                $rushSurchargePercent = max($rushSurchargePercent, $item->rush_surcharge_percent);
            }
            
            $orderItems[] = [
                'laundry_item_id' => $item->id,
                'quantity' => $quantity,
                'unit_price' => $item->base_price,
                'special_instructions' => null
            ];
        }

        if (empty($orderItems)) {
            return response()->json([
                'success' => false,
                'message' => 'No valid items selected'
            ], 400);
        }

        $pickupFee = $provider->laundryConfig->pickup_fee ?? 0;
        $commissionAmount = ($baseAmount + $rushSurcharge + $pickupFee) * ($commissionRate / 100);
        $totalAmount = $baseAmount + $rushSurcharge + $pickupFee + $commissionAmount;

        // Calculate expected return date
        $turnaroundHours = $request->service_mode === 'RUSH' 
            ? ($provider->laundryConfig->rush_turnaround_hours ?? 48)
            : ($provider->laundryConfig->normal_turnaround_hours ?? 120);
        
        $pickupDateTime = Carbon::parse($request->pickup_date . ' ' . $request->pickup_time);
        $expectedReturnDate = $pickupDateTime->copy()->addHours($turnaroundHours);

        // Create order
        DB::beginTransaction();
        try {
            // Generate unique order reference
            $orderReference = 'LND' . strtoupper(uniqid());
            
            // Check if order reference already exists (unlikely but safe)
            while (LaundryOrder::where('order_reference', $orderReference)->exists()) {
                $orderReference = 'LND' . strtoupper(uniqid());
            }
            
            $order = new LaundryOrder();
            $order->order_reference = $orderReference;
            $order->user_id = Auth::id();
            $order->service_provider_id = $provider->id;
            $order->booking_id = null; // Optional, set if needed
            $order->service_mode = $request->service_mode;
            $order->is_rush = $request->service_mode === 'RUSH';
            $order->rush_surcharge_percent = $rushSurchargePercent;
            $order->pickup_address = $request->pickup_address;
            $order->pickup_latitude = $request->pickup_latitude;
            $order->pickup_longitude = $request->pickup_longitude;
            $order->distance_km = $distance;
            $order->pickup_time = $pickupDateTime;
            $order->pickup_instructions = $request->pickup_instructions;
            $order->expected_return_date = $expectedReturnDate;
            $order->actual_return_date = null;
            $order->status = 'PENDING';
            $order->base_amount = $baseAmount;
            $order->rush_surcharge = $rushSurcharge;
            $order->pickup_fee = $pickupFee;
            $order->commission_amount = $commissionAmount;
            $order->total_amount = $totalAmount;
            $order->save();

            // Create order items
            foreach ($orderItems as $itemData) {
                $orderItem = new LaundryOrderItem();
                $orderItem->laundry_order_id = $order->id;
                $orderItem->laundry_item_id = $itemData['laundry_item_id'];
                $orderItem->quantity = $itemData['quantity'];
                $orderItem->unit_price = $itemData['unit_price'];
                $orderItem->special_instructions = $itemData['special_instructions'];
                $orderItem->save();
            }

            // Create notification for provider
            DB::table('notifications')->insert([
                'user_id' => $provider->user_id,
                'type' => 'ORDER',
                'title' => 'New Laundry Order',
                'message' => "New laundry order #{$order->order_reference} received",
                'related_entity_type' => 'LAUNDRY_ORDER',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            \Log::info('Order placed successfully', ['order_id' => $order->id, 'reference' => $order->order_reference]);

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_id' => $order->id,
                'order_reference' => $order->order_reference
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order placement DB error: ' . $e->getMessage());
            \Log::error('Order placement DB trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }

    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Validation error: ' . json_encode($e->errors()));
        return response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        \Log::error('Order placement error: ' . $e->getMessage());
        \Log::error('Order placement trace: ' . $e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to place order: ' . $e->getMessage()
        ], 500);
    }
}

    /**
     * Show order details
     */
    public function showOrder($id)
    {
        $order = LaundryOrder::with([
                'serviceProvider',
                'items.laundryItem'
            
            ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('laundry.order-show', compact('order'));
    }

    /**
     * Show order confirmation page
     */
    public function orderConfirmation($id)
    {
        $order = LaundryOrder::with([
                'serviceProvider',
                'orderItems.laundryItem'
            ])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('laundry.order-confirmation', compact('order'));
    }

    /**
     * Get user's orders (API endpoint)
     */
    public function getOrders(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = LaundryOrder::with('serviceProvider')
            ->where('user_id', Auth::id());

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }

    /**
     * Show user's orders page
     */
    public function myOrders()
    {
        $orders = LaundryOrder::with('serviceProvider')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('laundry.my-orders', compact('orders'));
    }

    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|max:500'
        ]);

        $order = LaundryOrder::where('user_id', Auth::id())
            ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED'])
            ->findOrFail($id);

        DB::beginTransaction();
        try {
            $order->status = 'CANCELLED';
            $order->cancellation_reason = $request->reason;
            $order->save();

            // Notify provider
            DB::table('notifications')->insert([
                'user_id' => $order->serviceProvider->user_id,
                'type' => 'ORDER',
                'title' => 'Order Cancelled',
                'message' => "Order #{$order->order_reference} was cancelled by customer. Reason: {$request->reason}",
                'related_entity_type' => 'LAUNDRY_ORDER',
                'related_entity_id' => $order->id,
                'channel' => 'IN_APP',
                'is_read' => false,
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order cancellation error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }

    /**
     * Show order rating page
     */
    public function rateOrder($id)
    {
        $order = LaundryOrder::with(['serviceProvider', 'items.laundryItem'])
            ->where('user_id', Auth::id())
            ->where('status', 'DELIVERED')
            ->whereDoesntHave('ratings')
            ->findOrFail($id);

        return view('laundry.rate-order', compact('order'));
    }

    /**
     * Submit order rating
     */
    public function submitRating(Request $request, $id)
    {
        $request->validate([
            'quality_rating' => 'required|integer|between:1,5',
            'delivery_rating' => 'required|integer|between:1,5',
            'value_rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $order = LaundryOrder::where('user_id', Auth::id())
            ->where('status', 'DELIVERED')
            ->findOrFail($id);

        // Check if already rated
        if (ServiceRating::where('order_id', $order->id)
            ->where('order_type', 'LAUNDRY')
            ->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Order already rated'
            ], 400);
        }

        $overallRating = ($request->quality_rating + $request->delivery_rating + $request->value_rating) / 3;

        DB::beginTransaction();
        try {
            $rating = ServiceRating::create([
                'user_id' => Auth::id(),
                'service_provider_id' => $order->service_provider_id,
                'order_id' => $order->id,
                'order_type' => 'LAUNDRY',
                'quality_rating' => $request->quality_rating,
                'delivery_rating' => $request->delivery_rating,
                'value_rating' => $request->value_rating,
                'overall_rating' => round($overallRating, 1),
                'comment' => $request->comment
            ]);

            // Update provider's average rating
            $provider = ServiceProvider::find($order->service_provider_id);
            $avgRating = ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('overall_rating');
            
            $provider->rating = round($avgRating, 1);
            $provider->total_orders = LaundryOrder::where('service_provider_id', $provider->id)
                ->where('status', 'DELIVERED')
                ->count();
            $provider->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Thank you for your rating!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Rating submission error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit rating'
            ], 500);
        }
    }

    /**
     * Calculate distance between two coordinates using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }
        
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 1);
    }
}