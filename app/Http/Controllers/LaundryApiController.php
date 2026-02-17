<?php

namespace App\Http\Controllers;

use App\Models\LaundryItem;
use App\Models\LaundryOrder;
use App\Models\LaundryOrderItem;
use App\Models\Payment;
use App\Models\ServiceProvider;
use App\Models\UserAddress;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LaundryApiController extends Controller
{
    public function getProviders(Request $request)
    {
        try {
            $user = Auth::user();
            $defaultAddress = null;
            
            if ($user) {
                $defaultAddress = $user->addresses()->where('is_default', true)->first();
            }
            
            $query = ServiceProvider::where('service_type', 'LAUNDRY')
                ->where('status', 'ACTIVE')
                ->with(['laundryConfig'])
                ->withCount('laundryOrders as total_orders')
                ->withAvg('serviceRatings as rating', 'overall_rating');
            
            // Search filter
            if ($request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('business_name', 'like', '%' . $request->search . '%')
                      ->orWhereHas('laundryItems', function ($itemQuery) use ($request) {
                          $itemQuery->where('item_name', 'like', '%' . $request->search . '%');
                      });
                });
            }
            
            // Item type filter
            if ($request->item_type) {
                $query->whereHas('laundryItems', function ($q) use ($request) {
                    $q->where('item_type', $request->item_type);
                });
            }
            
            $providers = $query->get();
            
            // Add distance and other info
            foreach ($providers as $provider) {
                if ($defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude) {
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
                
                // Add turnaround times from config
                $provider->normal_turnaround_hours = $provider->laundryConfig->normal_turnaround_hours ?? 120;
                $provider->rush_turnaround_hours = $provider->laundryConfig->rush_turnaround_hours ?? 48;
                $provider->pickup_fee = $provider->laundryConfig->pickup_fee ?? 0;
            }
            
            // Apply sorting
            switch ($request->sort) {
                case 'distance':
                    $providers = $providers->sortBy('distance_km');
                    break;
                case 'turnaround':
                    $providers = $providers->sortBy('normal_turnaround_hours');
                    break;
                case 'total_orders':
                    $providers = $providers->sortByDesc('total_orders');
                    break;
                case 'rating':
                default:
                    $providers = $providers->sortByDesc('rating');
                    break;
            }
            
            return response()->json([
                'success' => true,
                'providers' => $providers->values()->map(function ($provider) {
                    return [
                        'id' => $provider->id,
                        'business_name' => $provider->business_name,
                        'description' => $provider->description,
                        'rating' => $provider->rating ?? 0,
                        'total_orders' => $provider->total_orders ?? 0,
                        'city' => $provider->city ?? 'Dhaka',
                        'address' => $provider->address ?? '',
                        'distance_km' => $provider->distance_km,
                        'in_service_area' => $provider->in_service_area,
                        'normal_turnaround_hours' => $provider->normal_turnaround_hours,
                        'rush_turnaround_hours' => $provider->rush_turnaround_hours,
                        'pickup_fee' => $provider->pickup_fee,
                        'pickup_start_time' => $provider->laundryConfig->pickup_start_time ?? '09:00',
                        'pickup_end_time' => $provider->laundryConfig->pickup_end_time ?? '18:00',
                        'provides_pickup_service' => $provider->laundryConfig->provides_pickup_service ?? true
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading laundry providers: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load laundry providers'
            ], 500);
        }
    }
    
    public function getProviderItems($id)
    {
        try {
            $provider = ServiceProvider::where('id', $id)
                ->where('service_type', 'LAUNDRY')
                ->where('status', 'ACTIVE')
                ->with(['laundryConfig'])
                ->firstOrFail();
            
            $items = LaundryItem::where('service_provider_id', $id)
                ->orderBy('item_type')
                ->orderBy('item_name')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'name' => $item->item_name,
                        'item_type' => $item->item_type,
                        'base_price' => $item->base_price,
                        'total_price' => $item->total_price,
                        'rush_surcharge_percent' => $item->rush_surcharge_percent
                    ];
                });
            
            // Group items by type
            $groupedItems = $items->groupBy('item_type');
            
            return response()->json([
                'success' => true,
                'provider' => [
                    'id' => $provider->id,
                    'business_name' => $provider->business_name,
                    'description' => $provider->description,
                    'address' => $provider->address,
                    'city' => $provider->city,
                    'normal_turnaround_hours' => $provider->laundryConfig->normal_turnaround_hours ?? 120,
                    'rush_turnaround_hours' => $provider->laundryConfig->rush_turnaround_hours ?? 48,
                    'pickup_fee' => $provider->laundryConfig->pickup_fee ?? 0,
                    'pickup_start_time' => $provider->laundryConfig->pickup_start_time ?? '09:00',
                    'pickup_end_time' => $provider->laundryConfig->pickup_end_time ?? '18:00',
                    'provides_pickup_service' => $provider->laundryConfig->provides_pickup_service ?? true
                ],
                'items' => $items,
                'grouped_items' => $groupedItems
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading provider items: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load laundry items'
            ], 500);
        }
    }
    
    public function placeOrder(Request $request)
    {
        try {
            $request->validate([
                'service_provider_id' => 'required|exists:service_providers,id',
                'service_mode' => 'required|in:NORMAL,RUSH',
                'pickup_address' => 'required|string',
                'pickup_latitude' => 'nullable|numeric',
                'pickup_longitude' => 'nullable|numeric',
                'pickup_time' => 'required|date',
                'pickup_instructions' => 'nullable|string',
                'items' => 'required|array|min:1',
                'items.*.laundry_item_id' => 'required|exists:laundry_items,id',
                'items.*.quantity' => 'required|integer|min:1'
            ]);
            
            $user = Auth::user();
            $provider = ServiceProvider::with('laundryConfig')->find($request->service_provider_id);
            
            DB::beginTransaction();
            
            // Calculate distance
            $distance = 0;
            if ($request->pickup_latitude && $request->pickup_longitude && $provider->latitude && $provider->longitude) {
                $distance = $this->calculateDistance(
                    $provider->latitude,
                    $provider->longitude,
                    $request->pickup_latitude,
                    $request->pickup_longitude
                );
            }
            
            // Calculate order totals
            $baseAmount = 0;
            $rushSurcharge = 0;
            $commissionAmount = 0;
            $orderItems = [];
            
            foreach ($request->items as $item) {
                $laundryItem = LaundryItem::find($item['laundry_item_id']);
                
                $itemBaseTotal = $laundryItem->base_price * $item['quantity'];
                $baseAmount += $itemBaseTotal;
                
                // Calculate rush surcharge if applicable
                if ($request->service_mode === 'RUSH') {
                    $itemRushSurcharge = $itemBaseTotal * ($laundryItem->rush_surcharge_percent / 100);
                    $rushSurcharge += $itemRushSurcharge;
                }
                
                // Calculate commission
                $itemCommission = ($laundryItem->base_price * $laundryItem->commission_rate / 100) * $item['quantity'];
                $commissionAmount += $itemCommission;
                
                $orderItems[] = [
                    'laundry_item_id' => $laundryItem->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $laundryItem->base_price
                ];
            }
            
            // Calculate pickup fee
            $pickupFee = $provider->laundryConfig->pickup_fee ?? 0;
            
            // Calculate total amount
            $totalAmount = $baseAmount + $rushSurcharge + $pickupFee + $commissionAmount;
            
            // Calculate expected return date based on service mode
            $pickupTime = Carbon::parse($request->pickup_time);
            $turnaroundHours = $request->service_mode === 'RUSH' 
                ? ($provider->laundryConfig->rush_turnaround_hours ?? 48)
                : ($provider->laundryConfig->normal_turnaround_hours ?? 120);
            
            $expectedReturnDate = $pickupTime->copy()->addHours($turnaroundHours);
            
            // Create order
            $order = LaundryOrder::create([
                'order_reference' => 'LND-' . strtoupper(Str::random(8)),
                'user_id' => $user->id,
                'service_provider_id' => $request->service_provider_id,
                'service_mode' => $request->service_mode,
                'pickup_address' => $request->pickup_address,
                'pickup_latitude' => $request->pickup_latitude ?? $provider->latitude,
                'pickup_longitude' => $request->pickup_longitude ?? $provider->longitude,
                'distance_km' => $distance,
                'pickup_time' => $pickupTime,
                'pickup_instructions' => $request->pickup_instructions,
                'expected_return_date' => $expectedReturnDate,
                'status' => 'PENDING',
                'base_amount' => $baseAmount,
                'rush_surcharge' => $rushSurcharge,
                'pickup_fee' => $pickupFee,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount
            ]);
            
            // Create order items
            foreach ($orderItems as $item) {
                LaundryOrderItem::create(array_merge($item, ['laundry_order_id' => $order->id]));
            }
            
            // Create payment record
            Payment::create([
                'payment_reference' => 'PAY-' . strtoupper(Str::random(10)),
                'user_id' => $user->id,
                'payable_type' => 'LAUNDRY_ORDER',
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
                'order_reference' => $order->order_reference,
                'expected_return_date' => $expectedReturnDate->format('Y-m-d H:i:s')
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error placing laundry order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function getOrders(Request $request)
    {
        try {
            $user = Auth::user();
            
            $query = LaundryOrder::where('user_id', $user->id)
                ->with(['serviceProvider', 'items.laundryItem']);
            
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
                        'business_name' => $order->serviceProvider->business_name ?? 'Unknown',
                        'status' => $order->status,
                        'service_mode' => $order->service_mode,
                        'pickup_address' => $order->pickup_address,
                        'total_amount' => number_format($order->total_amount, 2),
                        'created_at' => $order->created_at,
                        'created_at_formatted' => $order->created_at->diffForHumans(),
                        'expected_return_date' => $order->expected_return_date->format('M d, Y'),
                        'items' => $order->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'name' => $item->laundryItem->item_name ?? 'Unknown',
                                'quantity' => $item->quantity,
                                'price' => $item->unit_price
                            ];
                        })
                    ];
                })
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading orders: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load orders'
            ], 500);
        }
    }
    
    public function getOrder($id)
    {
        try {
            $user = Auth::user();
            
            $order = LaundryOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['serviceProvider', 'items.laundryItem'])
                ->firstOrFail();
            
            return response()->json([
                'success' => true,
                'order' => [
                    'id' => $order->id,
                    'order_reference' => $order->order_reference,
                    'business_name' => $order->serviceProvider->business_name ?? 'Unknown',
                    'status' => $order->status,
                    'service_mode' => $order->service_mode,
                    'pickup_address' => $order->pickup_address,
                    'total_amount' => number_format($order->total_amount, 2),
                    'base_amount' => number_format($order->base_amount, 2),
                    'rush_surcharge' => number_format($order->rush_surcharge, 2),
                    'pickup_fee' => number_format($order->pickup_fee, 2),
                    'created_at' => $order->created_at,
                    'pickup_time' => $order->pickup_time->format('M d, Y h:i A'),
                    'expected_return_date' => $order->expected_return_date->format('M d, Y'),
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'name' => $item->laundryItem->item_name ?? 'Unknown',
                            'quantity' => $item->quantity,
                            'price' => $item->unit_price
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load order details'
            ], 500);
        }
    }
    
    public function cancelOrder($id)
    {
        try {
            $user = Auth::user();
            $order = LaundryOrder::where('id', $id)
                ->where('user_id', $user->id)
                ->firstOrFail();
            
            if (!in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled at this stage'
                ], 400);
            }
            
            DB::beginTransaction();
            
            $order->status = 'CANCELLED';
            $order->save();
            
            // Refund payment if applicable
            $payment = Payment::where('payable_type', 'LAUNDRY_ORDER')
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
            Log::error('Error cancelling order: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order'
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