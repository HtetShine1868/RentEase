<!-- New Subscription Modal -->
<div x-show="showSubscriptionModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showSubscriptionModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="createSubscription">
                <!-- Modal Header -->
                <div class="bg-white px-6 py-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900">Create New Subscription</h3>
                        <button type="button" @click="showSubscriptionModal = false" class="text-gray-400 hover:text-gray-500">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Modal Body -->
                <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                    <!-- Restaurant Selection -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Restaurant</label>
                        <select name="service_provider_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Choose a restaurant...</option>
                            @foreach($initialRestaurants as $restaurant)
                                @if($restaurant->foodConfig && $restaurant->foodConfig->supports_subscription)
                                <option value="{{ $restaurant->id }}">{{ $restaurant->business_name }}</option>
                                @endif
                            @endforeach
                            <template x-for="restaurant in restaurants" :key="restaurant.id">
                                <option x-show="restaurant.supports_subscription" 
                                        :value="restaurant.id" 
                                        x-text="restaurant.business_name"></option>
                            </template>
                        </select>
                    </div>
                    
                    <!-- Meal Type -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Meal Type</label>
                        <select name="meal_type_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                            <option value="">Select meal type...</option>
                            @foreach($mealTypes as $mealType)
                            <option value="{{ $mealType->id }}">{{ $mealType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" required 
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" required 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                    
                    <!-- Delivery Time -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Delivery Time</label>
                        <input type="time" name="delivery_time" required 
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500">
                    </div>
                    
                    <!-- Delivery Days -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Days</label>
                        <div class="grid grid-cols-7 gap-2">
                            @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $index => $day)
                            <label class="flex flex-col items-center p-2 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="delivery_days[]" value="{{ 1 << $index }}" class="mb-1">
                                <span class="text-xs">{{ $day }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Items from Cart -->
                    <div class="mb-4" x-show="cartItems.length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Selected Items</label>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <template x-for="item in cartItems" :key="item.id">
                                <div class="flex justify-between items-center py-2 border-b border-gray-200 last:border-0">
                                    <div>
                                        <span class="font-medium" x-text="item.name"></span>
                                        <span class="text-sm text-gray-500 ml-2" x-text="`× ${item.quantity}`"></span>
                                    </div>
                                    <span class="font-medium" x-text="`৳${(item.total_price * item.quantity).toFixed(2)}`"></span>
                                </div>
                            </template>
                        </div>
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="bg-indigo-50 rounded-lg p-4">
                        <h4 class="font-semibold text-gray-900 mb-2">Price Summary</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span>Daily Price</span>
                                <span x-text="`৳${cartTotal}`"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span>Subscription Discount (10%)</span>
                                <span class="text-green-600" x-text="`-৳${(cartTotal * 0.1).toFixed(2)}`"></span>
                            </div>
                            <div class="flex justify-between font-semibold pt-2 border-t border-indigo-200">
                                <span>Final Daily Price</span>
                                <span x-text="`৳${(cartTotal * 0.9).toFixed(2)}`"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showSubscriptionModal = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Create Subscription
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>