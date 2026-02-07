@extends('layouts.app')

@section('title', 'My Rental Dashboard')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Rental Dashboard</h1>
                    <p class="mt-2 text-gray-600">Manage your rentals, complaints, and reviews</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('rental.search') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search for Rental
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Bookings -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Bookings</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalBookings }}</div>
                    </div>
                </div>
            </div>

            <!-- Active Bookings -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Active Bookings</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $activeBookings }}</div>
                    </div>
                </div>
            </div>

            <!-- Total Spent -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Spent</div>
                        <div class="text-2xl font-bold text-gray-900">৳{{ number_format($totalSpent, 2) }}</div>
                    </div>
                </div>
            </div>

            <!-- Pending Reviews -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Pending Reviews</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $pendingReviews }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Active Rentals -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Current Active Rentals</h3>
            </div>
            
            @if($currentRentals->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($currentRentals as $booking)
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center">
                                        <div class="h-12 w-12 bg-gray-100 rounded-lg flex items-center justify-center mr-4">
                                            @if($booking->property->primaryImage)
                                                <img src="{{ Storage::url($booking->property->primaryImage->image_path) }}" 
                                                     alt="{{ $booking->property->name }}"
                                                     class="h-full w-full object-cover rounded-lg">
                                            @else
                                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                            <div class="flex items-center mt-1 text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                {{ $booking->property->area }}, {{ $booking->property->city }}
                                            </div>
                                            <div class="flex items-center mt-1">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                    {{ $booking->status }}
                                                </span>
                                                <span class="ml-2 text-sm text-gray-600">
                                                    {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - 
                                                    {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-4 md:mt-0 md:ml-6">
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">৳{{ number_format($booking->total_amount, 2) }}</div>
                                        <div class="text-sm text-gray-500">Total amount</div>
                                    </div>
                                    <div class="mt-3 flex space-x-2">
                                        <a href="{{ route('rental.booking.details', $booking) }}" 
                                           class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            View Details
                                        </a>
                                        @if($booking->canBeReviewed())
                                            <button onclick="openReviewModal({{ $booking->id }})"
                                                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                                Add Review
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No Active Rentals</h3>
                    <p class="mt-2 text-gray-500 max-w-md mx-auto">
                        You don't have any active rentals right now. Find your perfect place to stay.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('rental.search') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Search for Rental
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Tabs for Rental Management -->
        <div class="bg-white rounded-lg shadow">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex" aria-label="Tabs">
                    <button @click="activeTab = 'bookings'" 
                            :class="activeTab === 'bookings' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        Booking History
                    </button>
                    <button @click="activeTab = 'complaints'" 
                            :class="activeTab === 'complaints' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        My Complaints
                    </button>
                    <button @click="activeTab = 'reviews'" 
                            :class="activeTab === 'reviews' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="w-1/3 py-4 px-1 text-center border-b-2 font-medium text-sm">
                        My Reviews
                    </button>
                </nav>
            </div>
            
            <div class="p-6" x-data="{ activeTab: 'bookings' }">
                <!-- Booking History Tab -->
                <div x-show="activeTab === 'bookings'" x-cloak>
                    @if($bookings->count() > 0)
                        <div class="space-y-4">
                            @foreach($bookings as $booking)
                                <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-300 transition-colors">
                                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                            <div class="flex items-center mt-1 text-sm text-gray-600">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }} - 
                                                {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                            </div>
                                            <div class="flex items-center mt-2 space-x-2">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    @if($booking->status === 'CONFIRMED') bg-green-100 text-green-800
                                                    @elseif($booking->status === 'PENDING') bg-yellow-100 text-yellow-800
                                                    @elseif($booking->status === 'CANCELLED') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $booking->status }}
                                                </span>
                                                <span class="text-sm text-gray-600">
                                                    Reference: {{ $booking->booking_reference }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mt-3 md:mt-0 md:ml-6 md:text-right">
                                            <div class="text-lg font-semibold text-gray-900">৳{{ number_format($booking->total_amount, 2) }}</div>
                                            <div class="mt-2">
                                                <a href="{{ route('rental.booking.details', $booking) }}" 
                                                   class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                                    View Details
                                                    <svg class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($bookings->hasPages())
                            <div class="mt-6">
                                {{ $bookings->links() }}
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Booking History</h3>
                            <p class="mt-2 text-gray-500 max-w-md mx-auto">
                                You haven't made any bookings yet.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Complaints Tab -->
                <div x-show="activeTab === 'complaints'" x-cloak>
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h4 class="text-lg font-medium text-gray-900">My Complaints</h4>
                            <p class="text-sm text-gray-600">Manage your rental complaints</p>
                        </div>
                        <button onclick="openComplaintModal()"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                            <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            New Complaint
                        </button>
                    </div>
                    
                    @if($complaints->count() > 0)
                        <div class="space-y-4">
                            @foreach($complaints as $complaint)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="font-medium text-gray-900">{{ $complaint->title }}</h5>
                                            <p class="mt-1 text-sm text-gray-600">{{ Str::limit($complaint->description, 150) }}</p>
                                            <div class="flex items-center mt-2 space-x-4">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full 
                                                    @if($complaint->status === 'OPEN') bg-red-100 text-red-800
                                                    @elseif($complaint->status === 'IN_PROGRESS') bg-yellow-100 text-yellow-800
                                                    @elseif($complaint->status === 'RESOLVED') bg-green-100 text-green-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $complaint->status }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $complaint->created_at->format('M d, Y') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <a href="{{ route('complaints.show', $complaint) }}" 
                                               class="inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Complaints</h3>
                            <p class="mt-2 text-gray-500 max-w-md mx-auto">
                                You haven't submitted any complaints yet.
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Reviews Tab -->
                <div x-show="activeTab === 'reviews'" x-cloak>
                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                                <div class="border border-gray-200 rounded-lg p-6">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center mr-3">
                                                    @if($review->property->primaryImage)
                                                        <img src="{{ Storage::url($review->property->primaryImage->image_path) }}" 
                                                             alt="{{ $review->property->name }}"
                                                             class="h-full w-full rounded-full object-cover">
                                                    @else
                                                        <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                        </svg>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h5 class="font-medium text-gray-900">{{ $review->property->name }}</h5>
                                                    <div class="flex items-center mt-1">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->overall_rating)
                                                                <svg class="h-4 w-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @else
                                                                <svg class="h-4 w-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            @endif
                                                        @endfor
                                                        <span class="ml-2 text-sm text-gray-600">
                                                            {{ number_format($review->overall_rating, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            @if($review->comment)
                                                <div class="mt-4">
                                                    <p class="text-gray-700">{{ $review->comment }}</p>
                                                </div>
                                            @endif
                                            <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                                <div>
                                                    <div class="text-sm text-gray-500">Cleanliness</div>
                                                    <div class="font-medium">{{ $review->cleanliness_rating }}/5</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">Location</div>
                                                    <div class="font-medium">{{ $review->location_rating }}/5</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">Value</div>
                                                    <div class="font-medium">{{ $review->value_rating }}/5</div>
                                                </div>
                                                <div>
                                                    <div class="text-sm text-gray-500">Service</div>
                                                    <div class="font-medium">{{ $review->service_rating }}/5</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="ml-4 text-right">
                                            <div class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</div>
                                            @if($review->is_approved)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                                    Approved
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 mt-2">
                                                    Pending
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="h-16 w-16 mx-auto text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No Reviews</h3>
                            <p class="mt-2 text-gray-500 max-w-md mx-auto">
                                You haven't submitted any reviews yet.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Add Review</h3>
        <form id="reviewForm" method="POST" action="{{ route('rental.review.store') }}">
            @csrf
            <input type="hidden" name="booking_id" id="booking_id">
            
            <div class="space-y-4">
                <!-- Overall Rating -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Overall Rating</label>
                    <div class="flex space-x-1" id="overallRating">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    onclick="setRating('overall', {{ $i }})"
                                    class="text-gray-300 hover:text-yellow-400 rating-star"
                                    data-rating-type="overall"
                                    data-value="{{ $i }}">
                                <svg class="h-8 w-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="overall_rating" id="overall_rating" value="0">
                </div>

                <!-- Sub Ratings -->
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cleanliness</label>
                        <div class="flex space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        onclick="setRating('cleanliness', {{ $i }})"
                                        class="text-gray-300 hover:text-yellow-400 rating-star"
                                        data-rating-type="cleanliness"
                                        data-value="{{ $i }}">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="cleanliness_rating" id="cleanliness_rating" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                        <div class="flex space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        onclick="setRating('location', {{ $i }})"
                                        class="text-gray-300 hover:text-yellow-400 rating-star"
                                        data-rating-type="location"
                                        data-value="{{ $i }}">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="location_rating" id="location_rating" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Value</label>
                        <div class="flex space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        onclick="setRating('value', {{ $i }})"
                                        class="text-gray-300 hover:text-yellow-400 rating-star"
                                        data-rating-type="value"
                                        data-value="{{ $i }}">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="value_rating" id="value_rating" value="0">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Service</label>
                        <div class="flex space-x-1">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" 
                                        onclick="setRating('service', {{ $i }})"
                                        class="text-gray-300 hover:text-yellow-400 rating-star"
                                        data-rating-type="service"
                                        data-value="{{ $i }}">
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="service_rating" id="service_rating" value="0">
                    </div>
                </div>

                <!-- Comment -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                    <textarea name="comment" id="comment" rows="4" 
                              class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Share your experience..."></textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeReviewModal()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Submit Review
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Complaint</h3>
        <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Complaint Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Complaint Type</label>
                    <select name="complaint_type" id="complaint_type" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="PROPERTY">Property Issue</option>
                        <option value="SERVICE">Service Issue</option>
                        <option value="PAYMENT">Payment Issue</option>
                        <option value="OWNER">Owner Related</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <!-- Related Property -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Related Property</label>
                    <select name="related_id" id="related_id" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Property</option>
                        @foreach($userProperties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" id="title" 
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Brief description of your complaint"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Description</label>
                    <textarea name="description" id="description" rows="6" 
                              class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Please provide detailed information about your complaint..."
                              required></textarea>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="priority" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                        <option value="HIGH">High</option>
                        <option value="URGENT">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between items-center mb-6">
                <div>
                    <h4 class="text-lg font-medium text-gray-900">My Complaints</h4>
                    <p class="text-sm text-gray-600">Manage your rental complaints</p>
                </div>
                <button onclick="openComplaintModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Complaint
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Review Modal Functions
function openReviewModal(bookingId) {
    document.getElementById('booking_id').value = bookingId;
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    resetRatings();
}

function setRating(type, value) {
    // Update hidden input
    document.getElementById(`${type}_rating`).value = value;
    
    // Update star display
    const stars = document.querySelectorAll(`[data-rating-type="${type}"]`);
    stars.forEach((star, index) => {
        if (index < value) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}

function resetRatings() {
    const ratings = ['overall', 'cleanliness', 'location', 'value', 'service'];
    ratings.forEach(rating => {
        document.getElementById(`${rating}_rating`).value = 0;
        const stars = document.querySelectorAll(`[data-rating-type="${rating}"]`);
        stars.forEach(star => {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        });
    });
    document.getElementById('comment').value = '';
}

// Complaint Modal Functions
function openComplaintModal() {
    document.getElementById('complaintModal').classList.remove('hidden');
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.getElementById('complaintForm').reset();
}

// Close modals when clicking outside
document.getElementById('reviewModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeReviewModal();
    }
});

document.getElementById('complaintModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeComplaintModal();
    }
});
</script>

<!-- Alpine.js for tabs -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<style>
[x-cloak] { display: none !important; }
</style>
@endsection