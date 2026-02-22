@extends('dashboard')

@section('title', 'My Laundry Orders')
@section('subtitle', 'Track and manage your laundry orders')

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-[#174455]">My Laundry Orders</h2>
                <p class="text-gray-600">Track and manage all your laundry orders</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laundry.index') }}" 
                   class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors text-sm">
                    <i class="fas fa-plus mr-2"></i> New Order
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                    <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Order Tabs --}}
    <div class="bg-white rounded-lg shadow-sm p-1 flex space-x-1">
        <button onclick="filterOrders('all')" 
                class="tab-btn active flex-1 py-2 px-4 rounded-lg text-center font-medium transition-colors bg-[#174455] text-white"
                data-tab="all">
            All Orders
        </button>
        <button onclick="filterOrders('pending')" 
                class="tab-btn flex-1 py-2 px-4 rounded-lg text-center font-medium transition-colors hover:bg-gray-100"
                data-tab="pending">
            Pending
        </button>
        <button onclick="filterOrders('processing')" 
                class="tab-btn flex-1 py-2 px-4 rounded-lg text-center font-medium transition-colors hover:bg-gray-100"
                data-tab="processing">
            Processing
        </button>
        <button onclick="filterOrders('completed')" 
                class="tab-btn flex-1 py-2 px-4 rounded-lg text-center font-medium transition-colors hover:bg-gray-100"
                data-tab="completed">
            Completed
        </button>
    </div>

    {{-- Orders List --}}
    <div class="space-y-4" id="orders-list">
        @forelse($orders as $order)
        <div class="bg-white rounded-lg shadow-sm p-6 order-card" data-status="{{ $order->status }}">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                {{-- Left Section --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2 flex-wrap">
                        <span class="font-bold text-lg text-[#174455]">#{{ $order->order_reference }}</span>
                        @if($order->is_rush)
                            <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">
                                ⚡ RUSH
                            </span>
                        @endif
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
                        <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                            {{ str_replace('_', ' ', $order->status) }}
                        </span>
                        @if($order->is_overdue)
                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Overdue</span>
                        @endif
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <p class="text-gray-500">Provider</p>
                            <p class="font-medium">{{ $order->serviceProvider->business_name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Items</p>
                            {{-- FIXED: Changed orderItems to items --}}
                            <p class="font-medium">{{ $order->items ? $order->items->sum('quantity') : 0 }} items</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Pickup</p>
                            <p class="font-medium">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('M d, g:i A') : 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-gray-500">Total</p>
                            <p class="font-bold text-[#174455]">৳{{ number_format($order->total_amount, 2) }}</p>
                        </div>
                    </div>
                    
                    {{-- Progress Bar --}}
                    <div class="mt-3">
                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                            <span>Progress</span>
                            <span>{{ $order->progress_percentage }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                            <div class="bg-[#174455] h-1.5 rounded-full" style="width: {{ $order->progress_percentage }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Right Section - Actions --}}
                <div class="flex flex-col items-end gap-2">
                    <a href="{{ route('laundry.order.show', $order->id) }}" 
                       class="px-4 py-2 bg-[#174455] text-white text-sm rounded-lg hover:bg-[#1f556b] transition-colors text-center w-full">
                        View Details
                    </a>
                    
                    @if($order->status == 'DELIVERED')
                        @php
                            $hasRated = App\Models\ServiceRating::where('order_id', $order->id)
                                ->where('order_type', 'LAUNDRY')
                                ->exists();
                        @endphp
                        
                        @if(!$hasRated)
                            <a href="{{ route('laundry.rate.show', $order->id) }}" 
                               class="px-4 py-2 bg-[#ffdb9f] text-[#174455] text-sm rounded-lg hover:bg-[#f8c570] transition-colors text-center w-full">
                                Rate Service
                            </a>
                        @else
                            <span class="px-4 py-2 bg-gray-100 text-gray-500 text-sm rounded-lg text-center w-full">
                                <i class="fas fa-check-circle mr-1"></i> Rated
                            </span>
                        @endif
                    @endif
                    
                    @if(in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED']))
                        <button onclick="cancelOrder({{ $order->id }})" 
                                class="px-4 py-2 bg-red-50 text-red-600 text-sm rounded-lg hover:bg-red-100 transition-colors w-full">
                            Cancel Order
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-lg shadow-sm p-12 text-center text-gray-500">
            <i class="fas fa-tshirt text-5xl text-gray-300 mb-3"></i>
            <p class="text-lg font-medium">No laundry orders yet</p>
            <p class="text-sm mt-1">Book your first laundry service now</p>
            <div class="flex gap-3 justify-center mt-4">
                <a href="{{ route('laundry.index') }}" 
                   class="inline-block bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                    Find Laundry Services
                </a>
                <a href="{{ route('dashboard') }}" 
                   class="inline-block bg-gray-100 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                    Go to Dashboard
                </a>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($orders->hasPages())
    <div class="mt-4">
        {{ $orders->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    function filterOrders(status) {
        // Update active tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#174455]', 'text-white');
        });
        event.target.classList.add('bg-[#174455]', 'text-white');
        
        // Filter orders
        const orders = document.querySelectorAll('.order-card');
        orders.forEach(order => {
            const orderStatus = order.dataset.status;
            
            if (status === 'all') {
                order.style.display = 'block';
            } else if (status === 'pending') {
                if (['PENDING', 'PICKUP_SCHEDULED'].includes(orderStatus)) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            } else if (status === 'processing') {
                if (['PICKED_UP', 'IN_PROGRESS', 'READY', 'OUT_FOR_DELIVERY'].includes(orderStatus)) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            } else if (status === 'completed') {
                if (['DELIVERED', 'CANCELLED'].includes(orderStatus)) {
                    order.style.display = 'block';
                } else {
                    order.style.display = 'none';
                }
            }
        });
    }
    
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
                location.reload();
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