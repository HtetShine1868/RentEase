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
            ->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING'])
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
     * Display restaurants listing
     */
    public function restaurants(Request $request)
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
        
        // Get user location
        $userLocation = $this->getUserLocation($user);
        
        if ($userLocation['latitude'] && $userLocation['longitude']) {
            $query->selectRaw('
                sp.*,
                fsc.opening_time,
                fsc.closing_time,
                fsc.supports_subscription,
                (6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude)))) as distance_km,
                fsc.avg_preparation_minutes + (fsc.delivery_buffer_minutes * (6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude))))) as estimated_delivery_minutes',
                [
                    $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude'],
                    $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude']
                ]
            );
        } else {
            $query->selectRaw('sp.*, fsc.opening_time, fsc.closing_time, fsc.supports_subscription, 5.0 as distance_km, 45 as estimated_delivery_minutes');
        }
        
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
        
        $restaurants = $query->paginate(12);
        
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        return view('food.restaurants', [
            'title' => 'Browse Restaurants',
            'restaurants' => $restaurants,
            'mealTypes' => $mealTypes,
            'filters' => $request->only(['search', 'meal_type', 'sort'])
        ]);
    }
    
    /**
     * Show a specific restaurant
     */
    public function restaurant($id)
    {
        $restaurant = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.id', $id)
            ->where('sp.service_type', 'FOOD')
            ->select('sp.*', 'fsc.*')
            ->first();
            
        if (!$restaurant) {
            return redirect()->route('food.restaurants')->with('error', 'Restaurant not found.');
        }
        
        $menuItems = DB::table('food_items as fi')
            ->join('meal_types as mt', 'fi.meal_type_id', '=', 'mt.id')
            ->where('fi.service_provider_id', $id)
            ->where('fi.is_available', true)
            ->select('fi.*', 'mt.name as meal_type_name')
            ->orderBy('mt.display_order')
            ->orderBy('fi.name')
            ->get();
        
        return view('food.restaurant-show', [
            'title' => $restaurant->business_name,
            'restaurant' => $restaurant,
            'menuItems' => $menuItems
        ]);
    }
    
    /**
     * Show user's orders
     */
    public function orders(Request $request)
    {
        $user = Auth::user();
        
        $query = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->where('fo.user_id', $user->id);
        
        if ($request->has('status') && $request->status && $request->status != 'all') {
            $query->where('fo.status', $request->status);
        }
        
        $orders = $query->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fo.created_at', 'desc')
            ->paginate(10);
        
        // Get order items
        foreach ($orders as $order) {
            $order->items = DB::table('food_order_items as foi')
                ->join('food_items as fi', 'foi.food_item_id', '=', 'fi.id')
                ->where('foi.food_order_id', $order->id)
                ->select('fi.name', 'foi.quantity', 'foi.unit_price')
                ->get();
            
            $order->created_at_formatted = Carbon::parse($order->created_at)->format('M d, Y h:i A');
        }
        
        return view('food.orders', [
            'title' => 'My Food Orders',
            'orders' => $orders,
            'statusFilter' => $request->get('status', 'all')
        ]);
    }
    
    /**
     * Show order details
     */
    public function orderDetails($id)
    {
        $user = Auth::user();
        
        $order = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->where('fo.id', $id)
            ->where('fo.user_id', $user->id)
            ->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->first();
            
        if (!$order) {
            return redirect()->route('food.orders')->with('error', 'Order not found.');
        }
        
        $orderItems = DB::table('food_order_items as foi')
            ->join('food_items as fi', 'foi.food_item_id', '=', 'fi.id')
            ->where('foi.food_order_id', $id)
            ->select('fi.name', 'foi.quantity', 'foi.unit_price', 'foi.total_price')
            ->get();
        
        $order->created_at_formatted = Carbon::parse($order->created_at)->format('M d, Y h:i A');
        
        return view('food.order-show', [
            'title' => 'Order #' . $order->order_reference,
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }
    
    /**
     * Show user's subscriptions
     */
    public function subscriptions()
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
        
        return view('food.subscriptions', [
            'title' => 'My Food Subscriptions',
            'subscriptions' => $subscriptions
        ]);
    }
    
    /**
     * Show create subscription form
     */
    public function createSubscription(Request $request)
    {
        $restaurantId = $request->get('restaurant_id');
        
        $restaurants = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.service_type', 'FOOD')
            ->where('sp.status', 'ACTIVE')
            ->where('fsc.supports_subscription', true)
            ->select('sp.id', 'sp.business_name')
            ->get();
        
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        return view('food.subscription-create', [
            'title' => 'Create Subscription',
            'restaurants' => $restaurants,
            'mealTypes' => $mealTypes,
            'selectedRestaurantId' => $restaurantId
        ]);
    }
    
    /**
     * Store a new subscription
     */
    public function storeSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required|exists:service_providers,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'delivery_time' => 'required',
            'delivery_days' => 'required|array|min:1',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        
        // Calculate delivery days bitmask
        $deliveryDaysMask = 0;
        foreach ($request->delivery_days as $dayIndex) {
            $deliveryDaysMask |= (1 << $dayIndex);
        }
        
        // Get service provider for pricing
        $serviceProvider = DB::table('service_providers')
            ->where('id', $request->service_provider_id)
            ->first();
            
        if (!$serviceProvider) {
            return redirect()->back()->with('error', 'Service provider not found.');
        }
        
        // Calculate total price (simplified - you might want to calculate based on actual menu items)
        $daysCount = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date)) + 1;
        $deliveryDaysCount = count($request->delivery_days);
        $weeklyPrice = 500; // Example price
        $totalPrice = ($daysCount / 7) * $weeklyPrice;
        
        try {
            $subscriptionId = DB::table('food_subscriptions')->insertGetId([
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'meal_type_id' => $request->meal_type_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'delivery_time' => $request->delivery_time,
                'delivery_days' => $deliveryDaysMask,
                'daily_price' => $weeklyPrice / $deliveryDaysCount,
                'total_price' => $totalPrice,
                'status' => 'ACTIVE',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->route('food.subscriptions')
                ->with('success', 'Subscription created successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to create subscription. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Place a new food order
     */
    public function placeOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_provider_id' => 'required|exists:service_providers,id',
            'items' => 'required|array|min:1',
            'meal_date' => 'required|date|after_or_equal:today',
            'meal_type_id' => 'required|exists:meal_types,id',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $user = Auth::user();
        
        try {
            // Calculate total amount
            $baseAmount = 0;
            $commissionRate = 0;
            
            foreach ($request->items as $itemData) {
                $foodItem = DB::table('food_items')
                    ->where('id', $itemData['food_item_id'])
                    ->first();
                    
                if (!$foodItem || !$foodItem->is_available) {
                    return redirect()->back()
                        ->with('error', "Item {$foodItem->name} is not available.")
                        ->withInput();
                }
                
                $baseAmount += $foodItem->base_price * $itemData['quantity'];
                
                if ($foodItem->commission_rate > $commissionRate) {
                    $commissionRate = $foodItem->commission_rate;
                }
            }
            
            $commissionAmount = ($baseAmount * $commissionRate) / 100;
            $deliveryFee = 20; // Fixed delivery fee for example
            $totalAmount = $baseAmount + $commissionAmount + $deliveryFee;
            
            // Generate order reference
            $orderReference = 'FOOD-' . strtoupper(uniqid());
            
            // Create order
            $orderId = DB::table('food_orders')->insertGetId([
                'order_reference' => $orderReference,
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'order_type' => 'PAY_PER_EAT',
                'meal_date' => $request->meal_date,
                'meal_type_id' => $request->meal_type_id,
                'base_amount' => $baseAmount,
                'delivery_fee' => $deliveryFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
                'estimated_delivery_time' => now()->addMinutes(30),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create order items
            foreach ($request->items as $itemData) {
                DB::table('food_order_items')->insert([
                    'food_order_id' => $orderId,
                    'food_item_id' => $itemData['food_item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => DB::table('food_items')->where('id', $itemData['food_item_id'])->value('base_price'),
                    'created_at' => now()
                ]);
            }
            
            // Create payment record
            DB::table('payments')->insert([
                'payment_reference' => 'PAY-' . strtoupper(uniqid()),
                'user_id' => $user->id,
                'payable_type' => 'FOOD_ORDER',
                'payable_id' => $orderId,
                'amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return redirect()->route('food.orders')
                ->with('success', 'Order placed successfully!');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to place order. Please try again.')
                ->withInput();
        }
    }
    
    /**
     * Cancel an order
     */
    public function cancelOrder($id)
    {
        $user = Auth::user();
        
        $order = DB::table('food_orders')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }
        
        if (!in_array($order->status, ['PENDING', 'ACCEPTED'])) {
            return redirect()->back()->with('error', 'Cannot cancel order at this stage.');
        }
        
        DB::table('food_orders')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Order cancelled successfully.');
    }
    
    /**
     * Pause a subscription
     */
    public function pauseSubscription($id)
    {
        $user = Auth::user();
        
        $subscription = DB::table('food_subscriptions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$subscription) {
            return redirect()->back()->with('error', 'Subscription not found.');
        }
        
        if ($subscription->status !== 'ACTIVE') {
            return redirect()->back()->with('error', 'Only active subscriptions can be paused.');
        }
        
        DB::table('food_subscriptions')
            ->where('id', $id)
            ->update([
                'status' => 'PAUSED',
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Subscription paused successfully.');
    }
    
    /**
     * Cancel a subscription
     */
    public function cancelSubscription($id)
    {
        $user = Auth::user();
        
        $subscription = DB::table('food_subscriptions')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->first();
            
        if (!$subscription) {
            return redirect()->back()->with('error', 'Subscription not found.');
        }
        
        DB::table('food_subscriptions')
            ->where('id', $id)
            ->update([
                'status' => 'CANCELLED',
                'updated_at' => now()
            ]);
            
        return redirect()->back()->with('success', 'Subscription cancelled successfully.');
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
            
        if ($address) {
            return [
                'latitude' => $address->latitude ?? 23.8103,
                'longitude' => $address->longitude ?? 90.4125,
                'address' => $address->address_line1 . ', ' . $address->city . ', ' . $address->state
            ];
        }
        
        return [
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'address' => 'Dhaka, Bangladesh'
        ];
    }
}