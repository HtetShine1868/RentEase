<div class="space-y-6">
    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">📅 Filter by Date</label>
                <input type="date" id="normal-date-filter" value="{{ request('date', date('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">🔍 Search</label>
                <input type="text" id="normal-search" placeholder="Order #, Customer..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex items-end">
                <button onclick="applyNormalFilters()" class="px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#1f556b] transition-colors">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <button onclick="resetNormalFilters()" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>
        <div class="mt-2 text-sm text-gray-500">
            📊 Showing NORMAL orders for <span class="font-medium text-[#174455]" id="normal-current-date">{{ request('date', date('F j, Y')) }}</span>
        </div>
    </div>

    {{-- Section 1: MUST PICKUP TODAY --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-clock text-[#ffdb9f] mr-2"></i> 
                MUST PICKUP TODAY
                <span class="ml-3 bg-blue-100 text-blue-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['pickup_today']->count() }} orders
                </span>
            </h3>
            @if($orders['pickup_today']->count() > 5)
                <button class="view-all-section text-sm text-[#286b7f] hover:text-[#174455]" data-section="pickup">
                    View all {{ $orders['pickup_today']->count() }} →
                </button>
            @endif
        </div>
        
        <div class="p-6">
            @if($orders['pickup_today']->count() > 0)
                <div class="space-y-3">
                    @foreach($orders['pickup_today'] as $order)
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
                    <p class="text-sm mt-1">All pickup schedules are clear</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 2: MUST DELIVER TODAY --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-truck text-[#ffdb9f] mr-2"></i> 
                MUST DELIVER TODAY
                <span class="ml-3 bg-green-100 text-green-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['deliver_today']->count() }} orders
                </span>
            </h3>
            @if($orders['deliver_today']->count() > 5)
                <button class="view-all-section text-sm text-[#286b7f] hover:text-[#174455]" data-section="deliver">
                    View all {{ $orders['deliver_today']->count() }} →
                </button>
            @endif
        </div>
        
        <div class="p-6">
            @if($orders['deliver_today']->count() > 0)
                <div class="space-y-3">
                    @foreach($orders['deliver_today'] as $order)
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
                    <p class="text-sm mt-1">All deliveries are on schedule</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Section 3: ALL ACTIVE ORDERS (excluding delivered) --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-list text-[#ffdb9f] mr-2"></i> 
                ALL ACTIVE ORDERS
                <span class="ml-3 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['in_progress']->count() }} orders
                </span>
            </h3>
            
            {{-- Status Filter --}}
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
                <button onclick="applyNormalStatusFilter()" class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </div>
        
        <div class="p-6">
            @if($orders['in_progress']->count() > 0)
                <div class="space-y-3" id="normal-orders-list">
                    @foreach($orders['in_progress'] as $order)
                        <div class="normal-order-item" data-status="{{ $order->status }}">
                            @include('laundry-provider.orders.partials.order-card', [
                                'order' => $order,
                                'type' => 'progress'
                            ])
                        </div>
                    @endforeach
                </div>
                
                {{-- Load More Button --}}
                @if($orders['in_progress']->count() >= 20)
                    <div class="mt-6 text-center">
                        <button class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            <i class="fas fa-arrow-down mr-2"></i> Load More
                        </button>
                    </div>
                @endif
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                    <p class="text-lg font-medium">No active orders</p>
                    <p class="text-sm mt-1">All orders are completed or cancelled</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function applyNormalFilters() {
    const date = document.getElementById('normal-date-filter').value;
    const search = document.getElementById('normal-search').value;
    
    // Reload with filters
    window.location.href = `{{ route('laundry-provider.orders.index') }}?tab=normal&date=${date}&search=${search}`;
}

function resetNormalFilters() {
    document.getElementById('normal-date-filter').value = '{{ date('Y-m-d') }}';
    document.getElementById('normal-search').value = '';
    window.location.href = `{{ route('laundry-provider.orders.index') }}?tab=normal`;
}

function applyNormalStatusFilter() {
    const status = document.getElementById('normal-status-filter').value;
    const orderItems = document.querySelectorAll('.normal-order-item');
    
    orderItems.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// Initialize status filter on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const statusParam = urlParams.get('status');
    if (statusParam) {
        document.getElementById('normal-status-filter').value = statusParam;
        applyNormalStatusFilter();
    }
});
</script>