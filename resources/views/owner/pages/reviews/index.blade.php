@extends('owner.layout.owner-layout')

@section('title', 'Property Reviews - RentEase')
@section('page-title', 'Property Reviews')
@section('page-subtitle', 'View what guests are saying about your properties')

@push('styles')
<style>
    /* Rating Stars */
    .rating-stars {
        @apply flex items-center;
    }
    .star-filled {
        @apply text-yellow-400;
    }
    .star-empty {
        @apply text-gray-300;
    }
    
    /* Review Cards */
    .review-card {
        @apply bg-white rounded-xl border border-gray-200 p-6 mb-4 shadow-sm hover:shadow-md transition-shadow duration-200;
    }
    
    /* Stats Cards */
    .stat-card {
        @apply bg-white rounded-xl border border-gray-200 p-6;
    }
    
    /* Rating Progress Bar */
    .rating-progress {
        @apply h-2 bg-gray-200 rounded-full overflow-hidden;
    }
    .rating-progress-fill {
        @apply h-full bg-yellow-400 rounded-full;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Guest Reviews
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                See what guests are saying about your properties
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('owner.reviews.export') }}?{{ http_build_query(request()->query()) }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-download mr-2"></i>
                Export Reviews
            </a>
            <button onclick="window.location.reload()" 
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                <i class="fas fa-sync-alt mr-2"></i>
                Refresh
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Average Rating -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Average Rating</p>
                    <div class="flex items-baseline mt-2">
                        <span class="text-3xl font-bold text-gray-900">{{ $stats['avg_rating'] }}</span>
                        <span class="ml-1 text-gray-500">/5</span>
                    </div>
                    <div class="rating-stars mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= floor($stats['avg_rating']))
                                <i class="fas fa-star star-filled"></i>
                            @elseif($i - 0.5 <= $stats['avg_rating'])
                                <i class="fas fa-star-half-alt star-filled"></i>
                            @else
                                <i class="far fa-star star-empty"></i>
                            @endif
                        @endfor
                    </div>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-star text-purple-600 text-xl"></i>
                </div>
            </div>
            <p class="text-xs text-gray-500 mt-4">Based on {{ $stats['total_reviews'] }} reviews</p>
        </div>

        <!-- Total Reviews -->
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Reviews</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_reviews'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-comment text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Rating Breakdown -->
        <div class="stat-card lg:col-span-2">
            <p class="text-sm text-gray-500 mb-3">Rating Breakdown</p>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span>Cleanliness</span>
                        <span class="font-medium">{{ $stats['avg_cleanliness'] }}/5</span>
                    </div>
                    <div class="rating-progress">
                        <div class="rating-progress-fill" style="width: {{ ($stats['avg_cleanliness'] / 5) * 100 }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm mt-3 mb-1">
                        <span>Location</span>
                        <span class="font-medium">{{ $stats['avg_location'] }}/5</span>
                    </div>
                    <div class="rating-progress">
                        <div class="rating-progress-fill" style="width: {{ ($stats['avg_location'] / 5) * 100 }}%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between text-sm mb-1">
                        <span>Value</span>
                        <span class="font-medium">{{ $stats['avg_value'] }}/5</span>
                    </div>
                    <div class="rating-progress">
                        <div class="rating-progress-fill" style="width: {{ ($stats['avg_value'] / 5) * 100 }}%"></div>
                    </div>
                    
                    <div class="flex items-center justify-between text-sm mt-3 mb-1">
                        <span>Service</span>
                        <span class="font-medium">{{ $stats['avg_service'] }}/5</span>
                    </div>
                    <div class="rating-progress">
                        <div class="rating-progress-fill" style="width: {{ ($stats['avg_service'] / 5) * 100 }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rating Distribution -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rating Distribution</h3>
        <div class="space-y-3">
            @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $stats['distribution'][$i] ?? 0;
                    $percentage = $stats['total_reviews'] > 0 ? round(($count / $stats['total_reviews']) * 100) : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="w-12 text-sm font-medium">{{ $i }} star</div>
                    <div class="flex-1 rating-progress">
                        <div class="rating-progress-fill bg-yellow-400" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="w-16 text-sm text-gray-600">{{ $count }} ({{ $percentage }}%)</div>
                </div>
            @endfor
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <form method="GET" action="{{ route('owner.reviews.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Property Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <select name="property_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                            <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                                {{ $property->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Rating Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                    <select name="rating" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                    </select>
                </div>

                <!-- Date From -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>

                <!-- Date To -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" 
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" name="search" value="{{ request('search') }}" 
                           placeholder="Search by guest name, comment, or property..." 
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                </div>
            </div>

            <div class="flex justify-end space-x-3">
                <a href="{{ route('owner.reviews.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-rotate-left mr-2"></i> Reset
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>
            </div>
        </form>
    </div>

    <!-- Reviews List -->
    <div class="space-y-4">
        @forelse($reviews as $review)
            <div class="review-card">
                <div class="flex flex-col md:flex-row gap-4">
                    <!-- User Avatar -->
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                            @if($review->user->avatar_url)
                                <img src="{{ $review->user->avatar_url }}" alt="{{ $review->user->name }}" 
                                     class="h-12 w-12 rounded-full object-cover">
                            @else
                                <span class="text-purple-600 font-semibold text-lg">
                                    {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Review Content -->
                    <div class="flex-1">
                        <div class="flex flex-wrap items-center justify-between gap-2 mb-2">
                            <div>
                                <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                <div class="flex items-center gap-2 text-sm text-gray-500">
                                    <span>{{ $review->created_at->format('M d, Y') }}</span>
                                    <span>•</span>
                                    <span class="text-purple-600">{{ $review->property->name }}</span>
                                </div>
                            </div>
                            
                            <!-- Rating -->
                            <div class="flex items-center">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($review->overall_rating))
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                        @elseif($i - 0.5 <= $review->overall_rating)
                                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                                        @else
                                            <i class="far fa-star text-gray-300 text-sm"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="ml-2 text-sm font-medium text-gray-700">{{ number_format($review->overall_rating, 1) }}</span>
                            </div>
                        </div>
                        
                        <!-- Comment -->
                        @if($review->comment)
                            <p class="text-gray-700 mt-3">{{ $review->comment }}</p>
                        @else
                            <p class="text-gray-400 italic mt-3">No comment provided</p>
                        @endif
                        
                        <!-- Category Ratings (Optional - can be removed if too much) -->
                        <div class="flex flex-wrap gap-4 mt-4 text-xs text-gray-500 border-t pt-3">
                            <div>Cleanliness: {{ $review->cleanliness_rating }}/5</div>
                            <div>Location: {{ $review->location_rating }}/5</div>
                            <div>Value: {{ $review->value_rating }}/5</div>
                            <div>Service: {{ $review->service_rating }}/5</div>
                        </div>
                    </div>
                    
                    <!-- View Details Button -->
                    <div class="flex-shrink-0">
                        <a href="{{ route('owner.reviews.show', $review->id) }}" 
                           class="inline-flex items-center px-3 py-2 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors text-sm">
                            <i class="fas fa-eye mr-1"></i> View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-star text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700">No reviews found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your filters or come back later.</p>
                <a href="{{ route('owner.reviews.index') }}" 
                   class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 inline-block">
                    Clear Filters
                </a>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($reviews->hasPages())
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">{{ $reviews->firstItem() }} to {{ $reviews->lastItem() }}</span> 
                of <span class="font-semibold">{{ $reviews->total() }}</span> reviews
            </div>
            {{ $reviews->links() }}
        </div>
    @endif
</div>
@endsection