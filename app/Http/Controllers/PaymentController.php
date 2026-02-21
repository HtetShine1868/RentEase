<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Traits\Notifiable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    use Notifiable;

    /**
     * Show payment page
     */
    public function create(Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }
        
        if ($booking->isPaid()) {
            return redirect()->route('bookings.show', $booking)
                ->with('info', 'This booking is already paid.');
        }
        
        return view('rental.payment', compact('booking'));
    }
    
    /**
     * Process payment
     */
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
            'payment_reference' => 'PAY-' . strtoupper(uniqid()),
            'user_id' => Auth::id(),
            'payable_type' => 'BOOKING',
            'payable_id' => $booking->id,
            'amount' => $booking->total_amount,
            'commission_amount' => $booking->commission_amount,
            'payment_method' => $request->payment_method,
            'status' => 'PENDING',
        ]);

        // ============ SEND NOTIFICATIONS ============
        
        // Send payment initiated notification
        $this->createNotification(
            Auth::id(),
            'PAYMENT',
            'Payment Initiated',
            "Your payment of ₹{$booking->total_amount} for booking #{$booking->booking_reference} has been initiated.",
            'payment',
            $payment->id
        );
        
        // Notify property owner about payment initiation
        $this->createNotification(
            $booking->property->owner_id,
            'PAYMENT',
            'Payment Initiated by Guest',
            "Guest has initiated payment of ₹{$booking->total_amount} for booking #{$booking->booking_reference}.",
            'payment',
            $payment->id
        );
        
        // Simulate successful payment for demo
        if ($request->payment_method !== 'CASH') {
            sleep(1); // Simulate processing delay
            
            $payment->update([
                'status' => 'COMPLETED',
                'transaction_id' => 'TXN' . Str::random(12),
                'paid_at' => now(),
            ]);
            
            // Update booking status
            $booking->update(['status' => 'CONFIRMED']);

            // ============ SEND PAYMENT SUCCESS NOTIFICATIONS ============
            
            // Send payment success notification to user
            $this->sendPaymentNotification(
                Auth::id(),
                $payment->amount,
                'completed',
                $payment->id
            );

            // Send booking confirmation notification
            $this->sendBookingNotification(
                Auth::id(),
                $booking->booking_reference,
                'confirmed',
                $booking->id
            );

            // Notify property owner about successful payment
            $this->createNotification(
                $booking->property->owner_id,
                'PAYMENT',
                'Payment Received',
                "Payment of ₹{$payment->amount} received for booking #{$booking->booking_reference}.",
                'payment',
                $payment->id
            );
            
            return redirect()->route('payments.success', $payment);
            
        } else {
            // Cash payment - stays pending

            // ============ SEND CASH PAYMENT NOTIFICATION ============
            
            $this->sendSystemNotification(
                Auth::id(),
                'Cash Payment Requested',
                "You've chosen to pay by cash. Please pay ₹{$booking->total_amount} to the property owner directly."
            );

            return redirect()->route('bookings.show', $booking)
                ->with('info', 'Cash payment requested. Please pay the owner directly.');
        }
    }
    
    /**
     * Payment success page
     */
    public function success(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $booking = $payment->payable;
        
        return view('rental.payment-success', compact('payment', 'booking'));
    }
    
    /**
     * Payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== Auth::id() && !Auth::user()->hasRole(['OWNER', 'SUPERADMIN'])) {
            abort(403);
        }
        
        $payment->load('payable');
        
        // Mark payment notifications as read
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('related_entity_type', 'payment')
            ->where('related_entity_id', $payment->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
        
        return view('payments.show', compact('payment'));
    }
    
    /**
     * Cancel payment
     */
    public function cancel(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }
        
        $payment->update(['status' => 'CANCELLED']);

        // ============ SEND NOTIFICATIONS ============
        
        $this->sendPaymentNotification(
            Auth::id(),
            $payment->amount,
            'cancelled',
            $payment->id
        );

        $this->sendSystemNotification(
            Auth::id(),
            'Payment Cancelled',
            "Your payment of ₹{$payment->amount} has been cancelled."
        );
        
        return redirect()->route('bookings.show', $payment->payable)
            ->with('error', 'Payment cancelled.');
    }

    /**
     * Process refund (Admin action)
     */
    public function refund(Payment $payment)
    {
        if (!Auth::user()->hasRole(['OWNER', 'SUPERADMIN'])) {
            abort(403);
        }

        if ($payment->status !== 'COMPLETED') {
            return response()->json(['error' => 'Only completed payments can be refunded'], 422);
        }

        $payment->status = 'REFUNDED';
        $payment->save();

        // ============ SEND NOTIFICATIONS ============
        
        $this->sendPaymentNotification(
            $payment->user_id,
            $payment->amount,
            'refunded',
            $payment->id
        );

        $this->sendSystemNotification(
            $payment->user_id,
            'Refund Processed',
            "Your refund of ₹{$payment->amount} has been processed. It may take 5-7 business days to reflect in your account."
        );

        return response()->json(['success' => true, 'message' => 'Refund processed']);
    }
}