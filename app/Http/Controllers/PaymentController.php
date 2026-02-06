<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function create(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->isPaid()) {
            return redirect()->route('rental.booking.details', $booking)
                ->with('info', 'This booking is already paid.');
        }
        
        return view('rental.payment', compact('booking'));
    }
    
    public function store(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        $request->validate([
            'payment_method' => 'required|in:CASH,BANK_TRANSFER,MOBILE_BANKING,CARD',
        ]);
        
        // Create payment
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'payable_type' => Booking::class,
            'payable_id' => $booking->id,
            'amount' => $booking->total_amount,
            'commission_amount' => $booking->commission_amount,
            'payment_method' => $request->payment_method,
            'status' => 'PENDING',
        ]);
        
        // For demo purposes, we'll simulate payment processing
        // In real app, integrate with payment gateway here
        
        // Simulate successful payment for demo
        if ($request->payment_method !== 'CASH') {
            // Simulate bank/online payment processing
            sleep(1); // Simulate processing delay
            
            $payment->update([
                'status' => 'COMPLETED',
                'transaction_id' => 'TXN' . Str::random(12),
                'paid_at' => now(),
            ]);
            
            // Update booking status
            $booking->update(['status' => 'CONFIRMED']);
            
            return redirect()->route('rental.payment.success', $payment);
        } else {
            // Cash payment - stays pending
            return redirect()->route('rental.booking.details', $booking)
                ->with('info', 'Cash payment requested. Please pay the owner directly.');
        }
    }
    
    public function success(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking = $payment->payable; // Get the booking
        
        return view('rental.payment-success', compact('payment', 'booking'));
    }
    
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $payment->update(['status' => 'CANCELLED']);
        
        return redirect()->route('rental.booking.details', $payment->payable)
            ->with('error', 'Payment cancelled.');
    }
}