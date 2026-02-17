@extends('layouts.food-provider')

@section('title', 'Order Details - #' . $order->order_reference)

@section('header', 'Order Details')

@section('content')
<div class="space-y-6">
    <!-- Header with back button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('food-provider.orders.index') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Order #{{ $order->order_reference }}
            </h2>
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
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('food-provider.orders.print', $order->id) }}" target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-print mr-2"></i>
                Print Invoice
            </a>
            @if(in_array($order->status, ['PENDING', 'ACCEPTED', 'PREPARING']))
                <form action="{{ route('food-provider.orders.update-status', $order->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    @if($order->status === 'PENDING')
                        <input type="hidden" name="status" value="ACCEPTED">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                            <i class="fas fa-check mr-2"></i>
                            Accept Order
                        </button>
                    @elseif($order->status === 'ACCEPTED')
                        <input type="hidden" name="status" value="PREPARING">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700">
                            <i class="fas fa-utensils mr-2"></i>
                            Start Preparing
                        </button>
                    @elseif($order->status === 'PREPARING')
                        <input type="hidden" name="status" value="OUT_FOR_DELIVERY">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            <i class="fas fa-motorcycle mr-2"></i>
                            Ready for Delivery
                        </button>
                    @endif
                </form>
            @endif
            @if($order->status === 'OUT_FOR_DELIVERY')
                <form action="{{ route('food-provider.orders.update-status', $order->id) }}" method="POST" class="inline">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="DELIVERED">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        <i class="fas fa-check-circle mr-2"></i>
                        Mark as Delivered
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Order Information Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Customer Information -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-800 font-medium text-lg">
                                {{ substr($order->user->name ?? 'NA', 0, 2) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Delivery Address</h4>
                        <p class="text-sm text-gray-900">{{ $order->delivery_address }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Distance: {{ number_format($order->distance_km, 2) }} km
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Order Type:</dt>
                        <dd class="font-medium text-gray-900">
                            {{ $order->order_type === 'SUBSCRIPTION_MEAL' ? 'Subscription Meal' : 'Pay-per-eat' }}
                            @if($order->subscription)
                                <span class="text-xs text-gray-500">(Sub #{{ $order->subscription_id }})</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Meal Type:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->mealType->name ?? 'N/A' }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Meal Date:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->meal_date->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Ordered On:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->created_at->format('d M Y, h:i A') }}</dd>
                    </div>
                    @if($order->estimated_delivery_time)
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Est. Delivery:</dt>
                        <dd class="font-medium {{ $order->estimated_delivery_time->isPast() && !in_array($order->status, ['DELIVERED', 'CANCELLED']) ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $order->estimated_delivery_time->format('h:i A, d M') }}
                        </dd>
                    </div>
                    @endif
                    @if($order->actual_delivery_time)
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Delivered On:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->actual_delivery_time->format('h:i A, d M') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Base Amount:</dt>
                        <dd class="font-medium text-gray-900">₹{{ number_format($order->base_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Delivery Fee:</dt>
                        <dd class="font-medium text-gray-900">₹{{ number_format($order->delivery_fee, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                        <dt class="text-gray-700 font-medium">Total Amount:</dt>
                        <dd class="font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Commission ({{ number_format($order->commission_amount / $order->total_amount * 100, 1) ?? 8 }}%):</dt>
                        <dd class="font-medium text-gray-900">₹{{ number_format($order->commission_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm bg-green-50 p-2 rounded">
                        <dt class="text-green-700 font-medium">Your Earnings:</dt>
                        <dd class="font-bold text-green-700">₹{{ number_format($order->total_amount - $order->commission_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item->foodItem->name ?? 'Unknown Item' }}</div>
                            @if($item->foodItem && $item->foodItem->dietary_tags)
                                <div class="text-xs text-gray-500">
                                    @foreach(json_decode($item->foodItem->dietary_tags) as $tag)
                                        <span class="inline-block px-2 py-1 bg-gray-100 rounded-full mr-1">{{ $tag }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($item->total_price, 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                            {{ $item->special_instructions ?? '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-medium text-gray-500">Subtotal:</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">₹{{ number_format($order->items->sum('total_price'), 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Timeline -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Timeline</h3>
        </div>
        <div class="px-4 py-5 sm:p-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @php
                        $timeline = [
                            ['status' => 'Order Placed', 'time' => $order->created_at, 'icon' => 'fa-shopping-cart', 'completed' => true],
                            ['status' => 'Order Accepted', 'time' => $order->status === 'ACCEPTED' ? $order->updated_at : null, 'icon' => 'fa-check', 'completed' => in_array($order->status, ['ACCEPTED', 'PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED'])],
                            ['status' => 'Preparing', 'time' => $order->status === 'PREPARING' ? $order->updated_at : null, 'icon' => 'fa-utensils', 'completed' => in_array($order->status, ['PREPARING', 'OUT_FOR_DELIVERY', 'DELIVERED'])],
                            ['status' => 'Out for Delivery', 'time' => $order->status === 'OUT_FOR_DELIVERY' ? $order->updated_at : null, 'icon' => 'fa-motorcycle', 'completed' => in_array($order->status, ['OUT_FOR_DELIVERY', 'DELIVERED'])],
                            ['status' => 'Delivered', 'time' => $order->actual_delivery_time, 'icon' => 'fa-check-circle', 'completed' => $order->status === 'DELIVERED'],
                        ];
                    @endphp
                    
                    @foreach($timeline as $index => $event)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                @endif
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white 
                                            {{ $event['completed'] ? 'bg-green-500' : 'bg-gray-400' }}">
                                            <i class="fas {{ $event['icon'] }} text-white text-sm"></i>
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                        <div>
                                            <p class="text-sm text-gray-500">{{ $event['status'] }}</p>
                                        </div>
                                        @if($event['time'])
                                            <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                <time datetime="{{ $event['time'] }}">{{ $event['time']->format('h:i A') }}</time>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection