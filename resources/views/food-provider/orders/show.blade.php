@extends('layouts.food-provider')

@section('title', 'Order Details - #' . $order->order_number)

@section('header', 'Order #' . $order->order_number)

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Order Status Banner -->
    <div class="mb-6">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-12 w-12 rounded-full {{ $order->status_color }} flex items-center justify-center">
                                <i class="fas fa-utensils {{ $order->status_icon_color }} text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Order #{{ $order->order_number }}</h2>
                            <div class="mt-1 flex items-center space-x-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $order->status_bg }} {{ $order->status_text }}">
                                    <i class="fas {{ $order->status_icon }} mr-1 text-xs"></i>
                                    {{ ucfirst($order->status) }}
                                </span>
                                <span class="text-sm text-gray-500">
                                    Placed on {{ $order->created_at->format('F d, Y') }} at {{ $order->created_at->format('h:i A') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <div class="flex items-center space-x-3">
                            <span class="text-sm text-gray-500">Order Type:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->type_bg }} {{ $order->type_text }}">
                                <i class="fas {{ $order->type_icon }} mr-1"></i>
                                {{ $order->order_type }}
                            </span>
                            <span class="text-lg font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Order Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Timeline -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Status Timeline</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        Track the progress of this order
                    </p>
                </div>
                <div class="p-6">
                    @include('components.food-provider.order-timeline', [
                        'currentStatus' => $order->status
                    ])
                </div>
            </div>

            <!-- Order Items -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Items</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ $order->items->count() }} items in this order
                    </p>
                </div>
                <div class="p-6">
                    <div class="flow-root">
                        <ul role="list" class="-my-6 divide-y divide-gray-200">
                            @foreach($order->items as $item)
                            <li class="py-6 flex">
                                <div class="flex-shrink-0 w-24 h-24 border border-gray-200 rounded-md overflow-hidden">
                                    <img src="{{ $item->menuItem->image_url ?? 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop' }}" 
                                         alt="{{ $item->menuItem->name ?? 'Food Item' }}" 
                                         class="w-full h-full object-center object-cover">
                                </div>
                                <div class="ml-4 flex-1 flex flex-col">
                                    <div>
                                        <div class="flex justify-between text-base font-medium text-gray-900">
                                            <h3>{{ $item->menuItem->name ?? 'Item Name' }}</h3>
                                            <p class="ml-4">₹{{ number_format($item->price, 2) }}</p>
                                        </div>
                                        <div class="mt-1 flex items-center space-x-2">
                                            <span class="px-2 py-0.5 text-xs rounded-full {{ $item->menuItem->is_vegetarian ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $item->menuItem->is_vegetarian ? 'Vegetarian' : 'Non-Veg' }}
                                            </span>
                                            <span class="text-sm text-gray-500">{{ $item->menuItem->description ?? '' }}</span>
                                        </div>
                                        <div class="mt-2">
                                            <span class="text-xs text-gray-500">Quantity: {{ $item->quantity }}</span>
                                        </div>
                                    </div>
                                    <div class="flex-1 flex items-end justify-between text-sm">
                                        <p class="text-gray-500">Preparation Time: {{ $item->menuItem->preparation_time ?? '15-20' }} mins</p>
                                        <div class="flex">
                                            <p class="font-medium">Total: ₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Customer Notes & Special Instructions -->
            @if($order->special_instructions || $order->allergy_alert || $order->delivery_instructions)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Notes & Instructions</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($order->allergy_alert)
                    <!-- Allergy Alert -->
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Allergy Alert:</strong> {{ $order->allergy_alert }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($order->special_instructions)
                    <!-- Special Instructions -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Special Instructions</h4>
                        <div class="bg-gray-50 p-4 rounded-md">
                            <p class="text-sm text-gray-700">
                                "{{ $order->special_instructions }}"
                            </p>
                        </div>
                    </div>
                    @endif

                    @if($order->delivery_instructions)
                    <!-- Delivery Instructions -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Delivery Instructions</h4>
                        <div class="bg-blue-50 p-4 rounded-md">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-blue-500 mt-0.5 mr-2"></i>
                                <div>
                                    <p class="text-sm text-blue-700">
                                        <strong>Delivery Instructions:</strong> {{ $order->delivery_instructions }}
                                    </p>
                                    @if($order->is_contactless_delivery)
                                    <p class="text-xs text-blue-600 mt-1">
                                        Customer prefers contactless delivery
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Customer Info & Actions -->
        <div class="space-y-6">
            <!-- Customer Information -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Customer Information</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-800 font-medium text-lg">
                                {{ substr($order->user->name, 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <h5 class="font-medium text-gray-900">{{ $order->user->name }}</h5>
                            <div class="flex items-center text-sm text-gray-500">
                                <i class="fas fa-star text-yellow-400 mr-1"></i>
                                <span>{{ $order->user->rating ?? '4.5' }} • 
                                    @if($order->user->order_count > 10)
                                        Regular Customer
                                    @elseif($order->user->order_count > 5)
                                        Returning Customer
                                    @else
                                        New Customer
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-phone text-gray-400 mr-2 w-5"></i>
                            <span>{{ $order->user->phone ?? 'Not provided' }}</span>
                        </div>
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-envelope text-gray-400 mr-2 w-5"></i>
                            <span>{{ $order->user->email }}</span>
                        </div>
                        <div class="flex items-start text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-2 w-5 mt-0.5"></i>
                            <span>{{ $order->delivery_address }}</span>
                        </div>
                        <div class="pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-pin {{ $order->delivery_distance <= 2 ? 'text-green-400' : 'text-orange-400' }} mr-2"></i>
                                <span class="{{ $order->delivery_distance <= 2 ? 'text-green-600' : 'text-orange-600' }} font-medium">
                                    <i class="fas fa-check-circle mr-1"></i>
                                    Within {{ $order->delivery_distance }}km • 
                                    Delivery: {{ $order->estimated_delivery_time }} mins
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Order Summary</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">{{ $item->menuItem->name }} ({{ $item->quantity }})</span>
                            <span class="font-medium">₹{{ number_format($item->price * $item->quantity, 2) }}</span>
                        </div>
                        @endforeach
                        
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Fee</span>
                            <span class="font-medium">₹{{ number_format($order->delivery_fee, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Tax ({{ $order->tax_percentage }}%)</span>
                            <span class="font-medium">₹{{ number_format($order->tax_amount, 2) }}</span>
                        </div>
                        <div class="border-t pt-3">
                            <div class="flex justify-between text-base font-medium">
                                <span>Total</span>
                                <span>₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-1">
                                <span>Platform Commission ({{ $order->commission_percentage }}%)</span>
                                <span class="text-red-600">-₹{{ number_format($order->commission_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm font-medium text-green-700 mt-2">
                                <span>Your Earnings</span>
                                <span>₹{{ number_format($order->provider_earnings, 2) }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-200">
                        <div class="flex justify-between text-xs text-gray-500">
                            <span>Payment Method</span>
                            <span class="font-medium text-gray-700">
                                <i class="fas fa-credit-card mr-1"></i>
                                {{ ucfirst($order->payment_method) }} • 
                                <span class="{{ $order->payment_status == 'paid' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Update Status</h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('food-provider.orders.update-status', $order->id) }}" method="POST" class="space-y-3">
                        @csrf
                        @method('PATCH')
                        
                        @if($order->status == 'pending')
                        <button type="submit" name="status" value="accepted" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            <i class="fas fa-check mr-2"></i> Accept Order
                        </button>
                        @endif
                        
                        @if($order->status == 'accepted')
                        <button type="submit" name="status" value="preparing" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-utensils mr-2"></i> Start Preparing
                        </button>
                        @endif
                        
                        @if($order->status == 'preparing')
                        <button type="submit" name="status" value="out_for_delivery" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-shipping-fast mr-2"></i> Out for Delivery
                        </button>
                        @endif
                        
                        @if($order->status == 'out_for_delivery')
                        <button type="submit" name="status" value="delivered" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-flag-checkered mr-2"></i> Mark as Delivered
                        </button>
                        @endif
                        
                        @if(in_array($order->status, ['pending', 'accepted', 'preparing']))
                        <div class="pt-3 border-t">
                            <button type="button" data-modal-toggle="delay-modal"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 text-sm font-medium rounded-md shadow-sm text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <i class="fas fa-exclamation-triangle mr-2"></i> Report Delay
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Delivery Information -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delivery Information</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Estimated Prep Time</span>
                            <span class="font-medium">{{ $order->estimated_preparation_time }} mins</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Delivery Time</span>
                            <span class="font-medium">{{ $order->estimated_delivery_time }} mins</span>
                        </div>
                        <div class="pt-3 border-t">
                            <div class="flex justify-between text-sm font-medium">
                                <span>Total Estimated Time</span>
                                <span class="text-blue-600">{{ $order->estimated_total_time }} mins</span>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-clock mr-1"></i>
                                Order should be delivered by {{ $order->estimated_delivery_at->format('h:i A') }}
                            </div>
                        </div>
                    </div>
                    <div class="mt-6">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-600">Delivery Address</span>
                            <a href="https://maps.google.com/?q={{ urlencode($order->delivery_address) }}" 
                               target="_blank"
                               class="text-indigo-600 hover:text-indigo-500 text-xs">
                                <i class="fas fa-directions mr-1"></i> Get Directions
                            </a>
                        </div>
                        <div class="mt-2 p-3 bg-gray-50 rounded-md">
                            <p class="text-sm text-gray-700">
                                {{ $order->delivery_address }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Quick Actions</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-3">
                        <button type="button" onclick="window.print()"
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-print mr-2"></i> Print
                        </button>
                        <a href="{{ route('food-provider.orders.invoice', $order->id) }}"
                           class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-file-invoice mr-2"></i> Invoice
                        </a>
                        <a href="tel:{{ $order->user->phone }}"
                           class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-phone-alt mr-2"></i> Call
                        </a>
                        <button type="button"
                                class="inline-flex items-center justify-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-comment-alt mr-2"></i> Message
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Previous Orders from Same Customer -->
    @if($previousOrders->count() > 0)
    <div class="mt-8 bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Previous Orders from {{ $order->user->name }}</h3>
            <p class="mt-1 text-sm text-gray-500">
                Customer's order history
            </p>
        </div>
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Items
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($previousOrders as $prevOrder)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prevOrder->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                #{{ $prevOrder->order_number }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $prevOrder->items->count() }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                ₹{{ number_format($prevOrder->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $prevOrder->status == 'delivered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($prevOrder->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- Delay Report Modal -->
<div id="delay-modal" tabindex="-1" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0 h-modal md:h-full">
    <!-- Modal content here -->
</div>
@endsection