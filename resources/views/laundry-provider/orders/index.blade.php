@extends('layouts.laundry-provider')

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
                Manage laundry pickup and delivery orders
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('laundry-provider.orders.export') }}?{{ http_build_query(request()->except('page')) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i>
                Export Orders
            </a>
        </div>
    </div>

    <!-- Stats Cards Overview -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-box text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Orders</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Pickup</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] + $stats['pickup_scheduled'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-bolt text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rush Orders</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['rush'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Ready for Return</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['ready'] + $stats['out_for_delivery'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button onclick="switchTab('pickup')" 
                    id="pickup-tab"
                    class="tab-button border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm"
                    aria-current="page">
                <i class="fas fa-box-open mr-2"></i>
                Take Clothes (Pickup)
                <span class="ml-2 bg-indigo-100 text-indigo-600 py-0.5 px-2 rounded-full text-xs">
                    {{ $stats['pending'] + $stats['pickup_scheduled'] + $stats['picked_up'] }}
                </span>
            </button>
            <button onclick="switchTab('delivery')" 
                    id="delivery-tab"
                    class="tab-button border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                <i class="fas fa-truck mr-2"></i>
                Send Clothes (Delivery)
                <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">
                    {{ $stats['in_progress'] + $stats['ready'] + $stats['out_for_delivery'] }}
                </span>
            </button>
        </nav>
    </div>

    <!-- Pickup Tab Content -->
    <div id="pickup-content" class="tab-content">
        <!-- Pickup Filters -->
        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-filter mr-2 text-indigo-500"></i>
                        Filter Pickup Orders
                    </h3>
                    <button onclick="resetPickupFilters()" class="text-sm text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" id="pickup-search" placeholder="Order # or customer..." 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="pickup-status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="PENDING">Pending</option>
                            <option value="PICKUP_SCHEDULED">Pickup Scheduled</option>
                            <option value="PICKED_UP">Picked Up</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Mode</label>
                        <select id="pickup-mode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All</option>
                            <option value="NORMAL">Normal</option>
                            <option value="RUSH">Rush</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Pickup Date</label>
                        <input type="date" id="pickup-date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Pickup Orders Table -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-box-open text-blue-500 mr-2"></i>
                            Orders Ready for Pickup
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Orders that need to be picked up from customers</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <i class="fas fa-clock mr-1"></i> Pending: {{ $stats['pending'] }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <i class="fas fa-calendar mr-1"></i> Scheduled: {{ $stats['pickup_scheduled'] }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                            <i class="fas fa-check mr-1"></i> Picked: {{ $stats['picked_up'] }}
                        </span>
                    </div>
                </div>
            </div>
            
            @php
                $pickupOrders = $orders->filter(function($order) {
                    return in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED', 'PICKED_UP']);
                });
            @endphp

            @if($pickupOrders->isEmpty())
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-box-open text-4xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No pickup orders</h3>
                    <p class="mt-1 text-sm text-gray-500">All orders have been picked up or there are no pending pickups.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="pickup-table-body">
                            @foreach($pickupOrders as $order)
                                @php
                                    $isRush = $order->service_mode === 'RUSH';
                                    $pickupTime = \Carbon\Carbon::parse($order->pickup_time);
                                    $isOverdue = $pickupTime->isPast() && $order->status == 'PENDING';
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isRush ? 'bg-purple-50' : '' }} pickup-row"
                                    data-status="{{ $order->status }}"
                                    data-mode="{{ $order->service_mode }}"
                                    data-date="{{ $pickupTime->format('Y-m-d') }}"
                                    data-search="{{ strtolower($order->order_reference . ' ' . ($order->user->name ?? '')) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->order_reference }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->created_at->format('d M, h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->user->phone ?? 'No phone' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isRush ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas {{ $isRush ? 'fa-bolt' : 'fa-clock' }} mr-1"></i>
                                            {{ $order->service_mode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                'PICKUP_SCHEDULED' => 'bg-blue-100 text-blue-800',
                                                'PICKED_UP' => 'bg-indigo-100 text-indigo-800',
                                            ];
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] }}">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                        @if($isOverdue)
                                            <span class="ml-2 text-xs text-red-600">
                                                <i class="fas fa-exclamation-circle"></i> Overdue
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : 'text-gray-900' }}">
                                            {{ $pickupTime->format('d M, h:i A') }}
                                        </div>
                                        @if($isOverdue)
                                            <div class="text-xs text-red-500">
                                                {{ $pickupTime->diffForHumans() }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->items->sum('quantity') }} items
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->status == 'PENDING')
                                                <button onclick="schedulePickup({{ $order->id }})"
                                                        class="text-blue-600 hover:text-blue-900" title="Schedule Pickup">
                                                    <i class="fas fa-calendar-check"></i>
                                                </button>
                                            @endif
                                            @if($order->status == 'PICKUP_SCHEDULED')
                                                <button onclick="markPickedUp({{ $order->id }})"
                                                        class="text-green-600 hover:text-green-900" title="Mark as Picked Up">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <!-- Delivery Tab Content (Initially Hidden) -->
    <div id="delivery-content" class="tab-content hidden">
        <!-- Delivery Filters -->
        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-filter mr-2 text-indigo-500"></i>
                        Filter Delivery Orders
                    </h3>
                    <button onclick="resetDeliveryFilters()" class="text-sm text-indigo-600 hover:text-indigo-800">
                        <i class="fas fa-undo mr-1"></i> Reset
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" id="delivery-search" placeholder="Order # or customer..." 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select id="delivery-status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All Statuses</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="READY">Ready</option>
                            <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                            <option value="DELIVERED">Delivered</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Service Mode</label>
                        <select id="delivery-mode" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                            <option value="">All</option>
                            <option value="NORMAL">Normal</option>
                            <option value="RUSH">Rush</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Return Date</label>
                        <input type="date" id="delivery-return-date" 
                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Delivery Orders Table -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-truck text-green-500 mr-2"></i>
                            Orders Ready for Delivery
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Orders that need to be returned to customers</p>
                    </div>
                    <div class="flex space-x-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                            <i class="fas fa-spinner mr-1"></i> In Progress: {{ $stats['in_progress'] }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i> Ready: {{ $stats['ready'] }}
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                            <i class="fas fa-truck mr-1"></i> Out: {{ $stats['out_for_delivery'] }}
                        </span>
                    </div>
                </div>
            </div>
            
            @php
                $deliveryOrders = $orders->filter(function($order) {
                    return in_array($order->status, ['IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED']);
                });
            @endphp

            @if($deliveryOrders->isEmpty())
                <div class="text-center py-12">
                    <div class="mx-auto h-24 w-24 text-gray-400">
                        <i class="fas fa-truck text-4xl"></i>
                    </div>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No delivery orders</h3>
                    <p class="mt-1 text-sm text-gray-500">No orders are ready for delivery at the moment.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Progress</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expected Return</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200" id="delivery-table-body">
                            @foreach($deliveryOrders as $order)
                                @php
                                    $isRush = $order->service_mode === 'RUSH';
                                    $returnDate = \Carbon\Carbon::parse($order->expected_return_date);
                                    $isOverdue = $returnDate->isPast() && !in_array($order->status, ['DELIVERED', 'CANCELLED']);
                                    $daysLeft = now()->diffInDays($returnDate, false);
                                    
                                    $progressStatuses = ['IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY', 'DELIVERED'];
                                    $currentIndex = array_search($order->status, $progressStatuses);
                                    $progress = $currentIndex !== false ? round(($currentIndex + 1) / count($progressStatuses) * 100) : 0;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $isRush ? 'bg-purple-50' : '' }} delivery-row"
                                    data-status="{{ $order->status }}"
                                    data-mode="{{ $order->service_mode }}"
                                    data-return="{{ $returnDate->format('Y-m-d') }}"
                                    data-search="{{ strtolower($order->order_reference . ' ' . ($order->user->name ?? '')) }}">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->order_reference }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->created_at->format('d M, h:i A') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $order->user->name ?? 'N/A' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $order->user->phone ?? 'No phone' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $isRush ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                            <i class="fas {{ $isRush ? 'fa-bolt' : 'fa-clock' }} mr-1"></i>
                                            {{ $order->service_mode }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ $progress }}%</span>
                                        </div>
                                        <span class="text-xs mt-1 inline-block px-2 py-1 rounded-full 
                                            @if($order->status == 'IN_PROGRESS') bg-purple-100 text-purple-800
                                            @elseif($order->status == 'READY') bg-green-100 text-green-800
                                            @elseif($order->status == 'OUT_FOR_DELIVERY') bg-orange-100 text-orange-800
                                            @elseif($order->status == 'DELIVERED') bg-green-100 text-green-800
                                            @endif">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : ($daysLeft <= 2 ? 'text-orange-600' : 'text-gray-900') }}">
                                            {{ $returnDate->format('d M Y') }}
                                        </div>
                                        @if($isOverdue)
                                            <div class="text-xs text-red-500">
                                                <i class="fas fa-exclamation-circle"></i> Overdue
                                            </div>
                                        @elseif($daysLeft <= 2 && $daysLeft > 0)
                                            <div class="text-xs text-orange-500">
                                                {{ $daysLeft }} days left
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->items->sum('quantity') }} items
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-3">
                                            <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                               class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($order->status == 'READY')
                                                <button onclick="startDelivery({{ $order->id }})"
                                                        class="text-orange-600 hover:text-orange-900" title="Start Delivery">
                                                    <i class="fas fa-truck"></i>
                                                </button>
                                            @endif
                                            @if($order->status == 'OUT_FOR_DELIVERY')
                                                <button onclick="markDelivered({{ $order->id }})"
                                                        class="text-green-600 hover:text-green-900" title="Mark as Delivered">
                                                    <i class="fas fa-check-circle"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Tab switching functionality
function switchTab(tab) {
    // Update tab buttons
    document.getElementById('pickup-tab').classList.remove('border-indigo-500', 'text-indigo-600');
    document.getElementById('pickup-tab').classList.add('border-transparent', 'text-gray-500');
    document.getElementById('delivery-tab').classList.remove('border-indigo-500', 'text-indigo-600');
    document.getElementById('delivery-tab').classList.add('border-transparent', 'text-gray-500');
    
    // Hide both contents
    document.getElementById('pickup-content').classList.add('hidden');
    document.getElementById('delivery-content').classList.add('hidden');
    
    // Show selected tab
    if (tab === 'pickup') {
        document.getElementById('pickup-tab').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('pickup-tab').classList.add('border-indigo-500', 'text-indigo-600');
        document.getElementById('pickup-content').classList.remove('hidden');
    } else {
        document.getElementById('delivery-tab').classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('delivery-tab').classList.add('border-indigo-500', 'text-indigo-600');
        document.getElementById('delivery-content').classList.remove('hidden');
    }
}

// Pickup Filters
document.getElementById('pickup-search').addEventListener('input', filterPickup);
document.getElementById('pickup-status').addEventListener('change', filterPickup);
document.getElementById('pickup-mode').addEventListener('change', filterPickup);
document.getElementById('pickup-date').addEventListener('change', filterPickup);

function filterPickup() {
    const search = document.getElementById('pickup-search').value.toLowerCase();
    const status = document.getElementById('pickup-status').value;
    const mode = document.getElementById('pickup-mode').value;
    const date = document.getElementById('pickup-date').value;
    
    const rows = document.querySelectorAll('.pickup-row');
    
    rows.forEach(row => {
        let show = true;
        
        if (search && !row.dataset.search.includes(search)) {
            show = false;
        }
        
        if (status && row.dataset.status !== status) {
            show = false;
        }
        
        if (mode && row.dataset.mode !== mode) {
            show = false;
        }
        
        if (date && row.dataset.date !== date) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

// Delivery Filters
document.getElementById('delivery-search').addEventListener('input', filterDelivery);
document.getElementById('delivery-status').addEventListener('change', filterDelivery);
document.getElementById('delivery-mode').addEventListener('change', filterDelivery);
document.getElementById('delivery-return-date').addEventListener('change', filterDelivery);

function filterDelivery() {
    const search = document.getElementById('delivery-search').value.toLowerCase();
    const status = document.getElementById('delivery-status').value;
    const mode = document.getElementById('delivery-mode').value;
    const returnDate = document.getElementById('delivery-return-date').value;
    
    const rows = document.querySelectorAll('.delivery-row');
    
    rows.forEach(row => {
        let show = true;
        
        if (search && !row.dataset.search.includes(search)) {
            show = false;
        }
        
        if (status && row.dataset.status !== status) {
            show = false;
        }
        
        if (mode && row.dataset.mode !== mode) {
            show = false;
        }
        
        if (returnDate && row.dataset.return !== returnDate) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function resetPickupFilters() {
    document.getElementById('pickup-search').value = '';
    document.getElementById('pickup-status').value = '';
    document.getElementById('pickup-mode').value = '';
    document.getElementById('pickup-date').value = '';
    filterPickup();
}

function resetDeliveryFilters() {
    document.getElementById('delivery-search').value = '';
    document.getElementById('delivery-status').value = '';
    document.getElementById('delivery-mode').value = '';
    document.getElementById('delivery-return-date').value = '';
    filterDelivery();
}

// Action functions
function schedulePickup(orderId) {
    const date = prompt('Enter pickup date and time (YYYY-MM-DD HH:MM):');
    if (date) {
        fetch(`/laundry-provider/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'PICKUP_SCHEDULED' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Pickup scheduled successfully!');
                window.location.reload();
            }
        });
    }
}

function markPickedUp(orderId) {
    if (confirm('Mark this order as picked up?')) {
        fetch(`/laundry-provider/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'PICKED_UP' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order marked as picked up!');
                window.location.reload();
            }
        });
    }
}

function startDelivery(orderId) {
    if (confirm('Start delivery for this order?')) {
        fetch(`/laundry-provider/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'OUT_FOR_DELIVERY' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Delivery started!');
                window.location.reload();
            }
        });
    }
}

function markDelivered(orderId) {
    if (confirm('Mark this order as delivered?')) {
        fetch(`/laundry-provider/orders/${orderId}/status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ status: 'DELIVERED' })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order marked as delivered!');
                window.location.reload();
            }
        });
    }
}
</script>
@endpush
@endsection