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

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">📅 Filter by Date</label>
                <input type="date" id="rush-date-filter" value="{{ request('date', date('Y-m-d')) }}" 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">🔍 Search</label>
                <input type="text" id="rush-search" placeholder="Order #, Customer..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex items-end">
                <button onclick="applyRushFilters()" class="px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#1f556b] transition-colors">
                    <i class="fas fa-filter mr-2"></i>Apply
                </button>
                <button onclick="resetRushFilters()" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>
    </div>

    {{-- Section 1: MUST PICKUP TODAY - RUSH --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-clock text-[#ffdb9f] mr-2"></i> 
                MUST PICKUP TODAY - RUSH
                <span class="ml-3 bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['rush_pickup_today']->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($orders['rush_pickup_today']->count() > 0)
                <div class="space-y-3">
                    @foreach($orders['rush_pickup_today'] as $order)
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
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-truck text-[#ffdb9f] mr-2"></i> 
                MUST DELIVER TODAY - RUSH
                <span class="ml-3 bg-orange-100 text-orange-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['rush_deliver_today']->count() }} orders
                </span>
            </h3>
        </div>
        
        <div class="p-6">
            @if($orders['rush_deliver_today']->count() > 0)
                <div class="space-y-3">
                    @foreach($orders['rush_deliver_today'] as $order)
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

    {{-- Section 3: ALL RUSH ORDERS IN PROGRESS --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                <i class="fas fa-spinner text-[#ffdb9f] mr-2"></i> 
                RUSH IN PROGRESS
                <span class="ml-3 bg-purple-100 text-purple-800 text-sm px-3 py-1 rounded-full">
                    {{ $orders['rush_in_progress']->count() }} orders
                </span>
            </h3>
            
            {{-- Status Filter --}}
            <div class="flex items-center gap-2">
                <select id="rush-status-filter" class="text-sm border rounded-lg px-3 py-1.5 focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all">All Status</option>
                    <option value="PICKUP_SCHEDULED">Pickup Scheduled</option>
                    <option value="PICKED_UP">Picked Up</option>
                    <option value="IN_PROGRESS">In Progress</option>
                    <option value="READY">Ready</option>
                    <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                </select>
                <button onclick="applyRushStatusFilter()" class="text-sm px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </div>
        
        <div class="p-6">
            @if($orders['rush_in_progress']->count() > 0)
                <div class="space-y-3" id="rush-orders-list">
                    @foreach($orders['rush_in_progress'] as $order)
                        <div class="rush-order-item" data-status="{{ $order->status }}">
                            @include('laundry-provider.orders.partials.order-card', [
                                'order' => $order,
                                'type' => 'rush-progress',
                                'rush' => true
                            ])
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-2"></i>
                    <p>No rush orders in progress</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function applyRushFilters() {
    const date = document.getElementById('rush-date-filter').value;
    const search = document.getElementById('rush-search').value;
    
    window.location.href = `{{ route('laundry-provider.orders.index') }}?tab=rush&date=${date}&search=${search}`;
}

function resetRushFilters() {
    document.getElementById('rush-date-filter').value = '{{ date('Y-m-d') }}';
    document.getElementById('rush-search').value = '';
    window.location.href = `{{ route('laundry-provider.orders.index') }}?tab=rush`;
}

function applyRushStatusFilter() {
    const status = document.getElementById('rush-status-filter').value;
    const orderItems = document.querySelectorAll('.rush-order-item');
    
    orderItems.forEach(item => {
        if (status === 'all' || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}
</script>