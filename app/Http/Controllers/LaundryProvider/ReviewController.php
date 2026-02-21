<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRating;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    protected $provider;
    
    /**
     * Get the authenticated laundry provider
     */
    private function getProvider()
    {
        if (!$this->provider) {
            $this->provider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'LAUNDRY')
                ->firstOrFail();
        }
        return $this->provider;
    }
    
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $provider = $this->getProvider();
        
        $query = ServiceRating::with('user')
            ->where('service_provider_id', $provider->id)
            ->where('order_type', 'LAUNDRY');
        
        // Filter by rating
        if ($request->has('rating') && $request->rating != 'all') {
            $query->where('overall_rating', '>=', $request->rating)
                  ->where('overall_rating', '<', $request->rating + 1);
        }
        
        // Filter by date
        if ($request->has('date_range') && $request->date_range != 'all') {
            $now = now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', $now->today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
            }
        }
        
        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }
        
        // Sort
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);
        
        $reviews = $query->paginate(15)->withQueryString();
        
        // Get statistics
        $stats = [
            'average_rating' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('overall_rating') ?? 0,
                
            'total_reviews' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->count(),
                
            'average_quality' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('quality_rating') ?? 0,
                
            'average_delivery' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('delivery_rating') ?? 0,
                
            'average_value' => ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->avg('value_rating') ?? 0
        ];
        
        // Rating distribution
        $distribution = [];
        for ($i = 1; $i <= 5; $i++) {
            $distribution[$i] = ServiceRating::where('service_provider_id', $provider->id)
                ->where('order_type', 'LAUNDRY')
                ->whereBetween('overall_rating', [$i, $i + 0.9])
                ->count();
        }
        
        return view('laundry-provider.reviews.index', compact('reviews', 'stats', 'distribution'));
    }
    
    /**
     * Display the specified review
     */
    public function show($id)
    {
        $provider = $this->getProvider();
        
        $review = ServiceRating::with(['user'])
            ->where('service_provider_id', $provider->id)
            ->where('order_type', 'LAUNDRY')
            ->findOrFail($id);
        
        return view('laundry-provider.reviews.show', compact('review'));
    }
    
    /**
     * Export reviews to CSV
     */
    public function exportReviews(Request $request)
    {
        $provider = $this->getProvider();
        
        $query = ServiceRating::with('user')
            ->where('service_provider_id', $provider->id)
            ->where('order_type', 'LAUNDRY');
        
        // Apply filters
        if ($request->has('date_range') && $request->date_range != 'all') {
            $now = now();
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', $now->today());
                    break;
                case 'week':
                    $query->whereBetween('created_at', [$now->startOfWeek(), $now->endOfWeek()]);
                    break;
                case 'month':
                    $query->whereMonth('created_at', $now->month)
                          ->whereYear('created_at', $now->year);
                    break;
            }
        }
        
        if ($request->has('min_rating') && $request->min_rating > 0) {
            $query->where('overall_rating', '>=', $request->min_rating);
        }
        
        $reviews = $query->orderBy('created_at', 'desc')->get();
        
        $filename = "reviews-export-" . now()->format('Y-m-d') . ".csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($reviews) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'Date',
                'Customer Name',
                'Overall Rating',
                'Quality Rating',
                'Delivery Rating',
                'Value Rating',
                'Comment'
            ]);
            
            // Data rows
            foreach ($reviews as $review) {
                fputcsv($file, [
                    $review->created_at->format('Y-m-d H:i'),
                    $review->user->name,
                    number_format($review->overall_rating, 1),
                    $review->quality_rating,
                    $review->delivery_rating,
                    $review->value_rating,
                    $review->comment
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}