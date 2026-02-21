@extends('laundry-provider.layouts.provider')

@section('title', 'Order #' . $order->order_reference)
@section('page-title', 'Order Details')

@section('content')
<div class="max-w-4xl mx-auto">
    {{-- Header --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_reference }}</h2>
                <p class="text-gray-500">Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}</p>
            </div>
            <div class="flex gap-2">
                <span class="px-3 py-1 rounded-full text-sm font-medium 
                    {{ $order->service_mode == 'RUSH' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $order->service_mode }} MODE
                </span>
                @include('laundry-provider.components.status-badge', ['status' => $order->status])
            </div>
        </div>
    </div>

    {{-- Customer Info --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-user-circle text-gray-400 mr-2"></i> Customer Information
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="font-medium">{{ $order->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p class="font-medium">{{ $order->user->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $order->user->email }}</p>
            </div>
        </div>
    </div>

    {{-- Pickup & Delivery --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-truck text-gray-400 mr-2"></i> Pickup & Delivery
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="border-r pr-6">
                <h4 class="font-medium text-gray-700 mb-2">Pickup Details</h4>
                <p class="text-sm text-gray-600 mb-1">
                    <i class="far fa-calendar mr-2"></i> {{ \Carbon\Carbon::parse($order->pickup_time)->format('F j, Y') }}
                </p>
                <p class="text-sm text-gray-600 mb-2">
                    <i class="far fa-clock mr-2"></i> {{ \Carbon\Carbon::parse($order->pickup_time)->format('g:i A') }}
                </p>
                <p class="text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt mr-2"></i> {{ $order->pickup_address }}
                </p>
                @if($order->pickup_instructions)
                    <div class="mt-2 p-2 bg-yellow-50 rounded text-sm">
                        <i class="fas fa-info-circle text-yellow-600 mr-1"></i>
                        {{ $order->pickup_instructions }}
                    </div>
                @endif
            </div>
            
            <div>
                <h4 class="font-medium text-gray-700 mb-2">Delivery Details</h4>
                <p class="text-sm text-gray-600 mb-1">
                    <i class="far fa-calendar mr-2"></i> Expected: {{ \Carbon\Carbon::parse($order->expected_return_date)->format('F j, Y') }}
                </p>
                @if($order->actual_return_date)
                    <p class="text-sm text-gray-600 mb-2">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i> 
                        Delivered: {{ \Carbon\Carbon::parse($order->actual_return_date)->format('F j, Y \a\t g:i A') }}
                    </p>
                @endif
                <p class="text-sm text-gray-600">
                    <i class="fas fa-map-marker-alt mr-2"></i> {{ $order->delivery_address ?? $order->pickup_address }}
                </p>
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 flex items-center">
            <i class="fas fa-tshirt text-gray-400 mr-2"></i> Items
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Item</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500">Type</th>
                        <th class="px-4 py-2 text-center text-sm font-medium text-gray-500">Quantity</th>
                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Unit Price</th>
                        <th class="px-4 py-2 text-right text-sm font-medium text-gray-500">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->orderItems as $item)
                    <tr>
                        <td class="px-4 py-3">{{ $item->laundryItem->item_name ?? 'Item' }}</td>
                        <td class="px-4 py-3">{{ $item->laundryItem->item_type ?? 'Clothing' }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right">৳{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-medium">৳{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-medium">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">Subtotal:</td>
                        <td class="px-4 py-3 text-right">৳{{ number_format($order->base_amount, 2) }}</td>
                    </tr>
                    @if($order->rush_surcharge > 0)
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right text-orange-600">Rush Surcharge:</td>
                        <td class="px-4 py-3 text-right text-orange-600">+ ৳{{ number_format($order->rush_surcharge, 2) }}</td>
                    </tr>
                    @endif
                    @if($order->pickup_fee > 0)
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">Pickup Fee:</td>
                        <td class="px-4 py-3 text-right">+ ৳{{ number_format($order->pickup_fee, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="border-t-2">
                        <td colspan="4" class="px-4 py-3 text-right font-bold">Total:</td>
                        <td class="px-4 py-3 text-right font-bold text-lg">৳{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Actions --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold mb-4">Actions</h3>
        <div class="flex flex-wrap gap-3">
            @if($order->status == 'PENDING')
                <button class="accept-order-btn px-6 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600" data-id="{{ $order->id }}">
                    <i class="fas fa-check mr-2"></i> Accept Order
                </button>
            @endif
            
            @if($order->status == 'PICKED_UP')
                <button class="start-processing-btn px-6 py-2 bg-purple-500 text-white rounded-lg hover:bg-purple-600" data-id="{{ $order->id }}">
                    <i class="fas fa-play mr-2"></i> Start Processing
                </button>
            @endif
            
            @if($order->status == 'READY')
                <button class="out-for-delivery-btn px-6 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600" data-id="{{ $order->id }}">
                    <i class="fas fa-truck mr-2"></i> Out for Delivery
                </button>
            @endif
            
            <a href="tel:{{ $order->user->phone }}" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-phone mr-2"></i> Call Customer
            </a>
            
            <button onclick="window.print()" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                <i class="fas fa-print mr-2"></i> Print Invoice
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).on('click', '.accept-order-btn', function() {
    const orderId = $(this).data('id');
    
    if(confirm('Accept this order?')) {
        $.ajax({
            url: '{{ route("laundry-provider.orders.update-status", "") }}/' + orderId,
            method: 'PATCH',
            data: {
                status: 'PICKUP_SCHEDULED',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON?.message || 'Could not accept order');
            }
        });
    }
});

$(document).on('click', '.start-processing-btn', function() {
    const orderId = $(this).data('id');
    
    if(confirm('Start processing this order?')) {
        $.ajax({
            url: '{{ route("laundry-provider.orders.update-status", "") }}/' + orderId,
            method: 'PATCH',
            data: {
                status: 'IN_PROGRESS',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON?.message || 'Could not update order');
            }
        });
    }
});

$(document).on('click', '.out-for-delivery-btn', function() {
    const orderId = $(this).data('id');
    
    if(confirm('Mark this order as out for delivery?')) {
        $.ajax({
            url: '{{ route("laundry-provider.orders.update-status", "") }}/' + orderId,
            method: 'PATCH',
            data: {
                status: 'OUT_FOR_DELIVERY',
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            },
            error: function(xhr) {
                alert('Error: ' + xhr.responseJSON?.message || 'Could not update order');
            }
        });
    }
});
</script>
@endpush
@endsection