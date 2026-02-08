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

        <!-- Current Rental - Show only if check_out date is in future -->
        @if($currentBookings->count() > 0)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Current Stay</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($currentBookings as $booking)
                        @php
                            $isActive = \Carbon\Carbon::parse($booking->check_out)->isFuture();
                            $canExtend = $isActive && \Carbon\Carbon::parse($booking->check_out)->diffInDays(now()) <= 7;
                            $hasReviewed = $booking->propertyRating()->where('user_id', auth()->id())->exists();
                        @endphp
                        
                        @if($isActive)
                            <div class="bg-white rounded-lg shadow overflow-hidden border border-green-200">
                                <div class="p-6">
                                    <!-- Property Info -->
                                    <div class="flex items-start mb-4">
                                        <div class="h-16 w-16 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $booking->property->name }}</h3>
                                            <p class="text-sm text-gray-600">{{ $booking->property->address }}</p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $booking->property->city }}, {{ $booking->property->area }}</p>
                                        </div>
                                    </div>

                                    <!-- Booking Details -->
                                    <div class="space-y-2 mb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Check-in:</span>
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Check-out:</span>
                                            <span class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Duration:</span>
                                            <span class="font-medium">{{ $booking->duration_days }} days</span>
                                        </div>
                                        @if($booking->room)
                                            <div class="flex justify-between text-sm">
                                                <span class="text-gray-500">Room:</span>
                                                <span class="font-medium">{{ $booking->room->room_type }} ({{ $booking->room->room_number }})</span>
                                            </div>
                                        @endif
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-500">Total Amount:</span>
                                            <span class="font-medium">৳{{ number_format($booking->total_amount, 2) }}</span>
                                        </div>
                                    </div>

                                    <!-- Days remaining -->
                                    <div class="mb-4 p-2 bg-blue-50 rounded">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-blue-700">Days Remaining:</span>
                                            <span class="font-medium text-blue-700">
                                                {{ \Carbon\Carbon::parse($booking->check_out)->diffInDays(now()) }} days
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="mb-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            @if($booking->status === 'CHECKED_IN') bg-green-100 text-green-800
                                            @elseif($booking->status === 'CONFIRMED') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            @if($booking->status === 'CHECKED_IN')
                                                <svg class="mr-1.5 h-2 w-2 text-green-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Currently Staying
                                            @elseif($booking->status === 'CONFIRMED')
                                                <svg class="mr-1.5 h-2 w-2 text-yellow-400" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3" />
                                                </svg>
                                                Confirmed
                                            @else
                                                {{ $booking->status }}
                                            @endif
                                        </span>
                                    </div>

                                    <!-- Actions -->
                                    <div class="space-y-2">
                                        @if($booking->status === 'CONFIRMED')
                                            <form method="POST" action="{{ route('bookings.check-in', $booking->id) }}">
                                                @csrf
                                                <button type="submit" 
                                                        class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                    Check In Now
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($booking->status === 'CHECKED_IN')
                                            <form method="POST" action="{{ route('bookings.check-out', $booking->id) }}">
                                                @csrf
                                                <button type="submit" 
                                                        onclick="return confirm('Are you sure you want to check out? This action cannot be undone.')"
                                                        class="w-full inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                    Check Out
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Quick Actions -->
                                        <div class="grid grid-cols-2 gap-2">
                                            <a href="{{ route('bookings.show', $booking->id) }}" 
                                               class="inline-flex justify-center items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Details
                                            </a>
                                            
                                            @if($canExtend)
                                                <button type="button" 
                                                        onclick="openExtendModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}', {{ $booking->room ? $booking->room->total_price : $booking->property->total_price }})"
                                                        class="inline-flex justify-center items-center px-3 py-2 border border-blue-300 text-sm font-medium rounded-md text-blue-700 bg-white hover:bg-blue-50">
                                                    Extend Stay
                                                </button>
                                            @endif
                                            
                                            <button type="button" 
                                                    onclick="openComplaintModal({{ $booking->id }}, 'property', {{ $booking->property_id }})"
                                                    class="inline-flex justify-center items-center px-3 py-2 border border-red-300 text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                                                Report Issue
                                            </button>
                                            
                                            @if($booking->status === 'CHECKED_IN' && !$hasReviewed)
                                                <button type="button"
                                                        onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                                        class="inline-flex justify-center items-center px-3 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                                                    Write Review
                                                </button>
                                            @endif
                                        </div>
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
                <div class="bg-white shadow overflow-hidden rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Confirmed Bookings</h3>
                    </div>
                    <div class="divide-y divide-gray-200">
                        @foreach($upcomingBookings as $booking)
                            <div class="px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                            <div class="flex items-center text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('bookings.show', $booking->id) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            View Details
                                        </a>
                                        @if($booking->property->type === 'APARTMENT')
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                Apartment
                                            </span>
                                        @else
                                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                                {{ $booking->room ? $booking->room->room_type : 'Room' }}
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
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Booking History</h3>
                    </div>
                    
                    @if($pastBookings->count() > 0)
                        <div class="divide-y divide-gray-200">
                            @foreach($pastBookings as $booking)
                                @php
                                    $hasReviewed = $booking->propertyRating()->where('user_id', auth()->id())->exists();
                                @endphp
                                <div class="px-6 py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center flex-1">
                                            <div class="h-16 w-16 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
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
                                               class="text-sm text-indigo-600 hover:text-indigo-900">
                                                View Details
                                            </a>
                                            
                                            <!-- Review Button -->
                                            @if($booking->status === 'CHECKED_OUT' && !$hasReviewed)
                                                <button type="button"
                                                        onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                                        class="text-sm text-green-600 hover:text-green-900">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-sm font-medium text-gray-900">No booking history</h3>
                            <p class="mt-1 text-sm text-gray-500">Your past bookings will appear here.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar: Complaints & Reviews -->
            <div class="space-y-6">
                <!-- Pending Reviews -->
                @if($reviewableBookings->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Pending Reviews</h3>
                        </div>
                        <div class="p-6">
                            <p class="text-sm text-gray-600 mb-4">Please review your recent stays:</p>
                            <div class="space-y-3">
                                @foreach($reviewableBookings as $booking)
                                    <div class="border border-gray-200 rounded-lg p-3">
                                        <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                        <p class="text-xs text-gray-600 mt-1">
                                            Stayed: {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                        </p>
                                        <button type="button"
                                                onclick="openReviewModal({{ $booking->id }}, '{{ addslashes($booking->property->name) }}')"
                                                class="mt-2 w-full inline-flex justify-center items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            Write Review
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Recent Complaints -->
                @if($complaints->count() > 0)
                    <div class="bg-white shadow rounded-lg">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">Recent Complaints</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @foreach($complaints as $complaint)
                                <div class="px-6 py-4">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $complaint->title }}</h4>
                                            <div class="mt-1 flex items-center">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    @if($complaint->status === 'OPEN') bg-yellow-100 text-yellow-800
                                                    @elseif($complaint->status === 'RESOLVED') bg-green-100 text-green-800
                                                    @elseif($complaint->status === 'CLOSED') bg-gray-100 text-gray-800
                                                    @else bg-blue-100 text-blue-800 @endif">
                                                    {{ $complaint->status }}
                                                </span>
                                                <span class="ml-2 text-xs text-gray-600">
                                                    {{ \Carbon\Carbon::parse($complaint->created_at)->format('M d') }}
                                                </span>
                                            </div>
                                            @if($complaint->related_type === 'PROPERTY')
                                                <p class="text-xs text-gray-500 mt-1">{{ $complaint->property->name ?? 'Property' }}</p>
                                            @endif
                                        </div>
                                        @if($complaint->resolution)
                                            <span class="text-xs text-green-600">Resolved</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- No Current Rental Message -->
                @if($currentBookings->count() === 0 && $upcomingBookings->count() === 0)
                    <div class="bg-white shadow rounded-lg p-6 text-center">
                        <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <h3 class="mt-4 text-sm font-medium text-gray-900">No Active Rental</h3>
                        <p class="mt-1 text-sm text-gray-500">Find your perfect place to stay.</p>
                        <div class="mt-6">
                            <a href="{{ route('properties.search') }}" 
                               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                Search for Rental
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Submit Complaint</h3>
        </div>
        <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
            @csrf
            <input type="hidden" name="booking_id" id="complaintBookingId">
            <input type="hidden" name="complaint_type" id="complaintType">
            <input type="hidden" name="related_id" id="relatedId">
            <input type="hidden" name="related_type" id="relatedType">
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Brief description of the issue">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" required
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                  placeholder="Describe the issue in detail..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complaint Type</label>
                        <select name="complaint_type_display" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="updateComplaintType(this.value)">
                            <option value="">Select type</option>
                            <option value="PROPERTY">Property Issue</option>
                            <option value="FOOD_SERVICE">Food Service</option>
                            <option value="LAUNDRY_SERVICE">Laundry Service</option>
                            <option value="USER">User Issue</option>
                            <option value="SYSTEM">System Issue</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="MEDIUM">Medium</option>
                            <option value="LOW">Low</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeComplaintModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Review Modal -->
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

<!-- Extend Stay Modal -->
<div id="extendModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Extend Your Stay</h3>
            <p id="extendPropertyName" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <form id="extendForm" method="POST" action="{{ route('bookings.extend') }}">
            @csrf
            <input type="hidden" name="booking_id" id="extendBookingId">
            <input type="hidden" name="daily_price" id="extendDailyPrice">
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Check-out</label>
                        <input type="text" id="currentCheckout" readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Check-out Date</label>
                        <input type="date" name="new_check_out" required
                               id="newCheckoutDate"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Extension Days</label>
                        <input type="number" id="extensionDays" readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div class="p-3 bg-blue-50 rounded">
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-700">Daily Rate:</span>
                            <span class="font-medium text-blue-700" id="dailyRateDisplay"></span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-blue-700">Total Extension Cost:</span>
                            <span class="font-bold text-blue-700" id="totalExtensionCost"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeExtendModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Extend Stay
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Complaint Modal Functions
let currentComplaintData = {};

function openComplaintModal(bookingId, relatedType = 'property', relatedId = null) {
    currentComplaintData = {
        booking_id: bookingId,
        related_type: relatedType.toUpperCase(),
        related_id: relatedId
    };
    
    document.getElementById('complaintBookingId').value = bookingId;
    document.getElementById('relatedId').value = relatedId;
    document.getElementById('relatedType').value = relatedType.toUpperCase();
    
    document.getElementById('complaintModal').classList.remove('hidden');
}

function updateComplaintType(type) {
    document.getElementById('complaintType').value = type;
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.getElementById('complaintForm').reset();
    currentComplaintData = {};
}

// Review Modal Functions
let currentReviewBookingId = null;
let currentPropertyId = null;

function openReviewModal(bookingId, propertyName) {
    currentReviewBookingId = bookingId;
    document.getElementById('reviewBookingId').value = bookingId;
    document.getElementById('reviewPropertyName').textContent = propertyName;
    document.getElementById('reviewModal').classList.remove('hidden');
    
    // Reset ratings
    resetRatings();
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    resetRatings();
    currentReviewBookingId = null;
    currentPropertyId = null;
}

// Extend Stay Modal Functions
function openExtendModal(bookingId, propertyName, dailyPrice) {
    document.getElementById('extendBookingId').value = bookingId;
    document.getElementById('extendDailyPrice').value = dailyPrice;
    document.getElementById('extendPropertyName').textContent = propertyName;
    document.getElementById('dailyRateDisplay').textContent = '৳' + dailyPrice.toFixed(2);
    
    const checkoutInput = document.getElementById('newCheckoutDate');
    const today = new Date().toISOString().split('T')[0];
    checkoutInput.min = today;
    
    document.getElementById('extendModal').classList.remove('hidden');
    
    // Initialize date calculation
    updateExtensionCost();
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
    document.getElementById('extendForm').reset();
}

function updateExtensionCost() {
    const dailyPrice = parseFloat(document.getElementById('extendDailyPrice').value);
    const newCheckout = document.getElementById('newCheckoutDate').value;
    
    if (newCheckout) {
        const currentDate = new Date();
        const newDate = new Date(newCheckout);
        const diffTime = Math.abs(newDate - currentDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        document.getElementById('extensionDays').value = diffDays;
        document.getElementById('totalExtensionCost').textContent = '৳' + (diffDays * dailyPrice).toFixed(2);
    }
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

// Event Listeners
document.getElementById('newCheckoutDate').addEventListener('change', updateExtensionCost);

document.getElementById('complaintModal').addEventListener('click', function(e) {
    if (e.target === this) closeComplaintModal();
});

document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) closeReviewModal();
});

document.getElementById('extendModal').addEventListener('click', function(e) {
    if (e.target === this) closeExtendModal();
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

// Initialize current checkout date
@if($currentBookings->count() > 0)
    @foreach($currentBookings as $booking)
        document.addEventListener('DOMContentLoaded', function() {
            const checkoutElements = document.querySelectorAll('[data-booking-id="{{ $booking->id }}"]');
            checkoutElements.forEach(el => {
                el.textContent = '{{ \Carbon\Carbon::parse($booking->check_out)->format("Y-m-d") }}';
            });
        });
    @endforeach
@endif
</script>
@endsection