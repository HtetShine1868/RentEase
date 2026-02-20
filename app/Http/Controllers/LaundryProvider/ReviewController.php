<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\ServiceRating;
use App\Models\LaundryOrder;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReviewController extends Controller
{
    protected $serviceProvider;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->serviceProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'LAUNDRY')
                ->first();
                
            if (!$this->serviceProvider) {
                abort(403, 'No laundry service provider found for this user.');
            }
            
            return $next($request);
        });
    }

    /**
     * Display all reviews
     */
    public function index(Request $request)
    {
        $query = ServiceRating::with('user')
            ->where('service_provider_id', $this->serviceProvider->id)
            ->where('order_type', 'LAUNDRY');

        // Apply filters
        if ($request->filled('rating')) {
            $query->where('overall_rating', $request->rating);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('comment', 'like', "%{$search}%");
        }

        $reviews = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->count(),
            'average' => round(ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->avg('overall_rating') ?? 0, 1),
            'quality_avg' => round(ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->avg('quality_rating') ?? 0, 1),
            'delivery_avg' => round(ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->avg('delivery_rating') ?? 0, 1),
            'value_avg' => round(ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->avg('value_rating') ?? 0, 1),
            'five_star' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->where('overall_rating', 5)->count(),
            'four_star' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->where('overall_rating', 4)->count(),
            'three_star' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->where('overall_rating', 3)->count(),
            'two_star' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->where('overall_rating', 2)->count(),
            'one_star' => ServiceRating::where('service_provider_id', $this->serviceProvider->id)
                ->where('order_type', 'LAUNDRY')->where('overall_rating', 1)->count(),
        ];

        // Get rating distribution
        $distribution = [];
        for ($i = 5; $i >= 1; $i--) {
            $count = $stats[$i == 5 ? 'five_star' : ($i == 4 ? 'four_star' : ($i == 3 ? 'three_star' : ($i == 2 ? 'two_star' : 'one_star')))];
            $distribution[] = [
                'rating' => $i,
                'count' => $count,
                'percentage' => $stats['total'] > 0 ? round(($count / $stats['total']) * 100, 1) : 0
            ];
        }

        return view('laundry-provider.reviews.index', compact('reviews', 'stats', 'distribution'));
    }

    /**
     * Show single review
     */
    public function show($id)
    {
        $review = ServiceRating::with(['user', 'serviceProvider'])
            ->where('service_provider_id', $this->serviceProvider->id)
            ->where('order_type', 'LAUNDRY')
            ->findOrFail($id);

        $order = LaundryOrder::with('items.laundryItem')
            ->where('id', $review->order_id)
            ->first();

        return view('laundry-provider.reviews.show', compact('review', 'order'));
    }

    /**
     * Reply to review
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000'
        ]);

        $review = ServiceRating::where('service_provider_id', $this->serviceProvider->id)
            ->where('order_type', 'LAUNDRY')
            ->findOrFail($id);

        $review->provider_reply = $request->reply;
        $review->replied_at = Carbon::now();
        $review->save();

        // Notify user about reply
        $this->createNotification(
            $review->user_id,
            'SYSTEM',
            'Response to Your Review',
            "Laundry provider replied to your review: " . substr($request->reply, 0, 50) . "...",
            'laundry_review',
            $review->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully'
        ]);
    }

    /**
     * Export reviews to CSV
     */
    public function export(Request $request)
    {
        $query = ServiceRating::with('user')
            ->where('service_provider_id', $this->serviceProvider->id)
            ->where('order_type', 'LAUNDRY');

        if ($request->filled('rating')) {
            $query->where('overall_rating', $request->rating);
        }

        if ($request->filled('date_range')) {
            switch ($request->date_range) {
                case 'today':
                    $query->whereDate('created_at', Carbon::today());
                    break;
                case 'this_week':
                    $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                    break;
                case 'this_month':
                    $query->whereMonth('created_at', Carbon::now()->month);
                    break;
            }
        }

        $reviews = $query->get();

        $filename = 'laundry-reviews-' . Carbon::now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $columns = ['Date', 'Customer', 'Overall', 'Quality', 'Delivery', 'Value', 'Comment', 'Your Reply'];

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
                    $review->comment ?? '',
                    $review->provider_reply ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}