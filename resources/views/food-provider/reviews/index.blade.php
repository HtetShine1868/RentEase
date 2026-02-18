@extends('layouts.food-provider')

@section('title', 'Customer Reviews')

@section('header', 'Customer Reviews')

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Customer Reviews
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                See what customers are saying about your food service
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('food-provider.reviews.export') }}?{{ http_build_query(request()->except('page')) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i>
                Export Reviews
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Reviews -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-star text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Reviews</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-star text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Average Rating</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['average'] }}/5</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5 Star Reviews -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-smile text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">5 Star Reviews</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['five_star'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1 Star Reviews -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <i class="fas fa-frown text-red-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">1 Star Reviews</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['one_star'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('food-provider.reviews.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               placeholder="Customer or comment...">
                    </div>

                    <!-- Rating Filter -->
                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                        <select name="rating" 
                                id="rating" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Ratings</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select name="date_range" 
                                id="date_range" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>

                    <!-- Items per page -->
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700">Per Page</label>
                        <select name="per_page" 
                                id="per_page" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('food-provider.reviews.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($reviews->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-star text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No reviews yet</h3>
                <p class="mt-1 text-sm text-gray-500">Customers haven't left any reviews yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($reviews as $review)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <!-- Customer Avatar -->
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium text-lg">
                                            {{ substr($review->user->name ?? 'NA', 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Review Content -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-lg font-medium text-gray-900">
                                            {{ $review->user->name ?? 'Anonymous' }}
                                        </h4>
                                        <span class="text-sm text-gray-500">
                                            {{ $review->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    
                                    <!-- Rating Stars -->
                                    <div class="mt-1 flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                        <span class="ml-2 text-sm text-gray-600">{{ $review->overall_rating }} overall</span>
                                    </div>
                                    
                                    <!-- Comment -->
                                    @if($review->comment)
                                        <div class="mt-3 text-gray-700">
                                            <p class="text-sm">"{{ $review->comment }}"</p>
                                        </div>
                                    @endif
                                    
                                    <!-- Action Buttons -->
                                    <div class="mt-4 flex space-x-3">
                                        <a href="{{ route('food-provider.reviews.show', $review->id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-eye mr-1"></i>
                                            View Details
                                        </a>
                                        
                                        <button type="button"
                                                onclick="replyToReview({{ $review->id }})"
                                                class="inline-flex items-center px-3 py-1.5 border border-indigo-300 shadow-sm text-xs font-medium rounded-md text-indigo-700 bg-indigo-50 hover:bg-indigo-100">
                                            <i class="fas fa-reply mr-1"></i>
                                            Reply
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Order Reference -->
                            <div class="text-right">
                                <span class="text-xs text-gray-500">Order</span>
                                <div class="text-sm font-medium text-gray-900">#{{ $review->order_id }}</div>
                            </div>
                        </div>
                        
                        <!-- Provider Reply (if exists) -->
                        @if($review->provider_reply)
                            <div class="mt-4 ml-16 pl-4 border-l-4 border-indigo-200 bg-indigo-50 p-3 rounded-r-lg">
                                <div class="flex items-start">
                                    <i class="fas fa-reply text-indigo-400 mt-1 mr-2"></i>
                                    <div>
                                        <p class="text-xs font-medium text-indigo-600">Your Reply:</p>
                                        <p class="text-sm text-gray-700 mt-1">{{ $review->provider_reply }}</p>
                                        @if($review->replied_at)
                                            <p class="text-xs text-gray-500 mt-1">{{ $review->replied_at->diffForHumans() }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($reviews->previousPageUrl())
                        <a href="{{ $reviews->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($reviews->nextPageUrl())
                        <a href="{{ $reviews->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif

                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $reviews->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $reviews->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $reviews->total() }}</span>

                            reviews
                        </p>
                    </div>
                    <div>

                        {{ $reviews->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function replyToReview(reviewId) {
    const reply = prompt('Enter your reply to this review:');
    if (reply && reply.trim()) {
        fetch(`/food-provider/reviews/${reviewId}/reply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reply: reply })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Reply sent successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error sending reply');
        });
    }
}
</script>
@endpush
@endsection

