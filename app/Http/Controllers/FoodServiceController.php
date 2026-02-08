<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodServiceController extends Controller
{
    // Get restaurants with filters
    public function getRestaurants(Request $request)
    {
        $user = Auth::user();
        $userLocation = $this->getUserLocation($user);
        
        $query = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.service_type', 'FOOD')
            ->where('sp.status', 'ACTIVE');
        
        // Search filter
        if ($request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sp.business_name', 'LIKE', "%{$search}%")
                  ->orWhere('sp.description', 'LIKE', "%{$search}%")
                  ->orWhereExists(function($subquery) use ($search) {
                      $subquery->select(DB::raw(1))
                              ->from('food_items')
                              ->whereColumn('food_items.service_provider_id', 'sp.id')
                              ->where('food_items.name', 'LIKE', "%{$search}%");
                  });
            });
        }
        
        // Meal type filter
        if ($request->meal_type) {
            $query->whereExists(function($subquery) use ($request) {
                $subquery->select(DB::raw(1))
                        ->from('food_items as fi')
                        ->whereColumn('fi.service_provider_id', 'sp.id')
                        ->where('fi.meal_type_id', $request->meal_type)
                        ->where('fi.is_available', true);
            });
        }
        
        // Calculate distance if user location exists
        if ($userLocation['latitude'] && $userLocation['longitude']) {
            $query->selectRaw('
                sp.*,
                fsc.opening_time,
                fsc.closing_time,
                (6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude)))) as distance_km,
                fsc.avg_preparation_minutes + (fsc.delivery_buffer_minutes * (6371 * acos(cos(radians(?)) * cos(radians(sp.latitude)) * cos(radians(sp.longitude) - radians(?)) + sin(radians(?)) * sin(radians(sp.latitude))))) as estimated_delivery_minutes',
                [
                    $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude'],
                    $userLocation['latitude'], $userLocation['longitude'], $userLocation['latitude']
                ]
            );
        } else {
            $query->selectRaw('sp.*, fsc.opening_time, fsc.closing_time, 5.0 as distance_km, 45 as estimated_delivery_minutes');
        }
        
        // Sorting
        switch ($request->sort) {
            case 'distance':
                if ($userLocation['latitude'] && $userLocation['longitude']) {
                    $query->orderBy('distance_km');
                }
                break;
            case 'delivery_time':
                $query->orderBy('estimated_delivery_minutes');
                break;
            case 'total_orders':
                $query->orderByDesc('sp.total_orders');
                break;
            case 'rating':
            default:
                $query->orderByDesc('sp.rating');
        }
        
        $restaurants = $query->paginate(12);
        
        return response()->json([
            'restaurants' => $restaurants,
            'meal_types' => DB::table('meal_types')->orderBy('display_order')->get()
        ]);
    }
    
    // Get restaurant menu
    public function getRestaurantMenu($restaurantId)
    {
        $restaurant = DB::table('service_providers as sp')
            ->leftJoin('food_service_configs as fsc', 'sp.id', '=', 'fsc.service_provider_id')
            ->where('sp.id', $restaurantId)
            ->where('sp.service_type', 'FOOD')
            ->select('sp.*', 'fsc.*')
            ->first();
            
        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }
        
        $menuItems = DB::table('food_items as fi')
            ->join('meal_types as mt', 'fi.meal_type_id', '=', 'mt.id')
            ->where('fi.service_provider_id', $restaurantId)
            ->where('fi.is_available', true)
            ->select('fi.*', 'mt.name as meal_type_name')
            ->orderBy('mt.display_order')
            ->orderBy('fi.name')
            ->get();
        
        // Group items by meal type
        $menuItemsByType = $menuItems->groupBy('meal_type_id');
        
        return response()->json([
            'restaurant' => $restaurant,
            'menu_items' => $menuItems,
            'menu_by_type' => $menuItemsByType
        ]);
    }
    
    // Get user's food orders
    public function getUserOrders(Request $request)
    {
        $user = Auth::user();
        
        $query = DB::table('food_orders as fo')
            ->join('service_providers as sp', 'fo.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fo.meal_type_id', '=', 'mt.id')
            ->leftJoin('food_subscriptions as fs', 'fo.subscription_id', '=', 'fs.id')
            ->where('fo.user_id', $user->id);
        
        if ($request->status) {
            $query->where('fo.status', $request->status);
        }
        
        $orders = $query->select('fo.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderByDesc('fo.created_at')
            ->paginate(10);
        
        // Get order items for each order
        foreach ($orders as $order) {
            $order->items = DB::table('food_order_items as foi')
                ->join('food_items as fi', 'foi.food_item_id', '=', 'fi.id')
                ->where('foi.food_order_id', $order->id)
                ->select('fi.name', 'foi.quantity', 'foi.unit_price')
                ->get();
            
            $order->created_at_formatted = \Carbon\Carbon::parse($order->created_at)->format('M d, Y h:i A');
        }
        
        return response()->json(['orders' => $orders]);
    }
    
    // Get user's subscriptions
    public function getUserSubscriptions()
    {
        $user = Auth::user();
        
        $subscriptions = DB::table('food_subscriptions as fs')
            ->join('service_providers as sp', 'fs.service_provider_id', '=', 'sp.id')
            ->join('meal_types as mt', 'fs.meal_type_id', '=', 'mt.id')
            ->where('fs.user_id', $user->id)
            ->select('fs.*', 'sp.business_name', 'mt.name as meal_type')
            ->orderByDesc('fs.created_at')
            ->get();
        
        return response()->json(['subscriptions' => $subscriptions]);
    }
    
    // Create food order
    public function createOrder(Request $request)
    {
        $user = Auth::user();
        $userLocation = $this->getUserLocation($user);
        
        $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'order_type' => 'required|in:PAY_PER_EAT,SUBSCRIPTION_MEAL',
            'meal_date' => 'required|date|after_or_equal:today',
            'meal_type_id' => 'required|exists:meal_types,id',
            'delivery_instructions' => 'nullable|string'
        ]);
        
        DB::beginTransaction();
        
        try {
            // Get service provider
            $serviceProvider = DB::table('service_providers')
                ->where('id', $request->service_provider_id)
                ->where('service_type', 'FOOD')
                ->first();
                
            if (!$serviceProvider) {
                throw new \Exception('Service provider not found');
            }
            
            // Calculate distance
            $distance = $this->calculateDistance(
                $serviceProvider->latitude,
                $serviceProvider->longitude,
                $userLocation['latitude'],
                $userLocation['longitude']
            );
            
            // Generate order reference
            $orderReference = 'FOOD-' . strtoupper(uniqid());
            
            // Calculate total amount
            $baseAmount = 0;
            $commissionRate = 0;
            $deliveryFee = $this->calculateDeliveryFee($distance);
            
            foreach ($request->items as $item) {
                $foodItem = DB::table('food_items')->where('id', $item['food_item_id'])->first();
                
                // Check availability
                if (!$foodItem->is_available) {
                    throw new \Exception("{$foodItem->name} is not available");
                }
                
                if ($foodItem->daily_quantity && ($foodItem->sold_today + $item['quantity']) > $foodItem->daily_quantity) {
                    throw new \Exception("Only {$foodItem->daily_quantity -> $foodItem->sold_today} {$foodItem->name} left today");
                }
                
                $baseAmount += $foodItem->base_price * $item['quantity'];
                
                // Use the item's commission rate
                if ($foodItem->commission_rate > $commissionRate) {
                    $commissionRate = $foodItem->commission_rate;
                }
                
                // Update sold count
                DB::table('food_items')
                    ->where('id', $foodItem->id)
                    ->increment('sold_today', $item['quantity']);
            }
            
            $commissionAmount = ($baseAmount * $commissionRate) / 100;
            $totalAmount = $baseAmount + $commissionAmount + $deliveryFee;
            
            // Create order
            $orderId = DB::table('food_orders')->insertGetId([
                'order_reference' => $orderReference,
                'user_id' => $user->id,
                'service_provider_id' => $serviceProvider->id,
                'order_type' => $request->order_type,
                'meal_date' => $request->meal_date,
                'meal_type_id' => $request->meal_type_id,
                'delivery_address' => $userLocation['address'] ?? '',
                'delivery_latitude' => $userLocation['latitude'],
                'delivery_longitude' => $userLocation['longitude'],
                'distance_km' => $distance,
                'delivery_instructions' => $request->delivery_instructions,
                'estimated_delivery_time' => now()->addMinutes(30 + ($distance * 15)),
                'base_amount' => $baseAmount,
                'delivery_fee' => $deliveryFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Create order items
            foreach ($request->items as $item) {
                $foodItem = DB::table('food_items')->where('id', $item['food_item_id'])->first();
                
                DB::table('food_order_items')->insert([
                    'food_order_id' => $orderId,
                    'food_item_id' => $item['food_item_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $foodItem->base_price,
                    'special_instructions' => $item['special_instructions'] ?? null,
                    'created_at' => now()
                ]);
            }
            
            // Create payment record
            $paymentReference = 'PAY-' . strtoupper(uniqid());
            
            DB::table('payments')->insert([
                'payment_reference' => $paymentReference,
                'user_id' => $user->id,
                'payable_type' => 'FOOD_ORDER',
                'payable_id' => $orderId,
                'amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'BANK_TRANSFER',
                'status' => 'PENDING',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            // Send notification
            DB::table('notifications')->insert([
                'user_id' => $user->id,
                'type' => 'ORDER',
                'title' => 'Food Order Placed',
                'message' => "Your food order #{$orderReference} has been placed successfully.",
                'related_entity_type' => 'FOOD_ORDER',
                'related_entity_id' => $orderId,
                'created_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'order_id' => $orderId,
                'order_reference' => $orderReference,
                'payment_reference' => $paymentReference
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }
    
    // Helper methods
    private function getUserLocation($user)
    {
        // Get user's default address or current location
        $address = DB::table('user_addresses')
            ->where('user_id', $user->id)
            ->where('is_default', true)
            ->first();
            
        if ($address) {
            return [
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'address' => "{$address->address_line1}, {$address->city}, {$address->state}"
            ];
        }
        
        // Return default location (could be based on booking location)
        $booking = DB::table('bookings')
            ->where('user_id', $user->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->latest()
            ->first();
            
        if ($booking) {
            $property = DB::table('properties')->where('id', $booking->property_id)->first();
            if ($property) {
                return [
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                    'address' => $property->address
                ];
            }
        }
        
        // Default to Dhaka coordinates if nothing found
        return [
            'latitude' => 23.8103,
            'longitude' => 90.4125,
            'address' => 'Dhaka, Bangladesh'
        ];
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
    
    private function calculateDeliveryFee($distance)
    {
        // Base fee + per km charge
        $baseFee = 20;
        $perKmFee = 5;
        
        return $baseFee + ($distance * $perKmFee);
    }
}