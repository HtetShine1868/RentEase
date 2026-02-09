<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyRating;
use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    public function search(Request $request)
    {
        $query = Property::query()->with(['primaryImage', 'amenities', 'reviews']);
        
        // Only show active properties for non-owners
        if (!Auth::check() || (!Auth::user()->hasRole('OWNER') && !Auth::user()->hasRole('SUPERADMIN'))) {
            $query->active();
        }
        
        // Search by keyword
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('area', 'like', "%{$search}%");
            });
        }
        
        // Filter by property type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        
        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }
        
        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }
        
        // Filter by area
        if ($request->filled('area')) {
            $query->where('area', 'like', "%{$request->area}%");
        }
        
        // Filter by gender policy
        if ($request->filled('gender_policy')) {
            $query->where('gender_policy', $request->gender_policy);
        }
        
        // Filter by minimum rating
        if ($request->filled('min_rating')) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->havingRaw('AVG(overall_rating) >= ?', [$request->min_rating]);
            });
        }
        
        // Sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('base_price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('base_price', 'desc');
                break;
            case 'rating':
                $query->withAvg('reviews', 'overall_rating')->orderByDesc('reviews_avg_overall_rating');
                break;
            default:
                $query->latest();
                break;
        }
        
        // Get distinct cities for filter dropdown
        $cities = Property::active()->distinct()->pluck('city')->sort();
        
        $properties = $query->paginate(12)->withQueryString();
        
        return view('rental.search', compact('properties', 'cities'));
    }
    
    public function show(Property $property)
    {
        // Check if property is active or user is owner/admin
        if ($property->status !== 'ACTIVE' && !Auth::user()->hasRole('OWNER') && !Auth::user()->hasRole('SUPERADMIN')) {
            abort(404, 'Property not found');
        }
        
        $property->load([
            'images' => function($q) {
                $q->orderBy('is_primary', 'desc')->orderBy('display_order');
            },
            'amenities',
            'reviews.user:id,name,avatar_url',
            'owner:id,name,phone',
            'rooms' => function($q) {
                $q->where('status', 'AVAILABLE');
            }
        ]);
        
        // Calculate average rating
        $averageRating = $property->reviews->avg('overall_rating') ?? 0;
        
        // Related properties (same city, different property)
        $relatedProperties = Property::where('city', $property->city)
            ->where('id', '!=', $property->id)
            ->where('status', 'ACTIVE')
            ->with(['primaryImage'])
            ->limit(4)
            ->get();
        
        return view('rental.property-details', compact('property', 'relatedProperties', 'averageRating'));
    }
    
    public function rentApartment(Property $property)
    {
        if ($property->type !== 'APARTMENT') {
            abort(404);
        }
        
        // Check if property is available for rent
        if ($property->status !== 'ACTIVE') {
            abort(404, 'This property is not available for rent');
        }
        
        $property->load(['images', 'amenities', 'owner']);
        
        return view('rental.rent-apartment', compact('property'));
    }
    
    public function bookRoom(Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
            abort(404);
        }
        
        // Check if room is available
        if ($room->status !== 'AVAILABLE') {
            abort(404, 'This room is not available');
        }
        
        $property->load(['images', 'amenities', 'owner']);
        
        // Get other available rooms in same property
        $otherRooms = $property->rooms()
            ->where('id', '!=', $room->id)
            ->where('status', 'AVAILABLE')
            ->limit(3)
            ->get();
        
        return view('rental.room-booking', compact('property', 'room', 'otherRooms'));
    }
    
    public function checkAvailability(Request $request, Property $property)
    {
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
        ]);
        
        if ($property->type === 'APARTMENT') {
            $conflictingBookings = $property->bookings()
                ->where(function($query) use ($request) {
                    $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                          ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                          ->orWhere(function($q) use ($request) {
                              $q->where('check_in', '<', $request->check_in)
                                ->where('check_out', '>', $request->check_out);
                          });
                })
                ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
                ->exists();
            
            return response()->json([
                'available' => !$conflictingBookings,
                'message' => $conflictingBookings ? 'Property is booked' : 'Property available'
            ]);
        }
        
        if ($property->type === 'HOSTEL') {
            $availableRooms = $property->rooms()
                ->where('status', 'AVAILABLE')
                ->whereDoesntHave('bookings', function($query) use ($request) {
                    $query->where(function($query) use ($request) {
                        $query->whereBetween('check_in', [$request->check_in, $request->check_out])
                              ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                              ->orWhere(function($q) use ($request) {
                                  $q->where('check_in', '<', $request->check_in)
                                    ->where('check_out', '>', $request->check_out);
                              });
                    })
                    ->whereIn('status', ['CONFIRMED', 'CHECKED_IN']);
                })
                ->get();
            
            return response()->json([
                'available' => $availableRooms->count() > 0,
                'available_rooms' => $availableRooms,
                'message' => $availableRooms->count() > 0 ? "{$availableRooms->count()} rooms available" : 'No rooms available'
            ]);
        }
    }
    
  /**
     * Show user's rental dashboard (Main Method)
     */
    public function index()
    {
        $user = Auth::user();
        
        // Current bookings (CHECKED_IN or CONFIRMED with future check_out)
        $currentBookings = Booking::with(['property', 'room', 'payments'])
            ->where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'CHECKED_IN')
                      ->orWhere(function($q) {
                          $q->where('status', 'CONFIRMED')
                            ->whereDate('check_out', '>=', now());
                      });
            })
            ->whereDate('check_out', '>=', now())
            ->orderBy('check_in', 'desc')
            ->get()
            ->filter(function($booking) {
                return Carbon::parse($booking->check_out)->isFuture();
            });
        
        // Upcoming bookings (CONFIRMED with future check_in)
        $upcomingBookings = Booking::with(['property', 'room'])
            ->where('user_id', $user->id)
            ->where('status', 'CONFIRMED')
            ->whereDate('check_in', '>', now())
            ->orderBy('check_in', 'asc')
            ->get();
        
        // Past bookings
        $pastBookings = Booking::with(['property', 'room', 'payments'])
            ->where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'CHECKED_OUT')
                      ->orWhere('status', 'CANCELLED')
                      ->orWhere(function($q) {
                          $q->whereIn('status', ['CHECKED_IN', 'CONFIRMED'])
                            ->whereDate('check_out', '<', now());
                      });
            })
            ->orderBy('check_out', 'desc')
            ->paginate(10);
        
        // Bookings that can be reviewed (checked out within last 30 days and not reviewed)
        $reviewableBookings = Booking::with(['property'])
            ->where('user_id', $user->id)
            ->where('status', 'CHECKED_OUT')
            ->where('check_out', '>=', now()->subDays(30))
            ->whereDoesntHave('propertyRating', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->orderBy('check_out', 'desc')
            ->get();
        
        // Recent complaints
        $complaints = Complaint::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('rental.index', compact(
            'currentBookings',
            'upcomingBookings',
            'pastBookings',
            'reviewableBookings',
            'complaints'
        ));
    }
    
    /**
     * Check-in to a booking
     */
 // In RentalController.php - update checkInBooking method

public function checkInBooking(Booking $booking)
{
    if ($booking->user_id !== Auth::id()) {
        abort(403);
    }
    
    if ($booking->status !== 'CONFIRMED') {
        return redirect()->back()->with('error', 'Only confirmed bookings can be checked in.');
    }
    
    // FIXED: Use date comparison instead of datetime
    if (now()->toDateString() < Carbon::parse($booking->check_in)->toDateString()) {
        return redirect()->back()->with('error', 'Check-in is only allowed on or after the check-in date.');
    }
    
    $booking->update([
        'status' => 'CHECKED_IN',
        'updated_at' => now()
    ]);
    
    return redirect()->back()->with('success', 'Successfully checked in!');
}
    
    /**
     * Check-out from a booking
     */
    public function checkOutBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'CHECKED_IN') {
            return redirect()->back()->with('error', 'Only checked-in bookings can be checked out.');
        }
        
        $booking->update([
            'status' => 'CHECKED_OUT',
            'updated_at' => now()
        ]);
        
        // If it's a room booking, make room available
        if ($booking->room_id) {
            $booking->room()->update(['status' => 'AVAILABLE']);
        }
        
        return redirect()->back()->with('success', 'Successfully checked out!');
    }
    
    /**
     * Extend booking stay
     */
    public function extendBooking(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'new_check_out' => 'required|date|after:today'
        ]);
        
        $booking = Booking::where('user_id', Auth::id())->findOrFail($request->booking_id);
        
        // Check if booking is active
        if (!in_array($booking->status, ['CHECKED_IN', 'CONFIRMED']) || 
            Carbon::parse($booking->check_out)->isPast()) {
            return redirect()->back()->with('error', 'Cannot extend inactive or expired booking.');
        }
        
        $newCheckOut = Carbon::parse($request->new_check_out);
        $oldCheckOut = Carbon::parse($booking->check_out);
        
        if ($newCheckOut->lte($oldCheckOut)) {
            return redirect()->back()->with('error', 'New check-out date must be after current check-out date.');
        }
        
        // Calculate extension days and cost
        $extensionDays = $oldCheckOut->diffInDays($newCheckOut);
        $dailyPrice = $booking->room ? $booking->room->total_price : $booking->property->total_price;
        $extensionCost = $extensionDays * $dailyPrice;
        
        // Update booking
        $booking->update([
            'check_out' => $newCheckOut,
            'updated_at' => now()
        ]);
        
        // Create payment record for extension
        Payment::create([
            'payment_reference' => 'EXT-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'payable_type' => 'BOOKING',
            'payable_id' => $booking->id,
            'amount' => $extensionCost,
            'commission_amount' => 0, // No commission on extensions
            'payment_method' => 'BANK_TRANSFER',
            'status' => 'PENDING',
            'created_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Booking extended successfully! Please complete the payment for the extension.');
    }
    
    /**
     * Submit a review for a property
     */
    public function submitReview(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'property_id' => 'required|exists:properties,id',
            'cleanliness_rating' => 'required|integer|between:1,5',
            'location_rating' => 'required|integer|between:1,5',
            'value_rating' => 'required|integer|between:1,5',
            'service_rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000'
        ]);
        
        $booking = Booking::where('user_id', Auth::id())
            ->where('status', 'CHECKED_OUT')
            ->findOrFail($request->booking_id);
        
        // Check if already reviewed
        $existingReview = PropertyRating::where('booking_id', $booking->id)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($existingReview) {
            return redirect()->back()->with('error', 'You have already reviewed this property.');
        }
        
        // Calculate overall rating
        $overallRating = (
            $request->cleanliness_rating + 
            $request->location_rating + 
            $request->value_rating + 
            $request->service_rating
        ) / 4;
        
        // Create review
        PropertyRating::create([
            'user_id' => Auth::id(),
            'property_id' => $request->property_id,
            'booking_id' => $booking->id,
            'cleanliness_rating' => $request->cleanliness_rating,
            'location_rating' => $request->location_rating,
            'value_rating' => $request->value_rating,
            'service_rating' => $request->service_rating,
            'overall_rating' => $overallRating,
            'comment' => $request->comment,
            'is_approved' => true,
            'created_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Thank you for your review!');
    }
    
    /**
     * Submit a complaint
     */
    public function submitComplaint(Request $request)
    {
        $request->validate([
            'booking_id' => 'nullable|exists:bookings,id',
            'title' => 'required|string|max:200',
            'description' => 'required|string',
            'complaint_type' => 'required|in:PROPERTY,FOOD_SERVICE,LAUNDRY_SERVICE,USER,SYSTEM',
            'related_type' => 'required|in:PROPERTY,SERVICE_PROVIDER,USER',
            'related_id' => 'required|integer',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT'
        ]);
        
        // Generate unique complaint reference
        $complaintReference = 'COMP-' . strtoupper(uniqid());
        
        Complaint::create([
            'complaint_reference' => $complaintReference,
            'user_id' => Auth::id(),
            'complaint_type' => $request->complaint_type,
            'related_id' => $request->related_id,
            'related_type' => $request->related_type,
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'OPEN',
            'created_at' => now()
        ]);
        
        return redirect()->back()->with('success', 'Complaint submitted successfully. We will get back to you soon.');
    }
    
    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load([
            'property.images',
            'room',
            'payments',
            'propertyRating' => function($query) {
                $query->where('user_id', Auth::id());
            }
        ]);
        
        return view('rental.booking-details', compact('booking'));
    }
    
    /**
     * Cancel a booking
     */
    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!in_array($booking->status, ['CONFIRMED', 'PENDING'])) {
            return redirect()->back()->with('error', 'Only pending or confirmed bookings can be cancelled.');
        }
        
        $booking->update([
            'status' => 'CANCELLED',
            'updated_at' => now()
        ]);
        
        // Make room available if it's a hostel booking
        if ($booking->room_id) {
            $booking->room()->update(['status' => 'AVAILABLE']);
        }
        
        // Refund logic would go here
        // Payment::where('payable_id', $booking->id)
        //        ->where('payable_type', 'BOOKING')
        //        ->update(['status' => 'REFUNDED']);
        
        return redirect()->route('rental.index')->with('success', 'Booking cancelled successfully.');
    }
    
    /**
     * Show booking invoice
     */
    public function showInvoice(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load(['property', 'room', 'payments']);
        
        return view('rental.invoice', compact('booking'));
    }
    
    /**
     * Show all complaints
     */
    public function complaints()
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('rental.partials.complaint-modal', compact('complaints'));
    }
    
    /**
     * Show single complaint
     */
    public function showComplaint(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }
        
        return view('rental.complaint-details', compact('complaint'));
    }
}