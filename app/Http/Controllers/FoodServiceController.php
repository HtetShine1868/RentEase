<?php

namespace App\Http\Controllers;

use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use App\Models\ServiceRating;
use App\Traits\Notifiable; // ADD THIS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FoodServiceController extends Controller
{
    use Notifiable; // ADD THIS

    /**
     * Show the rating form for a delivered order
     */
    public function show(FoodOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'DELIVERED') {
            return redirect()->route('food.orders')
                ->with('error', 'You can only rate delivered orders.');
        }

        if ($order->rating()->exists()) {
            return redirect()->route('food.orders')
                ->with('info', 'You have already rated this order. You can edit your review.');
        }

        $order->load(['serviceProvider', 'items.foodItem', 'mealType']);

        return view('food.ratings.create', compact('order'));
    }

    /**
     * Show the edit form for an existing rating
     */
    public function edit(FoodOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $rating = $order->rating;

        if (!$rating) {
            return redirect()->route('food.rate.show', $order);
        }

        $order->load(['serviceProvider', 'items.foodItem', 'mealType']);

        return view('food.ratings.edit', compact('order', 'rating'));
    }

    /**
     * Store a new rating
     */
    public function store(Request $request, FoodOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$order->canBeRated()) {
            return back()->with('error', 'This order cannot be rated.');
        }

        $validated = $request->validate([
            'quality_rating' => 'required|integer|min:1|max:5',
            'delivery_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|max:2048'
        ]);

        DB::beginTransaction();

        try {
            // Calculate overall rating
            $overallRating = (
                $validated['quality_rating'] + 
                $validated['delivery_rating'] + 
                $validated['value_rating']
            ) / 3;

            // Handle image uploads
            $imagePaths = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('ratings/food', 'public');
                    $imagePaths[] = Storage::url($path);
                }
            }

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
                'comment' => $validated['comment'] ?? null,
                'images' => !empty($imagePaths) ? $imagePaths : null
            ]);

            // Update provider's rating stats
            $order->serviceProvider->updateRatingStats();

            DB::commit();

            // ============ ADD NOTIFICATIONS ============
                        
            \App\Models\Notification::create([
                'user_id' => Auth::id(),
                'type' => 'ORDER',
                'title' => 'Review Submitted',
                'message' => "Your review for order #{$order->order_reference} has been submitted. Thank you for your feedback!",
                'related_entity_type' => 'food_order',
                'related_entity_id' => $order->id,
                'is_read' => false,
                'created_at' => now()
            ]);

            // Notify the food provider about new review
            \App\Models\Notification::create([
                'user_id' => $order->serviceProvider->user_id,
                'type' => 'ORDER',
                'title' => 'New Review Received',
                'message' => "You received a new " . round($overallRating, 1) . "-star review from " . Auth::user()->name,
                'related_entity_type' => 'food_order',
                'related_entity_id' => $order->id,
                'is_read' => false,
                'created_at' => now()
            ]);

            return redirect()->route('food.orders')
                ->with('success', 'Thank you for your rating! Your feedback helps us improve.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to save rating. Please try again.');
        }
    }

    /**
     * Update an existing rating
     */
    public function update(Request $request, FoodOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $rating = $order->rating;

        if (!$rating) {
            return redirect()->route('food.rate.show', $order);
        }

        $validated = $request->validate([
            'quality_rating' => 'required|integer|min:1|max:5',
            'delivery_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        DB::beginTransaction();

        try {
            $overallRating = (
                $validated['quality_rating'] + 
                $validated['delivery_rating'] + 
                $validated['value_rating']
            ) / 3;

            $rating->update([
                'quality_rating' => $validated['quality_rating'],
                'delivery_rating' => $validated['delivery_rating'],
                'value_rating' => $validated['value_rating'],
                'overall_rating' => round($overallRating, 2),
                'comment' => $validated['comment'] ?? null
            ]);

            // Update provider's rating stats
            $order->serviceProvider->updateRatingStats();

            DB::commit();

            // ============ ADD NOTIFICATIONS ============
            
            $this->createNotification(
                Auth::id(),
                'ORDER',
                'Review Updated',
                "Your review for order #{$order->order_reference} has been updated.",
                'food_order',
                $order->id
            );

            return redirect()->route('food.orders')
                ->with('success', 'Your review has been updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to update rating.');
        }
    }

    /**
     * Delete a rating
     */
    public function destroy(FoodOrder $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $rating = $order->rating;

        if (!$rating) {
            return back()->with('error', 'Rating not found.');
        }

        DB::beginTransaction();

        try {
            // Delete images
            if ($rating->images) {
                foreach ($rating->images as $image) {
                    $path = str_replace('/storage/', '', $image);
                    Storage::disk('public')->delete($path);
                }
            }

            $rating->delete();

            // Update provider's rating stats
            $order->serviceProvider->updateRatingStats();

            DB::commit();

            // ============ ADD NOTIFICATIONS ============
            
            $this->createNotification(
                Auth::id(),
                'ORDER',
                'Review Deleted',
                "Your review for order #{$order->order_reference} has been deleted.",
                'food_order',
                $order->id
            );

            return redirect()->route('food.orders')
                ->with('success', 'Your review has been deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting rating: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete rating.');
        }
    }

    /**
     * Get restaurant ratings (API)
     */
    public function getRestaurantRatings($id)
    {
        try {
            $provider = ServiceProvider::findOrFail($id);

            $ratings = ServiceRating::where('service_provider_id', $id)
                ->where('order_type', 'FOOD')
                ->where('is_approved', true)
                ->with('user:id,name,avatar_url')
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            $stats = [
                'average' => round($provider->serviceRatings()->where('is_approved', true)->avg('overall_rating') ?? 0, 1),
                'total' => $provider->serviceRatings()->where('is_approved', true)->count(),
                'quality_avg' => round($provider->serviceRatings()->where('is_approved', true)->avg('quality_rating') ?? 0, 1),
                'delivery_avg' => round($provider->serviceRatings()->where('is_approved', true)->avg('delivery_rating') ?? 0, 1),
                'value_avg' => round($provider->serviceRatings()->where('is_approved', true)->avg('value_rating') ?? 0, 1),
                'breakdown' => $provider->rating_breakdown['distribution'] ?? [
                    '5' => 0, '4' => 0, '3' => 0, '2' => 0, '1' => 0
                ]
            ];

            return response()->json([
                'success' => true,
                'ratings' => $ratings->items(),
                'stats' => $stats,
                'pagination' => [
                    'current_page' => $ratings->currentPage(),
                    'last_page' => $ratings->lastPage(),
                    'total' => $ratings->total()
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error loading restaurant ratings: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load ratings'
            ], 500);
        }
    }

    /**
     * Get user's ratings
     */
    public function myRatings()
    {
        $ratings = ServiceRating::where('user_id', Auth::id())
            ->where('order_type', 'FOOD')
            ->with(['serviceProvider', 'order.items.foodItem'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('food.ratings.my-ratings', compact('ratings'));
    }

    /**
     * Mark rating as helpful
     */
    public function markHelpful(ServiceRating $rating)
    {
        $rating->increment('helpful_count');

        // ============ ADD NOTIFICATIONS ============
        
        // Notify the review owner that someone found their review helpful
        if ($rating->user_id !== Auth::id()) {
            $this->createNotification(
                $rating->user_id,
                'SYSTEM',
                'Review Helpful',
                "Someone found your review helpful!",
                'food_rating',
                $rating->id
            );
        }

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback!'
        ]);
    }
}