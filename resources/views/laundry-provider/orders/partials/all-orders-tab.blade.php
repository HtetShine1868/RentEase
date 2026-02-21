<div class="bg-white rounded-lg shadow-sm p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-[#174455] flex items-center">
            <i class="fas fa-list text-[#ffdb9f] mr-2"></i> 
            ALL ORDERS
            <span class="ml-2 text-sm bg-gray-100 text-gray-800 px-2 py-0.5 rounded-full">
                {{ isset($orders) ? $orders->count() : 0 }}
            </span>
        </h3>
        <div class="flex gap-2">
            <select id="status-filter" class="text-sm border rounded-lg px-3 py-1.5 focus:border-[#174455] focus:ring-[#174455]">
                <option value="all">All Status</option>
                <option value="pending">Pending</option>
                <option value="pickup_scheduled">Pickup Scheduled</option>
                <option value="picked_up">Picked Up</option>
                <option value="in_progress">In Progress</option>
                <option value="ready">Ready</option>
                <option value="out_for_delivery">Out for Delivery</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <button onclick="exportOrders()" class="px-3 py-1.5 bg-[#174455] text-white text-sm rounded-lg hover:bg-[#1f556b]">
                <i class="fas fa-download mr-1"></i> Export
            </button>
        </div>
    </div>
    
    <div class="space-y-3" id="orders-list">
        @if(isset($orders) && $orders->count() > 0)
            @foreach($orders as $order)
                @include('laundry-provider.orders.partials.order-card', [
                    'order' => $order,
                    'type' => $order->service_mode == 'RUSH' ? 'rush-progress' : 'progress',
                    'rush' => $order->service_mode == 'RUSH'
                ])
            @endforeach
        @else
            <div class="text-center py-12 text-gray-500">
                <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                <p class="text-lg">No orders found</p>
                <p class="text-sm">Try adjusting your filters or date range</p>
            </div>
        @endif
    </div>
    
    @if(isset($orders) && $orders->count() > 10)
        <div class="mt-6 flex items-center justify-between">
            <p class="text-sm text-gray-600">Showing 1-10 of {{ $orders->count() }} orders</p>
            <div class="flex gap-2">
                <button class="px-3 py-1 border rounded hover:bg-gray-50">Previous</button>
                <button class="px-3 py-1 bg-[#174455] text-white rounded hover:bg-[#1f556b]">Next</button>
            </div>
        </div>
    @endif
</div>

<script>
function exportOrders() {
    const date = document.querySelector('[x-data]').__x.$data.selectedDate || '{{ date('Y-m-d') }}';
    window.location.href = `/laundry-provider/orders/export?date=${date}`;
}

document.getElementById('status-filter')?.addEventListener('change', function() {
    const status = this.value;
    const orders = document.querySelectorAll('#orders-list .order-card');
    
    orders.forEach(order => {
        if (status === 'all' || order.dataset.status === status) {
            order.style.display = 'block';
        } else {
            order.style.display = 'none';
        }
    });
});
</script>