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
                <span>Commission: {{ $commissionRate ?? '8.00' }}% per order</span>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        @php
            // Use null-safe operator for all variables
            $stats = [
                [
                    'title' => 'Pending Orders',
                    'value' => $pendingOrders ?? 0,
                    'change' => '+2',
                    'icon' => 'fas fa-clock',
                    'color' => 'blue'
                ],
                [
                    'title' => 'Today\'s Orders',
                    'value' => $todayOrders ?? 0,
                    'change' => '+12%',
                    'icon' => 'fas fa-shopping-cart',
                    'color' => 'green'
                ],
                [
                    'title' => 'Delayed Orders',
                    'value' => $delayedOrders ?? 0,
                    'change' => '-1',
                    'icon' => 'fas fa-exclamation-triangle',
                    'color' => 'yellow'
                ],
                [
                    'title' => 'Today\'s Revenue',
                    'value' => '₹' . number_format($todayRevenue ?? 0, 2),
                    'change' => '+18%',
                    'icon' => 'fas fa-rupee-sign',
                    'color' => 'purple'
                ]
            ];
        @endphp
        
        @foreach($stats as $stat)
            @include('components.food-provider.stats-card', $stat)
        @endforeach
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
                    <button type="button" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-download mr-2"></i>
                        Export Orders
                    </button>
                    <a href="{{ route('food-provider.menu.items.create') ?? '#' }}"
                       class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Menu Item
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Filters Component (only include if it exists) -->
    @if(View::exists('food-provider.orders.components.order-filters'))
        @include('food-provider.orders.components.order-filters', [
            'showAdvanced' => false,
            'onFilter' => 'applyOrderFilters()'
        ])
    @endif

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
                    <form method="GET" action="{{ route('food-provider.orders.index') }}" class="relative">
                        @csrf
                        <input type="text" 
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search orders..." 
                               class="pl-10 pr-4 py-2 border border-gray-300 rounded-md text-sm focus:ring-indigo-500 focus:border-indigo-500 w-full sm:w-64">
                        <div class="absolute left-3 top-2.5">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        @if(request('search'))
                            <a href="{{ route('food-provider.orders.index') }}" 
                               class="absolute right-10 top-2.5 text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times"></i>
                            </a>
                        @endif
                    </form>
                </div>
            </div>
        </div>

        @if(isset($orders) && $orders->isEmpty())
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-shopping-cart text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No orders yet</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't received any orders yet.</p>
                <div class="mt-6">
                    <a href="{{ route('food-provider.menu.items.create') ?? '#' }}"
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-plus mr-2"></i>
                        Add Menu Items to Get Started
                    </a>
                </div>
            </div>
        @elseif(isset($orders))
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
                        @foreach($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $order->order_reference ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ isset($order->created_at) ? $order->created_at->format('h:i A') : 'N/A' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-indigo-800 font-medium">
                                                {{ isset($order->user->name) ? substr($order->user->name, 0, 2) : 'NA' }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->user->name ?? 'Unknown Customer' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            @php
                                                if (isset($order->user)) {
                                                    $orderCount = $order->user->foodOrders->where('service_provider_id', $order->service_provider_id)->count();
                                                    if($orderCount > 10) {
                                                        echo 'Regular Customer';
                                                    } elseif($orderCount > 5) {
                                                        echo 'Returning Customer';
                                                    } else {
                                                        echo 'New Customer';
                                                    }
                                                } else {
                                                    echo 'Customer';
                                                }
                                            @endphp
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ ($order->order_type ?? '') === 'SUBSCRIPTION_MEAL' ? 'bg-indigo-100 text-indigo-800' : 'bg-pink-100 text-pink-800' }}">
                                    {{ ($order->order_type ?? 'PAY_PER_EAT') === 'SUBSCRIPTION_MEAL' ? 'Subscription' : 'Pay-per-eat' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ isset($order->items) ? $order->items->count() . ' items' : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'PENDING' => 'bg-blue-100 text-blue-800',
                                        'ACCEPTED' => 'bg-yellow-100 text-yellow-800',
                                        'PREPARING' => 'bg-yellow-100 text-yellow-800',
                                        'OUT_FOR_DELIVERY' => 'bg-purple-100 text-purple-800',
                                        'DELIVERED' => 'bg-green-100 text-green-800',
                                        'CANCELLED' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusText = [
                                        'PENDING' => 'Pending',
                                        'ACCEPTED' => 'Accepted',
                                        'PREPARING' => 'Preparing',
                                        'OUT_FOR_DELIVERY' => 'Out for Delivery',
                                        'DELIVERED' => 'Delivered',
                                        'CANCELLED' => 'Cancelled'
                                    ];
                                    $orderStatus = $order->status ?? 'PENDING';
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$orderStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusText[$orderStatus] ?? 'Unknown' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">₹{{ isset($order->total_amount) ? number_format($order->total_amount, 2) : '0.00' }}</div>
                                <div class="text-xs text-gray-500">
                                    You get: ₹{{ isset($order->total_amount, $order->commission_amount) ? number_format($order->total_amount - $order->commission_amount, 2) : '0.00' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    @if(isset($order->id))
                                    <a href="{{ route('food-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
                                    @if(isset($order->status) && $order->status === 'PENDING')
                                    <form action="{{ isset($order->id) ? route('food-provider.orders.update-status', $order->id) : '#' }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="ACCEPTED">
                                        <button type="submit" 
                                                class="text-green-600 hover:text-green-900"
                                                title="Accept Order"
                                                onclick="return confirm('Accept this order?')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if(isset($order->status) && $order->status === 'PREPARING')
                                    <form action="{{ isset($order->id) ? route('food-provider.orders.update-status', $order->id) : '#' }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="OUT_FOR_DELIVERY">
                                        <button type="submit" 
                                                class="text-blue-600 hover:text-blue-900"
                                                title="Mark as Ready"
                                                onclick="return confirm('Mark this order as out for delivery?')">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <button type="button" onclick="{{ isset($order->id) ? "printOrder('{$order->id}')" : 'void(0)' }}"
                                            class="text-gray-600 hover:text-gray-900"
                                            title="Print">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden">
                <div class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <div class="p-4 hover:bg-gray-50">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="text-sm font-medium text-gray-900">{{ $order->order_reference ?? 'N/A' }}</div>
                                <div class="text-xs text-gray-500">{{ isset($order->created_at) ? $order->created_at->format('h:i A') : 'N/A' }}</div>
                            </div>
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-blue-100 text-blue-800',
                                    'ACCEPTED' => 'bg-yellow-100 text-yellow-800',
                                    'PREPARING' => 'bg-yellow-100 text-yellow-800',
                                    'OUT_FOR_DELIVERY' => 'bg-purple-100 text-purple-800',
                                    'DELIVERED' => 'bg-green-100 text-green-800',
                                    'CANCELLED' => 'bg-red-100 text-red-800'
                                ];
                                $orderStatus = $order->status ?? 'PENDING';
                            @endphp
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$orderStatus] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst(strtolower($orderStatus)) }}
                            </span>
                        </div>
                        
                        <div class="mt-2">
                            <div class="flex items-center">
                                <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center mr-2">
                                    <span class="text-indigo-800 font-medium text-xs">
                                        {{ isset($order->user->name) ? substr($order->user->name, 0, 2) : 'NA' }}
                                    </span>
                                </div>
                                <div class="text-sm">{{ $order->user->name ?? 'Unknown Customer' }}</div>
                            </div>
                        </div>
                        
                        <div class="mt-2 grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="ml-1 font-medium">
                                    {{ ($order->order_type ?? 'PAY_PER_EAT') === 'SUBSCRIPTION_MEAL' ? 'Subscription' : 'Pay-per-eat' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-gray-500">Items:</span>
                                <span class="ml-1 font-medium">{{ isset($order->items) ? $order->items->count() : 'N/A' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Amount:</span>
                                <span class="ml-1 font-medium">₹{{ isset($order->total_amount) ? number_format($order->total_amount, 2) : '0.00' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">You get:</span>
                                <span class="ml-1 font-medium text-green-600">
                                    ₹{{ isset($order->total_amount, $order->commission_amount) ? number_format($order->total_amount - $order->commission_amount, 2) : '0.00' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-3 flex justify-end space-x-2">
                            @if(isset($order->id))
                            <a href="{{ route('food-provider.orders.show', $order->id) }}" 
                               class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            @endif
                            @if(isset($order->status) && $order->status === 'PENDING')
                            <form action="{{ isset($order->id) ? route('food-provider.orders.update-status', $order->id) : '#' }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="ACCEPTED">
                                <button type="submit" 
                                        class="inline-flex items-center px-2 py-1 border border-green-300 text-xs font-medium rounded text-green-700 bg-white hover:bg-green-50"
                                        onclick="return confirm('Accept this order?')">
                                    <i class="fas fa-check mr-1"></i> Accept
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            @if(isset($orders) && method_exists($orders, 'links'))
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
                                <span class="font-medium">{{ $orders->firstItem() ?? 0 }}</span>
                                to
                                <span class="font-medium">{{ $orders->lastItem() ?? 0 }}</span>
                                of
                                <span class="font-medium">{{ $orders->total() ?? 0 }}</span>
                                orders
                            </p>
                        </div>
                        <div>
                            @if(method_exists($orders, 'links'))
                                {{ $orders->links('vendor.pagination.tailwind') }}
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        @else
            <!-- No orders data available -->
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Unable to load orders</h3>
                <p class="mt-1 text-sm text-gray-500">There was an error loading order data.</p>
            </div>
        @endif
    </div>

    <!-- Order Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Status Distribution -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Status Distribution</h3>
                <div class="space-y-4">
                    @if(isset($orderStatusDistribution) && count($orderStatusDistribution) > 0)
                        @foreach($orderStatusDistribution as $stat)
                            @php
                                $width = isset($stat['total'], $stat['count']) && $stat['total'] > 0 ? ($stat['count'] / $stat['total']) * 100 : 0;
                                $colors = [
                                    'PENDING' => 'bg-blue-500',
                                    'ACCEPTED' => 'bg-yellow-500',
                                    'PREPARING' => 'bg-yellow-500',
                                    'OUT_FOR_DELIVERY' => 'bg-purple-500',
                                    'DELIVERED' => 'bg-green-500',
                                    'CANCELLED' => 'bg-red-500'
                                ];
                                $status = $stat['status'] ?? 'UNKNOWN';
                            @endphp
                            <div>
                                <div class="flex justify-between text-sm mb-1">
                                    <span class="text-gray-600">{{ $status }}</span>
                                    <span class="font-medium">{{ $stat['count'] ?? 0 }} orders</span>
                                </div>
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full {{ $colors[$status] ?? 'bg-gray-500' }} rounded-full"
                                         style="width: {{ $width }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">No order distribution data available</p>
                        </div>
                    @endif
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
                @if(isset($todaysSchedule) && $todaysSchedule->isNotEmpty())
                    <div class="space-y-3">
                        @foreach($todaysSchedule as $schedule)
                            @php
                                $isDelayed = isset($schedule->status, $schedule->actual_delivery_time, $schedule->estimated_delivery_time) && 
                                            $schedule->status === 'DELIVERED' && 
                                            $schedule->actual_delivery_time > $schedule->estimated_delivery_time;
                                $isOnTime = isset($schedule->status, $schedule->actual_delivery_time, $schedule->estimated_delivery_time) && 
                                           $schedule->status === 'DELIVERED' && 
                                           $schedule->actual_delivery_time <= $schedule->estimated_delivery_time;
                            @endphp
                            <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-full {{ $isDelayed ? 'bg-red-100' : 'bg-green-100' }} flex items-center justify-center">
                                            <i class="fas fa-clock {{ $isDelayed ? 'text-red-600' : 'text-green-600' }}"></i>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ isset($schedule->estimated_delivery_time) ? $schedule->estimated_delivery_time->format('h:i A') : 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $schedule->user->name ?? 'Unknown Customer' }}</div>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-sm font-medium text-gray-900">{{ $schedule->order_reference ?? 'N/A' }}</div>
                                    <div class="text-xs {{ $isDelayed ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $isDelayed ? 'Delayed' : ($isOnTime ? 'On Time' : 'Scheduled') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="mx-auto h-12 w-12 text-gray-400">
                            <i class="fas fa-clock text-3xl"></i>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">No deliveries scheduled for today</p>
                    </div>
                @endif
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <a href="#" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                        View full schedule for the week →
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function printOrder(orderId) {
    if (orderId) {
        window.open('/food-provider/orders/' + orderId + '/print', '_blank');
    }
}
</script>
@endsection