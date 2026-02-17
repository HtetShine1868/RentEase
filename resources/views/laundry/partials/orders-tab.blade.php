<div>
    <!-- Filters and Actions -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-lg font-semibold text-gray-900">Your Laundry Orders</h3>
            <div class="flex gap-2 w-full sm:w-auto">
                <select x-model="orderStatusFilter" @change="loadOrders" class="flex-1 sm:flex-none border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Status</option>
                    <option value="PENDING">Pending</option>
                    <option value="PICKUP_SCHEDULED">Pickup Scheduled</option>
                    <option value="PICKED_UP">Picked Up</option>
                    <option value="IN_PROGRESS">In Progress</option>
                    <option value="READY">Ready</option>
                    <option value="OUT_FOR_DELIVERY">Out for Delivery</option>
                    <option value="DELIVERED">Delivered</option>
                    <option value="CANCELLED">Cancelled</option>
                </select>
                <button @click="loadOrders" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        <!-- Initial orders from server -->
        @if(count($recentOrders) > 0)
            @foreach($recentOrders as $order)
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-semibold text-gray-900">{{ $order['order_reference'] }}</span>
                            <span :class="getStatusBadgeClass('{{ $order['status'] }}')" 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                {{ str_replace('_', ' ', $order['status']) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $order['service_mode'] === 'RUSH' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ $order['service_mode'] }}
                            </span>
                        </div>
                        
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">{{ $order['business_name'] }}</span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i>{{ $order['created_at_formatted'] }}
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-calendar-check mr-1"></i>Expected: {{ $order['expected_return_date'] }}
                            </p>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($order['items'] as $item)
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700">
                                {{ $item['name'] }} × {{ $item['quantity'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="text-right w-full sm:w-auto">
                        <div class="text-lg font-bold text-gray-900">৳{{ number_format($order['total_amount'], 2) }}</div>
                        <div class="flex gap-2 mt-2 justify-end">
                            <button @click="viewOrderDetails({{ $order['id'] }})" 
                                    class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                                Details
                            </button>
                            @if(in_array($order['status'], ['PENDING', 'PICKUP_SCHEDULED']))
                            <button @click="cancelOrder({{ $order['id'] }})"
                                    class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                                Cancel
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @endif

        <!-- Dynamic orders from AJAX -->
        <template x-for="order in orders" :key="order.id">
            <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                <div class="flex flex-col sm:flex-row justify-between items-start gap-4">
                    <div class="flex-1">
                        <div class="flex items-center flex-wrap gap-2">
                            <span class="font-semibold text-gray-900" x-text="order.order_reference"></span>
                            <span :class="getStatusBadgeClass(order.status)" 
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span x-text="order.status.replace('_', ' ')"></span>
                            </span>
                            <span :class="order.service_mode === 'RUSH' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                                  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                                <span x-text="order.service_mode"></span>
                            </span>
                        </div>
                        
                        <div class="mt-2 space-y-1">
                            <p class="text-sm text-gray-600">
                                <span class="font-medium" x-text="order.business_name"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="far fa-clock mr-1"></i><span x-text="order.created_at_formatted"></span>
                            </p>
                            <p class="text-sm text-gray-500">
                                <i class="fas fa-calendar-check mr-1"></i>Expected: <span x-text="order.expected_return_date"></span>
                            </p>
                        </div>
                        
                        <!-- Order Items Preview -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            <template x-for="item in order.items" :key="item.id">
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-gray-100 text-xs text-gray-700">
                                    <span x-text="item.name"></span> × <span x-text="item.quantity"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                    
                    <div class="text-right w-full sm:w-auto">
                        <div class="text-lg font-bold text-gray-900" x-text="`৳${order.total_amount}`"></div>
                        <div class="flex gap-2 mt-2 justify-end">
                            <button @click="viewOrderDetails(order.id)" 
                                    class="text-indigo-600 hover:text-indigo-900 px-3 py-1 text-sm font-medium border border-indigo-200 rounded-md hover:bg-indigo-50">
                                Details
                            </button>
                            <button x-show="order.status === 'PENDING' || order.status === 'PICKUP_SCHEDULED'"
                                    @click="cancelOrder(order.id)"
                                    class="text-red-600 hover:text-red-900 px-3 py-1 text-sm font-medium border border-red-200 rounded-md hover:bg-red-50">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Empty State -->
    <div x-show="orders.length === 0 && {{ count($recentOrders) }} === 0" x-cloak class="text-center py-12">
        <div class="text-gray-300 text-6xl mb-4">
            <i class="fas fa-tshirt"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900">No laundry orders yet</h3>
        <p class="mt-2 text-gray-500">Your laundry orders will appear here</p>
        <button @click="activeTab = 'providers'" 
                class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Browse Providers
        </button>
    </div>
</div>