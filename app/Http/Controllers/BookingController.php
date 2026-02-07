<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['property', 'room'])
            ->latest()
            ->paginate(10);
            
        return view('rental.index', compact('bookings'));
    }
    
    public function show(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking->load(['property.images', 'room', 'payments']);
        
        return view('rental.booking-details', compact('booking'));
    }
    
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
        
        return redirect()->route('rental.booking.details', $booking)
            ->with('success', 'Apartment booking request submitted successfully! Please complete payment.');
    }
    
    public function storeRoom(Request $request, Property $property, Room $room)
    {
        if ($property->type !== 'HOSTEL' || $room->property_id !== $property->id) {
            abort(404);
        }
        
        if ($room->status !== 'AVAILABLE') {
            return back()->withErrors(['error' => 'This room is no longer available.']);
        }
        
        $request->validate([
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:500',
            'agree_terms' => 'required|accepted',
        ]);
        
        // Check room availability for dates
        $available = $room->isAvailableForDates($request->check_in, $request->check_out);
        
        if (!$available) {
            return back()->withErrors(['error' => 'This room is not available for the selected dates.']);
        }
        
        // Calculate prices
        $durationDays = date_diff(
            date_create($request->check_in), 
            date_create($request->check_out)
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
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'room_price_per_day' => $roomPricePerDay,
            'commission_amount' => $commissionAmount,
            'total_amount' => $totalAmount,
            'status' => 'PENDING',
        ]);
        
        // Update room status to reserved
        $room->update(['status' => 'RESERVED']);
        
        return redirect()->route('rental.booking.details', $booking)
            ->with('success', 'Room booking request submitted successfully! Please complete payment.');
    }
    
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
            'cancellation_reason' => 'Cancelled by user'
        ]);
        
        // If it's a room booking, make room available again
        if ($booking->room_id) {
            $booking->room()->update(['status' => 'AVAILABLE']);
        }
        
        return back()->with('success', 'Booking cancelled successfully.');
    }
}