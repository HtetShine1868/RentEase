@extends('layouts.app')

@section('title', 'My Rental')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Rental Dashboard</h1>
                    <p class="mt-2 text-gray-600">Manage your current rental, view history, and submit requests</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('properties.search') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Find New Rental
                    </a>
                </div>
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

        <!-- Current Rental Cards -->
        @if($currentBookings->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Current Stay</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($currentBookings as $booking)
                        @php
                            $isActive = \Carbon\Carbon::parse($booking->check_out)->isFuture();
                            $daysRemaining = \Carbon\Carbon::parse($booking->check_out)->diffInDays(now());
                            $canExtend = $isActive && $daysRemaining <= 7;
                            $hasReviewed = $booking->property->propertyRatings()->where('user_id', auth()->id())->exists();
                            $canCheckIn = $booking->status === 'CONFIRMED' && now()->toDateString() >= \Carbon\Carbon::parse($booking->check_in)->toDateString();
                        @endphp
                        
                        @if($isActive)
                            <div class="bg-white rounded-lg shadow-lg overflow-hidden border border-gray-200 hover:border-indigo-300 transition duration-300">
                                <!-- Status Banner -->
                                <div class="px-4 py-2 bg-indigo-600 text-white">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-semibold">
                                            @if($booking->status === 'CHECKED_IN')
                                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                                Currently Staying
                                            @else
                                                <svg class="inline w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                                </svg>
                                                Confirmed
                                            @endif
                                        </span>
                                        <span class="text-xs bg-white bg-opacity-20 px-2 py-1 rounded">
                                            @if($booking->property->type === 'APARTMENT')
                                                Apartment
                                            @else
                                                {{ $booking->room->room_type ?? 'Room' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="p-5">
                                    <!-- Property Info -->
                                    <div class="mb-4">
                                        <div class="flex items-start">
                                            <div class="h-12 w-12 bg-gradient-to-r from-indigo-100 to-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="font-bold text-gray-900 text-lg">{{ $booking->property->name }}</h3>
                                                <p class="text-sm text-gray-600">{{ $booking->property->city }}, {{ $booking->property->area }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Progress Bar for Days Remaining -->
                                    <div class="mb-4">
                                        <div class="flex justify-between text-sm mb-1">
                                            <span class="text-gray-600">Days Remaining</span>
                                            <span class="font-semibold text-indigo-600">{{ $daysRemaining }} days</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            @php
                                                $totalDays = \Carbon\Carbon::parse($booking->check_out)->diffInDays(\Carbon\Carbon::parse($booking->check_in));
                                                $progress = min(100, max(0, (($totalDays - $daysRemaining) / $totalDays) * 100));
                                                $progressColor = $daysRemaining <= 3 ? 'bg-red-500' : ($daysRemaining <= 7 ? 'bg-yellow-500' : 'bg-green-500');
                                            @endphp
                                            <div class="h-2 rounded-full {{ $progressColor }}" style="width: {{ $progress }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Stay Duration -->
                                    <div class="bg-gray-50 rounded-lg p-3 mb-4">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="text-center">
                                                <div class="text-xs text-gray-500">Check-in</div>
                                                <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->check_in)->format('Y') }}</div>
                                            </div>
                                            <div class="text-center">
                                                <div class="text-xs text-gray-500">Check-out</div>
                                                <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($booking->check_out)->format('Y') }}</div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-2">
                                            <div class="text-xs text-gray-500">Duration</div>
                                            <div class="font-semibold text-gray-900">{{ $booking->duration_days }} days</div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="mt-4">
                                        <a href="{{ route('rental.booking-details', $booking) }}" 
                                           class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 shadow-sm transition duration-300">
                                            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            View Details & Manage
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Upcoming Bookings -->
        @if($upcomingBookings->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Upcoming Stays</h2>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Confirmed Bookings</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($upcomingBookings as $booking)
                            <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-10 w-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $booking->property->name }}</h4>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('bookings.show', $booking->id) }}" 
                                           class="text-sm text-indigo-600 hover:text-indigo-900 font-medium hover:underline">
                                            View Details
                                        </a>
                                        @if($booking->property->type === 'APARTMENT')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Apartment
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ $booking->room->room_type ?? 'Room' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Past Bookings & Reviews -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Past Bookings -->
            <div class="lg:col-span-2">
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Booking History</h3>
                    </div>
                    
                    @if($pastBookings->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($pastBookings as $booking)
                                @php
                                    $hasReviewed = $booking->property->propertyRatings()->where('user_id', auth()->id())->exists();
                                @endphp
                                <div class="px-6 py-4 hover:bg-gray-50 transition duration-150">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">{{ $booking->property->name }}</h4>
                                                <div class="flex items-center text-sm text-gray-600 mt-1">
                                                    <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                                </div>
                                                <div class="mt-1 flex items-center space-x-2">
                                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                        @if($booking->status === 'CHECKED_OUT') bg-green-100 text-green-800
                                                        @elseif($booking->status === 'CANCELLED') bg-red-100 text-red-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $booking->status }}
                                                    </span>
                                                    <span class="text-xs text-gray-600">
                                                        {{ $booking->duration_days }} days • ৳{{ number_format($booking->total_amount, 2) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex flex-col items-end space-y-2">
                                            <a href="{{ route('bookings.show', $booking->id) }}" 
                                               class="text-sm text-indigo-600 hover:text-indigo-900 font-medium hover:underline">
                                                View Details
                                            </a>
                                            
                                            <!-- Review Button -->
                                            @if($booking->status === 'CHECKED_OUT' && !$hasReviewed)
                                                <button type="button"
                                                        onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                                        class="text-sm text-green-600 hover:text-green-900 hover:underline">
                                                    Write Review
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($pastBookings->hasPages())
                            <div class="px-6 py-4 border-t border-gray-200">
                                {{ $pastBookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="px-6 py-8 text-center">
                            <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">No booking history</h3>
                            <p class="mt-1 text-sm text-gray-500">Your past bookings will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar: Quick Stats & Actions -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="text-center p-3 bg-blue-50 rounded-lg">
                                <div class="text-2xl font-bold text-blue-600">{{ $currentBookings->count() }}</div>
                                <div class="text-xs text-blue-800 mt-1">Current Stays</div>
                            </div>
                            <div class="text-center p-3 bg-green-50 rounded-lg">
                                <div class="text-2xl font-bold text-green-600">{{ $pastBookings->total() }}</div>
                                <div class="text-xs text-green-800 mt-1">Total Bookings</div>
                            </div>
                            <div class="text-center p-3 bg-yellow-50 rounded-lg col-span-2">
                                <div class="text-2xl font-bold text-yellow-600">{{ $reviewableBookings->count() }}</div>
                                <div class="text-xs text-yellow-800 mt-1">Pending Reviews</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                @if($reviewableBookings->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">Pending Reviews</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 mb-4">Please review your recent stays:</p>
                            <div class="space-y-3">
                                @foreach($reviewableBookings as $booking)
                                    <div class="border border-green-200 rounded-lg p-3 bg-green-50">
                                        <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Stayed: {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                        </p>
                                        <button type="button"
                                                onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                                class="mt-2 w-full inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 transition duration-150">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Write Review
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- No Current Rental Message -->
                @if($currentBookings->count() === 0 && $upcomingBookings->count() === 0)
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">No Active Rental</h3>
                        <p class="mt-1 text-sm text-gray-500">Find your perfect place to stay.</p>
                        <div class="mt-6">
                            <a href="{{ route('properties.search') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gradient-to-r from-indigo-600 to-blue-600 hover:from-indigo-700 hover:to-blue-700 transition duration-150">
                                Search for Rental
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Review Modal (keep from previous version) -->
<div id="reviewModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Write Review</h3>
            <p id="reviewPropertyName" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <form id="reviewForm" method="POST" action="{{ route('property-ratings.store') }}">
            @csrf
            <input type="hidden" name="booking_id" id="reviewBookingId">
            <input type="hidden" name="property_id" id="reviewPropertyId">
            
            <div class="p-6">
                <div class="space-y-6">
                    <!-- Ratings -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cleanliness</label>
                            <div class="flex space-x-1" id="cleanlinessRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('cleanliness', {{ $i }})"
                                            class="text-gray-300 hover:text-yellow-400 rating-star"
                                            data-rating="{{ $i }}"
                                            data-category="cleanliness">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="cleanliness_rating" id="cleanlinessInput" value="0" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                            <div class="flex space-x-1" id="locationRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('location', {{ $i }})"
                                            class="text-gray-300 hover:text-yellow-400 rating-star"
                                            data-rating="{{ $i }}"
                                            data-category="location">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="location_rating" id="locationInput" value="0" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Value for Money</label>
                            <div class="flex space-x-1" id="valueRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('value', {{ $i }})"
                                            class="text-gray-300 hover:text-yellow-400 rating-star"
                                            data-rating="{{ $i }}"
                                            data-category="value">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="value_rating" id="valueInput" value="0" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                            <div class="flex space-x-1" id="serviceRating">
                                @for($i = 1; $i <= 5; $i++)
                                    <button type="button" 
                                            onclick="setRating('service', {{ $i }})"
                                            class="text-gray-300 hover:text-yellow-400 rating-star"
                                            data-rating="{{ $i }}"
                                            data-category="service">
                                        <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="service_rating" id="serviceInput" value="0" required>
                        </div>
                    </div>
                    
                    <!-- Comment -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                        <textarea name="comment" rows="4"
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                  placeholder="Share your experience..."></textarea>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeReviewModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Review Modal Functions
let currentReviewBookingId = null;

function openReviewModal(bookingId, propertyName) {
    currentReviewBookingId = bookingId;
    document.getElementById('reviewPropertyName').textContent = propertyName;
    document.getElementById('reviewBookingId').value = bookingId;
    document.getElementById('reviewModal').classList.remove('hidden');
    
    // Reset ratings
    resetRatings();
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    resetRatings();
    currentReviewBookingId = null;
}

// Rating Functions
function resetRatings() {
    const categories = ['cleanliness', 'location', 'value', 'service'];
    categories.forEach(category => {
        setRating(category, 0);
    });
}

function setRating(category, rating) {
    // Update input value
    document.getElementById(`${category}Input`).value = rating;
    
    // Update star display
    const stars = document.querySelectorAll(`[data-category="${category}"]`);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

// Close modal when clicking outside
document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeReviewModal();
});

// Form validation
document.getElementById('reviewForm').addEventListener('submit', function(e) {
    const categories = ['cleanliness', 'location', 'value', 'service'];
    const hasRating = categories.some(category => {
        return parseInt(document.getElementById(`${category}Input`).value) > 0;
    });
    
    if (!hasRating) {
        e.preventDefault();
        alert('Please provide at least one rating before submitting.');
    }
});
</script>
@endsection