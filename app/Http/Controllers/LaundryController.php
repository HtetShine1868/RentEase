<?php

namespace App\Http\Controllers;

use App\Models\LaundryOrder;
use App\Models\ServiceProvider;
use App\Models\MealType; // You might want to create a separate model for laundry item types
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LaundryController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return redirect()->route('login');
            }
            
            // Get user's default address for location-based services
            $defaultAddress = null;
            try {
                $defaultAddress = $user->addresses()->where('is_default', true)->first();
            } catch (\Exception $e) {
                Log::warning('Error loading user addresses: ' . $e->getMessage());
            }
            
            // Get initial laundry providers
            $initialProviders = collect();
            try {
                $initialProviders = ServiceProvider::where('service_type', 'LAUNDRY')
                    ->where('status', 'ACTIVE')
                    ->with(['laundryConfig'])
                    ->withCount('laundryOrders as total_orders')
                    ->withAvg('serviceRatings as rating', 'overall_rating')
                    ->limit(6)
                    ->get();
                
                // Calculate distance for each provider if user has address
                if ($defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude) {
                    foreach ($initialProviders as $provider) {
                        if ($provider->latitude && $provider->longitude) {
                            $provider->distance_km = $this->calculateDistance(
                                $defaultAddress->latitude,
                                $defaultAddress->longitude,
                                $provider->latitude,
                                $provider->longitude
                            );
                            
                            // Check if within service radius
                            $provider->in_service_area = $provider->distance_km <= ($provider->service_radius_km ?? 10);
                        } else {
                            $provider->distance_km = 5.0;
                            $provider->in_service_area = true;
                        }
                    }
                    
                    // Sort by distance
                    $initialProviders = $initialProviders->sortBy('distance_km')->values();
                }
            } catch (\Exception $e) {
                Log::error('Error loading laundry providers: ' . $e->getMessage());
            }
            
            // Get recent orders
            $recentOrders = collect();
            try {
                $recentOrders = LaundryOrder::where('user_id', $user->id)
                    ->with(['serviceProvider', 'items.laundryItem'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function ($order) {
                        return [
                            'id' => $order->id,
                            'order_reference' => $order->order_reference,
                            'business_name' => $order->serviceProvider?->business_name ?? 'Unknown',
                            'status' => $order->status,
                            'service_mode' => $order->service_mode,
                            'total_amount' => $order->total_amount,
                            'created_at' => $order->created_at,
                            'created_at_formatted' => $order->created_at ? $order->created_at->diffForHumans() : 'N/A',
                            'expected_return_date' => $order->expected_return_date ? $order->expected_return_date->format('M d, Y') : 'N/A',
                            'items' => $order->items->map(function ($item) {
                                return [
                                    'id' => $item->id,
                                    'name' => $item->laundryItem?->item_name ?? 'Unknown',
                                    'quantity' => $item->quantity,
                                    'price' => $item->unit_price
                                ];
                            })
                        ];
                    });
            } catch (\Exception $e) {
                Log::error('Error loading laundry orders: ' . $e->getMessage());
            }
            
            // Get laundry item types (you can create a separate table or enum)
            $itemTypes = [
                ['id' => 1, 'name' => 'Clothing', 'icon' => 'fa-tshirt'],
                ['id' => 2, 'name' => 'Bedding', 'icon' => 'fa-bed'],
                ['id' => 3, 'name' => 'Curtain', 'icon' => 'fa-window'],
                ['id' => 4, 'name' => 'Other', 'icon' => 'fa-tag'],
            ];
            
            // Calculate stats
            $stats = [
                'totalOrders' => 0,
                'pendingOrders' => 0,
                'completedOrders' => 0
            ];
            
            try {
                $stats = [
                    'totalOrders' => LaundryOrder::where('user_id', $user->id)->count(),
                    'pendingOrders' => LaundryOrder::where('user_id', $user->id)
                        ->whereIn('status', ['PENDING', 'PICKUP_SCHEDULED', 'PICKED_UP', 'IN_PROGRESS'])
                        ->count(),
                    'completedOrders' => LaundryOrder::where('user_id', $user->id)
                        ->where('status', 'DELIVERED')
                        ->count()
                ];
            } catch (\Exception $e) {
                Log::error('Error loading stats: ' . $e->getMessage());
            }
            
            return view('laundry.index', compact(
                'initialProviders',
                'recentOrders',
                'itemTypes',
                'stats',
                'defaultAddress'
            ));
            
        } catch (\Exception $e) {
            Log::error('Error in LaundryController@index: ' . $e->getMessage());
            return view('laundry.index', [
                'initialProviders' => collect(),
                'recentOrders' => collect(),
                'itemTypes' => [
                    ['id' => 1, 'name' => 'Clothing', 'icon' => 'fa-tshirt'],
                    ['id' => 2, 'name' => 'Bedding', 'icon' => 'fa-bed'],
                    ['id' => 3, 'name' => 'Curtain', 'icon' => 'fa-window'],
                    ['id' => 4, 'name' => 'Other', 'icon' => 'fa-tag'],
                ],
                'stats' => [
                    'totalOrders' => 0,
                    'pendingOrders' => 0,
                    'completedOrders' => 0
                ],
                'defaultAddress' => null
            ]);
        }
    }
    
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return 5.0;
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