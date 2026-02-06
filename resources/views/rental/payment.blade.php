@extends('layouts.app')

@section('title', 'Make Payment - ' . $booking->booking_reference)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('rental.booking.details', $booking) }}" 
                   class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Booking Details
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Make Payment</span>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Complete Your Payment</h2>
                <p class="text-gray-600 mt-1">Booking Reference: {{ $booking->booking_reference }}</p>
            </div>
            
            <form method="POST" action="{{ route('rental.booking.payment.store', $booking) }}" class="p-6">
                @csrf
                
                <!-- Booking Summary -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center mb-2">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $booking->property->area }}, {{ $booking->property->city }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-600">৳{{ number_format($booking->total_amount, 2) }}</div>
                            <div class="text-sm text-gray-500">Total amount due</div>
                        </div>
                    </div>
                    <div class="text-sm text-gray-600">
                        Stay: {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - 
                        {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Select Payment Method</h3>
                    
                    <div class="space-y-3">
                        <!-- Bank Transfer -->
                        <label class="relative flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500">
                            <div class="flex items-center h-5">
                                <input type="radio" name="payment_method" value="BANK_TRANSFER" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300" required>
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Bank Transfer</span>
                                    <span class="text-sm text-gray-500">2-3 business days</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Transfer directly to our bank account</p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                        </label>
                        
                        <!-- Mobile Banking -->
                        <label class="relative flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500">
                            <div class="flex items-center h-5">
                                <input type="radio" name="payment_method" value="MOBILE_BANKING" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Mobile Banking</span>
                                    <span class="text-sm text-gray-500">Instant</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">bKash, Nagad, Rocket</p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </label>
                        
                        <!-- Cash -->
                        <label class="relative flex items-start p-4 border border-gray-300 rounded-lg cursor-pointer hover:border-indigo-500">
                            <div class="flex items-center h-5">
                                <input type="radio" name="payment_method" value="CASH" 
                                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                            </div>
                            <div class="ml-3 flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-900">Cash Payment</span>
                                    <span class="text-sm text-gray-500">On arrival</span>
                                </div>
                                <p class="text-sm text-gray-500 mt-1">Pay directly to property owner</p>
                            </div>
                            <div class="ml-3 flex-shrink-0">
                                <svg class="h-6 w-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h4 class="font-medium text-blue-800 mb-2">Payment Instructions</h4>
                    <div class="text-sm text-blue-700 space-y-1">
                        <p>• For bank transfer: Account details will be provided after selection</p>
                        <p>• For mobile banking: You'll receive payment number</p>
                        <p>• For cash payment: Pay upon check-in</p>
                    </div>
                </div>

                <!-- Terms Agreement -->
                <div class="mb-6">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="terms" name="terms" required
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I agree to the payment terms and conditions
                            </label>
                            <p class="text-gray-500">By proceeding, you agree to our refund policy and payment terms.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('rental.booking.details', $booking) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        Process Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection