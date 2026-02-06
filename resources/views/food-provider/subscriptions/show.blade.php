@extends('layouts.food-provider')

@section('title', 'Subscription Details - Food Provider Dashboard')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('food-provider.subscriptions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-900">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Subscriptions
        </a>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Subscription Details</h1>
            <p class="text-gray-600 mt-2">Subscription #SUB-{{ str_pad(1, 6, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="flex space-x-3">
            <button class="px-4 py-2 bg-yellow-100 text-yellow-700 rounded-md hover:bg-yellow-200">
                Pause Subscription
            </button>
            <button class="px-4 py-2 bg-red-100 text-red-700 rounded-md hover:bg-red-200">
                Cancel Subscription
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Customer Information Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-lg mr-4">
                                JD
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900">John Doe</h3>
                                <p class="text-sm text-gray-600">Regular Customer</p>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>john.doe@example.com</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>+1 (555) 123-4567</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Delivery Address</h4>
                        <p class="text-sm text-gray-600">
                            123 Main Street<br>
                            Apartment 4B<br>
                            New York, NY 10001<br>
                            United States
                        </p>
                    </div>
                </div>
            </div>

            <!-- Subscription Plan Details -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Plan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Plan Details</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Plan Type:</span>
                                <span class="font-medium">Monthly Subscription</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Meal Types:</span>
                                <span class="font-medium">Lunch + Dinner</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Start Date:</span>
                                <span class="font-medium">March 1, 2024</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">End Date:</span>
                                <span class="font-medium">March 31, 2024</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Pricing</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Base Price:</span>
                                <span class="font-medium">$100.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subscription Discount:</span>
                                <span class="font-medium text-green-600">-$10.00</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-gray-900 font-semibold">Monthly Total:</span>
                                <span class="text-lg font-bold text-gray-900">$90.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meal Schedule Component -->
            @include('food-provider.subscriptions.components.meal-schedule')
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Subscription Status</h2>
                <div class="flex items-center mb-4">
                    <div class="h-3 w-3 rounded-full bg-green-500 mr-3"></div>
                    <span class="text-lg font-semibold text-green-600">Active</span>
                </div>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Days Remaining</p>
                        <p class="text-2xl font-bold text-gray-900">15 days</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Last Renewal</p>
                        <p class="text-sm font-medium text-gray-900">March 1, 2024</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Auto-renewal</p>
                        <p class="text-sm font-medium text-green-600">Enabled</p>
                    </div>
                </div>
            </div>

            <!-- Delivery Preferences -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Delivery Preferences</h2>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Preferred Delivery Time</p>
                        <div class="flex items-center">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">1:00 PM (Lunch)</span>
                        </div>
                        <div class="flex items-center mt-1">
                            <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium">7:00 PM (Dinner)</span>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Delivery Days</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(['Mon', 'Wed', 'Fri'] as $day)
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                {{ $day }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Special Instructions -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Special Instructions</h2>
                <div class="bg-gray-50 rounded-lg p-4">
                    <p class="text-sm text-gray-700">"Please avoid spicy food. Prefer vegetarian options when available. Ring doorbell twice for delivery."</p>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Deliveries</h2>
                <div class="space-y-3">
                    @for($i = 1; $i <= 3; $i++)
                    <div class="flex justify-between items-center border-b pb-3">
                        <div>
                            <p class="font-medium text-gray-900">March {{ 10 + $i }}, 2024</p>
                            <p class="text-sm text-gray-600">Lunch + Dinner</p>
                        </div>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                            Delivered
                        </span>
                    </div>
                    @endfor
                    <a href="#" class="block text-center text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Deliveries
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection