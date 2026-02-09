<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class FoodServiceController extends Controller
{
    /**
     * Display the food services dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $totalOrders = DB::table('food_orders')
            ->where('user_id', $user->id)
            ->count();
            
        $activeSubscriptions = DB::table('food_subscriptions')
            ->where('user_id', $user->id)
            ->where('status', 'ACTIVE')
            ->count();
            
        $pendingOrders = DB::table('food_orders')
            ->where('user_id', $user->id)
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY'])
            ->count();
        
        // Get recent orders
        $recentOrders = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->where('fo.user_id', $user->id)
            ->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fo.created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Format dates
        foreach ($recentOrders as $order) {
            $order->created_at_formatted = Carbon::parse($order->created_at)->format('M d, Y h:i A');
        }
        
        // Get meal types
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        return view('food.index', [
            'title' => 'Food Services',
            'totalOrders' => $totalOrders,
            'activeSubscriptions' => $activeSubscriptions,
            'pendingOrders' => $pendingOrders,
            'recentOrders' => $recentOrders,
            'mealTypes' => $mealTypes
        ]);
    }
    
    /**
     * Display restaurants listing (AJAX endpoint)
     */
    public function getRestaurants(Request $request)
    {
        $user = Auth::user();
        
        $query = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.service_type', 'FOOD')
            ->where('sp.status', 'ACTIVE');
        
        // Apply search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('sp.business_name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('sp.description', 'LIKE', '%' . $request->search . '%');
            });
        }
        
        // Apply meal type filter
        if ($request->has('meal_type') && $request->meal_type) {
            $query->whereExists(function($q) use ($request) {
                $q->select(DB::raw(1))
                  ->from('food_items as fi')
                  ->whereColumn('fi.service_provider_id', 'sp.id')
                  ->where('fi.meal_type_id', $request->meal_type)
                  ->where('fi.is_available', true);
            });
        }
        
        // Get user location (default to Dhaka if not available)
        $userLocation = $this->getUserLocation($user);
        
        // Calculate distance using Haversine formula
        $query->selectRaw('
            sp.*,
            fsc.opening_time,
            fsc.closing_time,
            fsc.supports_subscription,
            ROUND(6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude))), 2) as distance_km,
            fsc.avg_preparation_minutes + ROUND(fsc.delivery_buffer_minutes * (6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude))))) as estimated_delivery_minutes',
            [
                $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude'],
                $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude']
            ]
        );
        
        // Apply sorting
        $sortBy = $request->get('sort', 'rating');
        switch ($sortBy) {
            case 'distance':
                $query->orderBy('distance_km');
                break;
            case 'delivery_time':
                $query->orderBy('estimated_delivery_minutes');
                break;
            case 'total_orders':
                $query->orderByDesc('sp.total_orders');
                break;
            default:
                $query->orderByDesc('sp.rating');
        }
        
        $restaurants = $query->take(12)->get();
        
        return response()->json([
            'success' => true,
            'restaurants' => $restaurants
        ]);
    }
    
    /**
     * Get restaurant menu (AJAX endpoint)
     */
    public function getRestaurantMenu($id)
    {
        $restaurant = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.id', $id)
            ->where('sp.service_type', 'FOOD')
            ->select('sp.*', 'fsc.*')
            ->first();
            
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found.'
            ], 404);
        }
        
        $menuItems = DB::table('food_items as fi')
            ->join('meal_types as mt', 'fi.meal_type_id', '=', 'mt.id')
            ->where('fi.service_provider_id', $id)
            ->where('fi.is_available', true)
            ->select('fi.*', 'mt.name as meal_type_name')
            ->orderBy('mt.display_order')
            ->orderBy('fi.name')
            ->get();
        
        // Parse dietary tags JSON
        foreach ($menuItems as $item) {
            $item->dietary_tags = json_decode($item->dietary_tags) ?? [];
        }
        
        return response()->json([
            'success' => true,
            'restaurant' => $restaurant,
            'menu_items' => $menuItems
        ]);
    }
    
    /**
     * Get user orders (AJAX endpoint)
     */
    public function getOrders(Request $request)
    {
        $user = Auth::user();
        
        $query = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->where('fo.user_id', $user->id);
        
        if ($request->has('status') && $request->status) {
            $query->where('fo.status', $request->status);
        }
        
        $orders = $query->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fo.created_at', 'desc')
            ->get();
        
        // Get order items and format dates
        foreach ($orders as $order) {
            $order->items = DB::table('food_order_items as foi')
                ->join('food_items as fi', 'foi.food_item_id', '=', 'fi.id')
                ->where('foi.food_order_id', $order->id)
                ->select('fi.name', 'foi.quantity', 'foi.unit_price')
                ->get();
            
            $order->created_at_formatted = Carbon::parse($order->created_at)->format('M d, Y h:i A');
        }
        
        return response()->json([
            'success' => true,
            'orders' => $orders
        ]);
    }
    
    /**
     * Get user subscriptions (AJAX endpoint)
     */
    public function getSubscriptions()
    {
        $user = Auth::user();
        
        $subscriptions = DB::table('food_subscriptions as fs')
            ->join('service_providers as sp', 'fs.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fs.meal_type_id', '=', 'mt.id')
            ->where('fs.user_id', $user->id)
            ->select('fs.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fs.created_at', 'desc')
            ->get();
        
        // Format dates and delivery days
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($subscriptions as $sub) {
            $sub->start_date_formatted = Carbon::parse($sub->start_date)->format('M d, Y');
            $sub->end_date_formatted = Carbon::parse($sub->end_date)->format('M d, Y');
            
            // Parse delivery days bitmask
            $deliveryDays = [];
            for ($i = 0; $i < 7; $i++) {
                if ($sub->delivery_days & (1 << $i)) {
                    $deliveryDays[] = $days[$i];
                }
            }
            $sub->delivery_days_text = implode(', ', $deliveryDays);
        }
        
        return response()->json([
            'success' => true,
            'subscriptions' => $subscriptions
        ]);
    }
    
    /**
     * Place a new food order
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required|exists:service_providers,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'meal_date' => 'required|date|after_or_equal:today',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        
        DB::beginTransaction();
        try {
            // Calculate total amount and check item availability
            $baseAmount = 0;
            $commissionRate = 0;
            $serviceProviderId = null;
            
            foreach ($request->items as $item) {
                $foodItem = DB::table('food_items')
                    ->where('id', $item['food_item_id'])
                    ->first();
                    
                if (!$foodItem) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Food item not found.'
                    ], 404);
                }
                
                if (!$foodItem->is_available) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Item {$foodItem->name} is not available."
                    ], 400);
                }
                
                // Check daily quantity limit
                if ($foodItem->daily_quantity && 
                    $foodItem->sold_today >= $foodItem->daily_quantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Item {$foodItem->name} is sold out for today."
                    ], 400);
                }
                
                $serviceProviderId = $foodItem->service_provider_id;
                $baseAmount += $foodItem->base_price * $item['quantity'];
                
                if ($foodItem->commission_rate > $commissionRate) {
                    $commissionRate = $foodItem->commission_rate;
                }
            }
            
            // Get service provider for delivery fee
            $serviceProvider = DB::table('service_providers')
                ->where('id', $serviceProviderId)
                ->first();
                
            if (!$serviceProvider) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Service provider not found.'
                ], 404);
            }
            
            // Calculate distance
            $distance = $this->calculateDistance(
                $serviceProvider->latitude,
                $serviceProvider->longitude,
                $request->delivery_latitude,
                $request->delivery_longitude
            );
            
            // Calculate delivery fee (example: 20 BDT per km)
            $deliveryFee = max(20, round($distance * 20, 2));
            
            $commissionAmount = round(($baseAmount * $commissionRate) / 100, 2);
            $totalAmount = round($baseAmount + $commissionAmount + $deliveryFee, 2);
            
            // Generate order reference
            $orderReference = 'FOOD-' . date('Ymd') . '-' . strtoupper(uniqid());
            
            // Create order
            $orderId = DB::table('food_orders')->insertGetId([
                'order_reference' => $orderReference,
                'user_id' => $user->id,
                'service_provider_id' => $serviceProviderId,
                'order_type' => 'PAY_PER_EAT',
                'meal_date' => $request->meal_date,
                'meal_type_id' => $request->meal_type_id,
                'delivery_address' => $request->delivery_address,
                'delivery_latitude' => $request->delivery_latitude,
                'delivery_longitude' => $request->delivery_longitude,
                'distance_km' => round($distance, 2),
                'base_amount' => $baseAmount,
                'delivery_fee' => $deliveryFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
                'estimated_delivery_time' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create order items and update sold count
            foreach ($request->items as $item) {
                $foodItem = DB::table('food_items')->where('id', $item['food_item_id'])->first();
                
                DB::table('food_order_items')->insert([
                    'food_order_id' => $orderId,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $foodItem->base_price,
                    'created_at' => now()
                ]);
                
                // Update sold count
                DB::table('food_items')
                    ->where('id', $item['food_item_id'])
                    ->increment('sold_today', $item['quantity']);
            }
            
            // Create payment record
            $paymentReference = 'PAY-' . date('Ymd') . '-' . strtoupper(uniqid());
            DB::table('payments')->insert([
                'payment_reference' => $paymentReference,
                'user_id' => $user->id,
                'payable_type' => 'FOOD_ORDER',
                'payable_id' => $orderId,
                'amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'BANK_TRANSFER',
                'status' => 'COMPLETED', // Mock payment
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'order_reference' => $orderReference,
                'total_amount' => $totalAmount
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Order placement failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Cancel an order
     */
    public function cancelOrder(Request $request, $id)
    {
        $user = Auth::user();
        
        $order = DB::table('food_orders')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
        }
        
        if (!in_array($order->status, ['PENDING', 'ACCEPTED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel order at this stage.'
            ], 400);
        }
        
        DB::table('food_orders')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'updated_at' => now()
            ]);
            
        // Refund payment (mock)
        DB::table('payments')
            ->where('payable_type', 'FOOD_ORDER')
            ->where('payable_id', $id)
            ->update([
                'status' => 'REFUNDED',
                'updated_at' => now()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }
    
    /**
     * Create a new subscription
     */
    public function createSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required|exists:service_providers,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'delivery_time' => 'required|date_format:H:i',
            'delivery_days' => 'required|array|min:1',
            'delivery_days.*' => 'integer|between:0,6',
            'delivery_address' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Auth::user();
        
        // Check if service provider supports subscription
        $serviceProvider = DB::table('service_providers as sp')
            ->join('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.id', $request->service_provider_id)
            ->where('sp.service_type', 'FOOD')
            ->where('fsc.supports_subscription', true)
            ->first();
            
        if (!$serviceProvider) {
            return response()->json([
                'success' => false,
                'message' => 'This service provider does not support subscriptions.'
            ], 400);
        }
        
        // Calculate delivery days bitmask
        $deliveryDaysMask = 0;
        foreach ($request->delivery_days as $dayIndex) {
            $deliveryDaysMask |= (1 << $dayIndex);
        }
        
        // Calculate subscription duration
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $daysCount = $startDate->diffInDays($endDate) + 1;
        
        // Calculate number of delivery days in the period
        $deliveryDaysCount = 0;
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dayOfWeek = $currentDate->dayOfWeek;
            if ($deliveryDaysMask & (1 << $dayOfWeek)) {
                $deliveryDaysCount++;
            }
            $currentDate->addDay();
        }
        
        // Calculate pricing (simplified - you might want more complex logic)
        $mealType = DB::table('meal_types')->where('id', $request->meal_type_id)->first();
        $basePrice = 200; // Example base price per meal
        $dailyPrice = $basePrice;
        
        // Apply subscription discount
        $subscriptionDiscount = $serviceProvider->subscription_discount_percent ?? 10.00;
        $discountAmount = ($dailyPrice * $subscriptionDiscount) / 100;
        $dailyPrice -= $discountAmount;
        
        $totalPrice = round($dailyPrice * $deliveryDaysCount, 2);
        
        try {
            DB::beginTransaction();
            
            $subscriptionId = DB::table('food_subscriptions')->insertGetId([
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'meal_type_id' => $request->meal_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'delivery_time' => $request->delivery_time,
                'delivery_days' => $deliveryDaysMask,
                'daily_price' => $dailyPrice,
                'total_price' => $totalPrice,
                'discount_amount' => $discountAmount * $deliveryDaysCount,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create initial payment
            $paymentReference = 'SUB-' . date('Ymd') . '-' . strtoupper(uniqid());
            DB::table('payments')->insert([
                'payment_reference' => $paymentReference,
                'user_id' => $user->id,
                'payable_type' => 'FOOD_SUBSCRIPTION',
                'payable_id' => $subscriptionId,
                'amount' => $totalPrice,
                'commission_amount' => round(($totalPrice * 8) / 100, 2), // 8% commission
                'payment_method' => 'BANK_TRANSFER',
                'status' => 'COMPLETED',
                'paid_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Subscription created successfully!',
                'subscription_id' => $subscriptionId,
                'total_amount' => $totalPrice
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Subscription creation failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription. Please try again.'
            ], 500);
        }
    }
    
    /**
     * Cancel a subscription
     */
    public function cancelSubscription(Request $request, $id)
    {
        $user = Auth::user();
        
        $subscription = DB::table('food_subscriptions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found.'
            ], 404);
        }
        
        if (!in_array($subscription->status, ['ACTIVE', 'PAUSED'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel subscription at this stage.'
            ], 400);
        }
        
        DB::table('food_subscriptions')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'updated_at' => now()
            ]);
            
        return response()->json([
            'success' => true,
            'message' => 'Subscription cancelled successfully.'
        ]);
    }
    
    /**
     * Helper method to get user location
     */
    private function getUserLocation($user)
    {
        $address = DB::table('user_addresses')
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
            
        if ($address && $address->latitude && $address->longitude) {
            return [
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'address' => $address->address_line1 . ', ' . $address->city . ', ' . $address->state
            ];
        }
        
        // Default to Dhaka
        return [
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'address' => 'Dhaka, Bangladesh'
        ];
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in kilometers
        
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        return $angle * $earthRadius;
    }
}