<!-- Restaurant Menu Modal -->
<div x-show="showMenuModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showMenuModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
            <!-- Modal Header -->
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900" x-text="selectedRestaurant?.business_name"></h3>
                        <p class="text-sm text-gray-500 mt-1" x-text="selectedRestaurant?.address"></p>
                    </div>
                    <button @click="showMenuModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <!-- Modal Body -->
            <div class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <!-- Restaurant Info -->
                <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-gray-600" x-text="selectedRestaurant?.description"></p>
                            <div class="flex items-center mt-2 text-sm text-gray-500">
                                <i class="fas fa-clock mr-2"></i>
                                <span x-text="`${selectedRestaurant?.opening_time || '08:00'} - ${selectedRestaurant?.closing_time || '22:00'}`"></span>
                                <span class="mx-3">•</span>
                                <i class="fas fa-truck mr-2"></i>
                                <span>Free delivery</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold text-indigo-600" x-text="`৳${cartTotal}`"></div>
                            <p class="text-sm text-gray-500">Cart Total</p>
                        </div>
                    </div>
                </div>
                
                <!-- Meal Type Tabs -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Meal Types">
                        @foreach($mealTypes as $mealType)
                        <button @click="selectMealType({{ $mealType->id }})"
                                :class="selectedMealTypeId === {{ $mealType->id }} ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm">
                            {{ $mealType->name }}
                        </button>
                        @endforeach
                    </nav>
                </div>
                
                <!-- Food Items Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <template x-for="item in filteredMenuItems" :key="item.id">
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex justify-between items-start">
                                <div class="flex-1 pr-4">
                                    <h4 class="font-semibold text-gray-900" x-text="item.name"></h4>
                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2" x-text="item.description || 'No description available'"></p>
                                    
                                    <!-- Dietary Tags -->
                                    <div class="mt-2 flex flex-wrap gap-1">
                                        <template x-for="tag in (item.dietary_tags || [])" :key="tag">
                                            <span class="inline-block bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                <span x-text="tag"></span>
                                            </span>
                                        </template>
                                    </div>
                                    
                                    <!-- Availability -->
                                    <div class="mt-2 text-xs">
                                        <span x-show="item.calories" class="text-gray-500">
                                            <i class="fas fa-fire mr-1"></i><span x-text="item.calories + ' cal'"></span>
                                        </span>
                                        <span x-show="item.daily_quantity" class="ml-2 text-gray-500">
                                            <i class="fas fa-box mr-1"></i>
                                            <span x-text="`${item.daily_quantity - item.sold_today} left`"></span>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="text-right">
                                    <div class="text-lg font-bold text-gray-900" x-text="`৳${item.total_price}`"></div>
                                    <div class="text-sm text-gray-500 line-through" x-text="`৳${item.base_price}`"></div>
                                </div>
                            </div>
                            
                            <!-- Quantity Controls -->
                            <div class="mt-4 pt-4 border-t border-gray-100">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-500">Quantity</span>
                                    <div class="flex items-center space-x-3">
                                        <button @click="decreaseQuantity(item.id)"
                                                class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                                :disabled="getCartQuantity(item.id) <= 0">
                                            <i class="fas fa-minus text-xs"></i>
                                        </button>
                                        <span class="w-8 text-center font-medium" x-text="getCartQuantity(item.id)"></span>
                                        <button @click="increaseQuantity(item.id)"
                                                class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 hover:bg-gray-100 transition-colors flex items-center justify-center"
                                                :disabled="item.daily_quantity && getCartQuantity(item.id) >= (item.daily_quantity - item.sold_today)">
                                            <i class="fas fa-plus text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
                
                <!-- Empty Menu State -->
                <div x-show="filteredMenuItems.length === 0" class="text-center py-12">
                    <i class="fas fa-utensils text-gray-300 text-5xl mb-4"></i>
                    <p class="text-gray-500">No items available for this meal type</p>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                    <!-- Order Type Selection -->
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="radio" name="orderType" value="PAY_PER_EAT" class="mr-2" 
                                   :checked="orderType === 'PAY_PER_EAT'"
                                   @change="orderType = 'PAY_PER_EAT'">
                            <span class="text-sm">Pay Per Meal</span>
                        </label>
                        <label x-show="selectedRestaurant?.supports_subscription" class="flex items-center">
                            <input type="radio" name="orderType" value="SUBSCRIPTION" class="mr-2"
                                   :checked="orderType === 'SUBSCRIPTION'"
                                   @change="orderType = 'SUBSCRIPTION'">
                            <span class="text-sm">Subscribe (Save 10%)</span>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button @click="showMenuModal = false"
                                class="flex-1 sm:flex-none px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Continue Browsing
                        </button>
                        <button @click="placeOrder"
                                :disabled="cartTotal == 0"
                                :class="cartTotal == 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="flex-1 sm:flex-none px-6 py-2 rounded-lg text-white transition-colors">
                            <template x-if="orderType === 'SUBSCRIPTION'">
                                <span><i class="fas fa-calendar-alt mr-2"></i>Subscribe</span>
                            </template>
                            <template x-if="orderType !== 'SUBSCRIPTION'">
                                <span><i class="fas fa-shopping-cart mr-2"></i>Order Now</span>
                            </template>
                            <span x-show="cartTotal > 0" class="ml-2">(৳<span x-text="cartTotal"></span>)</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>