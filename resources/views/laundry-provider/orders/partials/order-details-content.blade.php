<div class="space-y-4">
    {{-- Order Header --}}
    <div class="flex items-center justify-between border-b pb-3">
        <div>
            <h4 class="font-bold text-lg text-[#174455]">#{{ $order->order_reference }}</h4>
            <p class="text-sm text-gray-500">Placed: {{ $order->created_at->format('M j, Y g:i A') }}</p>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-medium 
            {{ $order->service_mode == 'RUSH' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
            {{ $order->service_mode }}
        </span>
    </div>
    
    {{-- Customer Info --}}
    <div class="grid grid-cols-2 gap-4">
        <div>
            <p class="text-xs text-gray-500">Customer</p>
            <p class="font-medium">{{ $order->user->name }}</p>
            <p class="text-sm">{{ $order->user->phone }}</p>
        </div>
        <div>
            <p class="text-xs text-gray-500">Status</p>
            @php
                $statusColors = [
                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                    'PICKUP_SCHEDULED' => 'bg-blue-100 text-blue-800',
                    'PICKED_UP' => 'bg-purple-100 text-purple-800',
                    'IN_PROGRESS' => 'bg-indigo-100 text-indigo-800',
                    'READY' => 'bg-green-100 text-green-800',
                    'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                    'DELIVERED' => 'bg-gray-100 text-gray-800',
                    'CANCELLED' => 'bg-red-100 text-red-800'
                ];
            @endphp
            <span class="inline-block px-2 py-1 rounded-full text-xs {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
        </div>
    </div>
    
    {{-- Pickup Info --}}
    <div class="border-t pt-3">
        <p class="text-xs text-gray-500 mb-1">Pickup Details</p>
        <p class="text-sm">
            <i class="far fa-calendar text-gray-400 mr-1"></i> 
            {{ \Carbon\Carbon::parse($order->pickup_time)->format('M d, Y') }}
        </p>
        <p class="text-sm">
            <i class="far fa-clock text-gray-400 mr-1"></i> 
            {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}
        </p>
        <p class="text-sm mt-1">
            <i class="fas fa-map-marker-alt text-gray-400 mr-1"></i> 
            {{ $order->pickup_address }}
        </p>
        @if($order->pickup_instructions)
            <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                {{ $order->pickup_instructions }}
            </p>
        @endif
    </div>
    
    {{-- Items --}}
    <div class="border-t pt-3">
        <p class="text-xs text-gray-500 mb-2">Items</p>
        <div class="space-y-2 max-h-40 overflow-y-auto">
            @foreach($order->items as $item)
            <div class="flex justify-between text-sm">
                <span>
                    <span class="font-medium">{{ $item->quantity }}x</span> 
                    {{ $item->laundryItem->item_name ?? 'Item' }}
                </span>
                <span>৳{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    
    {{-- Price Summary --}}
    <div class="border-t pt-3">
        <div class="space-y-1">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span>৳{{ number_format($order->base_amount, 2) }}</span>
            </div>
            @if($order->rush_surcharge > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Rush Surcharge</span>
                <span class="text-orange-600">+ ৳{{ number_format($order->rush_surcharge, 2) }}</span>
            </div>
            @endif
            @if($order->pickup_fee > 0)
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Pickup Fee</span>
                <span>+ ৳{{ number_format($order->pickup_fee, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Commission</span>
                <span>৳{{ number_format($order->commission_amount, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold text-base pt-2 border-t">
                <span>Total</span>
                <span class="text-[#174455]">৳{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>
    </div>
    
    {{-- Expected Return --}}
    @if($order->expected_return_date)
    <div class="border-t pt-3">
        <p class="text-xs text-gray-500">Expected Return Date</p>
        <p class="font-medium">{{ \Carbon\Carbon::parse($order->expected_return_date)->format('M d, Y') }}</p>
    </div>
    @endif
</div>