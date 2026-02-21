@php
    // Ensure all required keys exist with default empty collections
    $rushPickupToday = isset($orders['rush_pickup_today']) ? $orders['rush_pickup_today'] : collect([]);
    $rushDeliverToday = isset($orders['rush_deliver_today']) ? $orders['rush_deliver_today'] : collect([]);
    $activeOrders = isset($orders['active']) ? $orders['active'] : collect([]);
@endphp

<div class="space-y-6">
    {{-- Rush Mode Info --}}
    <div class="bg-[#ffdb9f] bg-opacity-20 border-l-4 border-[#ffdb9f] p-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-bolt text-[#ffdb9f] text-xl mr-3"></i>
            <div>
                <h4 class="font-medium text-[#174455]">⚡ RUSH MODE: 2h Pickup | 2d Delivery</h4>
                <p class="text-sm text-[#286b7f]">These orders need immediate attention</p>
            </div>
        </div>
    </div>

    {{-- Section 1: MUST PICKUP TODAY - RUSH --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-clock text-[#ffdb9f] mr-2"></i> 
                MUST PICKUP TODAY - RUSH
                <span class="ml-3 bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    {{ $rushPickupToday->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($rushPickupToday->count() > 0)
                <div class="space-y-3">
                    @foreach($rushPickupToday as $order)
                        @include('laundry-provider.orders.partials.order-card', [
                            'order' => $order,
                            'type' => 'rush-pickup',
                            'rush' => true
                        ])
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>No rush orders to pickup today</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 2: MUST DELIVER TODAY - RUSH --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-truck text-[#ffdb9f] mr-2"></i> 
                MUST DELIVER TODAY - RUSH
                <span class="ml-3 bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    {{ $rushDeliverToday->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($rushDeliverToday->count() > 0)
                <div class="space-y-3">
                    @foreach($rushDeliverToday as $order)
                        @include('laundry-provider.orders.partials.order-card', [
                            'order' => $order,
                            'type' => 'rush-deliver',
                            'rush' => true
                        ])
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>No rush orders to deliver today</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 3: ALL RUSH ACTIVE ORDERS --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-spinner text-[#ffdb9f] mr-2"></i> 
                RUSH ACTIVE ORDERS
                <span class="ml-3 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                    {{ $activeOrders->count() }} orders
                </span>
            </h3>
            
            {{-- Status Filter --}}
            @if($activeOrders->count() > 0)
            <div class="flex items-center gap-2">
                <select id="rush-status-filter" class="text-sm border rounded-lg px-3 py-1.5 focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all">All Status</option>
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
                <div class="space-y-3" id="rush-orders-list">
                    @foreach($activeOrders as $order)
                        <div class="rush-order-item" data-status="{{ $order->status }}">
                            @include('laundry-provider.orders.partials.order-card', [
                                'order' => $order,
                                'type' => 'rush-active',
                                'rush' => true
                            ])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                    <p>No rush active orders</p>
                </div>
            @endif
        </div>
    </div>
</div>

@if($activeOrders->count() > 0)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('rush-status-filter');
    if (statusFilter) {
        statusFilter.addEventListener('change', function() {
            const status = this.value;
            const orderItems = document.querySelectorAll('.rush-order-item');
            
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