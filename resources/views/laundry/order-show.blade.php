@extends('dashboard')

@section('title', 'Order Details')
@section('subtitle', 'Track your laundry order')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        {{-- Header --}}
        <div class="px-6 py-4 bg-gray-50 border-b flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-[#174455]">Order #{{ $order->order_reference }}</h3>
                <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y g:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laundry.my-orders') }}" class="text-gray-600 hover:text-gray-800">
                    <i class="fas fa-arrow-left mr-1"></i> Back
                </a>
                <a href="{{ route('dashboard') }}" class="text-[#174455] hover:text-[#1f556b]">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
            </div>
        </div>

        {{-- Status Timeline --}}
        <div class="p-6 border-b">
            <h4 class="font-medium text-gray-900 mb-4">Order Status</h4>
            <div class="relative">
                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                
                @php
                    $statuses = [
                        'PENDING' => ['label' => 'Order Placed', 'icon' => 'fa-check-circle', 'time' => $order->created_at],
                        'PICKUP_SCHEDULED' => ['label' => 'Pickup Scheduled', 'icon' => 'fa-calendar-check', 'time' => $order->pickup_time],
                        'PICKED_UP' => ['label' => 'Picked Up', 'icon' => 'fa-box-open', 'time' => $order->pickup_time],
                        'IN_PROGRESS' => ['label' => 'Processing', 'icon' => 'fa-spinner', 'time' => null],
                        'READY' => ['label' => 'Ready for Delivery', 'icon' => 'fa-check-circle', 'time' => null],
                        'OUT_FOR_DELIVERY' => ['label' => 'Out for Delivery', 'icon' => 'fa-truck', 'time' => null],
                        'DELIVERED' => ['label' => 'Delivered', 'icon' => 'fa-check-double', 'time' => $order->actual_return_date],
                    ];
                    
                    $currentStatus = $order->status;
                @endphp
                
                @foreach($statuses as $key => $status)
                    @php
                        $isCompleted = array_search($key, array_keys($statuses)) <= array_search($currentStatus, array_keys($statuses));
                    @endphp
                    <div class="relative pl-12 pb-6 last:pb-0">
                        <div class="absolute left-2 -translate-x-1/2 w-8 h-8 rounded-full flex items-center justify-center
                            {{ $isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }}">
                            <i class="fas {{ $status['icon'] }}"></i>
                        </div>
                        <div>
                            <h5 class="font-medium {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">
                                {{ $status['label'] }}
                            </h5>
                            @if($status['time'])
                                <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($status['time'])->format('M d, g:i A') }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Progress Bar --}}
            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Progress</span>
                    <span>{{ $order->progress_percentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-[#174455] h-2 rounded-full" style="width: {{ $order->progress_percentage }}%"></div>
                </div>
            </div>

            @if($order->is_overdue)
                <div class="mt-4 p-3 bg-red-50 text-red-700 rounded-lg">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    This order is overdue. Please contact the provider.
                </div>
            @endif

            @if($order->urgency_level == 'urgent')
                <div class="mt-4 p-3 bg-orange-50 text-orange-700 rounded-lg">
                    <i class="fas fa-clock mr-2"></i>
                    This order requires attention soon.
                </div>
            @endif
        </div>

        {{-- Order Details --}}
        <div class="p-6 space-y-6">
            {{-- Provider Info --}}
            <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg">
                <div class="flex-shrink-0">
                    @if($order->serviceProvider && $order->serviceProvider->avatar_url)
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
                    <h4 class="font-semibold text-gray-900">{{ $order->serviceProvider->business_name ?? 'N/A' }}</h4>
                    <p class="text-sm text-gray-600">{{ $order->serviceProvider->contact_phone ?? 'N/A' }}</p>
                    <p class="text-sm text-gray-600">{{ $order->serviceProvider->address ?? 'N/A' }}</p>
                </div>
            </div>

            {{-- Order Info Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-xs text-gray-500">Service Mode</p>
                    <p class="font-medium">
                        @if($order->is_rush)
                            <span class="text-orange-600">⚡ Rush Service ({{ $order->rush_surcharge_percent }}% surcharge)</span>
                        @else
                            <span class="text-blue-600">Normal Service</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Distance</p>
                    <p class="font-medium">{{ $order->distance_km ?? 'N/A' }} km</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Expected Return</p>
                    <p class="font-medium">{{ $order->expected_return_date ? \Carbon\Carbon::parse($order->expected_return_date)->format('M d, Y') : 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <span class="px-2 py-1 rounded-full text-xs 
                        @if($order->status == 'DELIVERED') bg-green-100 text-green-800
                        @elseif($order->status == 'CANCELLED') bg-red-100 text-red-800
                        @elseif(in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED'])) bg-yellow-100 text-yellow-800
                        @else bg-blue-100 text-blue-800
                        @endif">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            {{-- Pickup Details --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Pickup Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500">Pickup Address</p>
                        <p class="text-sm">{{ $order->pickup_address }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Pickup Time</p>
                        <p class="text-sm">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('M d, Y g:i A') : 'N/A' }}</p>
                    </div>
                    @if($order->pickup_instructions)
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500">Instructions</p>
                        <p class="text-sm">{{ $order->pickup_instructions }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Items List --}}
            <div>
                <h4 class="font-medium text-gray-900 mb-3">Items</h4>
                <div class="space-y-2">
                    @if($order->items && $order->items->count() > 0)
                        @foreach($order->items as $item)
                        <div class="flex justify-between text-sm">
                            <span>
                                <span class="font-medium">{{ $item->quantity }}x</span> 
                                {{ $item->laundryItem->item_name ?? 'Item' }}
                            </span>
                            <span class="font-medium">৳{{ number_format($item->total_price ?? ($item->quantity * $item->unit_price), 2) }}</span>
                        </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500">No items found for this order.</p>
                    @endif
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
                    <span>MMK {{ number_format($order->pickup_fee, 2) }}</span>
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
            @if(in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED']))
            <div class="pt-4">
                <button onclick="cancelOrder({{ $order->id }})" 
                        class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors">
                    <i class="fas fa-times mr-2"></i> Cancel Order
                </button>
            </div>
            @endif

            @if($order->status == 'DELIVERED')
                @php
                    $hasRated = App\Models\ServiceRating::where('order_id', $order->id)
                        ->where('order_type', 'LAUNDRY')
                        ->exists();
                @endphp
                
                @if(!$hasRated)
                <div class="pt-4">
                    <a href="{{ route('laundry.rate.show', $order->id) }}" 
                       class="w-full block text-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition-colors">
                        <i class="fas fa-star mr-2"></i> Rate This Service
                    </a>
                </div>
                @else
                <div class="pt-4 p-4 bg-green-50 text-green-700 rounded-lg text-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    Thank you for rating this order!
                </div>
                @endif
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cancelOrder(orderId) {
        const reason = prompt('Please provide a reason for cancellation:');
        if (!reason) return;
        
        fetch(`/laundry/order/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order cancelled successfully');
                window.location.href = '{{ route("laundry.my-orders") }}';
            } else {
                alert(data.message || 'Error cancelling order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
</script>
@endpush
@endsection