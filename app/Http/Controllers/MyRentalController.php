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
}