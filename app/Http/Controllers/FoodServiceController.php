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
        
        // Get meal types
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        // Load some initial data for better UX
        $initialRestaurants = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.service_type', 'FOOD')
            ->where('sp.status', 'ACTIVE')
            ->select('sp.*', 'fsc.opening_time', 'fsc.closing_time', 'fsc.supports_subscription')
            ->limit(6)
            ->get();
        
        // Get user's recent orders
        $recentOrders = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->where('fo.user_id', $user->id)
            ->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fo.created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Get user's active subscriptions
        $subscriptions = DB::table('food_subscriptions as fs')
            ->join('service_providers as sp', 'fs.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fs.meal_type_id', '=', 'mt.id')
            ->where('fs.user_id', $user->id)
            ->select('fs.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderBy('fs.created_at', 'desc')
            ->get();
        
        // Format subscription delivery days
        $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($subscriptions as $sub) {
            $deliveryDays = [];
            for ($i = 0; $i < 7; $i++) {
                if ($sub->delivery_days & (1 << $i)) {
                    $deliveryDays[] = $days[$i];
                }
            }
            $sub->delivery_days_text = implode(', ', $deliveryDays);
            $sub->start_date_formatted = Carbon::parse($sub->start_date)->format('M d, Y');
            $sub->end_date_formatted = Carbon::parse($sub->end_date)->format('M d, Y');
        }
        
        // Format order dates
        foreach ($recentOrders as $order) {
            $order->created_at_formatted = Carbon::parse($order->created_at)->format('M d, Y h:i A');
        }
        
        return view('food.index', [
            'title' => 'Food Services',
            'totalOrders' => $totalOrders,
            'activeSubscriptions' => $activeSubscriptions,
            'pendingOrders' => $pendingOrders,
            'mealTypes' => $mealTypes,
            'initialRestaurants' => $initialRestaurants,
            'recentOrders' => $recentOrders,
            'subscriptions' => $subscriptions
        ]);
    }
    
    /**
     * AJAX: Get restaurants with filters
     */
    public function getRestaurants(Request $request)
    {
        try {
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
            
            // Format data for frontend
            foreach ($restaurants as $restaurant) {
                $restaurant->estimated_delivery_minutes = min(120, $restaurant->estimated_delivery_minutes ?? 45);
            }
            
            return response()->json([
                'success' => true,
                'restaurants' => $restaurants
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading restaurants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load restaurants. Please try again.'
            ], 500);
        }
    }
    
    /**
     * AJAX: Get restaurant menu
     */
    public function getRestaurantMenu($id)
    {
        try {
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
            
        } catch (\Exception $e) {
            \Log::error('Error loading restaurant menu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load restaurant menu.'
            ], 500);
        }
    }
    
    /**
     * AJAX: Get user orders
     */
    public function getOrders(Request $request)
    {
        try {
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
            
        } catch (\Exception $e) {
            \Log::error('Error loading orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders.'
            ], 500);
        }
    }
    
    /**
     * AJAX: Get user subscriptions
     */
    public function getSubscriptions()
    {
        try {
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
            
        } catch (\Exception $e) {
            \Log::error('Error loading subscriptions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscriptions.'
            ], 500);
        }
    }
    
    // ... (keep the other methods: placeOrder, cancelOrder, createSubscription, cancelSubscription, etc.)
    
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
 * Display restaurants page (traditional)
 */
public function restaurantsPage()
{
    $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
    return view('food.restaurants', [
        'title' => 'Browse Restaurants',
        'mealTypes' => $mealTypes
    ]);
}

/**
 * Display orders page (traditional)
 */
public function ordersPage()
{
    return view('food.orders', [
        'title' => 'My Orders'
    ]);
}

/**
 * Display subscriptions page (traditional)
 */
public function subscriptionsPage()
{
    return view('food.subscriptions', [
        'title' => 'My Subscriptions'
    ]);
}

/**
 * Display create subscription form
 */
public function createSubscriptionForm()
{
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
        'mealTypes' => $mealTypes
    ]);
}

/**
 * Store subscription (traditional POST)
 */
public function storeSubscription(Request $request)
{
    return $this->createSubscription($request);
}

/**
 * Pause subscription
 */
public function pauseSubscription(Request $request, $id)
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
        
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'message' => 'Subscription paused successfully.'
        ]);
    }
    
    return redirect()->back()->with('success', 'Subscription paused successfully.');
}
}