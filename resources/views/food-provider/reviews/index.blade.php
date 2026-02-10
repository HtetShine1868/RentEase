@extends('layouts.food-provider')

@section('title', 'Reviews & Ratings - Food Provider Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Reviews & Ratings</h1>
        <p class="text-gray-600 mt-2">Manage customer feedback and monitor your service quality</p>
    </div>

    <!-- Overall Rating Summary -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Overall Rating -->
            <div class="text-center md:text-left">
                <div class="flex items-center justify-center md:justify-start mb-4">
                    <div class="text-5xl font-bold text-gray-900 mr-4">4.8</div>
                    <div>
                        <div class="flex mb-1">
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-5 h-5 {{ $i <= 4 ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                            @endfor
                        </div>
                        <p class="text-sm text-gray-600">Based on 247 reviews</p>
                    </div>
                </div>
                <p class="text-gray-600">Excellent service! Keep up the good work.</p>
            </div>

            <!-- Rating Distribution -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rating Distribution</h3>
                <div class="space-y-2">
                    @php
                        $ratings = [
                            ['stars' => 5, 'count' => 150, 'percent' => 61],
                            ['stars' => 4, 'count' => 65, 'percent' => 26],
                            ['stars' => 3, 'count' => 20, 'percent' => 8],
                            ['stars' => 2, 'count' => 8, 'percent' => 3],
                            ['stars' => 1, 'count' => 4, 'percent' => 2]
                        ];
                    @endphp
                    
                    @foreach($ratings as $rating)
                    @php
                        $width = $rating['percent'] ?? 0;
                    @endphp                    
                    <div class="flex items-center">
                        <div class="w-10 text-sm text-gray-600">
                            {{ $rating['stars'] }} stars
                        </div>
                    
                        <div class="flex-1 mx-2">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-400 h-2 rounded-full"
                                     style="<?php echo 'width: '.$width.'%'; ?>">
                                </div>
                            </div>
                        </div>
                    
                        <div class="w-10 text-sm text-gray-600 text-right">
                            {{ $rating['count'] }}
                        </div>
                    </div>                    
                    @endforeach
                </div>
            </div>

            <!-- Rating Breakdown -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Rating Breakdown</h3>
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Food Quality</span>
                            <span class="text-sm font-medium">4.9</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: 98%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Delivery Time</span>
                            <span class="text-sm font-medium">4.7</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" style="width: 94%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm text-gray-600">Packaging</span>
                            <span class="text-sm font-medium">4.5</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-500 h-2 rounded-full" style="width: 90%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="p-4 border-b">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Ratings</option>
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="relative">
                        <select class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">With Comments Only</span>
                        </label>
                    </div>
                </div>
                <div class="relative">
                    <div class="flex items-center">
                        <input type="text" placeholder="Search reviews..." class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 w-full md:w-64">
                        <div class="absolute left-3 top-2.5 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reviews List -->
        <div class="divide-y divide-gray-200">
            @for($i = 1; $i <= 5; $i++)
                @include('food-provider.reviews.components.review-card', [
                    'reviewId' => $i,
                    'customerName' => ['John Doe', 'Jane Smith', 'Robert Johnson', 'Sarah Williams', 'Michael Brown'][$i-1],
                    'customerInitials' => ['JD', 'JS', 'RJ', 'SW', 'MB'][$i-1],
                    'rating' => [5, 4, 5, 3, 2][$i-1],
                    'date' => now()->subDays($i)->format('M d, Y'),
                    'comment' => [
                        'The food was absolutely delicious! Fresh ingredients and perfect seasoning. Will definitely order again.',
                        'Good quality food, but delivery was a bit late. Packaging could be better.',
                        'Excellent service! The food was hot when it arrived and tasted amazing.',
                        'Average experience. The food was okay but nothing special.',
                        'Disappointed with the order. The food was cold and some items were missing.'
                    ][$i-1],
                    'orderType' => ['Subscription', 'Pay-Per-Eat', 'Subscription', 'Pay-Per-Eat', 'Pay-Per-Eat'][$i-1],
                    'mealType' => ['Lunch', 'Dinner', 'Breakfast', 'Lunch', 'Dinner'][$i-1],
                    'response' => $i === 1 || $i === 3 ? 'Thank you for your wonderful feedback! We\'re glad you enjoyed your meal. Looking forward to serving you again!' : null,
                    'responseDate' => $i === 1 || $i === 3 ? now()->subDays($i-1)->format('M d, Y') : null
                ])
            @endfor
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex items-center justify-between">
                <div class="flex-1 flex justify-between sm:hidden">
                    <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Previous
                    </a>
                    <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Next
                    </a>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">1</span>
                            to
                            <span class="font-medium">5</span>
                            of
                            <span class="font-medium">247</span>
                            reviews
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-blue-50 text-sm font-medium text-blue-600">
                                1
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                2
                            </a>
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                3
                            </a>
                            <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty State -->
    @include('food-provider.components.empty-state', [
        'title' => 'No reviews yet',
        'message' => 'Customer reviews will appear here after they rate your service.',
        'icon' => 'review',
        'show' => false
    ])

    <!-- Response Modal (Hidden by default) -->
    @include('food-provider.reviews.components.response-modal')
</div>
@endsection

@push('styles')
<style>
    .review-card:hover {
        background-color: #f9fafb;
        transition: background-color 0.2s ease-in-out;
    }
    
    .rating-stars {
        display: inline-flex;
        direction: ltr;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize review functionality
        console.log('Reviews page loaded');
        
        // Example: Filter reviews by rating
        const ratingFilter = document.querySelector('select');
        if (ratingFilter) {
            ratingFilter.addEventListener('change', function() {
                const selectedRating = this.value;
                // In production: Filter reviews based on selected rating
                console.log('Filter by rating:', selectedRating);
            });
        }
    });
    
    function openResponseModal(reviewId, customerName) {
        const modal = document.getElementById('responseModal');
        const reviewIdElement = document.getElementById('response-review-id');
        const customerNameElement = document.getElementById('response-customer-name');
        
        reviewIdElement.textContent = 'Review #' + reviewId;
        customerNameElement.textContent = customerName;
        
        modal.classList.remove('hidden');
    }
    
    function closeResponseModal() {
        document.getElementById('responseModal').classList.add('hidden');
    }
</script>
@endpush