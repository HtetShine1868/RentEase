@extends('layouts.laundry-provider')

@section('title', 'Rush Orders')

@section('header', 'Rush Orders')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Rush Orders
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Priority orders that need faster turnaround
            </p>
        </div>
        <a href="{{ route('laundry-provider.orders.index') }}" 
           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to All Orders
        </a>
    </div>

    @if($rushOrders->isEmpty())
        <div class="bg-white shadow-sm sm:rounded-lg p-12 text-center">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <i class="fas fa-bolt text-4xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No Rush Orders</h3>
            <p class="mt-1 text-sm text-gray-500">You don't have any rush orders at the moment.</p>
        </div>
    @else
        <!-- Urgency Summary -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">Critical</p>
                        <p class="text-2xl font-bold text-red-600">{{ $rushOrders->where('urgency', 'critical')->count() }}</p>
                        <p class="text-xs text-red-600">Due within 24 hours</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-yellow-800">Warning</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $rushOrders->where('urgency', 'warning')->count() }}</p>
                        <p class="text-xs text-yellow-600">Due in 1-2 days</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 border-l-4 border-green-500 rounded-lg p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">On Track</p>
                        <p class="text-2xl font-bold text-green-600">{{ $rushOrders->where('urgency', 'normal')->count() }}</p>
                        <p class="text-xs text-green-600">More than 2 days left</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rush Orders List -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">All Rush Orders</h3>
            </div>
            <div class="divide-y divide-gray-200">
                @foreach($rushOrders as $order)
                    @php
                        $urgencyColors = [
                            'critical' => 'bg-red-50 border-red-200',
                            'warning' => 'bg-yellow-50 border-yellow-200',
                            'normal' => 'bg-green-50 border-green-200'
                        ];
                        $returnDate = \Carbon\Carbon::parse($order->expected_return_date);
                    @endphp
                    <div class="p-6 {{ $urgencyColors[$order->urgency] }} border-l-4 {{ $order->urgency == 'critical' ? 'border-red-500' : ($order->urgency == 'warning' ? 'border-yellow-500' : 'border-green-500') }}">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <i class="fas fa-bolt text-purple-500 mr-2"></i>
                                    <h4 class="text-lg font-medium text-gray-900">
                                        #{{ $order->order_reference }}
                                    </h4>
                                    <span class="ml-3 px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                        Rush Order
                                    </span>
                                </div>
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <p class="text-sm text-gray-500">Customer</p>
                                        <p class="text-sm font-medium">{{ $order->user->name ?? 'N/A' }}</p>
                                        <p class="text-xs text-gray-500">{{ $order->user->phone ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status</p>
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($order->status == 'PENDING') bg-yellow-100 text-yellow-800
                                            @elseif($order->status == 'PICKED_UP') bg-blue-100 text-blue-800
                                            @elseif($order->status == 'IN_PROGRESS') bg-purple-100 text-purple-800
                                            @elseif($order->status == 'READY') bg-green-100 text-green-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ str_replace('_', ' ', $order->status) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Expected Return</p>
                                        <p class="text-sm font-medium {{ $order->days_left <= 1 ? 'text-red-600' : ($order->days_left <= 2 ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ $returnDate->format('d M Y') }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            @if($order->days_left < 0)
                                                Overdue by {{ abs($order->days_left) }} days
                                            @elseif($order->days_left == 0)
                                                Due today
                                            @else
                                                {{ $order->days_left }} days left
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="ml-4">
                                <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                   class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    View Details
                                    <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection