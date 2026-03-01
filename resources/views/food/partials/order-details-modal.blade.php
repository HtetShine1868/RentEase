<!-- Order Details Modal -->
<div x-show="showOrderDetailsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showOrderDetailsModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Order Details</h3>
                    <button type="button" @click="showOrderDetailsModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4" x-show="selectedOrder">
                <!-- Order Reference -->
                <div class="text-center mb-6">
                    <div class="text-sm text-gray-500">Order Reference</div>
                    <div class="text-lg font-mono font-bold" x-text="selectedOrder?.order_reference"></div>
                </div>
                
                <!-- Status Timeline -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        <span :class="getStatusBadgeClass(selectedOrder?.status)" 
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <span x-text="selectedOrder?.status.replace('_', ' ')"></span>
                        </span>
                    </div>
                    
                    <!-- Progress Bar -->
                    <div class="relative">
                        <div class="overflow-hidden h-2 text-xs flex rounded bg-gray-200">
                            <div :style="'width: ' + getOrderProgress(selectedOrder?.status) + '%'" 
                                 class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-600"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>Placed</span>
                            <span>Preparing</span>
                            <span>Delivered</span>
                        </div>
                    </div>
                </div>
                
                <!-- Restaurant Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-store text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900" x-text="selectedOrder?.business_name"></p>
                            <p class="text-sm text-gray-500 mt-1" x-text="selectedOrder?.meal_type"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Order Items</h4>
                    <div class="space-y-2">
                        <template x-for="item in selectedOrder?.items" :key="item.id">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                <div>
                                    <span class="text-sm font-medium" x-text="item.name"></span>
                                    <span class="text-xs text-gray-500 ml-2" x-text="`× ${item.quantity}`"></span>
                                </div>
                                <span class="text-sm font-medium" x-text="`MMK {(item.price * item.quantity).toFixed(2)}`"></span>
                            </div>
                        </template>
                    </div>
                </div>
                
                <!-- Price Breakdown -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Price Breakdown</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal</span>
                            <span x-text="`MMK {selectedOrder?.total_amount}`"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span>Delivery Fee</span>
                            <span>MMK 40.00</span>
                        </div>
                        <div class="flex justify-between font-semibold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span x-text="`MMK {selectedOrder?.total_amount}`"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Delivery Info -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Delivery Information</h4>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-gray-400 mt-1 mr-2"></i>
                        <div>
                            <p class="text-sm text-gray-600" x-text="selectedOrder?.delivery_address"></p>
                            <p class="text-xs text-gray-500 mt-1">
                                <i class="far fa-clock mr-1"></i>
                                Ordered on <span x-text="new Date(selectedOrder?.created_at).toLocaleString()"></span>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button x-show="selectedOrder?.status === 'PENDING' || selectedOrder?.status === 'ACCEPTED'"
                            @click="cancelOrder(selectedOrder?.id); showOrderDetailsModal = false"
                            class="px-4 py-2 border border-red-300 rounded-lg text-red-700 hover:bg-red-50">
                        Cancel Order
                    </button>
                    <button @click="showOrderDetailsModal = false"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>