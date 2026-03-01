<!-- Provider Items Modal -->
<div x-show="showItemsModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showItemsModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="selectedProvider?.business_name"></h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="selectedProvider?.address"></p>
                    </div>
                    <button @click="showItemsModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- Provider Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600" x-text="selectedProvider?.description"></p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <i class="fas fa-clock mr-2"></i>
                                <span>Pickup: <span x-text="selectedProvider?.pickup_start_time + ' - ' + selectedProvider?.pickup_end_time"></span></span>
                                <span class="mx-3">•</span>
                                <i class="fas fa-truck mr-2"></i>
                                <span x-text="selectedProvider?.pickup_fee > 0 ? 'Pickup fee: ৳' + selectedProvider.pickup_fee : 'Free pickup'"></span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-600" x-text="`৳${cartTotal.toFixed(2)}`"></div>
                            <p class="text-sm text-gray-500">Cart Total</p>
                        </div>
                    </div>
                </div>
                
                <!-- Service Mode Selection -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Service Type</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative block cursor-pointer">
                            <input type="radio" x-model="serviceMode" value="NORMAL" class="sr-only">
                            <div :class="serviceMode === 'NORMAL' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                                 class="border-2 rounded-lg p-4 transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-gray-900">Normal Service</span>
                                        <p class="text-sm text-gray-500 mt-1" x-text="Math.floor(selectedProvider?.normal_turnaround_hours / 24) + ' days turnaround'"></p>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2" :class="serviceMode === 'NORMAL' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                        <div x-show="serviceMode === 'NORMAL'" class="w-2.5 h-2.5 bg-white rounded-full m-auto mt-0.5"></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                        
                        <label class="relative block cursor-pointer">
                            <input type="radio" x-model="serviceMode" value="RUSH" class="sr-only">
                            <div :class="serviceMode === 'RUSH' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'"
                                 class="border-2 rounded-lg p-4 transition-all">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-semibold text-gray-900">Rush Service</span>
                                        <p class="text-sm text-gray-500 mt-1" x-text="Math.floor(selectedProvider?.rush_turnaround_hours / 24) + ' days turnaround'"></p>
                                        <p class="text-xs text-orange-600 mt-1">+ Rush surcharge applies</p>
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2" :class="serviceMode === 'RUSH' ? 'border-indigo-500 bg-indigo-500' : 'border-gray-300'">
                                        <div x-show="serviceMode === 'RUSH'" class="w-2.5 h-2.5 bg-white rounded-full m-auto mt-0.5"></div>
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Pickup Schedule -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Pickup Schedule</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Date</label>
                            <input type="date" x-model="pickupDate" 
                                   :min="new Date().toISOString().split('T')[0]"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Time</label>
                            <input type="time" x-model="pickupTime" 
                                   :min="selectedProvider?.pickup_start_time"
                                   :max="selectedProvider?.pickup_end_time"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <!-- Expected Return -->
                    <div class="mt-3 p-3 bg-white rounded-lg border border-gray-200">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-600">Expected Return:</span>
                            <span class="font-semibold text-indigo-600" x-text="getExpectedReturnDate()"></span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Instructions (Optional)</label>
                        <textarea x-model="pickupInstructions" 
                                  rows="2"
                                  placeholder="Any special instructions for pickup?"
                                  class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500"></textarea>
                    </div>
                </div>
                
                <!-- Items by Category -->
                <div class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Services & Pricing</h4>
                    
                    <template x-for="(items, type) in groupedItems" :key="type">
                        <div class="mb-4">
                            <h5 class="text-sm font-semibold text-gray-700 mb-2 capitalize" x-text="type.toLowerCase()"></h5>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="item in items" :key="item.id">
                                    <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h6 class="font-medium text-gray-900" x-text="item.name"></h6>
                                                <div class="mt-1 text-sm">
                                                    <span class="text-gray-900 font-semibold" x-text="`MMK {item.base_price}`"></span>
                                                    <span class="text-gray-500 text-xs ml-1">per item</span>
                                                </div>
                                                    <div x-show="item.rush_surcharge_percent > 0" class="mt-1 text-xs text-orange-600">
                                                        +<span x-text="item.rush_surcharge_percent"></span>% rush surcharge
                                                    </div>
                                            </div>
                                            
                                            <div class="flex items-center space-x-2">
                                                <button @click="decreaseQuantity(item.id)"
                                                        class="w-6 h-6 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center"
                                                        :disabled="getCartQuantity(item.id) <= 0">
                                                    <i class="fas fa-minus text-xs"></i>
                                                </button>
                                                <span class="w-6 text-center text-sm font-medium" x-text="getCartQuantity(item.id)"></span>
                                                <button @click="increaseQuantity(item.id)"
                                                        class="w-6 h-6 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 flex items-center justify-center">
                                                    <i class="fas fa-plus text-xs"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Cart Summary -->
                <div x-show="cartItems.length > 0" class="mb-6 p-4 bg-indigo-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Order Summary</h4>
                    <div class="space-y-2">
                        <template x-for="item in cartItems" :key="item.id">
                            <div class="flex justify-between items-center text-sm">
                                <div>
                                    <span x-text="item.name"></span>
                                    <span class="text-gray-500 ml-1" x-text="`× ${item.quantity}`"></span>
                                </div>
                                <div class="text-right">
                                    <span x-text="`৳${(item.total_price * item.quantity).toFixed(2)}`"></span>
                                   <div x-show="serviceMode === 'RUSH' && item.rush_surcharge_percent > 0" 
                                        class="text-xs text-orange-600">
                                        +<span x-text="item.rush_surcharge_percent"></span>% rush
                                    </div>
                                </div>
                            </div>
                        </template>
                        
                        <div x-show="selectedProvider?.pickup_fee > 0" class="flex justify-between items-center text-sm pt-2 border-t border-indigo-200">
                            <span>Pickup Fee</span>
                            <span x-text="`MMK ${selectedProvider?.pickup_fee?.toFixed(2) || '0.00'}`"></span>
                        </div>
                        
                        <div class="flex justify-between items-center font-semibold pt-2 border-t border-indigo-200">
                            <span>Total Amount</span>
                            <span x-text="`MMK ${cartTotal.toFixed(2)}`"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex justify-end gap-3">
                    <button @click="showItemsModal = false"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button @click="placeOrder"
                            :disabled="cartTotal === 0 || isLoading"
                            :class="{
                                'bg-indigo-600 hover:bg-indigo-700': cartTotal > 0 && !isLoading,
                                'bg-gray-400 cursor-not-allowed': cartTotal === 0 || isLoading
                            }"
                            class="px-6 py-2 rounded-lg text-white transition-colors">
                        <span x-show="!isLoading">Place Order</span>
                        <span x-show="isLoading" class="flex items-center">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Placing Order...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>