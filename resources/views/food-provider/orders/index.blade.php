@extends('layouts.food-provider')

@section('title', 'Order Management')

@section('header', 'Orders')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Order Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and manage all customer orders
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-info-circle"></i>
                <span>Commission: 12% per order</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @include('components.food-provider.stats-card', [
            'title' => 'Pending Orders',
            'value' => '8',
            'change' => '+2',
            'icon' => 'fas fa-clock',
            'color' => 'blue'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Today\'s Orders',
            'value' => '24',
            'change' => '+12%',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'green'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Delayed Orders',
            'value' => '2',
            'change' => '-1',
            'icon' => 'fas fa-exclamation-triangle',
            'color' => 'yellow'
        ])
        
        @include('components.food-provider.stats-card', [
            'title' => 'Today\'s Revenue',
            'value' => '₹5,240',
            'change' => '+18%',
            'icon' => 'fas fa-rupee-sign',
            'color' => 'purple'
        ])
    </div>

    <!-- Quick Actions -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Manage orders efficiently with these actions
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-print mr-2"></i>
                        Print Orders
                    </button>
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-download mr-2"></i>
                        Export Orders
                    </button>
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-plus mr-2"></i>
                        New Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Filters Component -->
    @include('food-provider.orders.components.order-filters', [
        'showAdvanced' => false,
        'onFilter' => 'applyOrderFilters()'
    ])

    <!-- Orders Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <!-- Table Header -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Recent Orders
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Latest customer orders with status and actions
                    </p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <div class="relative">
                        <input type="text" 
                               placeholder="Search orders..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64">
                        <div class="absolute left-3 top-2.5">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Desktop Table -->
        <div class="hidden md:block">
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
                            Items
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @for($i = 1; $i <= 8; $i++)
                    @php
                        $orders = [
                            [
                                'id' => 'ORD-2023-001',
                                'customer' => 'John Doe',
                                'type' => 'pay-per-eat',
                                'items' => '2 items',
                                'status' => 'preparing',
                                'amount' => '₹450',
                                'time' => '10:30 AM',
                                'color' => 'blue'
                            ],
                            [
                                'id' => 'ORD-2023-002',
                                'customer' => 'Jane Smith',
                                'type' => 'subscription',
                                'items' => '3 items',
                                'status' => 'delivered',
                                'amount' => '₹680',
                                'time' => '09:45 AM',
                                'color' => 'green'
                            ],
                            [
                                'id' => 'ORD-2023-003',
                                'customer' => 'Robert Johnson',
                                'type' => 'pay-per-eat',
                                'items' => '1 item',
                                'status' => 'pending',
                                'amount' => '₹250',
                                'time' => '11:15 AM',
                                'color' => 'yellow'
                            ],
                            [
                                'id' => 'ORD-2023-004',
                                'customer' => 'Sarah Williams',
                                'type' => 'pay-per-eat',
                                'items' => '2 items',
                                'status' => 'out_for_delivery',
                                'amount' => '₹520',
                                'time' => '10:00 AM',
                                'color' => 'purple'
                            ],
                            [
                                'id' => 'ORD-2023-005',
                                'customer' => 'Michael Brown',
                                'type' => 'subscription',
                                'items' => '1 item',
                                'status' => 'delayed',
                                'amount' => '₹320',
                                'time' => '08:30 AM',
                                'color' => 'red'
                            ]
                        ];
                        $order = $orders[$i % 5];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $order['id'] }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $order['time'] }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium">
                                            {{ substr($order['customer'], 0, 2) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $order['customer'] }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        Regular Customer
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order['type'] === 'subscription' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800' }}">
                                {{ $order['type'] === 'subscription' ? 'Subscription' : 'Pay-per-eat' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order['items'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $order['status'] === 'delivered' ? 'bg-green-100 text-green-800' : 
                                   ($order['status'] === 'preparing' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($order['status'] === 'pending' ? 'bg-blue-100 text-blue-800' : 
                                   ($order['status'] === 'out_for_delivery' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $order['amount'] }}</div>
                            <div class="text-xs text-gray-500">
                                You get: ₹{{ number_format((int)str_replace('₹', '', $order['amount']) * 0.88, 2) }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('food-provider.orders.show', $i) }}" 
                                   class="text-indigo-600 hover:text-indigo-900"
                                   title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order['status'] === 'pending')
                                <button type="button" 
                                        class="text-green-600 hover:text-green-900"
                                        title="Accept Order">
                                    <i class="fas fa-check"></i>
                                </button>
                                @endif
                                @if($order['status'] === 'preparing')
                                <button type="button" 
                                        class="text-blue-600 hover:text-blue-900"
                                        title="Mark as Ready">
                                    <i class="fas fa-check-circle"></i>
                                </button>
                                @endif
                                <button type="button" 
                                        class="text-gray-600 hover:text-gray-900"
                                        title="Print">
                                    <i class="fas fa-print"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (Placeholder - will be implemented in Day 4) -->
        <div class="md:hidden p-4">
            <div class="text-center text-gray-500 py-8">
                <i class="fas fa-mobile-alt text-3xl mb-4"></i>
                <p>Mobile view will be implemented in Day 4</p>
                <p class="text-sm mt-2">Switch to desktop view for full order listing</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
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
                        <span class="font-medium">8</span>
                        of
                        <span class="font-medium">24</span>
                        orders
                    </p>
                </div>
                <div>
                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Previous</span>
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            1
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            2
                        </a>
                        <a href="#" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                            3
                        </a>
                        <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                            <span class="sr-only">Next</span>
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status Distribution -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="space-y-4">
                    @foreach([
                        ['status' => 'Pending', 'count' => 8, 'color' => 'bg-blue-500'],
                        ['status' => 'Preparing', 'count' => 5, 'color' => 'bg-yellow-500'],
                        ['status' => 'Out for Delivery', 'count' => 3, 'color' => 'bg-purple-500'],
                        ['status' => 'Delivered', 'count' => 24, 'color' => 'bg-green-500'],
                        ['status' => 'Delayed', 'count' => 2, 'color' => 'bg-red-500']
                        
                        ] as $stat)
                        @php
                            $width = ($stat['count'] / 42) * 100;
                        @endphp

                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $stat['status'] }}</span>
                                <span class="font-medium">{{ $stat['count'] }} orders</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $stat['color'] }} rounded-full"
                                     style="<?php echo 'width: '.$width.'%'; ?>">
                                </div>
                            </div>
                        </div>                       
                        @endforeach

                </div>
            </div>
        </div>

        <!-- Today's Schedule -->
        <div class="bg-white shadow-sm sm:rounded-lg lg:col-span-2">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Today's Delivery Schedule</h3>
                    <span class="text-sm text-gray-500">{{ now()->format('F d, Y') }}</span>
                </div>
                <div class="space-y-3">
                    @foreach([
                        ['time' => '11:00 AM', 'customer' => 'John Doe', 'order' => '#ORD-2023-001', 'status' => 'On Time'],
                        ['time' => '11:30 AM', 'customer' => 'Jane Smith', 'order' => '#ORD-2023-002', 'status' => 'On Time'],
                        ['time' => '12:00 PM', 'customer' => 'Robert Johnson', 'order' => '#ORD-2023-003', 'status' => 'Delayed'],
                        ['time' => '12:30 PM', 'customer' => 'Sarah Williams', 'order' => '#ORD-2023-004', 'status' => 'On Time'],
                        ['time' => '01:00 PM', 'customer' => 'Michael Brown', 'order' => '#ORD-2023-005', 'status' => 'On Time']
                    ] as $schedule)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-clock text-indigo-600"></i>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $schedule['time'] }}</div>
                                <div class="text-sm text-gray-500">{{ $schedule['customer'] }}</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">{{ $schedule['order'] }}</div>
                            <div class="text-xs {{ $schedule['status'] === 'On Time' ? 'text-green-600' : 'text-red-600' }}">
                                {{ $schedule['status'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View full schedule for the week →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection