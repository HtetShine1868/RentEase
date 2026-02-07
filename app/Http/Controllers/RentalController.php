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
    
    public function storeReview(Request $request, Property $property)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id,user_id,' . Auth::id(),
            'cleanliness_rating' => 'required|integer|min:1|max:5',
            'location_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);
        
        $overallRating = (
            $request->cleanliness_rating + 
            $request->location_rating + 
            $request->value_rating + 
            $request->service_rating
        ) / 4;
        
        PropertyRating::create([
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'booking_id' => $request->booking_id,
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
        /**
     * Show user's rental dashboard
     */
    public function myRental()
    {
        $user = Auth::user();
        
        // Get current active bookings
        $currentBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->whereDate('check_out', '>=', now())
            ->with(['property.images', 'room', 'payments'])
            ->latest()
            ->get();
        
        // Get upcoming bookings
        $upcomingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'CONFIRMED')
            ->whereDate('check_in', '>', now())
            ->with(['property.primaryImage', 'room'])
            ->latest()
            ->get();
        
        // Get past bookings
        $pastBookings = Booking::where('user_id', $user->id)
            ->where(function($query) {
                $query->where('status', 'CHECKED_OUT')
                      ->orWhere('status', 'CANCELLED')
                      ->orWhereDate('check_out', '<', now());
            })
            ->with(['property.primaryImage', 'room', 'payments'])
            ->latest()
            ->paginate(10);
        
        // Get user complaints
        $complaints = Complaint::where('user_id', $user->id)
            ->where('complaint_type', 'PROPERTY')
            ->with(['property'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Check if user can review past bookings
        $reviewableBookings = Booking::where('user_id', $user->id)
            ->where('status', 'CHECKED_OUT')
            ->whereDate('check_out', '>=', now()->subDays(30)) // Can review within 30 days
            ->whereDoesntHave('property.reviews', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->with('property')
            ->get();
        
        return view('rental.index', compact(
            'currentBookings',
            'upcomingBookings',
            'pastBookings',
            'complaints',
            'reviewableBookings'
        ));
    }
    
    /**
     * Submit a complaint for a rental
     */
    public function storeComplaint(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'required|string|max:1000',
            'priority' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
        ]);
        
        $complaint = Complaint::create([
            'complaint_reference' => 'CMP-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'complaint_type' => 'PROPERTY',
            'related_id' => $booking->property_id,
            'related_type' => 'PROPERTY',
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'status' => 'OPEN',
        ]);
        
        // If booking is provided, link it
        if ($booking) {
            $complaint->update([
                'related_id' => $booking->property_id,
                'related_type' => 'PROPERTY',
            ]);
        }
        
        return back()->with('success', 'Complaint submitted successfully!');
    }
    
    /**
     * Check-in to a booking
     */
    public function checkIn(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'CONFIRMED') {
            return back()->with('error', 'Only confirmed bookings can be checked in.');
        }
        
        if (now()->lt($booking->check_in)) {
            return back()->with('error', 'Check-in is only allowed on or after the check-in date.');
        }
        
        $booking->update(['status' => 'CHECKED_IN']);
        
        return back()->with('success', 'Successfully checked in!');
    }
    
    /**
     * Check-out from a booking
     */
    public function checkOut(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->status !== 'CHECKED_IN') {
            return back()->with('error', 'Only checked-in bookings can be checked out.');
        }
        
        $booking->update(['status' => 'CHECKED_OUT']);
        
        // If it's a room booking, make room available
        if ($booking->room_id) {
            $booking->room()->update(['status' => 'AVAILABLE']);
        }
        
        return back()->with('success', 'Successfully checked out!');
    }
}