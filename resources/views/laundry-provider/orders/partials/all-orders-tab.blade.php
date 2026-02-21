@php
    $ordersCollection = $orders ?? collect([]);
@endphp

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
        <h3 class="text-lg font-semibold text-[#174455] flex items-center">
            <i class="fas fa-list text-[#ffdb9f] mr-2"></i> 
            ALL ORDERS
            <span class="ml-3 bg-gray-100 text-gray-800 text-sm px-3 py-1 rounded-full">
                {{ method_exists($ordersCollection, 'total') ? $ordersCollection->total() : $ordersCollection->count() }} orders
            </span>
        </h3>
        
        {{-- Status Filter --}}
        @if($ordersCollection->count() > 0)
        <div class="flex items-center gap-2">
            <select id="all-status-filter" class="text-sm border rounded-lg px-3 py-1.5 focus:border-[#174455] focus:ring-[#174455]">
                <option value="all">All Status</option>
                <option value="PENDING">Pending</option>
                <option value="PICKUP_SCHEDULED">Pickup Scheduled</option>
                <option value="PICKED_UP">Picked Up</option>
                <option value="IN_PROGRESS">In Progress</option>
                <option value="READY">Ready</option>
                <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                <option value="DELIVERED">Delivered</option>
                <option value="CANCELLED">Cancelled</option>
            </select>
        </div>
        @endif
    </div>
    
    <div class="p-6">
        @if($ordersCollection->count() > 0)
            <div class="space-y-3" id="all-orders-list">
                @foreach($ordersCollection as $order)
                    <div class="all-order-item" data-status="{{ $order->status }}">
                        @include('laundry-provider.orders.partials.order-card', [
                            'order' => $order,
                            'type' => $order->service_mode == 'RUSH' ? 'rush' : 'normal',
                            'rush' => $order->service_mode == 'RUSH'
                        ])
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            @if(method_exists($ordersCollection, 'links'))
            <div class="mt-6">
                {{ $ordersCollection->links() }}
            </div>
            @endif
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                <p class="text-lg font-medium">No orders found</p>
            </div>
        @endif
    </div>
</div>

@if($ordersCollection->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('all-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const orderItems = document.querySelectorAll('.all-order-item');
            
            orderItems.forEach(item => {
                if (status === 'all' || item.dataset.status === status) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endif