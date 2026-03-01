<!-- Subscriptions Tab Content -->
<div>
    <!-- Subscriptions Grid -->
    <div x-show="!isLoading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <template x-for="subscription in subscriptions" :key="subscription.id">
            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                <!-- Subscription Header -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-white mb-1" 
                                x-text="subscription.business_name"></h3>
                            <span class="px-2 py-1 bg-white bg-opacity-20 rounded-full text-xs text-white"
                                  :class="subscription.status_badge[0]"
                                  x-text="subscription.status_badge[2]"></span>
                        </div>
                        <span class="text-white font-bold" x-text="'৳' + subscription.total_price"></span>
                    </div>
                </div>

                <!-- Subscription Body -->
                <div class="p-6">
                    <!-- Meal Details -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Meal Plan</h4>
                        <p class="text-gray-900 font-medium" x-text="subscription.meal_type"></p>
                        <p class="text-sm text-gray-600" x-text="subscription.delivery_time + ' daily'"></p>
                    </div>

                    <!-- Delivery Days -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Delivery Days</h4>
                        <div class="flex flex-wrap gap-1">
                            <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']" :key="day">
                                <span class="w-8 h-8 rounded-full flex items-center justify-center text-xs"
                                      :class="subscription.delivery_days.includes(day) ? 
                                             'bg-indigo-100 text-indigo-700 font-medium' : 
                                             'bg-gray-100 text-gray-400'"
                                      x-text="day"></span>
                            </template>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-4">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Subscription Period</h4>
                        <p class="text-sm text-gray-900">
                            <span x-text="subscription.start_date_formatted"></span> - 
                            <span x-text="subscription.end_date_formatted"></span>
                        </p>
                        <p x-show="subscription.days_remaining > 0" 
                           class="text-xs text-green-600 mt-1"
                           x-text="subscription.days_remaining + ' days remaining'"></p>
                    </div>

                    <!-- Price Breakdown -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-4">
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Daily Price:</span>
                                <span class="text-gray-900" x-text="'MMK' + subscription.daily_price"></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Discount:</span>
                                <span class="text-green-600" x-text="'-MMK' + subscription.discount_amount"></span>
                            </div>
                            <div class="flex justify-between font-medium pt-1 border-t border-gray-200">
                                <span class="text-gray-900">Total:</span>
                                <span class="text-indigo-600" x-text="'MMK' + subscription.total_price"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-3">
                        <template x-if="subscription.status === 'ACTIVE'">
                            <button @click="pauseSubscription(subscription.id)"
                                    class="flex-1 px-4 py-2 border border-yellow-300 text-yellow-700 rounded-lg text-sm hover:bg-yellow-50">
                                Pause
                            </button>
                        </template>
                        <template x-if="subscription.status === 'PAUSED'">
                            <button @click="resumeSubscription(subscription.id)"
                                    class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg text-sm hover:bg-green-700">
                                Resume
                            </button>
                        </template>
                        <template x-if="subscription.status === 'ACTIVE' || subscription.status === 'PAUSED'">
                            <button @click="cancelSubscription(subscription.id)"
                                    class="flex-1 px-4 py-2 border border-red-300 text-red-700 rounded-lg text-sm hover:bg-red-50">
                                Cancel
                            </button>
                        </template>
                        <template x-if="subscription.status === 'COMPLETED'">
                            <button @click="renewSubscription(subscription.id)"
                                    class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
                                Renew
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Loading State -->
    <div x-show="isLoading" class="text-center py-12">
        <div class="loading-spinner"></div>
        <p class="mt-4 text-gray-600">Loading subscriptions...</p>
    </div>

    <!-- No Subscriptions -->
    <div x-show="!isLoading && subscriptions.length === 0" class="text-center py-12">
        <svg class="h-16 w-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No active subscriptions</h3>
        <p class="text-gray-600 mb-4">Subscribe to your favorite restaurants and save money</p>
        <button @click="activeTab = 'restaurants'" 
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
            Browse Restaurants
        </button>
    </div>
</div>