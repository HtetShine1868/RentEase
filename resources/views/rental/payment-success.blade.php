@extends('layouts.app')

@section('title', 'Payment Successful')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center py-12">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-lg shadow p-8">
            <div class="text-center">
                <!-- Success Icon -->
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
                <p class="text-gray-600 mb-6">Your payment has been processed successfully.</p>
                
                <!-- Payment Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div class="text-gray-500">Transaction ID:</div>
                        <div class="font-medium text-gray-900">{{ $payment->transaction_id }}</div>
                        
                        <div class="text-gray-500">Amount Paid:</div>
                        <div class="font-medium text-green-600">৳{{ number_format($payment->amount, 2) }}</div>
                        
                        <div class="text-gray-500">Payment Method:</div>
                        <div class="font-medium text-gray-900">{{ $payment->payment_method }}</div>
                        
                        <div class="text-gray-500">Date:</div>
                        <div class="font-medium text-gray-900">{{ $payment->paid_at->format('M d, Y h:i A') }}</div>
                    </div>
                </div>
                
                <!-- Booking Info -->
                <div class="border-t border-gray-200 pt-4 mb-6">
                    <h4 class="font-medium text-gray-900 mb-2">Booking Information</h4>
                    <p class="text-sm text-gray-600">Reference: {{ $booking->booking_reference }}</p>
                    <p class="text-sm text-gray-600">{{ $booking->property->name }}</p>
                </div>
                
                <!-- Actions -->
                <div class="space-y-3">
                    <a href="{{ route('rental.booking.details', $booking) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        View Booking Details
                    </a>
                    <a href="{{ route('rental.search') }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Browse More Properties
                    </a>
                </div>
                
                <!-- Receipt Note -->
                <p class="mt-6 text-sm text-gray-500">
                    A payment receipt has been sent to your email. 
                    You can also download it from your booking details page.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection