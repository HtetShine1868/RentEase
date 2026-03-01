<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\PropertyRating;
use App\Models\Property;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews for owner's properties
     */
    public function index(Request $request)
    {
        $owner = Auth::user();
        
        // Get all property IDs owned by this owner
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id')->toArray();
        
        // If no properties, return empty result
        if (empty($propertyIds)) {
            $reviews = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            $properties = collect([]);
            $stats = $this->getEmptyStats();
            
            return view('owner.pages.reviews.index', compact('reviews', 'properties', 'stats'));
        }
        
        // Build query for property ratings
        $query = PropertyRating::with([
                'user:id,name,email,avatar_url',
                'property:id,name,type,city,area'
            ])
            ->whereIn('property_id', $propertyIds);
        
        // Apply filters
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        
        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            $query->whereBetween('overall_rating', [$rating, $rating + 0.9]);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhereHas('property', function($propQuery) use ($search) {
                    $propQuery->where('name', 'like', "%{$search}%");
                })
                ->orWhere('comment', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);
        
        // Get paginated reviews
        $reviews = $query->paginate(15)->withQueryString();
        
        // Calculate statistics
        $stats = $this->calculateStats($propertyIds);
        
        // Get all properties for filter dropdown
        $properties = Property::where('owner_id', $owner->id)
            ->select('id', 'name', 'type', 'city')
            ->orderBy('name')
            ->get();
        
        return view('owner.pages.reviews.index', compact('reviews', 'properties', 'stats'));
    }
    
    /**
     * Display review details
     */
    public function show($id)
    {
        $owner = Auth::user();
        
        $review = PropertyRating::with([
                'user:id,name,email,phone,avatar_url',
                'property:id,name,type,city,area,address,owner_id',
                'booking'
            ])
            ->findOrFail($id);
        
        // Verify ownership - Check if review's property belongs to this owner
        if ($review->property->owner_id !== $owner->id) {
            abort(403, 'Unauthorized access to this review');
        }
        
        return view('owner.pages.reviews.show', compact('review'));
    }
    
    /**
     * Calculate review statistics
     */
    private function calculateStats($propertyIds)
    {
        // Overall average rating
        $avgRating = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('overall_rating') ?? 0;
        
        // Total reviews
        $totalReviews = PropertyRating::whereIn('property_id', $propertyIds)->count();
        
        // Average ratings by category
        $avgCleanliness = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('cleanliness_rating') ?? 0;
        
        $avgLocation = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('location_rating') ?? 0;
        
        $avgValue = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('value_rating') ?? 0;
        
        $avgService = PropertyRating::whereIn('property_id', $propertyIds)
            ->avg('service_rating') ?? 0;
        
        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = PropertyRating::whereIn('property_id', $propertyIds)
                ->whereBetween('overall_rating', [$i, $i + 0.9])
                ->count();
        }
        
        // Calculate percentages for distribution
        $distributionPercentages = [];
        for ($i = 1; $i <= 5; $i++) {
            $distributionPercentages[$i] = $totalReviews > 0 
                ? round(($distribution[$i] / $totalReviews) * 100, 1) 
                : 0;
        }
        
        // Reviews by month (last 6 months)
        $monthlyTrends = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $count = PropertyRating::whereIn('property_id', $propertyIds)
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            
            $monthlyTrends[] = [
                'month' => $month->format('M Y'),
                'count' => $count
            ];
        }
        
        // Get top rated property
        $topRatedProperty = Property::whereIn('id', $propertyIds)
            ->withCount(['ratings as avg_rating' => function($query) {
                $query->select(DB::raw('coalesce(avg(overall_rating),0)'));
            }])
            ->orderBy('avg_rating', 'desc')
            ->first();
        
        // Get most reviewed property
        $mostReviewedProperty = Property::whereIn('id', $propertyIds)
            ->withCount('ratings')
            ->orderBy('ratings_count', 'desc')
            ->first();
        
        // Recent reviews count (last 30 days)
        $recentReviewsCount = PropertyRating::whereIn('property_id', $propertyIds)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();
        
        return [
            'avg_rating' => round($avgRating, 1),
            'total_reviews' => $totalReviews,
            'avg_cleanliness' => round($avgCleanliness, 1),
            'avg_location' => round($avgLocation, 1),
            'avg_value' => round($avgValue, 1),
            'avg_service' => round($avgService, 1),
            'distribution' => $distribution,
            'distribution_percentages' => $distributionPercentages,
            'monthly_trends' => $monthlyTrends,
            'top_rated_property' => $topRatedProperty ? $topRatedProperty->name : 'N/A',
            'top_rating' => $topRatedProperty ? round($topRatedProperty->avg_rating, 1) : 0,
            'most_reviewed_property' => $mostReviewedProperty ? $mostReviewedProperty->name : 'N/A',
            'most_reviewed_count' => $mostReviewedProperty ? $mostReviewedProperty->ratings_count : 0,
            'recent_reviews_count' => $recentReviewsCount
        ];
    }
    
    /**
     * Get empty stats array for when no properties exist
     */
    private function getEmptyStats()
    {
        return [
            'avg_rating' => 0,
            'total_reviews' => 0,
            'avg_cleanliness' => 0,
            'avg_location' => 0,
            'avg_value' => 0,
            'avg_service' => 0,
            'distribution' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'distribution_percentages' => [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0],
            'monthly_trends' => [
                ['month' => Carbon::now()->subMonths(5)->format('M Y'), 'count' => 0],
                ['month' => Carbon::now()->subMonths(4)->format('M Y'), 'count' => 0],
                ['month' => Carbon::now()->subMonths(3)->format('M Y'), 'count' => 0],
                ['month' => Carbon::now()->subMonths(2)->format('M Y'), 'count' => 0],
                ['month' => Carbon::now()->subMonths(1)->format('M Y'), 'count' => 0],
                ['month' => Carbon::now()->format('M Y'), 'count' => 0],
            ],
            'top_rated_property' => 'N/A',
            'top_rating' => 0,
            'most_reviewed_property' => 'N/A',
            'most_reviewed_count' => 0,
            'recent_reviews_count' => 0
        ];
    }
    
    /**
     * Export reviews to CSV
     */
    public function export(Request $request)
    {
        $owner = Auth::user();
        
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id')->toArray();
        
        if (empty($propertyIds)) {
            return redirect()->back()->with('error', 'No properties found to export.');
        }
        
        $query = PropertyRating::with(['user', 'property'])
            ->whereIn('property_id', $propertyIds);
        
        // Apply filters same as index
        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }
        
        if ($request->filled('rating')) {
            $rating = (int) $request->rating;
            $query->whereBetween('overall_rating', [$rating, $rating + 0.9]);
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->date_from));
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->date_to));
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->get();
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="reviews_' . date('Y-m-d_H-i-s') . '.csv"',
        ];
        
        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8 (for Excel compatibility)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Property Name',
                'Property Type',
                'Property City',
                'Guest Name',
                'Guest Email',
                'Overall Rating',
                'Cleanliness',
                'Location',
                'Value',
                'Service',
                'Comment',
                'Review Date'
            ]);
            
            // Data rows
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->id,
                    $review->property->name,
                    $review->property->type,
                    $review->property->city,
                    $review->user->name,
                    $review->user->email,
                    $review->overall_rating,
                    $review->cleanliness_rating,
                    $review->location_rating,
                    $review->value_rating,
                    $review->service_rating,
                    $review->comment ?? '',
                    $review->created_at->format('Y-m-d H:i:s')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Get review statistics for dashboard widget
     */
    public function getStats()
    {
        $owner = Auth::user();
        
        $propertyIds = Property::where('owner_id', $owner->id)->pluck('id')->toArray();
        
        if (empty($propertyIds)) {
            return response()->json([
                'success' => true,
                'data' => $this->getEmptyStats()
            ]);
        }
        
        $stats = $this->calculateStats($propertyIds);
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
    
    /**
     * Get reviews for a specific property
     */
    public function propertyReviews($propertyId)
    {
        $owner = Auth::user();
        
        // Verify property ownership
        $property = Property::where('id', $propertyId)
            ->where('owner_id', $owner->id)
            ->firstOrFail();
        
        $reviews = PropertyRating::with('user')
            ->where('property_id', $propertyId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('owner.pages.reviews.property', compact('reviews', 'property'));
    }
}