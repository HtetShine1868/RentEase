@php
    // Ensure all required keys exist with default empty collections
    $pickupToday = isset($orders['pickup_today']) ? $orders['pickup_today'] : collect([]);
    $deliverToday = isset($orders['deliver_today']) ? $orders['deliver_today'] : collect([]);
    $activeOrders = isset($orders['active']) ? $orders['active'] : collect([]);
@endphp

<div class="space-y-6">
    {{-- Section 1: MUST PICKUP TODAY --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-clock text-[#ffdb9f] mr-2"></i> 
                MUST PICKUP TODAY
                <span class="ml-3 bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                    {{ $pickupToday->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($pickupToday->count() > 0)
                <div class="space-y-3">
                    @foreach($pickupToday as $order)
                        @include('laundry-provider.orders.partials.order-card', [
                            'order' => $order,
                            'type' => 'pickup'
                        ])
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p class="text-lg font-medium">No orders to pickup today</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 2: MUST DELIVER TODAY --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-truck text-[#ffdb9f] mr-2"></i> 
                MUST DELIVER TODAY
                <span class="ml-3 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    {{ $deliverToday->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($deliverToday->count() > 0)
                <div class="space-y-3">
                    @foreach($deliverToday as $order)
                        @include('laundry-provider.orders.partials.order-card', [
                            'order' => $order,
                            'type' => 'deliver'
                        ])
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p class="text-lg font-medium">No orders to deliver today</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 3: ALL ACTIVE ORDERS --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-list text-[#ffdb9f] mr-2"></i> 
                ALL ACTIVE ORDERS
                <span class="ml-3 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                    {{ $activeOrders->count() }} orders
                </span>
            </h3>
            
            {{-- Status Filter --}}
            @if($activeOrders->count() > 0)
            <div class="flex items-center gap-2">
                <select id="normal-status-filter" class="text-sm border rounded-lg px-3 py-1.5 focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all">All Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="PICKUP_SCHEDULED">Pickup Scheduled</option>
                    <option value="PICKED_UP">Picked Up</option>
                    <option value="IN_PROGRESS">In Progress</option>
                    <option value="READY">Ready</option>
                    <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                </select>
            </div>
            @endif
        </div>
        
        <div class="p-6">
            @if($activeOrders->count() > 0)
                <div class="space-y-3" id="normal-orders-list">
                    @foreach($activeOrders as $order)
                        <div class="normal-order-item" data-status="{{ $order->status }}">
                            @include('laundry-provider.orders.partials.order-card', [
                                'order' => $order,
                                'type' => 'active'
                            ])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                    <p class="text-lg font-medium">No active orders</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($activeOrders->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('normal-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const orderItems = document.querySelectorAll('.normal-order-item');
            
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