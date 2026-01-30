@extends('layouts.food-provider')

@section('title', 'Dashboard - Food Provider')

@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Welcome back, {{ auth()->user()->name ?? 'Food Provider' }}!
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Here's what's happening with your restaurant today.
                    </p>
                    <div class="mt-2 flex items-center text-sm text-gray-500">
                        <i class="fas fa-store mr-1"></i>
                        <span>{{ auth()->user()->restaurant->name ?? 'Restaurant Name' }}</span>
                        <span class="mx-2">•</span>
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        <span>{{ auth()->user()->restaurant->coverage_radius ?? '5' }} km coverage</span>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <div class="flex items-center space-x-3">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <i class="fas fa-circle text-xs mr-2"></i>
                            Restaurant Active
                        </span>
                        <button type="button" 
                                class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-sync-alt mr-1 text-xs"></i>
                            Refresh
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @include('components.food-provider.stats-card', [
            'title' => 'Today Orders',
            'value' => '24',
            'change' => '+12%',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'blue'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Active Subscriptions',
            'value' => '18',
            'change' => '+5%',
            'icon' => 'fas fa-calendar-check',
            'color' => 'green'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Monthly Earnings',
            'value' => '₹15,240',
            'change' => '+18%',
            'icon' => 'fas fa-money-bill-wave',
            'color' => 'purple'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Average Rating',
            'value' => '4.7',
            'change' => '+0.2',
            'icon' => 'fas fa-star',
            'color' => 'yellow'
        ])
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Revenue Overview
                    </h3>
                    <select class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                        <option>Last 7 days</option>
                        <option selected>Last 30 days</option>
                        <option>Last 90 days</option>
                    </select>
                </div>
            </div>
            <div class="p-6">
                <!-- Chart placeholder -->
                <div class="h-64 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-gray-400 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-500">Revenue chart will be displayed here</p>
                        <p class="text-xs text-gray-400 mt-1">(Integration with chart library required)</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">₹15,240</div>
                        <div class="text-sm text-gray-500">This Month</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600">+18%</div>
                        <div class="text-sm text-gray-500">Growth</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Distribution -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Order Distribution
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach([
                        ['type' => 'Pay-per-eat', 'count' => 65, 'color' => 'bg-blue-500'],
                        ['type' => 'Subscription', 'count' => 35, 'color' => 'bg-green-500'],
                        ['type' => 'New Customers', 'count' => 28, 'color' => 'bg-purple-500'],
                        ['type' => 'Returning Customers', 'count' => 72, 'color' => 'bg-yellow-500']
                    ] as $stat)
                        @php
                            // Ensure width does not exceed 100%
                            $width = min($stat['count'], 100);
                        @endphp
                        <div class="mb-4">
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $stat['type'] }}</span>
                                <span class="font-medium">{{ $stat['count'] }}%</span>
                            </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full {{ $stat['color'] }}" style="<?php echo 'width: '.$width.'%'; ?>"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-blue-50 rounded-lg">
                        <div class="text-lg font-bold text-blue-700">65%</div>
                        <div class="text-sm text-blue-600">One-time Orders</div>
                    </div>
                    <div class="text-center p-3 bg-green-50 rounded-lg">
                        <div class="text-lg font-bold text-green-700">35%</div>
                        <div class="text-sm text-green-600">Subscriptions</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders & Notifications -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Orders Table -->
        <div class="lg:col-span-2">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Recent Orders
                        </h3>
                        <a href="{{ route('food-provider.orders.index') }}" 
                           class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            View all →
                        </a>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">
                        Latest customer orders
                    </p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order ID
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Customer
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <!-- Order 1 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #ORD-001
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    John Doe
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Pay-per-eat
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Delivered
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₹250
                                </td>
                            </tr>
                            <!-- Order 2 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #ORD-002
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Jane Smith
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        Subscription
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                        Preparing
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₹320
                                </td>
                            </tr>
                            <!-- Order 3 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #ORD-003
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Robert Johnson
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Pay-per-eat
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        Out for Delivery
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₹450
                                </td>
                            </tr>
                            <!-- Order 4 -->
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #ORD-004
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    Sarah Williams
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                        Pay-per-eat
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                        Delayed
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    ₹280
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Notifications Panel -->
        <div>
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg leading-6 font-medium text-gray-900">
                            Notifications
                        </h3>
                        <span class="text-xs text-gray-500">5 unread</span>
                    </div>
                </div>
                <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                    <!-- Notification 1 -->
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-shopping-cart text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-800">
                                    <span class="font-medium">New order</span> received from Jane Smith
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    10 minutes ago
                                </p>
                            </div>
                            <div class="ml-2">
                                <span class="h-2 w-2 bg-blue-500 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification 2 -->
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-check-circle text-green-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-800">
                                    Order <span class="font-medium">#ORD-002</span> has been delivered
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    45 minutes ago
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification 3 -->
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-800">
                                    Order <span class="font-medium">#ORD-004</span> delivery is delayed
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    1 hour ago
                                </p>
                            </div>
                            <div class="ml-2">
                                <span class="h-2 w-2 bg-yellow-500 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification 4 -->
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-star text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-800">
                                    You received a <span class="font-medium">5-star review</span> from John Doe
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    2 hours ago
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification 5 -->
                    <div class="px-4 py-3 hover:bg-gray-50">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-calendar-alt text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm text-gray-800">
                                    New subscription started by Robert Johnson
                                </p>
                                <p class="text-xs text-gray-500 mt-1">
                                    5 hours ago
                                </p>
                            </div>
                            <div class="ml-2">
                                <span class="h-2 w-2 bg-indigo-500 rounded-full"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="px-4 py-3 bg-gray-50">
                    <a href="{{ route('food-provider.notifications.index') }}" 
                       class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View all notifications →
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                Performance Metrics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">92%</div>
                    <div class="text-sm text-gray-500 mt-1">Customer Satisfaction</div>
                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 rounded-full" style="width: 92%"></div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">88%</div>
                    <div class="text-sm text-gray-500 mt-1">On-Time Delivery</div>
                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-blue-500 rounded-full" style="width: 88%"></div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">96%</div>
                    <div class="text-sm text-gray-500 mt-1">Order Accuracy</div>
                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-purple-500 rounded-full" style="width: 96%"></div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">24min</div>
                    <div class="text-sm text-gray-500 mt-1">Avg. Prep Time</div>
                    <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-500 rounded-full" style="width: 80%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                Quick Links
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                 <a href="{{ route('food-provider.menu.items.create') }}" 
                   class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-indigo-300 transition-colors">
                    <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center mb-3">
                        <i class="fas fa-plus text-indigo-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Add Menu Item</span>
                    <span class="text-xs text-gray-500 mt-1">Create new</span>
                </a>
                
                <a href="{{ route('food-provider.orders.index') }}" 
                   class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-green-300 transition-colors">
                    <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center mb-3">
                        <i class="fas fa-shopping-cart text-green-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Manage Orders</span>
                    <span class="text-xs text-gray-500 mt-1">8 pending</span>
                </a>
                
                 <a href="{{ route('food-provider.profile.edit') }}" 
                   class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-blue-300 transition-colors">
                    <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center mb-3">
                        <i class="fas fa-cog text-blue-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">Restaurant Settings</span>
                    <span class="text-xs text-gray-500 mt-1">Update profile</span>
                </a>
                
                <a href="{{ route('food-provider.earnings.index') }}" 
                   class="flex flex-col items-center justify-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 hover:border-purple-300 transition-colors">
                    <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center mb-3">
                        <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                    </div>
                    <span class="text-sm font-medium text-gray-900">View Earnings</span>
                    <span class="text-xs text-gray-500 mt-1">₹15,240 this month</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
