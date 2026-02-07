<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PropertyRating;
use App\Models\Complaint;
use App\Models\Property;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MyRentalController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get user's bookings
        $bookings = Booking::where('user_id', $user->id)
            ->with(['property.primaryImage', 'payments'])
            ->latest()
            ->paginate(10);
        
        // Get current active rentals (confirmed or checked-in)
        $currentRentals = Booking::where('user_id', $user->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->where('check_out', '>=', Carbon::today())
            ->with(['property.primaryImage'])
            ->get();
        
        // Get total bookings count
        $totalBookings = Booking::where('user_id', $user->id)->count();
        
        // Get active bookings count
        $activeBookings = $currentRentals->count();
        
        // Calculate total spent - FIXED
        $totalSpent = Payment::where('user_id', $user->id)
            ->where('payable_type', 'Booking') // Or 'booking' if using morph map
            ->where('status', 'COMPLETED')
            ->sum('amount');
        
        // Get user's reviews
        $reviews = PropertyRating::where('user_id', $user->id)
            ->with(['property.primaryImage'])
            ->latest()
            ->get();
        
        // Get pending reviews (bookings that ended but not reviewed)
        $pendingReviews = Booking::where('user_id', $user->id)
            ->where('status', 'CHECKED_OUT')
            ->where('check_out', '<=', Carbon::today()->subDays(7))
            ->whereDoesntHave('property.reviews', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();
        
        // Get user's complaints
        $complaints = Complaint::where('user_id', $user->id)
            ->latest()
            ->get();
        
        // Get properties user has booked (for complaint dropdown)
        $userProperties = Property::whereHas('bookings', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->get();
        
        return view('rental.index', compact(
            'bookings',
            'currentRentals',
            'totalBookings',
            'activeBookings',
            'totalSpent',
            'pendingReviews',
            'reviews',
            'complaints',
            'userProperties'
        ));
    }
    public function storeBookingReview(Request $request, Booking $booking)
{
    // Check if user owns this booking
    if ($booking->user_id !== Auth::id()) {
        abort(403);
    }
    
    // Check if booking can be reviewed
    if (!$booking->canBeReviewed()) {
        return back()->with('error', 'This booking cannot be reviewed.');
    }
    
    $request->validate([
        'cleanliness_rating' => 'required|integer|min:1|max:5',
        'location_rating' => 'required|integer|min:1|max:5',
        'value_rating' => 'required|integer|min:1|max:5',
        'service_rating' => 'required|integer|min:1|max:5',
        'comment' => 'nullable|string|max:1000',
    ]);
    
    // Calculate overall rating
    $overallRating = (
        $request->cleanliness_rating + 
        $request->location_rating + 
        $request->value_rating + 
        $request->service_rating
    ) / 4;
    
    $review = PropertyRating::create([
        'user_id' => Auth::id(),
        'property_id' => $booking->property_id,
        'booking_id' => $booking->id,
        'cleanliness_rating' => $request->cleanliness_rating,
        'location_rating' => $request->location_rating,
        'value_rating' => $request->value_rating,
        'service_rating' => $request->service_rating,
        'overall_rating' => $overallRating,
        'comment' => $request->comment,
        'is_approved' => true,
    ]);
    
    return back()->with('success', 'Thank you for your review!');
}
}