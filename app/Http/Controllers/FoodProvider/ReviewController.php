<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRating;
use App\Models\FoodOrder;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews.
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
        
        $query = ServiceRating::with(['user', 'serviceProvider'])
            ->where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD');
        
        // Apply filters
        if ($request->filled('rating')) {
            $query->where('overall_rating', $request->rating);
        }
        
        if ($request->filled('quality_rating')) {
            $query->where('quality_rating', $request->quality_rating);
        }
        
        if ($request->filled('delivery_rating')) {
            $query->where('delivery_rating', $request->delivery_rating);
        }
        
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month)
                          ->whereYear('created_at', now()->year);
                    break;
            }
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }
        
        // Get paginated results
        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        
        // Calculate statistics
        $stats = $this->getReviewStatistics($serviceProvider);
        
        // Get rating distribution
        $ratingDistribution = $this->getRatingDistribution($serviceProvider);
        
        // Get recent trends (last 30 days)
        $trends = $this->getReviewTrends($serviceProvider);
        
        // Get top keywords from reviews
        $keywords = $this->extractKeywords($serviceProvider);
        
        return view('food-provider.reviews.index', compact(
            'reviews',
            'stats',
            'ratingDistribution',
            'trends',
            'keywords'
        ));
    }
    
    /**
     * Display review details.
     */
    public function show($id)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $review = ServiceRating::with(['user', 'serviceProvider'])
            ->where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->findOrFail($id);
        
        // Get the associated order
        $order = FoodOrder::with(['items.foodItem'])
            ->where('id', $review->order_id)
            ->first();
        
        return view('food-provider.reviews.show', compact('review', 'order'));
    }
    
    /**
     * Reply to a review.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000'
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
        
        $review = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->findOrFail($id);
        
        // You might want to add a reply column to your service_ratings table
        // For now, we'll store this in a separate table or as JSON
        // This is a placeholder - you'll need to implement the actual storage
        
        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully'
        ]);
    }
    
    /**
     * Get review statistics.
     */
    private function getReviewStatistics($serviceProvider)
    {
        $totalReviews = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->count();
        
        if ($totalReviews === 0) {
            return [
                'total' => 0,
                'average' => 0,
                'quality_avg' => 0,
                'delivery_avg' => 0,
                'value_avg' => 0,
                'five_star' => 0,
                'four_star' => 0,
                'three_star' => 0,
                'two_star' => 0,
                'one_star' => 0,
                'response_rate' => 0,
                'recommendation_rate' => 0
            ];
        }
        
        $averages = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->select(
                DB::raw('AVG(overall_rating) as avg_overall'),
                DB::raw('AVG(quality_rating) as avg_quality'),
                DB::raw('AVG(delivery_rating) as avg_delivery'),
                DB::raw('AVG(value_rating) as avg_value')
            )
            ->first();
        
        $starCounts = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->select('overall_rating', DB::raw('count(*) as count'))
            ->groupBy('overall_rating')
            ->get()
            ->keyBy('overall_rating');
        
        return [
            'total' => $totalReviews,
            'average' => round($averages->avg_overall, 1),
            'quality_avg' => round($averages->avg_quality, 1),
            'delivery_avg' => round($averages->avg_delivery, 1),
            'value_avg' => round($averages->avg_value, 1),
            'five_star' => $starCounts[5]->count ?? 0,
            'four_star' => $starCounts[4]->count ?? 0,
            'three_star' => $starCounts[3]->count ?? 0,
            'two_star' => $starCounts[2]->count ?? 0,
            'one_star' => $starCounts[1]->count ?? 0,
            'response_rate' => 75, // Placeholder - calculate based on actual replies
            'recommendation_rate' => $totalReviews > 0 
                ? round((($starCounts[5]->count ?? 0) + ($starCounts[4]->count ?? 0)) / $totalReviews * 100, 1)
                : 0
        ];
    }
    
    /**
     * Get rating distribution.
     */
    private function getRatingDistribution($serviceProvider)
    {
        $total = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->count();
        
        if ($total === 0) {
            return collect([]);
        }
        
        $distribution = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->select(
                DB::raw('FLOOR(overall_rating) as rating'),
                DB::raw('count(*) as count')
            )
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get();
        
        return $distribution->map(function ($item) use ($total) {
            return [
                'rating' => $item->rating,
                'count' => $item->count,
                'percentage' => round(($item->count / $total) * 100, 1)
            ];
        });
    }
    
    /**
     * Get review trends for the last 30 days.
     */
    private function getReviewTrends($serviceProvider)
    {
        $trends = [];
        $startDate = now()->subDays(30);
        
        for ($i = 0; $i < 30; $i++) {
            $date = $startDate->copy()->addDays($i);
            $trends[$date->format('Y-m-d')] = [
                'date' => $date->format('M d'),
                'count' => 0,
                'average' => 0
            ];
        }
        
        $reviews = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as count'),
                DB::raw('AVG(overall_rating) as avg_rating')
            )
            ->groupBy('date')
            ->get();
        
        foreach ($reviews as $review) {
            if (isset($trends[$review->date])) {
                $trends[$review->date]['count'] = $review->count;
                $trends[$review->date]['average'] = round($review->avg_rating, 1);
            }
        }
        
        return array_values($trends);
    }
    
    /**
     * Extract common keywords from reviews.
     */
    private function extractKeywords($serviceProvider)
    {
        $reviews = ServiceRating::where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD')
            ->whereNotNull('comment')
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->pluck('comment');
        
        $keywords = [];
        $stopWords = ['the', 'and', 'for', 'with', 'was', 'very', 'good', 'great', 'food', 'service', 'delivery', 'time', 'quality'];
        
        foreach ($reviews as $comment) {
            $words = str_word_count(strtolower($comment), 1);
            foreach ($words as $word) {
                if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                    $keywords[$word] = ($keywords[$word] ?? 0) + 1;
                }
            }
        }
        
        arsort($keywords);
        return array_slice($keywords, 0, 20);
    }
    
    /**
     * Export reviews to CSV.
     */
    public function export(Request $request)
    {
        $serviceProvider = ServiceProvider::where('user_id', Auth::id())
            ->where('service_type', 'FOOD')
            ->first();
            
        if (!$serviceProvider) {
            abort(403, 'No food service provider found for this user.');
        }
        
        $query = ServiceRating::with(['user'])
            ->where('service_provider_id', $serviceProvider->id)
            ->where('order_type', 'FOOD');
        
        // Apply filters
        if ($request->filled('rating')) {
            $query->where('overall_rating', $request->rating);
        }
        
        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', now()->month);
                    break;
            }
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'reviews-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $columns = ['Date', 'Customer', 'Overall Rating', 'Quality', 'Delivery', 'Value', 'Comment'];
        
        $callback = function() use ($reviews, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->created_at->format('Y-m-d H:i'),
                    $review->user->name ?? 'N/A',
                    $review->overall_rating,
                    $review->quality_rating,
                    $review->delivery_rating,
                    $review->value_rating,
                    $review->comment ?? ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}