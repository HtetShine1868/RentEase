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
            
            <!-- Modal Body - Conditional Content -->
            <div x-show="orderType !== 'SUBSCRIPTION'" class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
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
            
            <!-- Subscription Options - Show when SUBSCRIPTION is selected -->
            <div x-show="orderType === 'SUBSCRIPTION'" class="px-6 py-4 max-h-[calc(100vh-200px)] overflow-y-auto">
                <div class="mb-6 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                    <div class="flex items-start">
                        <i class="fas fa-calendar-alt text-indigo-600 text-2xl mr-4 mt-1"></i>
                        <div>
                            <h4 class="font-medium text-indigo-900">Subscription Service</h4>
                            <p class="text-sm text-indigo-700 mt-1">Subscribe to get regular meals with 10% discount. Choose your meal plan below.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Subscription Plans -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    <!-- Daily Subscription -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer"
                         :class="subscriptionPlan === 'daily' ? 'border-indigo-500 ring-2 ring-indigo-200' : ''"
                         @click="subscriptionPlan = 'daily'">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Daily Plan</h4>
                                <p class="text-sm text-gray-500">Get meals delivered daily</p>
                            </div>
                            <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center"
                                 :class="subscriptionPlan === 'daily' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                <i x-show="subscriptionPlan === 'daily'" class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Breakfast:</span>
                                <span class="font-medium">MMK 150</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lunch:</span>
                                <span class="font-medium">MMK 250</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Dinner:</span>
                                <span class="font-medium">MMK 250</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-bold">
                                    <span>Daily Total:</span>
                                    <span>MMK 650</span>
                                </div>
                                <div class="flex justify-between text-sm text-green-600">
                                    <span>After 10% discount:</span>
                                    <span>MMK 585</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Weekly Subscription -->
                    <div class="border border-gray-200 rounded-lg p-6 hover:shadow-md transition-shadow cursor-pointer"
                         :class="subscriptionPlan === 'weekly' ? 'border-indigo-500 ring-2 ring-indigo-200' : ''"
                         @click="subscriptionPlan = 'weekly'">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Weekly Plan</h4>
                                <p class="text-sm text-gray-500">Save more with weekly subscription</p>
                            </div>
                            <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center"
                                 :class="subscriptionPlan === 'weekly' ? 'border-indigo-600 bg-indigo-600' : 'border-gray-300'">
                                <i x-show="subscriptionPlan === 'weekly'" class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">5 days/week:</span>
                                <span class="font-medium">MMK 2,925</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">7 days/week:</span>
                                <span class="font-medium">MMK 4,095</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <div class="flex justify-between font-bold">
                                    <span>Weekly Savings:</span>
                                    <span class="text-green-600">Up to 15%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Meal Selection for Subscription -->
                <div x-show="subscriptionPlan" class="mb-6">
                    <h4 class="font-medium text-gray-900 mb-3">Select Your Meals</h4>
                    
                    <!-- Breakfast Selection -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-sun text-yellow-500 mr-2"></i>
                            <h5 class="font-medium">Breakfast</h5>
                            <span class="ml-auto text-sm text-gray-500">Optional</span>
                        </div>
                        <select class="w-full border-gray-300 rounded-lg text-sm">
                            <option value="">Select breakfast item</option>
                            <option>Paratha & Curry</option>
                            <option>Bread & Butter</option>
                            <option>Fried Rice</option>
                        </select>
                    </div>
                    
                    <!-- Lunch Selection -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-utensils text-orange-500 mr-2"></i>
                            <h5 class="font-medium">Lunch</h5>
                            <span class="ml-auto text-sm text-gray-500">Required</span>
                        </div>
                        <select class="w-full border-gray-300 rounded-lg text-sm" required>
                            <option value="">Select lunch item</option>
                            <option>Rice & Chicken Curry</option>
                            <option>Khichuri</option>
                            <option>Polao & Beef</option>
                        </select>
                    </div>
                    
                    <!-- Dinner Selection -->
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center mb-3">
                            <i class="fas fa-moon text-blue-500 mr-2"></i>
                            <h5 class="font-medium">Dinner</h5>
                            <span class="ml-auto text-sm text-gray-500">Required</span>
                        </div>
                        <select class="w-full border-gray-300 rounded-lg text-sm" required>
                            <option value="">Select dinner item</option>
                            <option>Rice & Fish Curry</option>
                            <option>Biriyani</option>
                            <option>Chapati & Vegetable</option>
                        </select>
                    </div>
                </div>
                
                <!-- Delivery Schedule -->
                <div x-show="subscriptionPlan" class="mb-6 p-4 bg-gray-50 rounded-lg">
                    <h4 class="font-medium text-gray-900 mb-3">Delivery Schedule</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Start Date</label>
                            <input type="date" x-model="subscriptionStartDate" 
                                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                   class="w-full border-gray-300 rounded-lg text-sm">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Delivery Time</label>
                            <select x-model="subscriptionDeliveryTime" class="w-full border-gray-300 rounded-lg text-sm">
                                <option value="08:00">8:00 AM - 9:00 AM</option>
                                <option value="12:00">12:00 PM - 1:00 PM</option>
                                <option value="19:00">7:00 PM - 8:00 PM</option>
                            </select>
                        </div>
                    </div>
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
                                   @change="orderType = 'PAY_PER_EAT'; resetCart()">
                            <span class="text-sm">Pay Per Meal</span>
                        </label>
                        <label x-show="selectedRestaurant?.supports_subscription" class="flex items-center">
                            <input type="radio" name="orderType" value="SUBSCRIPTION" class="mr-2"
                                   :checked="orderType === 'SUBSCRIPTION'"
                                   @change="orderType = 'SUBSCRIPTION'; resetCart()">
                            <span class="text-sm">Subscribe (Save 10%)</span>
                        </label>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex gap-3 w-full sm:w-auto">
                        <button @click="showMenuModal = false"
                                class="flex-1 sm:flex-none px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                            Continue Browsing
                        </button>
                        
                        <!-- Pay Per Eat Button -->
                        <button x-show="orderType !== 'SUBSCRIPTION'"
                                @click="placeOrder"
                                :disabled="cartTotal == 0"
                                :class="cartTotal == 0 ? 'bg-gray-300 cursor-not-allowed' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="flex-1 sm:flex-none px-6 py-2 rounded-lg text-white transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            <span>Order Now</span>
                            <span x-show="cartTotal > 0" class="ml-2">(৳<span x-text="cartTotal"></span>)</span>
                        </button>
                        
                        <!-- Subscription Button -->
                        <button x-show="orderType === 'SUBSCRIPTION'"
                                @click="showSubscriptionModal = true"
                                :disabled="!subscriptionPlan"
                                :class="!subscriptionPlan ? 'bg-gray-300 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700'"
                                class="flex-1 sm:flex-none px-6 py-2 rounded-lg text-white transition-colors">
                            <i class="fas fa-calendar-alt mr-2"></i>
                            <span>Proceed to Subscribe</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Confirmation Modal -->
<div x-show="showSubscriptionModal" x-cloak class="fixed inset-0 z-[60] overflow-y-auto" x-transition>
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showSubscriptionModal = false"></div>
        </div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-xl font-bold text-gray-900">Confirm Subscription</h3>
                    <button @click="showSubscriptionModal = false" class="text-gray-400 hover:text-gray-500">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
            
            <div class="px-6 py-4">
                <div class="text-center mb-6">
                    <i class="fas fa-calendar-check text-green-500 text-5xl mb-4"></i>
                    <h4 class="text-lg font-semibold text-gray-900">You're about to subscribe!</h4>
                    <p class="text-sm text-gray-500 mt-2">Please review your subscription details below</p>
                </div>
                
                <!-- Subscription Summary -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <h5 class="font-medium text-gray-900 mb-3">Subscription Summary</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Plan:</span>
                            <span class="font-medium" x-text="subscriptionPlan === 'daily' ? 'Daily Plan' : 'Weekly Plan'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Start Date:</span>
                            <span class="font-medium" x-text="subscriptionStartDate || 'Not selected'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Delivery Time:</span>
                            <span class="font-medium" x-text="subscriptionDeliveryTime || 'Not selected'"></span>
                        </div>
                        <div class="border-t pt-2 mt-2">
                            <div class="flex justify-between font-bold">
                                <span>Monthly Total:</span>
                                <span class="text-green-600">MMK <span x-text="subscriptionPlan === 'daily' ? '17,550' : '12,675'"></span></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">You save 10% on every meal</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-yellow-50 p-4 rounded-lg">
                    <div class="flex">
                        <i class="fas fa-info-circle text-yellow-600 mr-3 mt-1"></i>
                        <div>
                            <p class="text-sm text-yellow-800 font-medium">Subscription Terms</p>
                            <p class="text-xs text-yellow-700 mt-1">You can pause or cancel your subscription anytime. Meals are delivered according to your selected schedule.</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                <button @click="showSubscriptionModal = false"
                        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button @click="createSubscription"
                        class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i>Confirm Subscription
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('restaurantMenu', () => ({
        // Restaurant and menu state
        showMenuModal: false,
        selectedRestaurant: null,
        menuItems: [],
        filteredMenuItems: [],
        selectedMealTypeId: null,
        mealTypes: @json($mealTypes),
        
        // Cart state
        cart: {},
        cartTotal: 0,
        
        // Order type state
        orderType: 'PAY_PER_EAT',
        
        // Subscription state - FIXED: All variables defined here
        subscriptionPlan: null,
        subscriptionStartDate: '',
        subscriptionDeliveryTime: '12:00',
        showSubscriptionModal: false,
        
        init() {
            // Listen for restaurant selection event
            window.addEventListener('show-restaurant-menu', (event) => {
                this.selectedRestaurant = event.detail.restaurant;
                this.menuItems = event.detail.menuItems || [];
                this.filteredMenuItems = this.menuItems;
                this.showMenuModal = true;
                
                // Set default meal type if available
                if (this.mealTypes && this.mealTypes.length > 0) {
                    this.selectedMealTypeId = this.mealTypes[0].id;
                    this.filterByMealType();
                }
            });
        },
        
        selectMealType(mealTypeId) {
            this.selectedMealTypeId = mealTypeId;
            this.filterByMealType();
        },
        
        filterByMealType() {
            if (!this.selectedMealTypeId) {
                this.filteredMenuItems = this.menuItems;
            } else {
                this.filteredMenuItems = this.menuItems.filter(
                    item => item.meal_type_id === this.selectedMealTypeId
                );
            }
        },
        
        getCartQuantity(itemId) {
            return this.cart[itemId] || 0;
        },
        
        increaseQuantity(itemId) {
            const currentQty = this.getCartQuantity(itemId);
            this.cart[itemId] = currentQty + 1;
            this.updateCartTotal();
        },
        
        decreaseQuantity(itemId) {
            const currentQty = this.getCartQuantity(itemId);
            if (currentQty > 0) {
                this.cart[itemId] = currentQty - 1;
                if (this.cart[itemId] === 0) {
                    delete this.cart[itemId];
                }
                this.updateCartTotal();
            }
        },
        
        updateCartTotal() {
            this.cartTotal = Object.entries(this.cart).reduce((total, [itemId, qty]) => {
                const item = this.menuItems.find(i => i.id == itemId);
                return total + (item ? item.total_price * qty : 0);
            }, 0);
        },
        
        resetCart() {
            this.cart = {};
            this.cartTotal = 0;
        },
        
        placeOrder() {
            if (this.cartTotal === 0) {
                alert('Please add items to your cart');
                return;
            }
            
            // Prepare order data
            const orderData = {
                restaurant_id: this.selectedRestaurant.id,
                items: Object.entries(this.cart).map(([itemId, quantity]) => ({
                    item_id: itemId,
                    quantity: quantity
                }))
            };
            
            // Send order
            fetch('/food/api/order/place', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify(orderData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showMenuModal = false;
                    this.resetCart();
                    alert('Order placed successfully!');
                    // Redirect to orders page or show success
                } else {
                    alert(data.message || 'Error placing order');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error placing order');
            });
        },
        
        createSubscription() {
            // Validate subscription details
            if (!this.subscriptionPlan) {
                alert('Please select a subscription plan');
                return;
            }
            
            if (!this.subscriptionStartDate) {
                alert('Please select a start date');
                return;
            }
            
            // Create subscription
            fetch('/food/api/subscription/create', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({
                    restaurant_id: this.selectedRestaurant.id,
                    plan: this.subscriptionPlan,
                    start_date: this.subscriptionStartDate,
                    delivery_time: this.subscriptionDeliveryTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showSubscriptionModal = false;
                    this.showMenuModal = false;
                    alert('Subscription created successfully!');
                    // Redirect to subscriptions page
                } else {
                    alert(data.message || 'Error creating subscription');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error creating subscription');
            });
        }
    }));
});
</script>