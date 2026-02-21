<!-- Orders Tab Content -->
<div>
    <!-- Status Filter -->
    <div class="mb-6 flex space-x-2 overflow-x-auto pb-2">
        <button @click="orderStatusFilter = ''; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="!orderStatusFilter ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            All Orders
        </button>
        <button @click="orderStatusFilter = 'PENDING'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'PENDING' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Pending
        </button>
        <button @click="orderStatusFilter = 'ACCEPTED'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'ACCEPTED' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Accepted
        </button>
        <button @click="orderStatusFilter = 'PREPARING'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'PREPARING' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Preparing
        </button>
        <button @click="orderStatusFilter = 'OUT_FOR_DELIVERY'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'OUT_FOR_DELIVERY' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Out for Delivery
        </button>
        <button @click="orderStatusFilter = 'DELIVERED'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'DELIVERED' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Delivered
        </button>
        <button @click="orderStatusFilter = 'CANCELLED'; loadOrders()"
                class="px-4 py-2 rounded-full text-sm font-medium transition whitespace-nowrap"
                :class="orderStatusFilter === 'CANCELLED' ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
            Cancelled
        </button>
    </div>

    <!-- Orders List -->
    <div x-show="!isLoading" class="space-y-4">
        <template x-for="order in orders" :key="order.id">
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <!-- Order Header -->
                <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm font-medium text-gray-900" 
                                  x-text="'Order #' + order.order_reference"></span>
                            <span class="text-sm text-gray-500" x-text="order.created_at_formatted"></span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <span class="px-3 py-1 rounded-full text-xs font-medium"
                                  :class="order.status_badge[0] + ' ' + order.status_badge[1]"
                                  x-text="order.status_badge[2]"></span>
                            <span class="text-lg font-bold text-indigo-600" 
                                  x-text="'৳' + order.total_amount"></span>
                        </div>
                    </div>
                </div>

                <!-- Order Body -->
                <div class="px-6 py-4">
                    <div class="flex flex-wrap gap-6">
                        <!-- Restaurant Info -->
                        <div class="flex-1 min-w-[200px]">
                            <h4 class="font-medium text-gray-900 mb-2" x-text="order.business_name"></h4>
                            <p class="text-sm text-gray-600 mb-1">
                                <span class="font-medium">Meal:</span> <span x-text="order.meal_type"></span>
                            </p>
                            <p class="text-sm text-gray-600">
                                <span class="font-medium">Delivery:</span> 
                                <span x-text="order.estimated_delivery_time"></span>
                            </p>
                        </div>

                        <!-- Delivery Address -->
                        <div class="flex-1 min-w-[200px]">
                            <h4 class="font-medium text-gray-900 mb-2">Delivery Address</h4>
                            <p class="text-sm text-gray-600" x-text="order.delivery_address"></p>
                            <p class="text-sm text-gray-500 mt-1" x-text="order.distance_km + ' km away'"></p>
                        </div>

                        <!-- Order Items Summary -->
                        <div class="flex-1 min-w-[200px]">
                            <h4 class="font-medium text-gray-900 mb-2">Items</h4>
                            <div class="space-y-1">
                                <template x-for="item in order.items.slice(0, 3)" :key="item.id">
                                    <p class="text-sm text-gray-600">
                                        <span x-text="item.quantity + 'x ' + item.name"></span>
                                    </p>
                                </template>
                                <p x-show="order.items.length > 3" 
                                   class="text-sm text-gray-500"
                                   x-text="'+' + (order.items.length - 3) + ' more items'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex justify-end">
                            <div class="w-64 space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Subtotal:</span>
                                    <span class="text-gray-900" x-text="'৳' + order.base_amount"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Delivery Fee:</span>
                                    <span class="text-gray-900" x-text="'৳' + order.delivery_fee"></span>
                                </div>
                                <div class="flex justify-between font-medium pt-1 border-t border-gray-200">
                                    <span class="text-gray-900">Total:</span>
                                    <span class="text-indigo-600" x-text="'৳' + order.total_amount"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Actions -->
                    <div class="mt-4 flex justify-end space-x-3">
                        <button @click="viewOrderDetails(order.id)"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 hover:bg-gray-50">
                            View Details
                        </button>
                        <button x-show="order.status === 'PENDING' || order.status === 'ACCEPTED'"
                                @click="cancelOrder(order.id)"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg text-sm hover:bg-red-700">
                            Cancel Order
                        </button>
                        <button x-show="order.status === 'DELIVERED'"
                                @click="reorderItems(order.id)"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                            Reorder
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="isLoading" class="text-center py-12">
        <div class="loading-spinner"></div>
        <p class="mt-4 text-gray-600">Loading orders...</p>
    </div>

    <!-- No Orders -->
    <div x-show="!isLoading && orders.length === 0" class="text-center py-12">
        <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
        <p class="text-gray-600 mb-4">You haven't placed any orders yet</p>
        <button @click="activeTab = 'restaurants'" 
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Browse Restaurants
        </button>
    </div>
</div>