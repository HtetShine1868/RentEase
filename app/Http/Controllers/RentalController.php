<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\PropertyRating;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\Payment;
use App\Models\User;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    use Notifiable;

    /**
     * Search for available properties excluding those already rented by the user
     */
    public function search(Request $request)
    {
        $user = Auth::user();
        
        $query = Property::query()->with(['primaryImage', 'amenities', 'reviews']);
        
        // Only show active properties for non-owners
        if (!Auth::check() || (!Auth::user()->hasRole('OWNER') && !Auth::user()->hasRole('SUPERADMIN'))) {
            $query->where('status', 'ACTIVE');
        }
        
        // =====================================================
        // EXCLUDE PROPERTIES ALREADY RENTED BY CURRENT USER
        // =====================================================

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
        if ($request->filled('min_price') && is_numeric($request->min_price) && $request->min_price >= 0) {
            $query->where('base_price', '>=', $request->min_price);
        }
        
        if ($request->filled('max_price') && is_numeric($request->max_price) && $request->max_price > 0) {
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
        
        // Filter by furnishing status (for apartments)
        if ($request->filled('furnishing_status')) {
            $query->where('furnishing_status', $request->furnishing_status);
        }
        
        // Filter by minimum bedrooms (for apartments)
        if ($request->filled('bedrooms') && is_numeric($request->bedrooms)) {
            $query->where('bedrooms', '>=', $request->bedrooms);
        }
        
        // Filter by minimum rating
        if ($request->filled('min_rating') && is_numeric($request->min_rating)) {
            $query->whereHas('reviews', function($q) use ($request) {
                $q->havingRaw('AVG(overall_rating) >= ?', [$request->min_rating]);
            });
        }
        
        // For hostels, only show properties with at least one available room
        if ($request->filled('type') && $request->type === 'HOSTEL') {
            $query->whereHas('rooms', function($q) {
                $q->where('status', 'AVAILABLE');
            });
        }
        
        // For apartments, check if they're available (no active bookings)
        if ($request->filled('type') && $request->type === 'APARTMENT') {
            $query->whereDoesntHave('bookings', function($q) {
                $q->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
                  ->where('check_out', '>=', now());
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
                $query->withAvg('reviews', 'overall_rating')
                      ->orderByDesc('reviews_avg_overall_rating');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->latest();
                break;
        }
        
        // Get distinct cities for filter dropdown
        $cities = Property::where('status', 'ACTIVE')
            ->distinct()
            ->pluck('city')
            ->filter()
            ->sort()
            ->values();
        
        // Get distinct areas for filter dropdown based on selected city
        $areas = [];
        if ($request->filled('city')) {
            $areas = Property::where('status', 'ACTIVE')
                ->where('city', $request->city)
                ->distinct()
                ->pluck('area')
                ->filter()
                ->sort()
                ->values();
        }
        
        // Get min and max price range for price slider
        $priceRange = [
            'min' => Property::where('status', 'ACTIVE')->min('base_price') ?? 0,
            'max' => Property::where('status', 'ACTIVE')->max('base_price') ?? 100000
        ];
        
        // Paginate results
        $properties = $query->paginate(12)->withQueryString();
        
        // Pass all data to the view
        return view('rental.search', compact(
            'properties', 
            'cities', 
            'areas', 
            'priceRange'
        ));
    }
    
    /**
     * Show property details
     */
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
    /**
 * Show individual room details
 */
public function showRoom(Property $property, Room $room)
{
    // Verify the room belongs to the property
    if ($room->property_id !== $property->id) {
        abort(404);
    }

    // Check if property is active or user is owner/admin
    if ($property->status !== 'ACTIVE' && !Auth::user()->hasRole('OWNER') && !Auth::user()->hasRole('SUPERADMIN')) {
        abort(404, 'Property not found');
    }

    // Check if room is available (or show anyway with status indicator)
    $property->load(['images', 'amenities', 'owner']);
    
    // Get other available rooms in same property
    $otherRooms = $property->rooms()
        ->where('id', '!=', $room->id)
        ->where('status', 'AVAILABLE')
        ->limit(3)
        ->get();

    return view('rental.room-details', compact('property', 'room', 'otherRooms'));
}
    
    /**
     * Show apartment rental form
     */
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
    
    /**
     * Show room booking form
     */
public function bookRoom(Property $property, Room $room)
{
    // Log the attempt
    \Log::info('bookRoom method called', [
        'property_id' => $property->id,
        'room_id' => $room->id,
        'url' => request()->fullUrl()
    ]);
    
    try {
        // Check property type
        if ($property->type !== 'HOSTEL') {
            \Log::error('Property is not a HOSTEL', ['type' => $property->type]);
            abort(404, 'Property is not a hostel');
        }
        
        // Check if room belongs to property
        if ($room->property_id !== $property->id) {
            \Log::error('Room does not belong to property', [
                'room_property_id' => $room->property_id,
                'property_id' => $property->id
            ]);
            abort(404, 'Room does not belong to this property');
        }
        
        // Check if room is available
        if ($room->status !== 'AVAILABLE') {
            \Log::error('Room is not available', ['status' => $room->status]);
            abort(404, 'This room is not available');
        }
        
        // Load relationships
        \Log::info('Loading property relationships');
        $property->load(['images', 'amenities', 'owner']);
        
        // Get other available rooms
        \Log::info('Getting other available rooms');
        $otherRooms = $property->rooms()
            ->where('id', '!=', $room->id)
            ->where('status', 'AVAILABLE')
            ->limit(3)
            ->get();
        
        \Log::info('Rendering view', ['other_rooms_count' => $otherRooms->count()]);
        
        return view('rental.room-booking', compact('property', 'room', 'otherRooms'));
        
    } catch (\Exception $e) {
        \Log::error('Exception in bookRoom method', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Return a proper error response
        return response()->view('errors.500', ['error' => $e->getMessage()], 500);
    }
}
    /**
     * Check property availability
     */
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
        
        // Send notification
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'checked in',
            $booking->id
        );
        
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
        
        // Send notification
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'checked out',
            $booking->id
        );
        
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
        $payment = Payment::create([
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
        
        // Send notification
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'extended',
            $booking->id
        );
        
        $this->sendPaymentNotification(
            Auth::id(),
            $extensionCost,
            'pending',
            $payment->id
        );
        
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
        $review = PropertyRating::create([
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
        
        // Send notification
        $this->createNotification(
            Auth::id(),
            'SYSTEM',
            'Review Submitted',
            "Your review for {$booking->property->name} has been submitted. Thank you!",
            'booking',
            $booking->id
        );
        
        // Notify property owner
        $this->createNotification(
            $booking->property->owner_id,
            'BOOKING',
            'New Review',
            "Your property {$booking->property->name} received a new review.",
            'property',
            $booking->property_id
        );
        
        return redirect()->back()->with('success', 'Thank you for your review!');
    }
    
    /**
     * Submit a complaint - UPDATED WITH NOTIFICATIONS
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
        
        $complaint = Complaint::create([
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

        // ============ ADD NOTIFICATIONS ============
        
        try {
            // 1. Create notification for the user
            $notification = \App\Models\Notification::create([
                'user_id' => Auth::id(),
                'type' => 'COMPLAINT',
                'title' => 'Complaint Submitted',
                'message' => "Your complaint #{$complaint->complaint_reference} has been submitted successfully.",
                'related_entity_type' => 'complaint',
                'related_entity_id' => $complaint->id,
                'is_read' => false,
                'channel' => 'IN_APP',
                'is_sent' => true,
                'sent_at' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            \Log::info("✅ Complaint notification created in RentalController with ID: " . $notification->id);
            
            // 2. Notify property owner if complaint is about a property
            if ($request->complaint_type === 'PROPERTY' && $request->related_id) {
                $property = Property::find($request->related_id);
                if ($property && $property->owner_id !== Auth::id()) {
                    \App\Models\Notification::create([
                        'user_id' => $property->owner_id,
                        'type' => 'COMPLAINT',
                        'title' => 'New Complaint About Your Property',
                        'message' => "A new {$request->priority} priority complaint has been filed about your property '{$property->name}'.",
                        'related_entity_type' => 'complaint',
                        'related_entity_id' => $complaint->id,
                        'is_read' => false,
                        'channel' => 'IN_APP',
                        'is_sent' => true,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }
            }
            
            // 3. Notify admins
            $admins = User::whereHas('roles', function($q) {
                $q->where('name', 'SUPERADMIN');
            })->get();

            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'user_id' => $admin->id,
                    'type' => 'COMPLAINT',
                    'title' => 'New Complaint Requires Attention',
                    'message' => "New {$request->priority} priority complaint #{$complaint->complaint_reference} from " . Auth::user()->name,
                    'related_entity_type' => 'complaint',
                    'related_entity_id' => $complaint->id,
                    'is_read' => false,
                    'channel' => 'IN_APP',
                    'is_sent' => true,
                    'sent_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error("❌ Failed to create complaint notification in RentalController: " . $e->getMessage());
        }
        
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
        
        // Mark booking-related notifications as read
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('related_entity_type', 'booking')
            ->where('related_entity_id', $booking->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
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
        
        // Send notification
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'cancelled',
            $booking->id
        );
        
        // Notify property owner
        $this->createNotification(
            $booking->property->owner_id,
            'BOOKING',
            'Booking Cancelled',
            "Booking #{$booking->booking_reference} has been cancelled by the user.",
            'booking',
            $booking->id
        );
        
        return redirect()->route('rental.index')->with('success', 'Booking cancelled successfully.');
    }
    
    /**
     * Show invoice
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
     * List all user complaints
     */
    public function complaints()
    {
        $complaints = Complaint::where('user_id', Auth::id())
            ->with(['assignedToUser'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('rental.complaints', compact('complaints'));
    }

    /**
     * Show single complaint
     */
    public function showComplaint(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id()) {
            abort(403);
        }

        $complaint->load(['assignedToUser']);

        // Mark complaint notifications as read
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('related_entity_type', 'complaint')
            ->where('related_entity_id', $complaint->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return view('rental.complaint-details', compact('complaint'));
    }
}