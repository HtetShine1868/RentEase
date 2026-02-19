<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use App\Models\ServiceRating;
use Illuminate\Http\Request;
use App\Traits\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FoodRatingController extends Controller
{
    /**
     * Show the rating form for a delivered order
     */
    use Notifiable;
    public function show(FoodOrder $order)
    {
        // Check if order belongs to user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be rated
        if ($order->status !== 'DELIVERED') {
            return redirect()->route('food.orders')
                ->with('error', 'You can only rate delivered orders.');
        }

        // Check if already rated
        if ($order->rating()->exists()) {
            return redirect()->route('food.index')
                ->with('info', 'You have already rated this order.');
        }

        $order->load(['serviceProvider', 'items.foodItem']);

        return view('food.ratings.create', [
            'title' => 'Rate Your Order',
            'order' => $order
        ]);
    }

    /**
     * Store the rating for an order
     */
    public function store(Request $request, FoodOrder $order)
    {
        // Check if order belongs to user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be rated
        if ($order->status !== 'DELIVERED') {
            return back()->with('error', 'You can only rate delivered orders.');
        }

        // Check if already rated
        if ($order->rating()->exists()) {
            return back()->with('error', 'You have already rated this order.');
        }

        $validated = $request->validate([
            'quality_rating' => 'required|integer|min:1|max:5',
            'delivery_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            // Calculate overall rating
            $overallRating = (
                $validated['quality_rating'] + 
                $validated['delivery_rating'] + 
                $validated['value_rating']
            ) / 3;

            // Create rating
            $rating = ServiceRating::create([
                'user_id' => Auth::id(),
                'service_provider_id' => $order->service_provider_id,
                'order_id' => $order->id,
                'order_type' => 'FOOD',
                'quality_rating' => $validated['quality_rating'],
                'delivery_rating' => $validated['delivery_rating'],
                'value_rating' => $validated['value_rating'],
                'overall_rating' => round($overallRating, 2),
                'comment' => $validated['comment'] ?? null
            ]);

            // Update service provider's average rating
            $this->updateProviderRating($order->service_provider_id);

            DB::commit();

            return redirect()->route('food.orders')
                ->with('success', 'Thank you for your rating!');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to save rating. Please try again.');
        }
    }

    /**
     * Get restaurant ratings (for API)
     */
    public function restaurantRatings($id)
    {
        $provider = ServiceProvider::findOrFail($id);
        
        $ratings = ServiceRating::where('service_provider_id', $id)
            ->where('order_type', 'FOOD')
            ->with('user:id,name,avatar_url')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $stats = [
            'average' => $provider->serviceRatings()->avg('overall_rating') ?? 0,
            'total' => $provider->serviceRatings()->count(),
            'quality_avg' => $provider->serviceRatings()->avg('quality_rating') ?? 0,
            'delivery_avg' => $provider->serviceRatings()->avg('delivery_rating') ?? 0,
            'value_avg' => $provider->serviceRatings()->avg('value_rating') ?? 0,
            'breakdown' => [
                '5' => $provider->serviceRatings()->where('overall_rating', '>=', 4.5)->count(),
                '4' => $provider->serviceRatings()->whereBetween('overall_rating', [3.5, 4.49])->count(),
                '3' => $provider->serviceRatings()->whereBetween('overall_rating', [2.5, 3.49])->count(),
                '2' => $provider->serviceRatings()->whereBetween('overall_rating', [1.5, 2.49])->count(),
                '1' => $provider->serviceRatings()->where('overall_rating', '<', 1.5)->count(),
            ]
        ];

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'ratings' => $ratings,
                'stats' => $stats
            ]);
        }

        return view('food.ratings.restaurant', compact('provider', 'ratings', 'stats'));
    }

    /**
     * Update provider's average rating
     */
    private function updateProviderRating($providerId)
    {
        $provider = ServiceProvider::find($providerId);
        if ($provider) {
            $avgRating = $provider->serviceRatings()->avg('overall_rating') ?? 0;
            $totalRatings = $provider->serviceRatings()->count();
            
            $provider->update([
                'rating' => round($avgRating, 2),
                'total_ratings' => $totalRatings
            ]);
        }
    }
}