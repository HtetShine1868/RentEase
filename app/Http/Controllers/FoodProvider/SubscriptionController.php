<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use App\Models\FoodSubscription;
use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscriptions.
     */
    public function index(Request $request)
    {
        // Get the authenticated user's service provider record
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $query = FoodSubscription::with(['user', 'mealType'])
            ->where('service_provider_id', $serviceProvider->id);
        
        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('meal_type')) {
            $query->where('meal_type_id', $request->meal_type);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                })->orWhere('id', 'like', "%{$search}%");
            });
        }
        
        // Get active subscriptions count
        $activeCount = FoodSubscription::where('service_provider_id', $serviceProvider->id)
            ->where('status', 'ACTIVE')
            ->count();
        
        // Get today's deliveries count
        $todayDeliveries = FoodSubscription::where('service_provider_id', $serviceProvider->id)
            ->where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->whereRaw('delivery_days & ? > 0', [pow(2, today()->dayOfWeek)])
            ->count();
        
        // Get monthly revenue from subscriptions
        $monthlyRevenue = FoodOrder::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'SUBSCRIPTION_MEAL')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total_amount');
        
        $subscriptions = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Get meal types for filter
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        return view('food-provider.subscriptions.index', compact(
            'subscriptions',
            'activeCount',
            'todayDeliveries',
            'monthlyRevenue',
            'mealTypes'
        ));
    }
    
    /**
     * Display today's delivery schedule.
     */
    public function todayDeliveries(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $today = today();
        $dayOfWeek = $today->dayOfWeek;
        $dayBit = pow(2, $dayOfWeek);
        
        // Get active subscriptions that should be delivered today
        $subscriptions = FoodSubscription::with(['user', 'mealType'])
            ->where('service_provider_id', $serviceProvider->id)
            ->where('status', 'ACTIVE')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->whereRaw('delivery_days & ? > 0', [$dayBit])
            ->orderBy('delivery_time')
            ->get();
        
        // Check if orders have already been created for today
        $existingOrders = FoodOrder::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'SUBSCRIPTION_MEAL')
            ->whereDate('meal_date', $today)
            ->pluck('subscription_id')
            ->toArray();
        
        // Get meal types
        $mealTypes = DB::table('meal_types')->orderBy('display_order')->get();
        
        return view('food-provider.subscriptions.today', compact(
            'subscriptions',
            'existingOrders',
            'mealTypes',
            'today'
        ));
    }
    
    /**
     * Generate orders for today's subscriptions.
     */
    public function generateTodayOrders(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json([
                'success' => false,
                'message' => 'Service provider not found'
            ], 403);
        }
        
        $today = today();
        $dayOfWeek = $today->dayOfWeek;
        $dayBit = pow(2, $dayOfWeek);
        
        DB::beginTransaction();
        
        try {
            // Get all active subscriptions that should be delivered today
            $subscriptions = FoodSubscription::with(['user', 'mealType'])
                ->where('service_provider_id', $serviceProvider->id)
                ->where('status', 'ACTIVE')
                ->whereDate('start_date', '<=', $today)
                ->whereDate('end_date', '>=', $today)
                ->whereRaw('delivery_days & ? > 0', [$dayBit])
                ->get();
            
            $createdCount = 0;
            $skippedCount = 0;
            
            foreach ($subscriptions as $subscription) {
                // Check if order already exists for today
                $existingOrder = FoodOrder::where('service_provider_id', $serviceProvider->id)
                    ->where('subscription_id', $subscription->id)
                    ->whereDate('meal_date', $today)
                    ->exists();
                
                if ($existingOrder) {
                    $skippedCount++;
                    continue;
                }
                
                // Get user's default address or subscription address
                $user = $subscription->user;
                $defaultAddress = $user->addresses()->where('is_default', true)->first();
                
                if (!$defaultAddress) {
                    $skippedCount++;
                    continue;
                }
                
                // Calculate distance (you may want to implement actual distance calculation)
                $distance = $this->calculateDistance(
                    $serviceProvider->latitude,
                    $serviceProvider->longitude,
                    $defaultAddress->latitude,
                    $defaultAddress->longitude
                );
                
                // Create order for this subscription
                $order = new FoodOrder();
                $order->order_reference = 'SUB-' . strtoupper(uniqid());
                $order->user_id = $subscription->user_id;
                $order->service_provider_id = $serviceProvider->id;
                $order->subscription_id = $subscription->id;
                $order->order_type = 'SUBSCRIPTION_MEAL';
                $order->meal_date = $today;
                $order->meal_type_id = $subscription->meal_type_id;
                $order->delivery_address = $defaultAddress->address_line1 . ', ' . $defaultAddress->city . ', ' . $defaultAddress->state;
                $order->delivery_latitude = $defaultAddress->latitude ?? $serviceProvider->latitude;
                $order->delivery_longitude = $defaultAddress->longitude ?? $serviceProvider->longitude;
                $order->distance_km = $distance;
                $order->delivery_instructions = null;
                $order->status = 'PENDING';
                $order->estimated_delivery_time = now()->setTimeFromTimeString($subscription->delivery_time);
                $order->base_amount = $subscription->daily_price;
                $order->delivery_fee = 0; // Subscription includes delivery
                
                // Calculate commission
                $commissionRate = DB::table('commission_configs')
                    ->where('service_type', 'FOOD')
                    ->value('rate') ?? 8.00;
                $order->commission_amount = ($order->base_amount * $commissionRate) / 100;
                $order->total_amount = $order->base_amount;
                
                $order->save();
                
                $createdCount++;
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Created {$createdCount} new orders. Skipped {$skippedCount} (already exist or missing address)."
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Error generating orders: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Display the specified subscription.
     */
    public function show($id)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $subscription = FoodSubscription::with(['user', 'mealType', 'user.addresses'])
            ->where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);
        
        // Get order history for this subscription
        $orders = FoodOrder::where('subscription_id', $subscription->id)
            ->orderBy('meal_date', 'desc')
            ->get();
        
        // Calculate delivery days pattern
        $deliveryDays = $this->getDeliveryDaysArray($subscription->delivery_days);
        
        return view('food-provider.subscriptions.show', compact('subscription', 'orders', 'deliveryDays'));
    }
    
    /**
     * Update subscription status.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:ACTIVE,PAUSED,CANCELLED,COMPLETED'
        ]);
        
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json([
                'success' => false,
                'message' => 'Service provider not found'
            ], 403);
        }
        
        $subscription = FoodSubscription::where('service_provider_id', $serviceProvider->id)
            ->findOrFail($id);
        
        $oldStatus = $subscription->status;
        $subscription->status = $request->status;
        $subscription->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Subscription status updated successfully'
        ]);
    }
    
    /**
     * Get delivery statistics.
     */
    public function statistics(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            return response()->json(['error' => 'Service provider not found'], 403);
        }
        
        $stats = [
            'active' => FoodSubscription::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'ACTIVE')
                ->count(),
            
            'paused' => FoodSubscription::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'PAUSED')
                ->count(),
            
            'cancelled' => FoodSubscription::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'CANCELLED')
                ->count(),
            
            'completed' => FoodSubscription::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'COMPLETED')
                ->count(),
        ];
        
        // Weekly delivery distribution
        $weeklyDistribution = [];
        for ($i = 0; $i < 7; $i++) {
            $dayBit = pow(2, $i);
            $count = FoodSubscription::where('service_provider_id', $serviceProvider->id)
                ->where('status', 'ACTIVE')
                ->whereRaw('delivery_days & ? > 0', [$dayBit])
                ->count();
            
            $weeklyDistribution[date('l', strtotime("Sunday +{$i} days"))] = $count;
        }
        
        // Meal type distribution
        $mealTypeDistribution = FoodSubscription::where('service_provider_id', $serviceProvider->id)
            ->where('status', 'ACTIVE')
            ->select('meal_type_id', DB::raw('count(*) as count'))
            ->groupBy('meal_type_id')
            ->with('mealType')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->mealType->name => $item->count];
            });
        
        return response()->json([
            'success' => true,
            'stats' => $stats,
            'weekly_distribution' => $weeklyDistribution,
            'meal_type_distribution' => $mealTypeDistribution
        ]);
    }
    
    /**
     * Calculate distance between two coordinates.
     */
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
        
        return round($earthRadius * $c, 2);
    }
    
    /**
     * Convert delivery days bitmask to array of day names.
     */
    private function getDeliveryDaysArray($bitmask)
    {
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $deliveryDays = [];
        
        for ($i = 0; $i < 7; $i++) {
            if ($bitmask & pow(2, $i)) {
                $deliveryDays[] = $days[$i];
            }
        }
        
        return $deliveryDays;
    }
}