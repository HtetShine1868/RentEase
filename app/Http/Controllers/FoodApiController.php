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
use App\Models\ServiceRating;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FoodApiController extends Controller
{
    /**
     * Get restaurants with location-based filtering
     */
    public function getRestaurants(Request $request)
    {
        try {
            $user = Auth::user();
            $latitude = $request->get('latitude');
            $longitude = $request->get('longitude');
            $radius = $request->get('radius', 10); // Default 10km radius
            
            $query = ServiceProvider::where('service_type', 'FOOD')
                ->where('status', 'ACTIVE')
                ->with(['foodConfig'])
                ->withCount('foodOrders as total_orders')
                ->withCount('serviceRatings as total_ratings')
                ->withAvg('serviceRatings as rating', 'overall_rating')
                ->withAvg('serviceRatings as avg_quality', 'quality_rating')
                ->withAvg('serviceRatings as avg_delivery', 'delivery_rating')
                ->withAvg('serviceRatings as avg_value', 'value_rating');
            
            // If coordinates provided, calculate distance
            if ($latitude && $longitude) {
                $haversine = "(6371 * acos(cos(radians($latitude)) 
                            * cos(radians(latitude)) 
                            * cos(radians(longitude) - radians($longitude)) 
                            + sin(radians($latitude)) 
                            * sin(radians(latitude))))";
                
                $query->select('service_providers.*')
                    ->selectRaw("{$haversine} AS distance")
                    ->havingRaw("distance <= ?", [$radius])
                    ->orderBy('distance');
            }
            
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
            
            // Only show restaurants that are currently open
            if ($request->boolean('open_now')) {
                $currentTime = now()->format('H:i:s');
                $query->whereHas('foodConfig', function ($q) use ($currentTime) {
                    $q->where('opening_time', '<=', $currentTime)
                      ->where('closing_time', '>=', $currentTime);
                });
            }
            
            // Cuisine type filter
            if ($request->filled('cuisine')) {
                $query->whereHas('foodConfig', function ($q) use ($request) {
                    $q->where('cuisine_type', 'like', '%' . $request->cuisine . '%');
                });
            }
            
            $restaurants = $query->get();
            
            // Process each restaurant with additional data
            foreach ($restaurants as $restaurant) {
                // Add restaurant status info
                if ($restaurant->foodConfig) {
                    $currentTime = now()->format('H:i:s');
                    $restaurant->is_open = $currentTime >= $restaurant->foodConfig->opening_time && 
                                           $currentTime <= $restaurant->foodConfig->closing_time;
                    $restaurant->opening_time_formatted = Carbon::parse($restaurant->foodConfig->opening_time)->format('h:i A');
                    $restaurant->closing_time_formatted = Carbon::parse($restaurant->foodConfig->closing_time)->format('h:i A');
                } else {
                    $restaurant->is_open = true;
                    $restaurant->opening_time_formatted = '8:00 AM';
                    $restaurant->closing_time_formatted = '10:00 PM';
                }
                
                // Get cuisine types from foodConfig
                if ($restaurant->foodConfig && $restaurant->foodConfig->cuisine_type) {
                    $restaurant->cuisine_types = array_map('trim', explode(',', $restaurant->foodConfig->cuisine_type));
                } else {
                    $restaurant->cuisine_types = [];
                }
                
                // Get discount percentage
                $restaurant->discount_percent = $restaurant->foodConfig->subscription_discount_percent ?? 0;
                
                // Get minimum order amount (you can add this to your food_configs table if needed)
                $restaurant->min_order_amount = $restaurant->foodConfig->min_order_amount ?? 100;
                
                // Calculate estimated delivery time based on distance
                if (isset($restaurant->distance)) {
                    $restaurant->estimated_delivery_minutes = 30 + ceil($restaurant->distance * 5);
                    $restaurant->delivery_fee = $restaurant->distance <= 2 ? 0 : round($restaurant->distance * 10, 2);
                    $restaurant->in_service_area = $restaurant->distance <= $restaurant->service_radius_km;
                } else {
                    $restaurant->estimated_delivery_minutes = 45;
                    $restaurant->delivery_fee = 0;
                    $restaurant->in_service_area = true;
                }
                
                // Add rating breakdown
                $restaurant->rating_breakdown = [
                    'quality' => round($restaurant->avg_quality ?? 0, 1),
                    'delivery' => round($restaurant->avg_delivery ?? 0, 1),
                    'value' => round($restaurant->avg_value ?? 0, 1)
                ];
                
                // Get popular items (top 3 most ordered)
                $restaurant->popular_items = FoodItem::where('service_provider_id', $restaurant->id)
                    ->where('is_available', true)
                    ->orderBy('sold_today', 'desc')
                    ->limit(3)
                    ->get(['id', 'name', 'base_price', 'total_price', 'image']);
            }
            
            // Apply sorting
            $sort = $request->get('sort', 'recommended');
            
            switch ($sort) {
                case 'distance':
                    $restaurants = $restaurants->sortBy('distance');
                    break;
                    
                case 'rating':
                    $restaurants = $restaurants->sortByDesc('rating');
                    break;
                    
                case 'rating_low':
                    $restaurants = $restaurants->sortBy('rating');
                    break;
                    
                case 'delivery_time':
                    $restaurants = $restaurants->sortBy('estimated_delivery_minutes');
                    break;
                    
                case 'delivery_fee':
                    $restaurants = $restaurants->sortBy('delivery_fee');
                    break;
                    
                case 'total_orders':
                    $restaurants = $restaurants->sortByDesc('total_orders');
                    break;
                    
                case 'total_ratings':
                    $restaurants = $restaurants->sortByDesc('total_ratings');
                    break;
                    
                case 'recommended':
                default:
                    // Mix of rating and distance
                    $restaurants = $restaurants->sortByDesc(function($r) {
                        return ($r->rating ?? 0) * 0.7 + (1 / max($r->distance ?? 1, 1)) * 30;
                    });
                    break;
            }
            
            // Pagination
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
                        'distance' => isset($restaurant->distance) ? round($restaurant->distance, 1) : null,
                        'distance_km' => isset($restaurant->distance) ? number_format($restaurant->distance, 1) . ' km' : null,
                        'estimated_delivery_minutes' => $restaurant->estimated_delivery_minutes,
                        'delivery_fee' => $restaurant->delivery_fee,
                        'opening_time' => $restaurant->foodConfig->opening_time ?? '08:00:00',
                        'closing_time' => $restaurant->foodConfig->closing_time ?? '22:00:00',
                        'opening_time_formatted' => $restaurant->opening_time_formatted,
                        'closing_time_formatted' => $restaurant->closing_time_formatted,
                        'is_open' => $restaurant->is_open,
                        'supports_subscription' => $restaurant->foodConfig->supports_subscription ?? false,
                        'discount_percent' => $restaurant->discount_percent,
                        'min_order_amount' => $restaurant->min_order_amount,
                        'in_service_area' => $restaurant->in_service_area,
                        'cuisine_types' => $restaurant->cuisine_types,
                        'rating_breakdown' => $restaurant->rating_breakdown,
                        'popular_items' => $restaurant->popular_items,
                        'image' => $restaurant->primary_image ? Storage::url($restaurant->primary_image) : null
                    ];
                }),
                'current_page' => (int)$page,
                'last_page' => ceil($total / $perPage),
                'total' => $total,
                'per_page' => $perPage,
                'filters' => [
                    'search' => $request->search,
                    'meal_type' => $request->meal_type,
                    'sort' => $sort,
                    'radius' => $radius,
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]
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
    
    /**
     * Get restaurant menu with details
     */
    public function getRestaurantMenu($id)
    {
        try {
            $restaurant = ServiceProvider::where('id', $id)
                ->where('service_type', 'FOOD')
                ->where('status', 'ACTIVE')
                ->with(['foodConfig'])
                ->firstOrFail();
            
            $currentTime = now()->format('H:i:s');
            $isOpen = true;
            
            if ($restaurant->foodConfig) {
                $isOpen = $currentTime >= $restaurant->foodConfig->opening_time && 
                          $currentTime <= $restaurant->foodConfig->closing_time;
            }
            
            $menuItems = FoodItem::where('service_provider_id', $id)
                ->where('is_available', true)
                ->with('mealType')
                ->get()
                ->groupBy('meal_type_id')
                ->map(function ($items, $mealTypeId) {
                    $mealType = $items->first()->mealType;
                    return [
                        'meal_type_id' => (int)$mealTypeId,
                        'meal_type_name' => $mealType->name,
                        'display_order' => $mealType->display_order,
                        'items' => $items->map(function ($item) {
                            $availableQuantity = $item->daily_quantity ? 
                                max(0, $item->daily_quantity - $item->sold_today) : null;
                            
                            return [
                                'id' => $item->id,
                                'name' => $item->name,
                                'description' => $item->description,
                                'meal_type_id' => $item->meal_type_id,
                                'base_price' => (float)$item->base_price,
                                'total_price' => (float)$item->total_price,
                                'formatted_price' => '৳' . number_format($item->total_price, 2),
                                'dietary_tags' => $item->dietary_tags,
                                'calories' => $item->calories,
                                'daily_quantity' => $item->daily_quantity,
                                'sold_today' => $item->sold_today,
                                'available_quantity' => $availableQuantity,
                                'is_available' => $availableQuantity === null || $availableQuantity > 0,
                                'image' => $item->image ? Storage::url($item->image) : null,
                                'preparation_time' => $item->preparation_time ?? 15
                            ];
                        })->values()
                    ];
                })->values()
                ->sortBy('display_order')
                ->values();
            
            return response()->json([
                'success' => true,
                'restaurant' => [
                    'id' => $restaurant->id,
                    'business_name' => $restaurant->business_name,
                    'description' => $restaurant->description,
                    'address' => $restaurant->address,
                    'city' => $restaurant->city,
                    'latitude' => $restaurant->latitude,
                    'longitude' => $restaurant->longitude,
                    'phone' => $restaurant->contact_phone,
                    'email' => $restaurant->contact_email,
                    'opening_time' => $restaurant->foodConfig->opening_time ?? '08:00:00',
                    'closing_time' => $restaurant->foodConfig->closing_time ?? '22:00:00',
                    'opening_time_formatted' => $restaurant->foodConfig ? 
                        Carbon::parse($restaurant->foodConfig->opening_time)->format('h:i A') : '8:00 AM',
                    'closing_time_formatted' => $restaurant->foodConfig ? 
                        Carbon::parse($restaurant->foodConfig->closing_time)->format('h:i A') : '10:00 PM',
                    'is_open' => $isOpen,
                    'supports_subscription' => $restaurant->foodConfig->supports_subscription ?? false,
                    'avg_preparation_minutes' => $restaurant->foodConfig->avg_preparation_minutes ?? 30,
                    'delivery_buffer_minutes' => $restaurant->foodConfig->delivery_buffer_minutes ?? 15,
                    'subscription_discount_percent' => $restaurant->foodConfig->subscription_discount_percent ?? 10,
                    'service_radius_km' => $restaurant->service_radius_km,
                    'rating' => $restaurant->rating ?? 0,
                    'total_ratings' => $restaurant->total_ratings ?? 0
                ],
                'menu' => $menuItems
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading restaurant menu: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load restaurant menu'
            ], 500);
        }
    }
    
    /**
     * Get restaurant ratings and reviews
     */
    public function getRestaurantRatings($id)
    {
        try {
            $restaurant = ServiceProvider::findOrFail($id);
            
            $ratings = ServiceRating::where('service_provider_id', $id)
                ->where('order_type', 'FOOD')
                ->with('user:id,name,avatar_url')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $stats = [
                'average' => round($restaurant->rating ?? 0, 1),
                'total' => $restaurant->total_ratings ?? 0,
                'quality_avg' => round($restaurant->avg_quality ?? 0, 1),
                'delivery_avg' => round($restaurant->avg_delivery ?? 0, 1),
                'value_avg' => round($restaurant->avg_value ?? 0, 1),
                'breakdown' => [
                    5 => ServiceRating::where('service_provider_id', $id)
                        ->where('overall_rating', '>=', 4.5)->count(),
                    4 => ServiceRating::where('service_provider_id', $id)
                        ->whereBetween('overall_rating', [3.5, 4.49])->count(),
                    3 => ServiceRating::where('service_provider_id', $id)
                        ->whereBetween('overall_rating', [2.5, 3.49])->count(),
                    2 => ServiceRating::where('service_provider_id', $id)
                        ->whereBetween('overall_rating', [1.5, 2.49])->count(),
                    1 => ServiceRating::where('service_provider_id', $id)
                        ->where('overall_rating', '<', 1.5)->count()
                ]
            ];
            
            return response()->json([
                'success' => true,
                'ratings' => $ratings->map(function ($rating) {
                    return [
                        'id' => $rating->id,
                        'user_name' => $rating->user->name,
                        'user_avatar' => $rating->user->avatar_url,
                        'overall_rating' => $rating->overall_rating,
                        'quality_rating' => $rating->quality_rating,
                        'delivery_rating' => $rating->delivery_rating,
                        'value_rating' => $rating->value_rating,
                        'comment' => $rating->comment,
                        'created_at' => $rating->created_at,
                        'created_at_formatted' => $rating->created_at->diffForHumans()
                    ];
                }),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $ratings->currentPage(),
                    'last_page' => $ratings->lastPage(),
                    'total' => $ratings->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ratings'
            ], 500);
        }
    }
    
    /**
     * Get nearby restaurants based on location
     */
    public function getNearbyRestaurants(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'radius' => 'nullable|numeric|min:1|max:50'
            ]);
            
            $latitude = $request->latitude;
            $longitude = $request->longitude;
            $radius = $request->get('radius', 10);
            
            $haversine = "(6371 * acos(cos(radians($latitude)) 
                        * cos(radians(latitude)) 
                        * cos(radians(longitude) - radians($longitude)) 
                        + sin(radians($latitude)) 
                        * sin(radians(latitude))))";
            
            $restaurants = ServiceProvider::where('service_type', 'FOOD')
                ->where('status', 'ACTIVE')
                ->select('service_providers.*')
                ->selectRaw("{$haversine} AS distance")
                ->having('distance', '<=', $radius)
                ->orderBy('distance')
                ->with(['foodConfig'])
                ->limit(50)
                ->get()
                ->map(function ($restaurant) {
                    return [
                        'id' => $restaurant->id,
                        'business_name' => $restaurant->business_name,
                        'latitude' => $restaurant->latitude,
                        'longitude' => $restaurant->longitude,
                        'distance' => round($restaurant->distance, 2),
                        'address' => $restaurant->address,
                        'rating' => round($restaurant->rating ?? 0, 1),
                        'is_open' => $this->isRestaurantOpen($restaurant)
                    ];
                });
            
            return response()->json([
                'success' => true,
                'restaurants' => $restaurants,
                'center' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting nearby restaurants: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get nearby restaurants'
            ], 500);
        }
    }
    
    /**
     * Reverse geocode coordinates to address
     */
    public function reverseGeocode(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);
            
            $lat = $request->latitude;
            $lng = $request->longitude;
            
            // Using OpenStreetMap Nominatim (free, no API key required)
            $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Your App Name/1.0');
            $response = curl_exec($ch);
            curl_close($ch);
            
            $data = json_decode($response, true);
            
            $address = $data['display_name'] ?? 'Unknown location';
            
            // Extract address components
            $addressComponents = $data['address'] ?? [];
            $city = $addressComponents['city'] ?? 
                    $addressComponents['town'] ?? 
                    $addressComponents['village'] ?? 
                    $addressComponents['state'] ?? '';
            
            return response()->json([
                'success' => true,
                'address' => $address,
                'city' => $city,
                'display_name' => $data['display_name'] ?? null,
                'raw' => $addressComponents
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Reverse geocoding error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get address'
            ], 500);
        }
    }
    
    /**
     * Search locations
     */
    public function searchLocations(Request $request)
    {
        try {
            $request->validate([
                'query' => 'required|string|min:3'
            ]);
            
            $query = urlencode($request->query);
            $url = "https://nominatim.openstreetmap.org/search?format=json&q={$query}&limit=10&addressdetails=1";
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Your App Name/1.0');
            $response = curl_exec($ch);
            curl_close($ch);
            
            $results = json_decode($response, true);
            
            $formattedResults = array_map(function($result) {
                return [
                    'place_id' => $result['place_id'],
                    'display_name' => $result['display_name'],
                    'lat' => $result['lat'],
                    'lon' => $result['lon'],
                    'type' => $result['type'],
                    'class' => $result['class']
                ];
            }, $results);
            
            return response()->json([
                'success' => true,
                'results' => $formattedResults
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Location search error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to search locations'
            ], 500);
        }
    }
    
    /**
     * Get user's saved addresses
     */
    public function getUserAddresses()
    {
        try {
            $user = Auth::user();
            $addresses = UserAddress::where('user_id', $user->id)
                ->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return response()->json([
                'success' => true,
                'addresses' => $addresses->map(function ($address) {
                    return [
                        'id' => $address->id,
                        'address_type' => $address->address_type,
                        'address_line1' => $address->address_line1,
                        'address_line2' => $address->address_line2,
                        'city' => $address->city,
                        'state' => $address->state,
                        'postal_code' => $address->postal_code,
                        'country' => $address->country,
                        'full_address' => $address->address_line1 . 
                            ($address->address_line2 ? ', ' . $address->address_line2 : '') . 
                            ', ' . $address->city . ', ' . $address->state . 
                            ($address->postal_code ? ' - ' . $address->postal_code : ''),
                        'latitude' => $address->latitude,
                        'longitude' => $address->longitude,
                        'is_default' => $address->is_default
                    ];
                })
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error getting addresses: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to get addresses'
            ], 500);
        }
    }
    
    /**
     * Save new address
     */
    public function saveAddress(Request $request)
    {
        try {
            $request->validate([
                'address_line1' => 'required|string|max:255',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'address_type' => 'nullable|in:HOME,WORK,OTHER',
                'is_default' => 'boolean'
            ]);
            
            $user = Auth::user();
            
            DB::beginTransaction();
            
            // If this is default, unset other defaults
            if ($request->is_default) {
                UserAddress::where('user_id', $user->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
            }
            
            $address = UserAddress::create([
                'user_id' => $user->id,
                'address_type' => $request->address_type ?? 'HOME',
                'address_line1' => $request->address_line1,
                'address_line2' => $request->address_line2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country ?? 'Bangladesh',
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => $request->is_default ?? false
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Address saved successfully',
                'address' => $address
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving address: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to save address'
            ], 500);
        }
    }
    
    /**
     * Place an order
     */
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'service_provider_id' => 'required|exists:service_providers,id',
                'meal_type_id' => 'required|exists:meal_types,id',
                'meal_date' => 'required|date|after_or_equal:today',
                'delivery_address' => 'required|string',
                'delivery_latitude' => 'required|numeric',
                'delivery_longitude' => 'required|numeric',
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
            
            // Calculate distance
            $distance = $this->calculateDistance(
                $restaurant->latitude,
                $restaurant->longitude,
                $request->delivery_latitude,
                $request->delivery_longitude
            );
            
            // Check if within service area
            if ($distance > $restaurant->service_radius_km) {
                return response()->json([
                    'success' => false,
                    'message' => 'Delivery address is outside restaurant service area'
                ], 400);
            }
            
            // Calculate order totals
            $baseAmount = 0;
            $commissionAmount = 0;
            $orderItems = [];
            
            foreach ($request->items as $item) {
                $foodItem = FoodItem::find($item['food_item_id']);
                
                // Check availability
                if ($foodItem->daily_quantity && 
                    ($foodItem->sold_today + $item['quantity']) > $foodItem->daily_quantity) {
                    DB::rollBack();
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
            
            // Calculate delivery fee
            $deliveryFee = $distance <= 2 ? 0 : round($distance * 10, 2);
            
            $totalAmount = $baseAmount + $commissionAmount + $deliveryFee;
            
            // Create order
            $order = FoodOrder::create([
                'order_reference' => 'FOOD-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'order_type' => 'PAY_PER_EAT',
                'meal_date' => $request->meal_date,
                'meal_type_id' => $request->meal_type_id,
                'delivery_address' => $request->delivery_address,
                'delivery_latitude' => $request->delivery_latitude,
                'delivery_longitude' => $request->delivery_longitude,
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
                'status' => 'PENDING',
                'paid_at' => null
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully',
                'order_reference' => $order->order_reference,
                'order_id' => $order->id,
                'estimated_delivery_time' => $order->estimated_delivery_time->format('h:i A')
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
    
    /**
     * Get user's orders
     */
    public function getOrders(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = FoodOrder::where('user_id', $user->id)
                ->with(['serviceProvider', 'mealType', 'items.foodItem']);
            
            if ($request->status) {
                $query->where('status', $request->status);
            }
            
            $orders = $query->orderBy('created_at', 'desc')->paginate(10);
            
            return response()->json([
                'success' => true,
                'orders' => $orders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_reference' => $order->order_reference,
                        'business_name' => $order->serviceProvider->business_name,
                        'status' => $order->status,
                        'status_badge' => $this->getStatusBadge($order->status),
                        'meal_type' => $order->mealType->name,
                        'delivery_address' => $order->delivery_address,
                        'distance_km' => $order->distance_km,
                        'base_amount' => number_format($order->base_amount, 2),
                        'delivery_fee' => number_format($order->delivery_fee, 2),
                        'commission_amount' => number_format($order->commission_amount, 2),
                        'total_amount' => number_format($order->total_amount, 2),
                        'created_at' => $order->created_at,
                        'created_at_formatted' => $order->created_at->format('M d, Y h:i A'),
                        'created_at_human' => $order->created_at->diffForHumans(),
                        'estimated_delivery_time' => $order->estimated_delivery_time->format('h:i A'),
                        'actual_delivery_time' => $order->actual_delivery_time ? 
                            $order->actual_delivery_time->format('h:i A') : null,
                        'items' => $order->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->foodItem->name,
                                'quantity' => $item->quantity,
                                'price' => $item->unit_price,
                                'total' => $item->total_price
                            ];
                        })
                    ];
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'total' => $orders->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders'
            ], 500);
        }
    }
    
    /**
     * Cancel an order
     */
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
            
            // Restore item quantities
            foreach ($order->items as $item) {
                $foodItem = FoodItem::find($item->food_item_id);
                if ($foodItem) {
                    $foodItem->sold_today = max(0, $foodItem->sold_today - $item->quantity);
                    $foodItem->save();
                }
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
    
    /**
     * Get user's subscriptions
     */
    public function getSubscriptions(Request $request)
    {
        try {
            $user = Auth::user();
            
            $subscriptions = FoodSubscription::where('user_id', $user->id)
                ->with(['serviceProvider', 'mealType'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            return response()->json([
                'success' => true,
                'subscriptions' => $subscriptions->map(function ($subscription) {
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
                        'status_badge' => $this->getSubscriptionStatusBadge($subscription->status),
                        'meal_type' => $subscription->mealType->name,
                        'delivery_time' => Carbon::parse($subscription->delivery_time)->format('h:i A'),
                        'delivery_days' => $selectedDays,
                        'delivery_days_text' => implode(', ', $selectedDays),
                        'start_date' => $subscription->start_date->format('Y-m-d'),
                        'end_date' => $subscription->end_date->format('Y-m-d'),
                        'start_date_formatted' => $subscription->start_date->format('M d, Y'),
                        'end_date_formatted' => $subscription->end_date->format('M d, Y'),
                        'daily_price' => number_format($subscription->daily_price, 2),
                        'total_price' => number_format($subscription->total_price, 2),
                        'discount_amount' => number_format($subscription->discount_amount, 2),
                        'days_remaining' => Carbon::now()->diffInDays($subscription->end_date, false)
                    ];
                }),
                'pagination' => [
                    'current_page' => $subscriptions->currentPage(),
                    'last_page' => $subscriptions->lastPage(),
                    'total' => $subscriptions->total()
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error loading subscriptions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load subscriptions'
            ], 500);
        }
    }
    
    /**
     * Create a subscription
     */
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
                $bitValue = 1 << $dayOfWeek;
                
                if ($request->delivery_days & $bitValue) {
                    $deliveryDaysCount++;
                }
                
                $currentDate->addDay();
            }
            
            // Calculate total price
            $totalPrice = $dailyPrice * $deliveryDaysCount;
            
            // Apply subscription discount
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
                'commission_amount' => $finalPrice * 0.1,
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
    
    /**
     * Pause a subscription
     */
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
    
    /**
     * Resume a subscription
     */
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
    
    /**
     * Cancel a subscription
     */
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
            
            // Refund payment for remaining days (pro-rated)
            $payment = Payment::where('payable_type', 'FOOD_SUBSCRIPTION')
                ->where('payable_id', $subscription->id)
                ->first();
            
            if ($payment && $payment->status === 'COMPLETED') {
                // Calculate refund amount based on remaining days
                $totalDays = Carbon::parse($subscription->start_date)->diffInDays($subscription->end_date) + 1;
                $usedDays = Carbon::parse($subscription->start_date)->diffInDays(now());
                $remainingDays = max(0, $totalDays - $usedDays);
                
                if ($remainingDays > 0) {
                    $refundAmount = ($payment->amount / $totalDays) * $remainingDays;
                    // Process refund (implementation depends on payment gateway)
                }
            }
            
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
    
    /**
     * Mark review as helpful
     */
    public function markHelpful($id)
    {
        try {
            $user = Auth::user();
            $rating = ServiceRating::findOrFail($id);
            
            // You can implement a helpful votes table here
            // For now, just return success
            
            return response()->json([
                'success' => true,
                'message' => 'Thank you for your feedback'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Error marking helpful: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark as helpful'
            ], 500);
        }
    }
    
    /**
     * Helper Methods
     */
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
    
    private function isRestaurantOpen($restaurant)
    {
        if (!$restaurant->foodConfig) {
            return true;
        }
        
        $currentTime = now()->format('H:i:s');
        return $currentTime >= $restaurant->foodConfig->opening_time && 
               $currentTime <= $restaurant->foodConfig->closing_time;
    }
    
    private function getStatusBadge($status)
    {
        $badges = [
            'PENDING' => ['bg-yellow-100', 'text-yellow-800', 'Pending'],
            'ACCEPTED' => ['bg-blue-100', 'text-blue-800', 'Accepted'],
            'PREPARING' => ['bg-purple-100', 'text-purple-800', 'Preparing'],
            'OUT_FOR_DELIVERY' => ['bg-indigo-100', 'text-indigo-800', 'Out for Delivery'],
            'DELIVERED' => ['bg-green-100', 'text-green-800', 'Delivered'],
            'CANCELLED' => ['bg-red-100', 'text-red-800', 'Cancelled']
        ];
        
        return $badges[$status] ?? ['bg-gray-100', 'text-gray-800', $status];
    }
    
    private function getSubscriptionStatusBadge($status)
    {
        $badges = [
            'ACTIVE' => ['bg-green-100', 'text-green-800', 'Active'],
            'PAUSED' => ['bg-yellow-100', 'text-yellow-800', 'Paused'],
            'CANCELLED' => ['bg-red-100', 'text-red-800', 'Cancelled'],
            'COMPLETED' => ['bg-blue-100', 'text-blue-800', 'Completed']
        ];
        
        return $badges[$status] ?? ['bg-gray-100', 'text-gray-800', $status];
    }
}