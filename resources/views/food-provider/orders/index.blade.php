@extends('layouts.food-provider')

@section('title', 'Order Management')

@section('header', 'Orders')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:textq-3xl">
                Order Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and manage all customer orders
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <i class="fas fa-info-circle"></i>
                <span>Commission: {{ number_format($commissionRate, 2) }}% per order</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @php
            $stats = [
                [
                    'title' => 'Pending Orders',
                    'value' => $stats['pending'] ?? 0,
                    'change' => $stats['pending'] > 0 ? '+' . $stats['pending'] : '0',
                    'icon' => 'fas fa-clock',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Today\'s Orders',
                    'value' => $stats['today'] ?? 0,
                    'change' => $stats['today'] > 0 ? '+' . $stats['today'] : '0',
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'green'
                ],
                [
                    'title' => 'Delayed Orders',
                    'value' => $stats['delayed'] ?? 0,
                    'change' => $stats['delayed'] > 0 ? $stats['delayed'] : '0',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'yellow'
                ],
                [
                    'title' => 'Today\'s Revenue',
                    'value' => '₹' . number_format($stats['revenue_today'] ?? 0, 2),
                    'change' => $stats['revenue_today'] > 0 ? '+' . number_format($stats['revenue_today'] / 100, 0) . '%' : '0%',
                    'icon' => 'fas fa-rupee-sign',
                    'color' => 'purple'
                ]
            ];
        @endphp
        
        @foreach($stats as $stat)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-{{ $stat['color'] }}-100 rounded-md p-3">
                            <i class="{{ $stat['icon'] }} text-{{ $stat['color'] }}-600"></i>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    {{ $stat['title'] }}
                                </dt>
                                <dd class="flex items-baseline">
                                    <div class="text-2xl font-semibold text-gray-900">
                                        {{ $stat['value'] }}
                                    </div>
                                    <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                        {{ $stat['change'] }}
                                    </div>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('food-provider.orders.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ request('search') }}"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-3 pr-10 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Order ID or Customer">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" 
                                id="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                            <option value="ACCEPTED" {{ request('status') == 'ACCEPTED' ? 'selected' : '' }}>Accepted</option>
                            <option value="PREPARING" {{ request('status') == 'PREPARING' ? 'selected' : '' }}>Preparing</option>
                            <option value="OUT_FOR_DELIVERY" {{ request('status') == 'OUT_FOR_DELIVERY' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="DELIVERED" {{ request('status') == 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
                            <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
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
                            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                        <select name="sort" 
                                id="sort" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Order Date</option>
                            <option value="total_amount" {{ request('sort') == 'total_amount' ? 'selected' : '' }}>Amount</option>
                            <option value="status" {{ request('sort') == 'status' ? 'selected' : '' }}>Status</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('food-provider.orders.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
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
                    <button type="button" onclick="window.print()"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-print mr-2"></i>
                        Print Orders
                    </button>
                    <a href="{{ route('food-provider.orders.export') }}?{{ http_build_query(request()->except('page')) }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-download mr-2"></i>
                        Export Orders
                    </a>
                    <a href="{{ route('food-provider.menu.items.create') }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Menu Item
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($orders->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-shopping-cart text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't received any orders yet.</p>
                <div class="mt-6">
                    <a href="{{ route('food-provider.menu.items.create') }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Menu Items to Get Started
                    </a>
                </div>
            </div>
        @else
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order ID / Time
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
                                Est. Delivery
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    #{{ $order->order_reference }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->created_at->format('h:i A, d M') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium text-sm">
                                                {{ substr($order->user->name ?? 'NA', 0, 2) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->user->name ?? 'Unknown Customer' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                $orderCount = App\Models\FoodOrder::where('user_id', $order->user_id)
                                                    ->where('service_provider_id', $order->service_provider_id)
                                                    ->count();
                                                
                                                if($orderCount > 10) {
                                                    echo 'Regular';
                                                } elseif($orderCount > 5) {
                                                    echo 'Returning';
                                                } else {
                                                    echo 'New';
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $order->order_type === 'SUBSCRIPTION_MEAL' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ $order->order_type === 'SUBSCRIPTION_MEAL' ? 'Subscription' : 'Pay-per-eat' }}
                                </span>
                                @if($order->order_type === 'SUBSCRIPTION_MEAL' && $order->subscription)
                                    <span class="ml-1 text-xs text-gray-500">#{{ $order->subscription_id }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $order->items->count() }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'PENDING' => 'bg-yellow-100 text-yellow-800',
                                        'ACCEPTED' => 'bg-blue-100 text-blue-800',
                                        'PREPARING' => 'bg-purple-100 text-purple-800',
                                        'OUT_FOR_DELIVERY' => 'bg-indigo-100 text-indigo-800',
                                        'DELIVERED' => 'bg-green-100 text-green-800',
                                        'CANCELLED' => 'bg-red-100 text-red-800'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₹{{ number_format($order->total_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">
                                    You get: ₹{{ number_format($order->total_amount - $order->commission_amount, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($order->estimated_delivery_time)
                                    <div class="text-sm {{ $order->estimated_delivery_time->isPast() && !in_array($order->status, ['DELIVERED', 'CANCELLED']) ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $order->estimated_delivery_time->format('h:i A') }}
                                    </div>
                                    @if($order->actual_delivery_time)
                                        <div class="text-xs text-gray-500">
                                            Delivered: {{ $order->actual_delivery_time->format('h:i A') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">Not set</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('food-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if(in_array($order->status, ['PENDING', 'ACCEPTED', 'PREPARING']))
                                        <form action="{{ route('food-provider.orders.update-status', $order->id) }}" 
                                              method="POST" 
                                              class="inline status-form"
                                              data-order-id="{{ $order->id }}">
                                            @csrf
                                            @method('PATCH')
                                            
                                            @if($order->status === 'PENDING')
                                                <input type="hidden" name="status" value="ACCEPTED">
                                                <button type="submit" 
                                                        class="text-green-600 hover:text-green-900"
                                                        title="Accept Order"
                                                        onclick="return confirm('Accept this order?')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @elseif($order->status === 'ACCEPTED')
                                                <input type="hidden" name="status" value="PREPARING">
                                                <button type="submit" 
                                                        class="text-yellow-600 hover:text-yellow-900"
                                                        title="Start Preparing"
                                                        onclick="return confirm('Start preparing this order?')">
                                                    <i class="fas fa-utensils"></i>
                                                </button>
                                            @elseif($order->status === 'PREPARING')
                                                <input type="hidden" name="status" value="OUT_FOR_DELIVERY">
                                                <button type="submit" 
                                                        class="text-blue-600 hover:text-blue-900"
                                                        title="Mark as Ready for Delivery"
                                                        onclick="return confirm('Mark this order as ready for delivery?')">
                                                    <i class="fas fa-motorcycle"></i>
                                                </button>
                                            @endif
                                        </form>
                                    @endif
                                    
                                    @if($order->status === 'OUT_FOR_DELIVERY')
                                        <form action="{{ route('food-provider.orders.update-status', $order->id) }}" 
                                              method="POST" 
                                              class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="DELIVERED">
                                            <button type="submit" 
                                                    class="text-green-600 hover:text-green-900"
                                                    title="Mark as Delivered"
                                                    onclick="return confirm('Mark this order as delivered?')">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('food-provider.orders.print', $order->id) }}" 
                                       target="_blank"
                                       class="text-gray-600 hover:text-gray-900"
                                       title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($orders->previousPageUrl())
                        <a href="{{ $orders->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($orders->nextPageUrl())
                        <a href="{{ $orders->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $orders->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $orders->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $orders->total() }}</span>
                            orders
                        </p>
                    </div>
                    <div>
                        {{ $orders->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Order Status Distribution & Today's Schedule -->
    @if($orders->isNotEmpty())
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status Distribution -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="space-y-4">
                    @forelse($orderStatusDistribution as $stat)
                        @php
                            $colors = [
                                'PENDING' => 'bg-yellow-500',
                                'ACCEPTED' => 'bg-blue-500',
                                'PREPARING' => 'bg-purple-500',
                                'OUT_FOR_DELIVERY' => 'bg-indigo-500',
                                'DELIVERED' => 'bg-green-500',
                                'CANCELLED' => 'bg-red-500'
                            ];
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ str_replace('_', ' ', $stat['status']) }}</span>
                                <span class="font-medium">{{ $stat['count'] }} orders ({{ $stat['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $colors[$stat['status']] ?? 'bg-gray-500' }} rounded-full"
                                     style="width: {{ $stat['percentage'] }}%">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">No order distribution data available</p>
                        </div>
                    @endforelse
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
                
                @if($todaysSchedule->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($todaysSchedule as $schedule)
                            @php
                                $isDelayed = $schedule->status !== 'DELIVERED' && 
                                            $schedule->estimated_delivery_time && 
                                            $schedule->estimated_delivery_time->isPast();
                                
                                $isOnTime = $schedule->status === 'DELIVERED' && 
                                           $schedule->actual_delivery_time && 
                                           $schedule->estimated_delivery_time && 
                                           $schedule->actual_delivery_time <= $schedule->estimated_delivery_time;
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full {{ $isDelayed ? 'bg-red-100' : ($isOnTime ? 'bg-green-100' : 'bg-yellow-100') }} flex items-center justify-center">
                                            <i class="fas fa-clock {{ $isDelayed ? 'text-red-600' : ($isOnTime ? 'text-green-600' : 'text-yellow-600') }}"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $schedule->estimated_delivery_time ? $schedule->estimated_delivery_time->format('h:i A') : 'Not scheduled' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $schedule->user->name ?? 'Unknown Customer' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">#{{ $schedule->order_reference }}</div>
                                    <div class="text-xs {{ $isDelayed ? 'text-red-600' : ($isOnTime ? 'text-green-600' : 'text-yellow-600') }}">
                                        {{ $isDelayed ? 'Delayed' : ($isOnTime ? 'On Time' : 'Scheduled') }}
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
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">No deliveries scheduled for today</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
// Select all checkboxes functionality
document.getElementById('select-all')?.addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.order-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
});

// Bulk status update
document.getElementById('bulk-status-update')?.addEventListener('click', function() {
    const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selectedOrders.length === 0) {
        alert('Please select at least one order');
        return;
    }
    
    const status = prompt('Enter new status (ACCEPTED, PREPARING, OUT_FOR_DELIVERY, CANCELLED):');
    if (!status) return;
    
    fetch('{{ route("food-provider.orders.bulk-status") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            order_ids: selectedOrders,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    });
});

// Auto-refresh orders every 30 seconds (optional)
let refreshInterval = setInterval(function() {
    if (document.visibilityState === 'visible') {
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            // Update only the orders table section
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTable = doc.querySelector('.bg-white.shadow-sm.sm\\:rounded-lg.overflow-hidden');
            const currentTable = document.querySelector('.bg-white.shadow-sm.sm\\:rounded-lg.overflow-hidden');
            
            if (newTable && currentTable) {
                currentTable.innerHTML = newTable.innerHTML;
            }
        });
    }
}, 30000); // Refresh every 30 seconds

// Clear interval when leaving page
window.addEventListener('beforeunload', function() {
    clearInterval(refreshInterval);
});

// Print order function
function printOrder(orderId) {
    window.open('{{ url("food-provider/orders") }}/' + orderId + '/print', '_blank');
}

// Status update forms - prevent double submission
document.querySelectorAll('.status-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        if (this.dataset.submitted) {
            e.preventDefault();
        } else {
            this.dataset.submitted = true;
        }
    });
});
</script>
@endpush
@endsection