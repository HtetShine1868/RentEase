@extends('laundry-provider.layouts.provider')

@section('title', 'Customer Reviews')
@section('subtitle', 'See what customers are saying about your service')

@section('content')
<div class="space-y-6">
    {{-- Header with stats --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-[#174455]">Customer Reviews</h2>
                <p class="text-gray-600">View all feedback from your customers</p>
            </div>
        </div>
    </div>

    {{-- Rating Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Overall Rating</p>
            <div class="flex items-center">
                <span class="text-3xl font-bold text-[#174455]">{{ number_format($stats['average_rating'], 1) }}</span>
                <div class="ml-2 flex">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['average_rating']))
                            <i class="fas fa-star text-yellow-400"></i>
                        @else
                            <i class="far fa-star text-gray-300"></i>
                        @endif
                    @endfor
                </div>
            </div>
            <p class="text-sm text-gray-500 mt-1">Based on {{ $stats['total_reviews'] }} reviews</p>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Quality</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-green-600">{{ number_format($stats['average_quality'], 1) }}</span>
                <div class="ml-2 flex">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['average_quality']))
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @else
                            <i class="far fa-star text-gray-300 text-sm"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Delivery</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-blue-600">{{ number_format($stats['average_delivery'], 1) }}</span>
                <div class="ml-2 flex">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['average_delivery']))
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @else
                            <i class="far fa-star text-gray-300 text-sm"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6">
            <p class="text-sm text-gray-500 mb-1">Value for Money</p>
            <div class="flex items-center">
                <span class="text-2xl font-bold text-purple-600">{{ number_format($stats['average_value'], 1) }}</span>
                <div class="ml-2 flex">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= round($stats['average_value']))
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @else
                            <i class="far fa-star text-gray-300 text-sm"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Distribution --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-[#174455] mb-4">Rating Distribution</h3>
        <div class="space-y-2">
            @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $distribution[$i] ?? 0;
                    $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                @endphp
                <div class="flex items-center gap-2">
                    <div class="w-12 text-sm font-medium">{{ $i }} star</div>
                    <div class="flex-1 h-4 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="w-24 text-sm text-gray-600">{{ $count }} ({{ number_format($percentage, 1) }}%)</div>
                </div>
            @endfor
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('laundry-provider.reviews.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by customer or comment..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Rating</label>
                <select name="rating" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all">All Ratings</option>
                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars</option>
                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars</option>
                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars</option>
                    <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars</option>
                    <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star</option>
                </select>
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select name="date_range" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
                @if(request()->anyFilled(['search', 'rating', 'date_range']))
                    <a href="{{ route('laundry-provider.reviews.index') }}" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Reviews List --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-medium text-[#174455]">All Reviews ({{ $reviews->total() }})</h3>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Sort by:</label>
                <select onchange="window.location.href = this.value" class="text-sm border rounded-lg px-2 py-1">
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'desc']) }}" 
                        {{ request('sort') == 'created_at' && request('direction') == 'desc' ? 'selected' : '' }}>
                        Newest First
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'created_at', 'direction' => 'asc']) }}"
                        {{ request('sort') == 'created_at' && request('direction') == 'asc' ? 'selected' : '' }}>
                        Oldest First
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'overall_rating', 'direction' => 'desc']) }}"
                        {{ request('sort') == 'overall_rating' && request('direction') == 'desc' ? 'selected' : '' }}>
                        Highest Rated
                    </option>
                    <option value="{{ request()->fullUrlWithQuery(['sort' => 'overall_rating', 'direction' => 'asc']) }}"
                        {{ request('sort') == 'overall_rating' && request('direction') == 'asc' ? 'selected' : '' }}>
                        Lowest Rated
                    </option>
                </select>
            </div>
        </div>
        
        <div class="divide-y divide-gray-200">
            @forelse($reviews as $review)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="flex">
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
                                <span class="text-sm font-medium text-gray-700">{{ number_format($review->overall_rating, 1) }}</span>
                                <span class="text-sm text-gray-500">•</span>
                                <span class="text-sm text-gray-500">{{ $review->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <p class="font-medium text-gray-900">{{ $review->user->name }}</p>
                                @if($review->comment)
                                    <p class="text-gray-700 mt-2">{{ $review->comment }}</p>
                                @else
                                    <p class="text-gray-400 italic mt-2">No comment provided</p>
                                @endif
                            </div>
                            
                            <div class="flex gap-4 text-sm text-gray-600">
                                <span><span class="font-medium">Quality:</span> {{ $review->quality_rating }}/5</span>
                                <span><span class="font-medium">Delivery:</span> {{ $review->delivery_rating }}/5</span>
                                <span><span class="font-medium">Value:</span> {{ $review->value_rating }}/5</span>
                            </div>
                        </div>
                        
                        <div class="flex flex-col items-end">
                            <a href="{{ route('laundry-provider.reviews.show', $review->id) }}" 
                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                View Details <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                            <span class="text-xs text-gray-400 mt-2">Review ID: #{{ $review->id }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-gray-500">
                    <i class="far fa-star text-5xl text-gray-300 mb-3"></i>
                    <p class="text-lg font-medium">No reviews found</p>
                    <p class="text-sm mt-1">When customers leave reviews, they'll appear here</p>
                    @if(request()->anyFilled(['search', 'rating', 'date_range']))
                        <a href="{{ route('laundry-provider.reviews.index') }}" class="mt-4 inline-block bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200">
                            Clear Filters
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
        
        {{-- Pagination --}}
        @if($reviews->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $reviews->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function exportReviews() {
        window.location.href = '{{ route("laundry-provider.reviews.analytics.export") }}' + window.location.search;
    }
</script>
@endpush