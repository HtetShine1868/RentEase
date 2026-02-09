@extends('layouts.app')

@section('title', 'Booking Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Booking Details</h1>
                    <p class="mt-2 text-gray-600">Manage your stay, submit reviews, and extend if needed</p>
                </div>
                <a href="{{ route('rental.index') }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
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
                        <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Property Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start">
                                <div class="h-16 w-16 bg-gradient-to-r from-indigo-100 to-blue-100 rounded-lg flex items-center justify-center mr-4">
                                    <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900">{{ $booking->property->name }}</h2>
                                    <p class="text-gray-600 mt-1">{{ $booking->property->address }}</p>
                                    <p class="text-sm text-gray-500">{{ $booking->property->city }}, {{ $booking->property->area }}</p>
                                    <div class="mt-2">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                                            @if($booking->status === 'CHECKED_IN') bg-green-100 text-green-800
                                            @elseif($booking->status === 'CONFIRMED') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-2xl font-bold text-gray-900">৳{{ number_format($booking->total_amount, 2) }}</div>
                                <div class="text-sm text-gray-500">Total Amount</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stay Timeline -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Stay Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="relative">
                            <!-- Timeline -->
                            <div class="flex justify-between items-center mb-8">
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">Check-in</div>
                                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</div>
                                </div>
                                
                                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                                
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">Current</div>
                                    <div class="text-sm text-gray-600">{{ now()->format('M d, Y') }}</div>
                                </div>
                                
                                <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                                
                                <div class="text-center">
                                    <div class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center mx-auto mb-2">
                                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </div>
                                    <div class="text-sm font-semibold text-gray-900">Check-out</div>
                                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</div>
                                </div>
                            </div>
                            
                            <!-- Days Counter -->
                            @php
                                $totalDays = \Carbon\Carbon::parse($booking->check_out)->diffInDays(\Carbon\Carbon::parse($booking->check_in));
                                $daysPassed = \Carbon\Carbon::parse(now())->diffInDays(\Carbon\Carbon::parse($booking->check_in));
                                $daysRemaining = \Carbon\Carbon::parse($booking->check_out)->diffInDays(now());
                                $progress = min(100, max(0, ($daysPassed / $totalDays) * 100));
                            @endphp
                            
                            <div class="mt-8">
                                <div class="flex justify-between text-sm mb-2">
                                    <span class="text-gray-600">Stay Progress</span>
                                    <span class="font-semibold">{{ $daysPassed }} of {{ $totalDays }} days</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-3">
                                    <div class="bg-gradient-to-r from-green-400 to-blue-500 h-3 rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                                
                                <!-- Days Remaining Alert -->
                                @if($daysRemaining <= 3)
                                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-red-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.771-.833-2.502 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <div>
                                                <h4 class="text-sm font-semibold text-red-800">Stay Ending Soon!</h4>
                                                <p class="text-sm text-red-600">Only {{ $daysRemaining }} days remaining. Extend your stay if needed.</p>
                                            </div>
                                        </div>
                                    </div>
                                @elseif($daysRemaining <= 7)
                                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-yellow-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.771-.833-2.502 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                            </svg>
                                            <div>
                                                <h4 class="text-sm font-semibold text-yellow-800">Stay Ending in {{ $daysRemaining }} Days</h4>
                                                <p class="text-sm text-yellow-600">Consider extending your stay if you need more time.</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions Section -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Manage Your Stay</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Check-in Button -->
                            @if($booking->status === 'CONFIRMED' && now()->toDateString() >= \Carbon\Carbon::parse($booking->check_in)->toDateString())
                                <form method="POST" action="{{ route('bookings.check-in', $booking) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="w-full flex items-center justify-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 shadow-sm transition duration-300">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Check-in Now
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Check-out Button -->
                            @if($booking->status === 'CHECKED_IN')
                                <form method="POST" action="{{ route('bookings.check-out', $booking) }}">
                                    @csrf
                                    <button type="submit" 
                                            onclick="return confirm('Are you sure you want to check out? This will end your stay.')"
                                            class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition duration-300">
                                        <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        Check-out
                                    </button>
                                </form>
                            @endif
                            
                            <!-- Extend Stay Button -->
                            @if($booking->status === 'CHECKED_IN' && $daysRemaining <= 7)
                                <button type="button"
                                        onclick="openExtendModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}', {{ $booking->room ? $booking->room->total_price : $booking->property->total_price }})"
                                        class="w-full flex items-center justify-center px-4 py-3 border border-blue-300 rounded-lg text-sm font-medium text-blue-700 bg-white hover:bg-blue-50 shadow-sm transition duration-300">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Extend Stay
                                </button>
                            @endif
                            
                            <!-- Report Issue Button -->
                            <button type="button"
                                    onclick="openComplaintModal({{ $booking->id }}, 'property', {{ $booking->property_id }})"
                                    class="w-full flex items-center justify-center px-4 py-3 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 shadow-sm transition duration-300">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.771-.833-2.502 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                                Report Issue
                            </button>
                            
                            <!-- Write Review Button -->
                            @if($booking->status === 'CHECKED_IN' && !$hasReviewed)
                                <button type="button"
                                        onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                        class="w-full flex items-center justify-center px-4 py-3 border border-green-300 rounded-lg text-sm font-medium text-green-700 bg-white hover:bg-green-50 shadow-sm transition duration-300">
                                    <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                    </svg>
                                    Write Review
                                </button>
                            @endif
                            
                            <!-- View Invoice -->
                            <a href="{{ route('bookings.invoice', $booking) }}" 
                               class="w-full flex items-center justify-center px-4 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 shadow-sm transition duration-300">
                                <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                View Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Booking Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Booking Details</h3>
                    </div>
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Booking Reference:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Booking Date:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}</dd>
                            </div>
                            @if($booking->room)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Room:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->room->room_type }} ({{ $booking->room->room_number }})</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Room Price/Day:</dt>
                                    <dd class="text-sm font-medium text-gray-900">৳{{ number_format($booking->room_price_per_day, 2) }}</dd>
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Property Type:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $booking->property->type }}</dd>
                                </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Duration:</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $booking->duration_days }} days</dd>
                            </div>
                            <div class="flex justify-between">
                                <dt class="text-sm text-gray-600">Commission:</dt>
                                <dd class="text-sm font-medium text-gray-900">৳{{ number_format($booking->commission_amount, 2) }}</dd>
                            </div>
                            <div class="pt-4 border-t">
                                <div class="flex justify-between">
                                    <dt class="text-base font-semibold text-gray-900">Total Amount:</dt>
                                    <dd class="text-base font-bold text-gray-900">৳{{ number_format($booking->total_amount, 2) }}</dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Payment Status -->
                @if($booking->payments->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">Payment Status</h3>
                        </div>
                        <div class="p-6">
                            @foreach($booking->payments as $payment)
                                <div class="flex justify-between items-center mb-3 pb-3 {{ !$loop->last ? 'border-b' : '' }}">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $payment->payment_reference }}</div>
                                        <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}</div>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($payment->status === 'COMPLETED') bg-green-100 text-green-800
                                        @elseif($payment->status === 'PENDING') bg-yellow-100 text-yellow-800
                                        @elseif($payment->status === 'FAILED') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $payment->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Quick Contact -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Need Help?</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <a href="{{ route('complaints.index') }}" 
                               class="flex items-center text-sm text-indigo-600 hover:text-indigo-900 hover:underline">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                View All Complaints
                            </a>
                            <a href="{{ route('rental.index') }}" 
                               class="flex items-center text-sm text-gray-600 hover:text-gray-900 hover:underline">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Back to Rental Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include modals from the main rental page -->
@include('rental.partials.modals')

@endsection