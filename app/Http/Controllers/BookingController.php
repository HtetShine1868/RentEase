<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Display a listing of user's bookings
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['property', 'room'])
            ->latest()
            ->paginate(10);
            
        return view('rental.index', compact('bookings'));
    }
    
    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load(['property.images', 'room', 'payments']);
        
        // Mark notifications as read
        Notification::where('user_id', Auth::id())
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
     * Store a new apartment booking
     */
    public function storeApartment(Request $request, Property $property)
    {
        if ($property->type !== 'APARTMENT') {
            abort(404);
        }
        
        $request->validate([
            'move_in_date' => 'required|date|after_or_equal:today',
            'duration_months' => 'required|integer|min:' . $property->min_stay_months,
            'occupants' => 'required|integer|min:1|max:' . ($property->bedrooms * 2),
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'agree_terms' => 'required|accepted',
        ]);
        
        // Check availability for apartment
        $checkOut = date('Y-m-d', strtotime($request->move_in_date . ' + ' . $request->duration_months . ' months'));
        
        $conflictingBookings = $property->bookings()
            ->where(function($query) use ($request, $checkOut) {
                $query->whereBetween('check_in', [$request->move_in_date, $checkOut])
                      ->orWhereBetween('check_out', [$request->move_in_date, $checkOut])
                      ->orWhere(function($q) use ($request, $checkOut) {
                          $q->where('check_in', '<', $request->move_in_date)
                            ->where('check_out', '>', $checkOut);
                      });
            })
            ->whereIn('status', ['CONFIRMED', 'CHECKED_IN'])
            ->exists();
        
        if ($conflictingBookings) {
            return back()->withErrors(['error' => 'This apartment is already booked for the selected dates.']);
        }
        
        // Calculate prices
        $totalMonths = $request->duration_months;
        $roomPricePerMonth = $property->base_price;
        $commissionRate = $property->commission_rate;
        $commissionAmount = ($roomPricePerMonth * $commissionRate / 100) * $totalMonths;
        $totalAmount = ($roomPricePerMonth * $totalMonths) + $commissionAmount;
        
        // Create booking
        $booking = Booking::create([
            'booking_reference' => 'APT-' . strtoupper(Str::random(8)),
            'user_id' => Auth::id(),
            'property_id' => $property->id,
            'room_id' => null,
            'check_in' => $request->move_in_date,
            'check_out' => $checkOut,
            'room_price_per_day' => $roomPricePerMonth / 30, // Convert to daily rate
            'commission_amount' => $commissionAmount,
            'total_amount' => $totalAmount,
            'status' => 'PENDING',
        ]);
        
        // Send notifications
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'pending',
            $booking->id
        );
        
        $this->createNotification(
            $property->owner_id,
            'BOOKING',
            'New Booking Received',
            "New booking request for {$property->name} from " . Auth::user()->name,
            'booking',
            $booking->id
        );

        $this->sendSystemNotification(
            Auth::id(),
            'Booking Created Successfully',
            'Your booking request has been submitted and is pending confirmation.'
        );
        
        return redirect()->route('bookings.show', $booking)
            ->with('success', 'Apartment booking request submitted successfully! Please complete payment.');
    }
    
    /**
     * Store a new room booking
     */

public function storeRoom(Request $request, Property $property, Room $room)
{
    if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
        abort(404);
    }
    
    if ($room->status !== 'AVAILABLE') {
        return back()->withErrors(['error' => 'This room is no longer available.']);
    }
    
    $request->validate([
        'move_in_date' => 'required|date|after_or_equal:today',
        'months' => 'required|integer|min:1|max:12',
        'phone' => 'required|string|max:20',
        'emergency_contact' => 'nullable|string|max:20',
        'notes' => 'nullable|string|max:500',
        'agree_terms' => 'required|accepted',
    ]);
    
    // Calculate check-out date based on move-in date and months
    $checkIn = $request->move_in_date;
    $checkOut = date('Y-m-d', strtotime($checkIn . ' + ' . $request->months . ' months'));
    
    // Check room availability for dates
    $available = $room->isAvailableForDates($checkIn, $checkOut);
    
    if (!$available) {
        return back()->withErrors(['error' => 'This room is not available for the selected period.']);
    }
    
    // Calculate prices
    $durationDays = date_diff(
        date_create($checkIn), 
        date_create($checkOut)
    )->days;
    
    $roomPricePerDay = $room->base_price / 30; // Monthly to daily
    $commissionRate = $room->commission_rate;
    $totalRoomPrice = $roomPricePerDay * $durationDays;
    $commissionAmount = $totalRoomPrice * ($commissionRate / 100);
    $totalAmount = $totalRoomPrice + $commissionAmount;
    
    // Create booking
    $booking = Booking::create([
        'booking_reference' => 'HST-' . strtoupper(Str::random(8)),
        'user_id' => Auth::id(),
        'property_id' => $property->id,
        'room_id' => $room->id,
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'room_price_per_day' => $roomPricePerDay,
        'commission_amount' => $commissionAmount,
        'total_amount' => $totalAmount,
        'status' => 'PENDING',
    ]);

    // Send notifications
    $this->sendBookingNotification(
        Auth::id(),
        $booking->booking_reference,
        'pending',
        $booking->id
    );

    $this->createNotification(
        $property->owner_id,
        'BOOKING',
        'New Room Booking',
        "New booking for Room {$room->room_number} at {$property->name} for {$request->months} months",
        'booking',
        $booking->id
    );
    
    $this->sendSystemNotification(
        Auth::id(),
        'Room Booked Successfully',
        'Your room booking request has been submitted and is pending confirmation.'
    );
    
    // Update room status to reserved
    $room->update(['status' => 'RESERVED']);
    
    return redirect()->route('payments.create', $booking)
        ->with('success', 'Room booking request submitted successfully! Please complete payment.');
}
    
    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if (!in_array($booking->status, ['PENDING', 'CONFIRMED'])) {
            return back()->withErrors(['error' => 'This booking cannot be cancelled.']);
        }
        
        $booking->update([
            'status' => 'CANCELLED',
            'cancelled_at' => now(),
            'cancellation_reason' => 'Cancelled by user'
        ]);
        
        // If it's a room booking, make room available again
        if ($booking->room_id) {
            $booking->room()->update(['status' => 'AVAILABLE']);
        }
        
        // Send cancellation notifications
        $this->sendBookingNotification(
            Auth::id(),
            $booking->booking_reference,
            'cancelled',
            $booking->id
        );

        $this->createNotification(
            $booking->property->owner_id,
            'BOOKING',
            'Booking Cancelled',
            "Booking #{$booking->booking_reference} has been cancelled by the user.",
            'booking',
            $booking->id
        );
        
        $this->sendSystemNotification(
            Auth::id(),
            'Booking Cancelled',
            'Your booking has been successfully cancelled.'
        );
        
        return redirect()->route('bookings.index')
            ->with('success', 'Booking cancelled successfully.');
    }
    
    // ==================== NOTIFICATION METHODS ====================
    
    /**
     * Send booking notification to user
     * 
     * @param int $userId
     * @param string $bookingReference
     * @param string $status
     * @param int $bookingId
     * @return void
     */
    private function sendBookingNotification($userId, $bookingReference, $status, $bookingId)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => 'BOOKING_STATUS',
                'title' => 'Booking ' . ucfirst($status),
                'message' => "Your booking #{$bookingReference} is now {$status}.",
                'related_entity_type' => 'booking',
                'related_entity_id' => $bookingId,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            // Log error but don't break the main process
            \Log::error('Failed to send booking notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Create notification for property owner or other users
     * 
     * @param int $userId
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $entityType
     * @param int|null $entityId
     * @return void
     */
    private function createNotification($userId, $type, $title, $message, $entityType, $entityId = null)
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
            \Log::error('Failed to create notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send system notification to user
     * 
     * @param int $userId
     * @param string $title
     * @param string $message
     * @return void
     */
    private function sendSystemNotification($userId, $title, $message)
    {
        try {
            Notification::create([
                'user_id' => $userId,
                'type' => 'SYSTEM',
                'title' => $title,
                'message' => $message,
                'related_entity_type' => 'system',
                'related_entity_id' => null,
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to send system notification: ' . $e->getMessage());
        }
    }
    
    /**
     * Send notification to multiple users
     * 
     * @param array $userIds
     * @param string $type
     * @param string $title
     * @param string $message
     * @param string $entityType
     * @param int|null $entityId
     * @return void
     */
    private function sendBulkNotifications($userIds, $type, $title, $message, $entityType, $entityId = null)
    {
        try {
            $notifications = [];
            foreach ($userIds as $userId) {
                $notifications[] = [
                    'user_id' => $userId,
                    'type' => $type,
                    'title' => $title,
                    'message' => $message,
                    'related_entity_type' => $entityType,
                    'related_entity_id' => $entityId,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            
            if (!empty($notifications)) {
                Notification::insert($notifications);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send bulk notifications: ' . $e->getMessage());
        }
    }
}