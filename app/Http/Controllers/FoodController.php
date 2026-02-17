<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\FoodSubscription;
use App\Models\MealType;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's default address for location-based services
        $defaultAddress = $user->addresses()->where('is_default', true)->first();
        
        // Get initial restaurants (with location-based sorting if address exists)
        $initialRestaurants = ServiceProvider::where('service_type', 'FOOD')
            ->where('status', 'ACTIVE')
            ->with(['foodConfig'])
            ->withCount('foodOrders as total_orders')
            ->withAvg('serviceRatings as rating', 'overall_rating')
            ->limit(6)
            ->get();
        
        // Calculate distance for each restaurant if user has address
        if ($defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude) {
            foreach ($initialRestaurants as $restaurant) {
                if ($restaurant->latitude && $restaurant->longitude) {
                    $restaurant->distance_km = $this->calculateDistance(
                        $defaultAddress->latitude,
                        $defaultAddress->longitude,
                        $restaurant->latitude,
                        $restaurant->longitude
                    );
                    
                    // Estimate delivery time (base 30 min + 5 min per km)
                    $restaurant->estimated_delivery_minutes = 30 + ceil($restaurant->distance_km * 5);
                } else {
                    $restaurant->distance_km = 5.0;
                    $restaurant->estimated_delivery_minutes = 45;
                }
            }
            
            // Sort by distance
            $initialRestaurants = $initialRestaurants->sortBy('distance_km')->values();
        }
        
        // Get recent orders
        $recentOrders = [];
        if ($user) {
            $orders = FoodOrder::where('user_id', $user->id)
                ->with(['serviceProvider', 'mealType', 'items.foodItem'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
                
            foreach ($orders as $order) {
                $recentOrders[] = [
                    'id' => $order->id,
                    'order_reference' => $order->order_reference,
                    'business_name' => $order->serviceProvider->business_name ?? 'Unknown',
                    'status' => $order->status,
                    'meal_type' => $order->mealType->name ?? 'Unknown',
                    'delivery_address' => $order->delivery_address,
                    'total_amount' => $order->total_amount,
                    'created_at' => $order->created_at,
                    'created_at_formatted' => $order->created_at->diffForHumans(),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->foodItem->name ?? 'Unknown',
                            'quantity' => $item->quantity,
                            'price' => $item->unit_price
                        ];
                    })->toArray()
                ];
            }
        }
        
        // Get active subscriptions
        $subscriptions = [];
        if ($user) {
            $subs = FoodSubscription::where('user_id', $user->id)
                ->whereIn('status', ['ACTIVE', 'PAUSED'])
                ->with(['serviceProvider', 'mealType'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            foreach ($subs as $subscription) {
                $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                $selectedDays = [];
                for ($i = 0; $i < 7; $i++) {
                    if ($subscription->delivery_days & (1 << $i)) {
                        $selectedDays[] = $days[$i];
                    }
                }
                
                $subscriptions[] = [
                    'id' => $subscription->id,
                    'business_name' => $subscription->serviceProvider->business_name ?? 'Unknown',
                    'status' => $subscription->status,
                    'meal_type' => $subscription->mealType->name ?? 'Unknown',
                    'delivery_time' => $subscription->delivery_time ? $subscription->delivery_time->format('h:i A') : '12:00 PM',
                    'delivery_days_text' => implode(', ', $selectedDays) ?: 'All days',
                    'start_date' => $subscription->start_date,
                    'end_date' => $subscription->end_date,
                    'start_date_formatted' => $subscription->start_date ? $subscription->start_date->format('M d, Y') : 'N/A',
                    'end_date_formatted' => $subscription->end_date ? $subscription->end_date->format('M d, Y') : 'N/A',
                    'daily_price' => number_format($subscription->daily_price, 2),
                    'total_price' => number_format($subscription->total_price, 2),
                    'discount_amount' => number_format($subscription->discount_amount, 2)
                ];
            }
        }
        
        $mealTypes = MealType::orderBy('display_order')->get();
        
        $stats = [
            'totalOrders' => $user ? FoodOrder::where('user_id', $user->id)->count() : 0,
            'activeSubscriptions' => $user ? FoodSubscription::where('user_id', $user->id)
                ->where('status', 'ACTIVE')
                ->count() : 0,
            'pendingOrders' => $user ? FoodOrder::where('user_id', $user->id)
                ->whereIn('status', ['PENDING', 'ACCEPTED', 'PREPARING'])
                ->count() : 0
        ];
        
        return view('food.index', compact(
            'initialRestaurants',
            'recentOrders',
            'subscriptions',
            'mealTypes',
            'stats',
            'defaultAddress'
        ));
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 5.0; // Default distance
        }
        
        $earthRadius = 6371; // km
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        
        return round($distance, 1);
    }
}