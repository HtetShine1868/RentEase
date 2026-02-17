<?php

namespace App\Http\Controllers;

use App\Models\FoodItem;
use App\Models\FoodOrder;
use App\Models\FoodOrderItem;
use App\Models\FoodSubscription;
use App\Models\MealType;
use App\Models\Payment;
use App\Models\ServiceProvider;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FoodApiController extends Controller
{
public function getRestaurants(Request $request)
{
    try {
        $user = Auth::user();
        $defaultAddress = $user->addresses()->where('is_default', true)->first();
        
        $query = ServiceProvider::where('service_type', 'FOOD')
            ->where('status', 'ACTIVE')
            ->with(['foodConfig'])
            ->withCount('foodOrders as total_orders')
            ->withCount('serviceRatings as total_ratings')
            ->withAvg('serviceRatings as rating', 'overall_rating')
            ->withAvg('serviceRatings as avg_quality', 'quality_rating')
            ->withAvg('serviceRatings as avg_delivery', 'delivery_rating')
            ->withAvg('serviceRatings as avg_value', 'value_rating');
        
        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('business_name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('foodItems', function ($itemQuery) use ($search) {
                      $itemQuery->where('name', 'like', '%' . $search . '%');
                  });
            });
        }
        
        // Meal type filter
        if ($request->filled('meal_type')) {
            $query->whereHas('foodItems', function ($q) use ($request) {
                $q->where('meal_type_id', $request->meal_type);
            });
        }
        
        // City filter
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        // Minimum rating filter
        if ($request->filled('min_rating') && is_numeric($request->min_rating)) {
            $query->having('rating', '>=', $request->min_rating);
        }
        
        // Only show restaurants that are currently open (optional)
        if ($request->boolean('open_now')) {
            $currentTime = now()->format('H:i:s');
            $query->whereHas('foodConfig', function ($q) use ($currentTime) {
                $q->where('opening_time', '<=', $currentTime)
                  ->where('closing_time', '>=', $currentTime);
            });
        }
        
        $restaurants = $query->get();
        
        // Add distance and estimated delivery time with consistent formatting
        foreach ($restaurants as $restaurant) {
            if ($defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude) {
                $distance = $this->calculateDistance(
                    $defaultAddress->latitude,
                    $defaultAddress->longitude,
                    $restaurant->latitude,
                    $restaurant->longitude
                );
                
                // Format consistently with 1 decimal place
                $restaurant->distance_km = number_format($distance, 1);
                $restaurant->in_service_area = $distance <= $restaurant->service_radius_km;
                $restaurant->estimated_delivery_minutes = 30 + ceil($distance * 5);
                
                // Add delivery fee calculation (optional)
                $restaurant->delivery_fee = $distance <= 2 ? 0 : round($distance * 10, 2);
            } else {
                $restaurant->distance_km = '5.0';
                $restaurant->in_service_area = true;
                $restaurant->estimated_delivery_minutes = 45;
                $restaurant->delivery_fee = 0;
            }
            
            // Get cuisine types from foodConfig
            if ($restaurant->foodConfig && $restaurant->foodConfig->cuisine_type) {
                $restaurant->cuisine_types = array_map('trim', explode(',', $restaurant->foodConfig->cuisine_type));
            } else {
                $restaurant->cuisine_types = [];
            }
            
            // Get discount percentage
            $restaurant->discount_percent = $restaurant->foodConfig->subscription_discount_percent ?? 0;
            
            // Check if restaurant is currently open
            if ($restaurant->foodConfig) {
                $currentTime = now()->format('H:i:s');
                $restaurant->is_open = $currentTime >= $restaurant->foodConfig->opening_time && 
                                       $currentTime <= $restaurant->foodConfig->closing_time;
            } else {
                $restaurant->is_open = true;
            }
            
            // Get minimum order amount (you can add this to your food_configs table if needed)
            $restaurant->min_order_amount = 100; // Default value
            
            // Add rating breakdown
            $restaurant->rating_breakdown = [
                'quality' => round($restaurant->avg_quality ?? 0, 1),
                'delivery' => round($restaurant->avg_delivery ?? 0, 1),
                'value' => round($restaurant->avg_value ?? 0, 1)
            ];
        }
        
        // Apply sorting
        $sort = $request->get('sort', 'rating');
        
        switch ($sort) {
            case 'distance':
                $restaurants = $restaurants->sortBy(function($r) {
                    return floatval($r->distance_km);
                });
                break;
                
            case 'rating_low':
                $restaurants = $restaurants->sortBy('rating');
                break;
                
            case 'delivery_time':
                $restaurants = $restaurants->sortBy('estimated_delivery_minutes');
                break;
                
            case 'total_orders':
                $restaurants = $restaurants->sortByDesc('total_orders');
                break;
                
            case 'total_ratings':
                $restaurants = $restaurants->sortByDesc('total_ratings');
                break;
                
            case 'rating':
            default:
                $restaurants = $restaurants->sortByDesc('rating');
                break;
        }
        
        // Pagination (if needed)
        $page = $request->get('page', 1);
        $perPage = 12;
        $total = $restaurants->count();
        $paginated = $restaurants->forPage($page, $perPage)->values();
        
        return response()->json([
            'success' => true,
            'restaurants' => $paginated->map(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'business_name' => $restaurant->business_name,
                    'description' => $restaurant->description,
                    'rating' => round($restaurant->rating ?? 0, 1),
                    'total_ratings' => $restaurant->total_ratings ?? 0,
                    'total_orders' => $restaurant->total_orders ?? 0,
                    'city' => $restaurant->city,
                    'address' => $restaurant->address,
                    'latitude' => $restaurant->latitude,
                    'longitude' => $restaurant->longitude,
                    'distance_km' => $restaurant->distance_km,
                    'estimated_delivery_minutes' => $restaurant->estimated_delivery_minutes,
                    'delivery_fee' => $restaurant->delivery_fee,
                    'opening_time' => $restaurant->foodConfig->opening_time ?? '08:00',
                    'closing_time' => $restaurant->foodConfig->closing_time ?? '22:00',
                    'is_open' => $restaurant->is_open ?? true,
                    'supports_subscription' => $restaurant->foodConfig->supports_subscription ?? false,
                    'discount_percent' => $restaurant->discount_percent ?? 0,
                    'min_order_amount' => $restaurant->min_order_amount ?? 100,
                    'in_service_area' => $restaurant->in_service_area,
                    'cuisine_types' => $restaurant->cuisine_types,
                    'avg_quality' => round($restaurant->avg_quality ?? 0, 1),
                    'avg_delivery' => round($restaurant->avg_delivery ?? 0, 1),
                    'avg_value' => round($restaurant->avg_value ?? 0, 1),
                    'rating_breakdown' => $restaurant->rating_breakdown,
                    'image' => $restaurant->primary_image ? Storage::url($restaurant->primary_image) : null
                ];
            }),
            'current_page' => (int)$page,
            'last_page' => ceil($total / $perPage),
            'total' => $total,
            'per_page' => $perPage
        ]);
        
    } catch (\Exception $e) {
        \Log::error('Error loading restaurants: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to load restaurants. Please try again.',
            'error' => config('app.debug') ? $e->getMessage() : null
        ], 500);
    }
}


    
    public function getRestaurantMenu($id)
    {
        try {
            $restaurant = ServiceProvider::where('id', $id)
                ->where('service_type', 'FOOD')
                ->where('status', 'ACTIVE')
                ->with(['foodConfig'])
                ->firstOrFail();
            
            $menuItems = FoodItem::where('service_provider_id', $id)
                ->where('is_available', true)
                ->with('mealType')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'description' => $item->description,
                        'meal_type_id' => $item->meal_type_id,
                        'meal_type_name' => $item->mealType->name,
                        'base_price' => $item->base_price,
                        'total_price' => $item->total_price,
                        'dietary_tags' => $item->dietary_tags,
                        'calories' => $item->calories,
                        'daily_quantity' => $item->daily_quantity,
                        'sold_today' => $item->sold_today
                    ];
                });
            
            return response()->json([
                'success' => true,
                'restaurant' => [
                    'id' => $restaurant->id,
                    'business_name' => $restaurant->business_name,
                    'description' => $restaurant->description,
                    'address' => $restaurant->address,
                    'city' => $restaurant->city,
                    'opening_time' => $restaurant->foodConfig->opening_time ?? '08:00',
                    'closing_time' => $restaurant->foodConfig->closing_time ?? '22:00',
                    'supports_subscription' => $restaurant->foodConfig->supports_subscription ?? false
                ],
                'menu_items' => $menuItems
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading restaurant menu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load restaurant menu'
            ], 500);
        }
    }
    
    public function getOrders(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = FoodOrder::where('user_id', $user->id)
                ->with(['serviceProvider', 'mealType', 'items.foodItem']);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $orders = $query->orderBy('created_at', 'desc')->get();
            
            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_reference' => $order->order_reference,
                        'business_name' => $order->serviceProvider->business_name,
                        'status' => $order->status,
                        'meal_type' => $order->mealType->name,
                        'delivery_address' => $order->delivery_address,
                        'total_amount' => number_format($order->total_amount, 2),
                        'created_at' => $order->created_at,
                        'created_at_formatted' => $order->created_at->diffForHumans(),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->foodItem->name,
                                'quantity' => $item->quantity,
                                'price' => $item->unit_price
                            ];
                        })
                    ];
                })
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders'
            ], 500);
        }
    }
    
    public function getSubscriptions(Request $request)
    {
        try {
            $user = Auth::user();
            
            $subscriptions = FoodSubscription::where('user_id', $user->id)
                ->with(['serviceProvider', 'mealType'])
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($subscription) {
                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    $selectedDays = [];
                    for ($i = 0; $i < 7; $i++) {
                        if ($subscription->delivery_days & (1 << $i)) {
                            $selectedDays[] = $days[$i];
                        }
                    }
                    
                    return [
                        'id' => $subscription->id,
                        'business_name' => $subscription->serviceProvider->business_name,
                        'status' => $subscription->status,
                        'meal_type' => $subscription->mealType->name,
                        'delivery_time' => $subscription->delivery_time->format('h:i A'),
                        'delivery_days_text' => implode(', ', $selectedDays),
                        'start_date' => $subscription->start_date,
                        'end_date' => $subscription->end_date,
                        'start_date_formatted' => $subscription->start_date->format('M d, Y'),
                        'end_date_formatted' => $subscription->end_date->format('M d, Y'),
                        'daily_price' => number_format($subscription->daily_price, 2),
                        'total_price' => number_format($subscription->total_price, 2),
                        'discount_amount' => number_format($subscription->discount_amount, 2)
                    ];
                });
            
            return response()->json([
                'success' => true,
                'subscriptions' => $subscriptions
            ]);
        } catch (\Exception $e) {
            \Log::error('Error loading subscriptions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscriptions'
            ], 500);
        }
    }
    
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'service_provider_id' => 'required|exists:service_providers,id',
                'meal_type_id' => 'required|exists:meal_types,id',
                'meal_date' => 'required|date',
                'delivery_address' => 'required|string',
                'delivery_latitude' => 'nullable|numeric',
                'delivery_longitude' => 'nullable|numeric',
                'items' => 'required|array|min:1',
                'items.*.food_item_id' => 'required|exists:food_items,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);
            
            $user = Auth::user();
            
            // Check if restaurant is open
            $restaurant = ServiceProvider::with('foodConfig')->find($request->service_provider_id);
            $currentTime = Carbon::now()->format('H:i:s');
            
            if ($currentTime < ($restaurant->foodConfig->opening_time ?? '08:00:00') ||
                $currentTime > ($restaurant->foodConfig->closing_time ?? '22:00:00')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Restaurant is currently closed'
                ], 400);
            }
            
            DB::beginTransaction();
            
            // Calculate order totals
            $baseAmount = 0;
            $commissionAmount = 0;
            $totalAmount = 0;
            $orderItems = [];
            
            foreach ($request->items as $item) {
                $foodItem = FoodItem::find($item['food_item_id']);
                
                // Check availability
                if ($foodItem->daily_quantity && 
                    ($foodItem->sold_today + $item['quantity']) > $foodItem->daily_quantity) {
                    return response()->json([
                        'success' => false,
                        'message' => "Item {$foodItem->name} is out of stock"
                    ], 400);
                }
                
                $itemBaseTotal = $foodItem->base_price * $item['quantity'];
                $itemCommission = ($foodItem->base_price * $foodItem->commission_rate / 100) * $item['quantity'];
                
                $baseAmount += $itemBaseTotal;
                $commissionAmount += $itemCommission;
                
                $orderItems[] = [
                    'food_item_id' => $foodItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $foodItem->base_price
                ];
                
                // Update sold today
                $foodItem->sold_today += $item['quantity'];
                $foodItem->save();
            }
            
            $totalAmount = $baseAmount + $commissionAmount;
            
            // Calculate distance for delivery fee (if applicable)
            $distance = 0;
            if ($request->delivery_latitude && $request->delivery_longitude) {
                $distance = $this->calculateDistance(
                    $restaurant->latitude,
                    $restaurant->longitude,
                    $request->delivery_latitude,
                    $request->delivery_longitude
                );
            }
            
            $deliveryFee = $distance * 10; // 10 BDT per km
            $totalAmount += $deliveryFee;
            
            // Create order
            $order = FoodOrder::create([
                'order_reference' => 'FOOD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'order_type' => 'PAY_PER_EAT',
                'meal_date' => $request->meal_date,
                'meal_type_id' => $request->meal_type_id,
                'delivery_address' => $request->delivery_address,
                'delivery_latitude' => $request->delivery_latitude ?? $restaurant->latitude,
                'delivery_longitude' => $request->delivery_longitude ?? $restaurant->longitude,
                'distance_km' => $distance,
                'status' => 'PENDING',
                'estimated_delivery_time' => Carbon::now()->addMinutes(30 + ceil($distance * 5)),
                'base_amount' => $baseAmount,
                'delivery_fee' => $deliveryFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount
            ]);
            
            // Create order items
            foreach ($orderItems as $item) {
                FoodOrderItem::create(array_merge($item, ['food_order_id' => $order->id]));
            }
            
            // Create payment record
            Payment::create([
                'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'payable_type' => 'FOOD_ORDER',
                'payable_id' => $order->id,
                'amount' => $totalAmount,
                'commission_amount' => $commissionAmount,
                'payment_method' => 'BANK_TRANSFER',
                'status' => 'COMPLETED',
                'paid_at' => now()
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_reference' => $order->order_reference
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error placing order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function cancelOrder($id)
    {
        try {
            $user = Auth::user();
            $order = FoodOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            if (!in_array($order->status, ['PENDING', 'ACCEPTED'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled at this stage'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $order->status = 'CANCELLED';
            $order->save();
            
            // Refund payment if applicable
            $payment = Payment::where('payable_type', 'FOOD_ORDER')
                ->where('payable_id', $order->id)
                ->first();
            
            if ($payment && $payment->status === 'COMPLETED') {
                $payment->status = 'REFUNDED';
                $payment->save();
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
            ], 500);
        }
    }
public function createSubscription(Request $request)
{
    try {
        \Log::info('Creating subscription with data:', $request->all());
        
        $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'meal_type_id' => 'required|exists:meal_types,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'delivery_time' => 'required',
            'delivery_days' => 'required|integer|min:1|max:127',
            'items' => 'required|array|min:1',
            'items.*.food_item_id' => 'required|exists:food_items,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);
        
        $user = Auth::user();
        
        // Get restaurant with config
        $restaurant = ServiceProvider::with('foodConfig')->find($request->service_provider_id);
        
        if (!$restaurant) {
            return response()->json([
                'success' => false,
                'message' => 'Restaurant not found'
            ], 404);
        }
        
        DB::beginTransaction();
        
        // Calculate daily price based on items
        $dailyPrice = 0;
        foreach ($request->items as $item) {
            $foodItem = FoodItem::find($item['food_item_id']);
            if (!$foodItem) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Food item not found'
                ], 404);
            }
            $dailyPrice += $foodItem->total_price * $item['quantity'];
        }
        
        // Calculate number of days
        $start = Carbon::parse($request->start_date);
        $end = Carbon::parse($request->end_date);
        $totalDays = $start->diffInDays($end) + 1;
        
        // Calculate how many delivery days in the selected period
        $deliveryDaysCount = 0;
        $currentDate = $start->copy();
        
        while ($currentDate <= $end) {
            $dayOfWeek = $currentDate->dayOfWeek; // 0 = Sunday, 6 = Saturday
            $bitValue = 1 << $dayOfWeek; // Convert to bitmask value
            
            if ($request->delivery_days & $bitValue) {
                $deliveryDaysCount++;
            }
            
            $currentDate->addDay();
        }
        
        // Calculate total price
        $totalPrice = $dailyPrice * $deliveryDaysCount;
        
        // Apply subscription discount (from restaurant config or default 10%)
        $discountPercent = $restaurant->foodConfig->subscription_discount_percent ?? 10;
        $discountAmount = $totalPrice * ($discountPercent / 100);
        $finalPrice = $totalPrice - $discountAmount;
        
        // Create subscription
        $subscription = FoodSubscription::create([
            'user_id' => $user->id,
            'service_provider_id' => $request->service_provider_id,
            'meal_type_id' => $request->meal_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'delivery_time' => $request->delivery_time,
            'delivery_days' => $request->delivery_days,
            'status' => 'ACTIVE',
            'daily_price' => $dailyPrice,
            'total_price' => $finalPrice,
            'discount_amount' => $discountAmount
        ]);
        
        // Create payment record
        Payment::create([
            'payment_reference' => 'SUB-' . strtoupper(Str::random(10)),
            'user_id' => $user->id,
            'payable_type' => 'FOOD_SUBSCRIPTION',
            'payable_id' => $subscription->id,
            'amount' => $finalPrice,
            'commission_amount' => $finalPrice * 0.1, // 10% commission
            'payment_method' => 'BANK_TRANSFER',
            'status' => 'COMPLETED',
            'paid_at' => now()
        ]);
        
        DB::commit();
        
        \Log::info('Subscription created successfully:', ['id' => $subscription->id]);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription created successfully',
            'subscription' => [
                'id' => $subscription->id,
                'total_price' => $finalPrice,
                'daily_price' => $dailyPrice
            ]
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        \Log::error('Subscription validation error:', $e->errors());
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error creating subscription: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to create subscription: ' . $e->getMessage()
        ], 500);
    }
}
    public function cancelSubscription($id)
    {
        try {
            $user = Auth::user();
            $subscription = FoodSubscription::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            if (!in_array($subscription->status, ['ACTIVE', 'PAUSED'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription cannot be cancelled'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $subscription->status = 'CANCELLED';
            $subscription->save();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error cancelling subscription: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription'
            ], 500);
        }
    }
    
public function pauseSubscription($id)
{
    try {
        \Log::info('Attempting to pause subscription: ' . $id);
        
        $user = Auth::user();
        $subscription = FoodSubscription::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$subscription) {
            \Log::error('Subscription not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found'
            ], 404);
        }
        
        \Log::info('Current subscription status: ' . $subscription->status);
        
        if ($subscription->status !== 'ACTIVE') {
            return response()->json([
                'success' => false,
                'message' => 'Only active subscriptions can be paused. Current status: ' . $subscription->status
            ], 400);
        }
        
        DB::beginTransaction();
        
        $subscription->status = 'PAUSED';
        $subscription->save();
        
        DB::commit();
        
        \Log::info('Subscription paused successfully: ' . $id);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription paused successfully'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error pausing subscription: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to pause subscription: ' . $e->getMessage()
        ], 500);
    }
}

public function resumeSubscription($id)
{
    try {
        \Log::info('Attempting to resume subscription: ' . $id);
        
        $user = Auth::user();
        $subscription = FoodSubscription::where('id', $id)
            ->where('user_id', $user->id)
            ->first();
        
        if (!$subscription) {
            \Log::error('Subscription not found: ' . $id);
            return response()->json([
                'success' => false,
                'message' => 'Subscription not found'
            ], 404);
        }
        
        \Log::info('Current subscription status: ' . $subscription->status);
        
        if ($subscription->status !== 'PAUSED') {
            return response()->json([
                'success' => false,
                'message' => 'Only paused subscriptions can be resumed. Current status: ' . $subscription->status
            ], 400);
        }
        
        DB::beginTransaction();
        
        $subscription->status = 'ACTIVE';
        $subscription->save();
        
        DB::commit();
        
        \Log::info('Subscription resumed successfully: ' . $id);
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription resumed successfully'
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error resuming subscription: ' . $e->getMessage());
        \Log::error($e->getTraceAsString());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to resume subscription: ' . $e->getMessage()
        ], 500);
    }
}
    

    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 0;
        }
        
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return round($distance, 2);
    }
}