@extends('layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_reference)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('rental.search') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Search
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <a href="{{ route('rental.my-bookings') }}" 
                   class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    My Bookings
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</span>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="mb-6 rounded-md bg-blue-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Booking Header -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
                    <div class="flex items-center mt-2">
                        <span class="text-sm font-medium px-3 py-1 rounded-full 
                            @if($booking->status === 'CONFIRMED') bg-green-100 text-green-800
                            @elseif($booking->status === 'PENDING') bg-yellow-100 text-yellow-800
                            @elseif($booking->status === 'CHECKED_IN') bg-blue-100 text-blue-800
                            @elseif($booking->status === 'CANCELLED') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $booking->status }}
                        </span>
                        <span class="ml-4 text-gray-600">Reference: {{ $booking->booking_reference }}</span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="text-right">
                        <div class="text-2xl font-bold text-gray-900">৳{{ number_format($booking->total_amount, 2) }}</div>
                        <div class="text-sm text-gray-500">Total amount</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Property Information</h3>
                    <div class="flex items-start">
                        @if($booking->property->primaryImage)
                            <img src="{{ Storage::url($booking->property->primaryImage->image_path) }}" 
                                 alt="{{ $booking->property->name }}"
                                 class="h-24 w-24 object-cover rounded-lg mr-4">
                        @elseif($booking->property->images->count() > 0)
                            <img src="{{ Storage::url($booking->property->images->first()->image_path) }}" 
                                 alt="{{ $booking->property->name }}"
                                 class="h-24 w-24 object-cover rounded-lg mr-4">
                        @else
                            <div class="h-24 w-24 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ $booking->property->address }}, {{ $booking->property->area }}, {{ $booking->property->city }}
                            </p>
                            <div class="mt-2 flex flex-wrap gap-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                    {{ $booking->property->type === 'HOSTEL' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                    {{ $booking->property->type_name }}
                                </span>
                                @if($booking->property->bedrooms)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ $booking->property->bedrooms }} Beds
                                    </span>
                                @endif
                                @if($booking->property->bathrooms)
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        {{ $booking->property->bathrooms }} Baths
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Details</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-sm text-gray-500">Check-in Date</div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('F d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Check-out Date</div>
                            <div class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('F d, Y') }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Duration</div>
                            <div class="font-medium">{{ $booking->duration_days }} days</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Booked On</div>
                            <div class="font-medium">{{ $booking->created_at->format('F d, Y h:i A') }}</div>
                        </div>
                    </div>
                    
                    <!-- Room Details (for hostel bookings) -->
                    @if($booking->room)
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h4 class="font-medium text-gray-900 mb-2">Room Information</h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">Room Type</div>
                                    <div class="font-medium">{{ $booking->room->room_type_name }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Room Number</div>
                                    <div class="font-medium">{{ $booking->room->room_number }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Capacity</div>
                                    <div class="font-medium">{{ $booking->room->capacity }} persons</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">Floor</div>
                                    <div class="font-medium">{{ $booking->room->floor_number ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Price Breakdown -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Price Breakdown</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Base Price</span>
                            <span class="font-medium">
                                ৳{{ number_format($booking->room_price_per_day * $booking->duration_days, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Commission ({{ $booking->property->commission_rate }}%)</span>
                            <span class="font-medium">৳{{ number_format($booking->commission_amount, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2">
                            <div class="flex justify-between">
                                <span class="text-lg font-medium text-gray-900">Total Amount</span>
                                <span class="text-2xl font-bold text-indigo-600">৳{{ number_format($booking->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($booking->payments->count() > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Payment History</h3>
                        <div class="space-y-4">
                            @foreach($booking->payments as $payment)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $payment->payment_reference }}</div>
                                            <div class="text-sm text-gray-600 mt-1">
                                                {{ $payment->created_at->format('M d, Y h:i A') }}
                                                @if($payment->paid_at)
                                                    • Paid: {{ $payment->paid_at->format('M d, Y') }}
                                                @endif
                                            </div>
                                            <div class="mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium
                                                    @if($payment->status === 'COMPLETED') bg-green-100 text-green-800
                                                    @elseif($payment->status === 'PENDING') bg-yellow-100 text-yellow-800
                                                    @elseif($payment->status === 'FAILED') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $payment->status }}
                                                </span>
                                                <span class="ml-2 text-sm text-gray-600">
                                                    {{ $payment->payment_method }}
                                                </span>
                                            </div>
                                            @if($payment->transaction_id)
                                                <div class="mt-1 text-sm text-gray-600">
                                                    Transaction: {{ $payment->transaction_id }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <div class="text-lg font-semibold 
                                                @if($payment->status === 'COMPLETED') text-green-600
                                                @elseif($payment->status === 'PENDING') text-yellow-600
                                                @else text-red-600 @endif">
                                                ৳{{ number_format($payment->amount, 2) }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Commission: ৳{{ number_format($payment->commission_amount, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Actions Card -->
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                    
                    <!-- Payment Status -->
                    @if($booking->status === 'PENDING' && !$booking->isPaid())
                        <!-- Payment Button -->
                        <a href="{{ route('rental.booking.payment', $booking) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 mb-3">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                            </svg>
                            Make Payment
                        </a>
                    @elseif($booking->isPaid())
                        <div class="w-full inline-flex justify-center items-center px-4 py-3 border border-green-300 text-base font-medium rounded-md text-green-700 bg-green-50 mb-3">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Payment Completed
                        </div>
                    @endif
                    
                    <!-- Cancel Booking Button (only for PENDING bookings) -->
                    @if($booking->status === 'PENDING')
                        <form method="POST" action="{{ route('rental.booking.cancel', $booking) }}" class="mb-3">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('Are you sure you want to cancel this booking? This action cannot be undone.')"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel Booking
                            </button>
                        </form>
                    @endif
                    
                    <!-- View Property -->
                    <a href="{{ route('rental.property.details', $booking->property) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        View Property
                    </a>
                    
                    <!-- Contact Owner (for active bookings) -->
                    @if($booking->status === 'CONFIRMED' || $booking->status === 'CHECKED_IN')
                        <button type="button"
                                onclick="alert('Owner Contact: {{ $booking->property->owner->phone ?? "Contact details not available" }}')"
                                class="w-full mt-3 inline-flex justify-center items-center px-4 py-3 border border-blue-300 text-base font-medium rounded-md text-blue-700 bg-blue-50 hover:bg-blue-100">
                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            Contact Owner
                        </button>
                    @endif
                </div>

                <!-- Contact Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Need Help?</h3>
                    <div class="space-y-3">
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500">Support</div>
                                <div class="font-medium">+880 1234 567890</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500">Email</div>
                                <div class="font-medium">support@yourdomain.com</div>
                            </div>
                        </div>
                        <div class="flex items-center">
                            <svg class="h-5 w-5 text-gray-400 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <div class="text-sm text-gray-500">Hours</div>
                                <div class="font-medium">9:00 AM - 6:00 PM (Daily)</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Booking Timeline</h3>
                    <div class="space-y-4">
                        <!-- Booking Created -->
                        <div class="flex items-start">
                            <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">Booking Created</div>
                                <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y h:i A') }}</div>
                            </div>
                        </div>
                        
                        <!-- Payment Status -->
                        @if($booking->isPaid())
                            <div class="flex items-start">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Payment Confirmed</div>
                                    <div class="text-sm text-gray-500">
                                        {{ $booking->payments->where('status', 'COMPLETED')->first()->paid_at->format('M d, Y h:i A') ?? 'Payment completed' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Booking Status -->
                        @if($booking->status === 'CONFIRMED')
                            <div class="flex items-start">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Booking Confirmed</div>
                                    <div class="text-sm text-gray-500">Ready for check-in</div>
                                </div>
                            </div>
                        @elseif($booking->status === 'CHECKED_IN')
                            <div class="flex items-start">
                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Checked In</div>
                                    <div class="text-sm text-gray-500">Currently staying</div>
                                </div>
                            </div>
                        @elseif($booking->status === 'CHECKED_OUT')
                            <div class="flex items-start">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center flex-shrink-0">
                                    <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <div class="font-medium text-gray-900">Checked Out</div>
                                    <div class="text-sm text-gray-500">Stay completed</div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Important Dates -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Important Dates</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Check-in Date</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Check-in Time</span>
                            <span class="font-medium">After 2:00 PM</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Check-out Date</span>
                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Check-out Time</span>
                            <span class="font-medium">Before 12:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Booking Modal -->
<div id="cancelModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Cancel Booking</h3>
        <p class="text-sm text-gray-600 mb-6">Are you sure you want to cancel this booking? This action cannot be undone.</p>
        <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeCancelModal()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Keep Booking
            </button>
            <form method="POST" action="{{ route('rental.booking.cancel', $booking) }}" id="cancelForm">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Cancel Booking
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function openCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

// Close modal when clicking outside
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
@endsection