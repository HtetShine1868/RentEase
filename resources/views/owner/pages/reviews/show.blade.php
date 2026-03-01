@extends('owner.layout.owner-layout')

@section('title', 'Review Details - RentEase')
@section('page-title', 'Review Details')
@section('page-subtitle', 'View complete review information')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">Review Details</h3>
            <a href="{{ route('owner.reviews.index') }}" class="text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left mr-1"></i> Back to Reviews
            </a>
        </div>
        
        <div class="p-6">
            <!-- User Info -->
            <div class="flex items-center gap-4 mb-6 pb-6 border-b">
                <div class="h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                    @if($review->user->avatar_url)
                        <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}" 
                             class="h-16 w-16 rounded-full object-cover">
                    @else
                        <span class="text-purple-600 font-bold text-xl">
                            {{ strtoupper(substr($review->user->name, 0, 2)) }}
                        </span>
                    @endif
                </div>
                <div>
                    <h4 class="text-xl font-bold text-gray-900">{{ $review->user->name }}</h4>
                    <p class="text-gray-600">{{ $review->user->email }}</p>
                    @if($review->user->phone)
                        <p class="text-gray-600 text-sm mt-1">{{ $review->user->phone }}</p>
                    @endif
                </div>
            </div>
            
            <!-- Property Info -->
            <div class="mb-6 pb-6 border-b">
                <h5 class="text-sm font-medium text-gray-500 mb-2">Property</h5>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-lg font-semibold text-gray-900">{{ $review->property->name }}</p>
                        <p class="text-gray-600">{{ $review->property->type }} • {{ $review->property->city }}, {{ $review->property->area }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ $review->property->address }}</p>
                    </div>
                    @if($review->booking)
                        <a href="{{ route('owner.bookings.show', $review->booking->id) }}" 
                           class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100">
                            View Booking
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Review Details -->
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h5 class="text-sm font-medium text-gray-500">Review Information</h5>
                    <span class="text-sm text-gray-500">Posted {{ $review->created_at->diffForHumans() }}</span>
                </div>
                
                <!-- Overall Rating -->
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-2">Overall Rating</p>
                    <div class="flex items-center">
                        <div class="flex text-2xl">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($review->overall_rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @elseif($i - 0.5 <= $review->overall_rating)
                                    <i class="fas fa-star-half-alt text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="ml-3 text-xl font-bold text-gray-900">{{ number_format($review->overall_rating, 1) }}</span>
                    </div>
                </div>
                
                <!-- Category Ratings -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Cleanliness</p>
                        <div class="flex items-center justify-center mt-1">
                            <span class="text-lg font-bold">{{ $review->cleanliness_rating }}</span>
                            <span class="text-gray-400 text-sm">/5</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Location</p>
                        <div class="flex items-center justify-center mt-1">
                            <span class="text-lg font-bold">{{ $review->location_rating }}</span>
                            <span class="text-gray-400 text-sm">/5</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Value</p>
                        <div class="flex items-center justify-center mt-1">
                            <span class="text-lg font-bold">{{ $review->value_rating }}</span>
                            <span class="text-gray-400 text-sm">/5</span>
                        </div>
                    </div>
                    <div class="bg-gray-50 p-3 rounded-lg text-center">
                        <p class="text-xs text-gray-500">Service</p>
                        <div class="flex items-center justify-center mt-1">
                            <span class="text-lg font-bold">{{ $review->service_rating }}</span>
                            <span class="text-gray-400 text-sm">/5</span>
                        </div>
                    </div>
                </div>
                
                <!-- Comment -->
                @if($review->comment)
                <div class="mt-4">
                    <p class="text-sm text-gray-500 mb-2">Guest Comment</p>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <p class="text-gray-700">{{ $review->comment }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection