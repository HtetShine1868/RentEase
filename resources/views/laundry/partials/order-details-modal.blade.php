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
                    <div class="text-lg font-mono font-bold" x-text="selectedOrder?.order_reference || 'N/A'"></div>
                </div>
                
                <!-- Status -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Status</span>
                        <span :class="getStatusBadgeClass(selectedOrder?.status)" 
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <span x-text="selectedOrder?.status?.replace('_', ' ') || 'Unknown'"></span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Service Mode</span>
                        <span :class="selectedOrder?.service_mode === 'RUSH' ? 'bg-orange-100 text-orange-800' : 'bg-blue-100 text-blue-800'"
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                            <span x-text="selectedOrder?.service_mode"></span>
                        </span>
                    </div>
                </div>
                
                <!-- Provider Info -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-store text-indigo-600 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-900" x-text="selectedOrder?.business_name || 'Unknown'"></p>
                        </div>
                    </div>
                </div>
                
                <!-- Schedule -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Pickup Time</p>
                        <p class="text-sm font-medium" x-text="selectedOrder?.pickup_time"></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-xs text-gray-500">Expected Return</p>
                        <p class="text-sm font-medium" x-text="selectedOrder?.expected_return_date"></p>
                    </div>
                </div>
                
                <!-- Order Items -->
                <div class="mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Order Items</h4>
                    <div class="space-y-2">
                        <template x-if="selectedOrder?.items && selectedOrder.items.length > 0">
                            <template x-for="item in selectedOrder.items" :key="item.id">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100 last:border-0">
                                    <div>
                                        <span class="text-sm font-medium" x-text="item.name || 'Unknown'"></span>
                                        <span class="text-xs text-gray-500 ml-2" x-text="`× ${item.quantity || 0}`"></span>
                                    </div>
                                    <span class="text-sm font-medium" x-text="`৳${((item.price || 0) * (item.quantity || 0)).toFixed(2)}`"></span>
                                </div>
                            </template>
                        </template>
                    </div>
                </div>
                
                <!-- Price Breakdown -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-medium text-gray-900 mb-2">Price Breakdown</h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span>Subtotal</span>
                            <span x-text="`৳${(selectedOrder?.base_amount || 0).toFixed(2)}`"></span>
                        </div>
                        <div x-show="selectedOrder?.rush_surcharge > 0" class="flex justify-between text-sm">
                            <span>Rush Surcharge</span>
                            <span x-text="`৳${(selectedOrder?.rush_surcharge || 0).toFixed(2)}`"></span>
                        </div>
                        <div x-show="selectedOrder?.pickup_fee > 0" class="flex justify-between text-sm">
                            <span>Pickup Fee</span>
                            <span x-text="`৳${(selectedOrder?.pickup_fee || 0).toFixed(2)}`"></span>
                        </div>
                        <div class="flex justify-between font-semibold pt-2 border-t border-gray-200">
                            <span>Total</span>
                            <span x-text="`৳${(selectedOrder?.total_amount || 0).toFixed(2)}`"></span>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6">
                    <button x-show="selectedOrder?.status === 'PENDING' || selectedOrder?.status === 'PICKUP_SCHEDULED'"
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
            
            <!-- Empty State -->
            <div x-show="!selectedOrder" class="px-6 py-12 text-center">
                <i class="fas fa-tshirt text-gray-300 text-5xl mb-4"></i>
                <p class="text-gray-500">No order selected</p>
                <button @click="showOrderDetailsModal = false" class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>