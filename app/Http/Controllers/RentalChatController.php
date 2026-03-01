<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use App\Models\Booking;
use App\Models\Notification;
use App\Models\PropertyImage;
use App\Models\PropertyRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RentalController extends Controller
{
    /**
     * Display user's rental dashboard
     */
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // Current active stays (checked in or confirmed with check-in date <= today)
        $currentBookings = Booking::with(['property', 'room', 'property.primaryImage'])
            ->where('user_id', $userId)
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->where('check_in', '<=', $today)
            ->where('check_out', '>=', $today)
            ->orderBy('check_in')
            ->get();

        // Upcoming bookings (confirmed but check-in in future)
        $upcomingBookings = Booking::with(['property', 'room'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('check_in', '>', $today)
            ->orderBy('check_in')
            ->get();

        // Pending requests (awaiting owner approval)
        $pendingBookings = Booking::with(['property', 'room'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_PENDING)
            ->orderBy('created_at', 'desc')
            ->get();

        // Approved bookings (waiting for payment)
        $approvedBookings = Booking::with(['property', 'room'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_APPROVED)
            ->orderBy('approved_at', 'desc')
            ->get();

        // Payment pending bookings (payment initiated but not confirmed)
        $paymentPendingBookings = Booking::with(['property', 'room', 'payments'])
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_PAYMENT_PENDING)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Past bookings (completed stays)
        $pastBookings = Booking::with(['property', 'room'])
            ->where('user_id', $userId)
            ->whereIn('status', [Booking::STATUS_CHECKED_OUT, Booking::STATUS_REJECTED, Booking::STATUS_CANCELLED])
            ->orderBy('check_out', 'desc')
            ->paginate(10);

        // Reviewable bookings (checked out and not yet reviewed)
        $reviewableBookings = Booking::with('property')
            ->where('user_id', $userId)
            ->where('status', Booking::STATUS_CHECKED_OUT)
            ->whereDoesntHave('property.propertyRatings', function($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->get();

        return view('rental.index', compact(
            'currentBookings',
            'upcomingBookings',
            'pendingBookings',
            'approvedBookings',
            'paymentPendingBookings',
            'pastBookings',
            'reviewableBookings'
        ));
    }

    /**
     * Search for properties
     */
    public function search(Request $request)
    {
        $query = Property::with(['primaryImage', 'amenities'])
            ->whereIn('status', ['ACTIVE', 'PENDING']);

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

        // Filter by type
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

        // Filter by gender policy
        if ($request->filled('gender_policy')) {
            $query->where('gender_policy', $request->gender_policy);
        }

        // Filter by bedrooms
        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', '>=', $request->bedrooms);
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
                      ->orderBy('reviews_avg_overall_rating', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $properties = $query->paginate(12)->withQueryString();

        // Get unique cities for filter dropdown
        $cities = Property::whereIn('status', ['ACTIVE', 'PENDING'])
            ->distinct()
            ->orderBy('city')
            ->pluck('city');

        return view('rental.search', compact('properties', 'cities'));
    }

    /**
     * Show property details
     */
    public function show(Property $property)
    {
        if (!in_array($property->status, ['ACTIVE', 'PENDING'])) {
            abort(404);
        }

        $property->load(['images', 'amenities', 'rooms' => function($q) {
            $q->where('status', 'AVAILABLE');
        }, 'owner']);

        // Get reviews with ratings
        $reviews = PropertyRating::with('user')
            ->where('property_id', $property->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $averageRating = $reviews->avg('overall_rating') ?? 0;

        // Related properties (same city, type, different id)
        $relatedProperties = Property::with('primaryImage')
            ->where('id', '!=', $property->id)
            ->where('city', $property->city)
            ->whereIn('status', ['ACTIVE', 'PENDING'])
            ->limit(4)
            ->get();

        return view('rental.property-details', compact('property', 'reviews', 'averageRating', 'relatedProperties'));
    }

    /**
     * Show room details
     */
    public function showRoom(Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
            abort(404);
        }

        if (!in_array($property->status, ['ACTIVE', 'PENDING']) || $room->status !== 'AVAILABLE') {
            abort(404);
        }

        $property->load(['amenities', 'owner']);

        return view('rental.room-details', compact('property', 'room'));
    }

    /**
     * Show apartment rental request form
     */
    public function rentApartment(Property $property)
    {
        if ($property->type !== 'APARTMENT') {
            abort(404);
        }

        if (!in_array($property->status, ['ACTIVE', 'PENDING'])) {
            abort(404);
        }

        return view('rental.rent', compact('property'));
    }

    /**
     * Show room booking request form
     */
    public function bookRoom(Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
            abort(404);
        }

        if (!in_array($property->status, ['ACTIVE', 'PENDING']) || $room->status !== 'AVAILABLE') {
            abort(404);
        }

        return view('rental.book-room', compact('property', 'room'));
    }

    /**
     * Submit apartment rental request
     */
    public function submitRentalRequest(Request $request, Property $property)
    {
        if ($property->type !== 'APARTMENT') {
            abort(404);
        }

        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1|max:' . ($property->bedrooms * 2),
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'nullable|string|max:5',
            'special_requests' => 'nullable|string|max:1000',
            'agree_terms' => 'required|accepted',
        ]);

        // Combine phone with country code
        $fullPhone = ($request->phone_country_code ?? '+880') . $request->phone;

        // Check for conflicting confirmed bookings
        $hasConflict = Booking::where('property_id', $property->id)
            ->where(function($q) use ($validated) {
                $q->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                  ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                  ->orWhere(function($q2) use ($validated) {
                      $q2->where('check_in', '<=', $validated['check_in'])
                         ->where('check_out', '>=', $validated['check_out']);
                  });
            })
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->exists();

        if ($hasConflict) {
            return back()->with('error', 'This property is already booked for the selected dates.')->withInput();
        }

        // Calculate duration and totals
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $durationDays = $checkIn->diffInDays($checkOut);

        $roomPricePerDay = $property->base_price / 30;
        $totalRoomPrice = $roomPricePerDay * $durationDays;
        $commissionAmount = $totalRoomPrice * ($property->commission_rate / 100);
        $totalAmount = $totalRoomPrice + $commissionAmount;

        DB::beginTransaction();

        try {
            // Create booking request
            $booking = Booking::create([
                'booking_reference' => 'APT-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'property_id' => $property->id,
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'duration_days' => $durationDays,
                'room_price_per_day' => $roomPricePerDay,
                'total_room_price' => $totalRoomPrice,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'guest_count' => $validated['guest_count'],
                'special_requests' => $validated['special_requests'],
                'contact_phone' => $fullPhone,
                'status' => Booking::STATUS_PENDING,
            ]);

            // Notify owner
            $this->sendNotification(
                $property->owner_id,
                'NEW_BOOKING_REQUEST',
                'New Booking Request',
                "You have a new booking request for {$property->name} from " . Auth::user()->name,
                'booking',
                $booking->id
            );

            // Notify user
            $this->sendNotification(
                Auth::id(),
                'BOOKING_SUBMITTED',
                'Booking Request Submitted',
                "Your booking request for {$property->name} has been submitted. You'll be notified once the owner responds.",
                'booking',
                $booking->id
            );

            DB::commit();

            return redirect()->route('rental.booking-details', $booking)
                ->with('success', 'Your rental request has been submitted! You will be notified once the owner approves.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Booking request error: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit request. Please try again.')->withInput();
        }
    }

    /**
     * Submit room booking request
     */
    public function submitRoomRequest(Request $request, Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
            abort(404);
        }

        if ($room->status !== 'AVAILABLE') {
            return back()->with('error', 'This room is no longer available.')->withInput();
        }

        $validated = $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guest_count' => 'required|integer|min:1|max:' . $room->capacity,
            'phone' => 'required|string|max:20',
            'phone_country_code' => 'nullable|string|max:5',
            'special_requests' => 'nullable|string|max:1000',
            'agree_terms' => 'required|accepted',
        ]);

        // Combine phone with country code
        $fullPhone = ($request->phone_country_code ?? '+880') . $request->phone;

        // Check for conflicting confirmed bookings
        $hasConflict = Booking::where('room_id', $room->id)
            ->where(function($q) use ($validated) {
                $q->whereBetween('check_in', [$validated['check_in'], $validated['check_out']])
                  ->orWhereBetween('check_out', [$validated['check_in'], $validated['check_out']])
                  ->orWhere(function($q2) use ($validated) {
                      $q2->where('check_in', '<=', $validated['check_in'])
                         ->where('check_out', '>=', $validated['check_out']);
                  });
            })
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->exists();

        if ($hasConflict) {
            return back()->with('error', 'This room is already booked for the selected dates.')->withInput();
        }

        // Calculate duration and totals
        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $durationDays = $checkIn->diffInDays($checkOut);

        $roomPricePerDay = $room->base_price / 30;
        $totalRoomPrice = $roomPricePerDay * $durationDays;
        $commissionAmount = $totalRoomPrice * ($room->commission_rate / 100);
        $totalAmount = $totalRoomPrice + $commissionAmount;

        DB::beginTransaction();

        try {
            // Create booking request
            $booking = Booking::create([
                'booking_reference' => 'ROM-' . strtoupper(uniqid()),
                'user_id' => Auth::id(),
                'property_id' => $property->id,
                'room_id' => $room->id,
                'check_in' => $validated['check_in'],
                'check_out' => $validated['check_out'],
                'duration_days' => $durationDays,
                'room_price_per_day' => $roomPricePerDay,
                'total_room_price' => $totalRoomPrice,
                'commission_amount' => $commissionAmount,
                'total_amount' => $totalAmount,
                'guest_count' => $validated['guest_count'],
                'special_requests' => $validated['special_requests'],
                'contact_phone' => $fullPhone,
                'status' => Booking::STATUS_PENDING,
            ]);

            // Temporarily mark room as reserved
            $room->update(['status' => 'RESERVED']);

            // Notify owner
            $this->sendNotification(
                $property->owner_id,
                'NEW_BOOKING_REQUEST',
                'New Room Booking Request',
                "You have a new booking request for Room {$room->room_number} at {$property->name} from " . Auth::user()->name,
                'booking',
                $booking->id
            );

            // Notify user
            $this->sendNotification(
                Auth::id(),
                'BOOKING_SUBMITTED',
                'Booking Request Submitted',
                "Your booking request for Room {$room->room_number} has been submitted. You'll be notified once the owner responds.",
                'booking',
                $booking->id
            );

            DB::commit();

            return redirect()->route('rental.booking-details', $booking)
                ->with('success', 'Your booking request has been submitted! You will be notified once the owner approves.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Room booking request error: ' . $e->getMessage());
            return back()->with('error', 'Failed to submit request. Please try again.')->withInput();
        }
    }

    /**
     * Show booking details
     */
    public function showBooking(Booking $booking)
    {
        if ($booking->user_id !== Auth::id() && $booking->property->owner_id !== Auth::id()) {
            abort(403);
        }

        $booking->load(['property', 'room', 'user', 'payments']);

        // Get payment deadline for approved bookings
        $paymentDeadline = null;
        $hoursLeft = null;
        $isExpiring = false;
        
        if ($booking->status === Booking::STATUS_APPROVED && $booking->approved_at) {
            $paymentDeadline = Carbon::parse($booking->approved_at)->addHours(24);
            $hoursLeft = max(0, now()->diffInHours($paymentDeadline, false));
            $isExpiring = $hoursLeft < 6;
        }

        return view('rental.booking-details', compact('booking', 'paymentDeadline', 'hoursLeft', 'isExpiring'));
    }

    /**
     * Cancel booking (by user)
     */
    public function cancelBooking(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_APPROVED])) {
            return back()->with('error', 'This booking cannot be cancelled at this stage.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        DB::beginTransaction();

        try {
            $oldStatus = $booking->status;
            $booking->status = Booking::STATUS_CANCELLED;
            $booking->cancellation_reason = $request->cancellation_reason;
            $booking->save();

            // If it was a room booking, make room available again
            if ($booking->room_id) {
                $booking->room->update(['status' => 'AVAILABLE']);
            }

            // Notify owner
            $this->sendNotification(
                $booking->property->owner_id,
                'BOOKING_CANCELLED',
                'Booking Cancelled',
                "Booking #{$booking->booking_reference} has been cancelled by the guest. Reason: {$request->cancellation_reason}",
                'booking',
                $booking->id
            );

            DB::commit();

            return redirect()->route('rental.index')->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Cancel booking error: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel booking. Please try again.');
        }
    }

    /**
     * Check property availability (AJAX)
     */
    public function checkAvailability(Request $request, Property $property)
    {
        $request->validate([
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);

        $isAvailable = !Booking::where('property_id', $property->id)
            ->where(function($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut])
                  ->orWhereBetween('check_out', [$checkIn, $checkOut])
                  ->orWhere(function($q2) use ($checkIn, $checkOut) {
                      $q2->where('check_in', '<=', $checkIn)
                         ->where('check_out', '>=', $checkOut);
                  });
            })
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->exists();

        return response()->json([
            'available' => $isAvailable,
            'message' => $isAvailable ? 'Property is available for these dates' : 'Property is not available for these dates'
        ]);
    }

    /**
     * Submit property review
     */
    public function submitReview(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'property_id' => 'required|exists:properties,id',
            'cleanliness_rating' => 'required|integer|min:1|max:5',
            'location_rating' => 'required|integer|min:1|max:5',
            'value_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::where('id', $request->booking_id)
            ->where('user_id', Auth::id())
            ->where('status', Booking::STATUS_CHECKED_OUT)
            ->firstOrFail();

        // Check if already reviewed
        $existingReview = PropertyRating::where('user_id', Auth::id())
            ->where('property_id', $request->property_id)
            ->where('booking_id', $booking->id)
            ->exists();

        if ($existingReview) {
            return back()->with('error', 'You have already reviewed this property.');
        }

        $overallRating = (
            $request->cleanliness_rating +
            $request->location_rating +
            $request->value_rating +
            $request->service_rating
        ) / 4;

        PropertyRating::create([
            'user_id' => Auth::id(),
            'property_id' => $request->property_id,
            'booking_id' => $booking->id,
            'cleanliness_rating' => $request->cleanliness_rating,
            'location_rating' => $request->location_rating,
            'value_rating' => $request->value_rating,
            'service_rating' => $request->service_rating,
            'overall_rating' => round($overallRating, 1),
            'comment' => $request->comment,
            'is_approved' => true,
        ]);

        // Notify owner
        $this->sendNotification(
            $booking->property->owner_id,
            'NEW_REVIEW',
            'New Property Review',
            Auth::user()->name . ' has left a review for your property ' . $booking->property->name,
            'property',
            $booking->property_id
        );

        return redirect()->back()->with('success', 'Thank you for your review!');
    }

    /**
     * Start chat from property page
     */
    public function startFromProperty(Property $property)
    {
        if (Auth::id() === $property->owner_id) {
            return redirect()->route('properties.show', $property)
                ->with('error', 'You cannot chat with yourself.');
        }

        // Check if there's an existing active booking for chat
        $existingBooking = Booking::where('user_id', Auth::id())
            ->where('property_id', $property->id)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_APPROVED, Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN])
            ->first();

        if ($existingBooking) {
            return redirect()->route('rental.chat.show', $existingBooking);
        }

        // If no active booking, just redirect to property page with message
        return redirect()->route('properties.show', $property)
            ->with('info', 'You need to make a booking request to start a chat with the owner.');
    }

    /**
     * Send notification
     */
    private function sendNotification($userId, $type, $title, $message, $entityType, $entityId)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'related_entity_type' => $entityType,
                'related_entity_id' => $entityId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send notification: ' . $e->getMessage());
        }
    }
}