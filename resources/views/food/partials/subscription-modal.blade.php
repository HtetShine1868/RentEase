<!-- New Subscription Modal -->
<div x-show="showSubscriptionModal" 
     x-cloak 
     class="fixed inset-0 z-50 overflow-y-auto"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">
    
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
             @click="showSubscriptionModal = false"></div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="createSubscription">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Create Subscription
                            </h3>
                            
                            <!-- Restaurant Info -->
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">Restaurant</p>
                                <p class="font-medium text-gray-900" x-text="selectedRestaurant?.business_name"></p>
                            </div>

                            <!-- Cart Summary -->
                            <div class="mb-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Selected Items</h4>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-2">
                                    <template x-for="item in cartItems" :key="item.id">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-gray-600">
                                                <span x-text="item.quantity"></span>x <span x-text="item.name"></span>
                                            </span>
                                            <span class="text-gray-900" x-text="'৳' + (item.total_price * item.quantity).toFixed(2)"></span>
                                        </div>
                                    </template>
                                    <div class="border-t border-gray-200 pt-2 mt-2">
                                        <div class="flex justify-between font-medium">
                                            <span>Daily Total</span>
                                            <span class="text-indigo-600" x-text="'৳' + cartTotal.toFixed(2)"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subscription Details -->
                            <div class="space-y-4">
                                <!-- Date Range -->
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            Start Date
                                        </label>
                                        <input type="date" 
                                               name="start_date"
                                               :min="new Date().toISOString().split('T')[0]"
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">
                                            End Date
                                        </label>
                                        <input type="date" 
                                               name="end_date"
                                               :min="new Date().toISOString().split('T')[0]"
                                               required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                    </div>
                                </div>

                                <!-- Delivery Time -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Preferred Delivery Time
                                    </label>
                                    <input type="time" 
                                           name="delivery_time"
                                           value="12:00"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                                </div>

                                <!-- Delivery Days -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Delivery Days
                                    </label>
                                    <div class="grid grid-cols-7 gap-2">
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Sun</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="1"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Mon</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="2"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Tue</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="4"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Wed</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="8"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Thu</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="16"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Fri</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="32"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                        <label class="flex flex-col items-center">
                                            <span class="text-xs text-gray-600 mb-1">Sat</span>
                                            <input type="checkbox" 
                                                   name="delivery_days[]" 
                                                   value="64"
                                                   checked
                                                   class="rounded text-indigo-600 focus:ring-indigo-500">
                                        </label>
                                    </div>
                                </div>

                                <!-- Discount Info -->
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <div class="flex items-start">
                                        <svg class="h-5 w-5 text-green-600 mt-0.5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                  d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-green-800">
                                                Subscription Discount
                                            </p>
                                            <p class="text-xs text-green-700 mt-1">
                                                Get <span x-text="selectedRestaurant?.discount_percent || 10"></span>% off on subscription orders
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="service_provider_id" :value="selectedRestaurant?.id">
                            <input type="hidden" name="meal_type_id" :value="selectedMealTypeId">
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                            :disabled="isLoading"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                        <span x-show="!isLoading">Create Subscription</span>
                        <span x-show="isLoading" class="flex items-center">
                            <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                    <button type="button" 
                            @click="showSubscriptionModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>