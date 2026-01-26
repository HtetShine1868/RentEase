@extends('layouts.food-provider')

@section('title', 'Restaurant Profile')

@section('header', 'Restaurant Profile')

@section('content')
<div class="space-y-6">
    <!-- Profile Header -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="md:flex md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-16 w-16 sm:h-20 sm:w-20">
                            <img class="h-16 w-16 sm:h-20 sm:w-20 rounded-lg object-cover border-2 border-white shadow"
                                 src="{{ auth()->user()->restaurant->logo_url ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200&h=200&fit=crop' }}"
                                 alt="Restaurant Logo">
                        </div>
                        <div class="ml-4 md:ml-6">
                            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                                {{ auth()->user()->restaurant->name ?? 'Your Restaurant' }}
                            </h2>
                            <div class="mt-1 flex flex-col sm:flex-row sm:flex-wrap sm:mt-0 sm:space-x-4">
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <i class="fas fa-map-marker-alt mr-2"></i>
                                    {{ auth()->user()->restaurant->address ?? 'No address set' }}
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <i class="fas fa-star mr-2 text-yellow-400"></i>
                                    4.7 (128 reviews)
                                </div>
                                <div class="mt-2 flex items-center text-sm text-gray-500">
                                    <i class="fas fa-clock mr-2"></i>
                                    Active since {{ auth()->user()->created_at->format('M Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-4 flex space-x-3 md:mt-0 md:ml-4">
                    <a href="{{ route('food-provider.profile.edit') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Profile
                    </a>
                    <button type="button" 
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-eye mr-2"></i>
                        Preview
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Total Orders -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Orders
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                1,248
                            </dd>
                            <dd class="text-xs text-green-600">
                                +12% this month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-rupee-sign text-blue-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Revenue
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                ₹2,45,680
                            </dd>
                            <dd class="text-xs text-blue-600">
                                +18% this month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Subscribers -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-users text-purple-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Active Subscribers
                            </dt>
                            <dd class="text-lg font-medium text-gray-900">
                                42
                            </dd>
                            <dd class="text-xs text-purple-600">
                                +5 this week
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Information -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                Restaurant Details
            </h3>
            <div class="border-t border-gray-200">
                <dl class="divide-y divide-gray-200">
                    <!-- Description -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Description
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ auth()->user()->restaurant->description ?? 'No description provided. Add a compelling description to attract more customers.' }}
                        </dd>
                    </div>

                    <!-- Contact Information -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Contact Information
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="space-y-2">
                                <div class="flex items-center">
                                    <i class="fas fa-phone text-gray-400 w-5"></i>
                                    <span class="ml-2">{{ auth()->user()->phone ?? '+91 9876543210' }}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-envelope text-gray-400 w-5"></i>
                                    <span class="ml-2">{{ auth()->user()->email ?? 'Not set' }}</span>
                                </div>
                            </div>
                        </dd>
                    </div>

                    <!-- Service Coverage -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Service Coverage
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-circle text-xs mr-1"></i>
                                    {{ auth()->user()->restaurant->coverage_radius ?? '5' }} km radius
                                </span>
                                <span class="text-xs text-gray-500">Approx. {{ (auth()->user()->restaurant->coverage_radius ?? 5) * 2 }} km delivery range</span>
                            </div>
                        </dd>
                    </div>

                    <!-- Meal Types Offered -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Meal Types Offered
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    <i class="fas fa-sun mr-1"></i> Breakfast
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    <i class="fas fa-utensils mr-1"></i> Lunch
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    <i class="fas fa-moon mr-1"></i> Dinner
                                </span>
                            </div>
                        </dd>
                    </div>

                    <!-- Service Types -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Service Types
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex flex-wrap gap-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                    <i class="fas fa-calendar-alt mr-1"></i> Monthly Subscription
                                </span>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-pink-100 text-pink-800">
                                    <i class="fas fa-shopping-cart mr-1"></i> Pay-Per-Eat
                                </span>
                            </div>
                        </dd>
                    </div>

                    <!-- Commission Rate -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Commission Rate
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="font-medium">12%</span> per transaction
                                    <p class="text-xs text-gray-500 mt-1">Platform commission deducted from each order</p>
                                </div>
                                <button type="button" 
                                        class="text-xs text-indigo-600 hover:text-indigo-500">
                                    View Details
                                </button>
                            </div>
                        </dd>
                    </div>

                    <!-- Restaurant Status -->
                    <div class="py-4 sm:py-5 sm:grid sm:grid-cols-3 sm:gap-4">
                        <dt class="text-sm font-medium text-gray-500">
                            Restaurant Status
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-2 w-2 rounded-full bg-green-500 mr-2"></div>
                                    <span>Active & Accepting Orders</span>
                                </div>
                                <button type="button" 
                                        class="text-xs text-red-600 hover:text-red-500">
                                    <i class="fas fa-power-off mr-1"></i>
                                    Go Offline
                                </button>
                            </div>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Quick Actions -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('food-provider.menu.items.create') }}" 
                       class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="bg-indigo-100 p-2 rounded-md">
                                <i class="fas fa-plus text-indigo-600"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Add New Menu Item</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('food-provider.settings.index') }}" 
                       class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="bg-blue-100 p-2 rounded-md">
                                <i class="fas fa-cog text-blue-600"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">Update Settings</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                    
                    <a href="{{ route('food-provider.orders.index') }}" 
                       class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="bg-green-100 p-2 rounded-md">
                                <i class="fas fa-shopping-cart text-green-600"></i>
                            </div>
                            <span class="ml-3 text-sm font-medium text-gray-900">View Pending Orders</span>
                        </div>
                        <i class="fas fa-chevron-right text-gray-400"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- Performance Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    Performance Summary
                </h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Customer Satisfaction</span>
                            <span class="font-medium">92%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="width: 92%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">On-Time Delivery</span>
                            <span class="font-medium">88%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: 88%"></div>
                        </div>
                    </div>
                    
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600">Order Accuracy</span>
                            <span class="font-medium">96%</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-purple-500 rounded-full" style="width: 96%"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Avg. Preparation Time</span>
                        <span class="font-medium text-gray-900">24 minutes</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection