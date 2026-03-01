@extends('dashboard')

@section('title', 'Order Confirmation')
@section('subtitle', 'Your laundry order has been placed')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        {{-- Success Header --}}
        <div class="bg-green-50 p-8 text-center border-b">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-green-700 mb-2">Order Placed Successfully!</h2>
            <p class="text-gray-600">Your laundry order has been confirmed</p>
        </div>

        {{-- Order Details --}}
        <div class="p-6 space-y-6">
            {{-- Order Reference --}}
            <div class="text-center">
                <p class="text-sm text-gray-500">Order Reference</p>
                <p class="text-2xl font-bold text-[#174455]">{{ $order->order_reference }}</p>
            </div>

            {{-- Provider Info --}}
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    @if($order->serviceProvider->avatar_url)
                        <img src="{{ Storage::url($order->serviceProvider->avatar_url) }}" 
                             alt="{{ $order->serviceProvider->business_name }}"
                             class="w-16 h-16 rounded-lg object-cover">
                    @else
                        <div class="w-16 h-16 rounded-lg bg-[#174455] flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $order->serviceProvider->business_name }}</h4>
                    <p class="text-sm text-gray-600">{{ $order->serviceProvider->contact_phone }}</p>
                </div>
            </div>

            {{-- Order Details Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Service Mode</p>
                    <p class="font-medium">
                        @if($order->service_mode == 'RUSH')
                            <span class="text-orange-600">⚡ Rush Service</span>
                        @else
                            <span class="text-blue-600">Normal Service</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Pickup Date & Time</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($order->pickup_time)->format('M d, Y g:i A') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Expected Return</p>
                    <p class="font-medium">{{ \Carbon\Carbon::parse($order->expected_return_date)->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            {{-- Pickup Address --}}
            <div>
                <p class="text-xs text-gray-500 mb-1">Pickup Address</p>
                <p class="text-sm text-gray-700">{{ $order->pickup_address }}</p>
                @if($order->pickup_instructions)
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        {{ $order->pickup_instructions }}
                    </p>
                @endif
            </div>

            {{-- Items List --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Order Items</h4>
                <div class="space-y-2">
                    @foreach($order->orderItems as $item)
                    <div class="flex justify-between text-sm">
                        <span>
                            <span class="font-medium">{{ $item->quantity }}x</span> 
                            {{ $item->laundryItem->item_name }}
                        </span>
                        <span class="font-medium">MMK {{ number_format($item->total_price, 2) }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Price Summary --}}
            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Subtotal</span>
                    <span>MMK {{ number_format($order->base_amount, 2) }}</span>
                </div>
                @if($order->rush_surcharge > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Rush Surcharge</span>
                    <span>MMK {{ number_format($order->rush_surcharge, 2) }}</span>
                </div>
                @endif
                @if($order->pickup_fee > 0)
                <div class="flex justify-between">
                    <span class="text-gray-600">Pickup Fee</span>
                    <span>MMk {{ number_format($order->pickup_fee, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between">
                    <span class="text-gray-600">Commission</span>
                    <span>MMK {{ number_format($order->commission_amount, 2) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg">
                    <span>Total</span>
                    <span class="text-[#174455]">MMK {{ number_format($order->total_amount, 2) }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-4">
                <a href="{{ route('laundry.order.show', $order->id) }}" 
                   class="flex-1 text-center px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#1f556b] transition-colors">
                    <i class="fas fa-eye mr-2"></i> Track Order
                </a>
                <a href="{{ route('laundry.index') }}" 
                   class="flex-1 text-center px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-search mr-2"></i> Browse More
                </a>
            </div>
        </div>
    </div>
</div>
@endsection