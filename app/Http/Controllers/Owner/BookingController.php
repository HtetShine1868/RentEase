<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Property;

class BookingController extends Controller
{
    // Show all bookings
   public function index()
{
    $bookings = Booking::with(['user', 'property', 'room'])->get();
    $properties = Property::all(); // ✅ now this works!

    return view('owner.pages.bookings.index', compact('bookings', 'properties'));
}

    // Show a single booking
    public function show(Booking $booking)
    {
        $booking->load(['user', 'property', 'room']);
        return view('owner.pages.bookings.show', compact('booking'));
    }

    // Update booking status
    public function updateStatus(Booking $booking)
    {
        $booking->status = request('status');
        $booking->save();

        return redirect()->route('owner.bookings.show', $booking->id)
                         ->with('success', 'Booking status updated.');
    }

    // Update payment status
    public function updatePaymentStatus(Booking $booking)
    {
        $booking->payment_status = request('payment_status');
        $booking->save();

        return redirect()->route('owner.bookings.show', $booking->id)
                         ->with('success', 'Payment status updated.');
    }
}