@extends('dashboard')

@section('title', 'Food Services')

@section('content')
<div class="space-y-6" x-data="foodServices()" x-init="init()">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Food Services</h2>
                <p class="mt-2 text-gray-600">Order meals or subscribe for regular delivery</p>
            </div>
            <div class="flex space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['totalOrders'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Total Orders</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['activeSubscriptions'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Active Subscriptions</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pendingOrders'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Pending</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'restaurants'; loadRestaurants();" 
                        :class="activeTab === 'restaurants' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Browse Restaurants
                </button>
                <button @click="activeTab = 'orders'; loadOrders();" 
                        :class="activeTab === 'orders' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Orders
                </button>
                <button @click="activeTab = 'subscriptions'; loadSubscriptions();" 
                        :class="activeTab === 'subscriptions' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Subscriptions
                </button>
            </nav>
        </div>

        <!-- Tab Content -->
        <div class="p-6">
            <!-- Loading Indicator -->
            <div x-show="isLoading" class="text-center py-12">
                <div class="loading-spinner"></div>
                <p class="mt-4 text-gray-600">Loading...</p>
            </div>

            <!-- Restaurants Tab -->
            <div x-show="activeTab === 'restaurants'" x-cloak>
                @include('food.partials.restaurants-tab')
            </div>

            <!-- Orders Tab -->
            <div x-show="activeTab === 'orders'" x-cloak>
                @include('food.partials.orders-tab')
            </div>

            <!-- Subscriptions Tab -->
            <div x-show="activeTab === 'subscriptions'" x-cloak>
                @include('food.partials.subscriptions-tab')
            </div>
        </div>
    </div>

    <!-- Restaurant Menu Modal -->
    <template x-if="showMenuModal">
        @include('food.partials.menu-modal')
    </template>

    <!-- New Subscription Modal -->
    <template x-if="showSubscriptionModal">
        @include('food.partials.subscription-modal')
    </template>

    <!-- Order Details Modal -->
    <template x-if="showOrderDetailsModal">
        @include('food.partials.order-details-modal')
    </template>

    <!-- Reviews Modal -->
    <template x-if="showReviewsModal">
        @include('food.partials.reviews-modal')
    </template>

    <!-- Location Modal - This one is separate because it has its own x-data -->
    @include('food.partials.location-modal')
</div>

<script>
function foodServices() {
    return {
        // Tab state
        activeTab: 'restaurants',
        
        // Filters
        searchQuery: '',
        selectedMealType: '',
        sortBy: 'rating',
        orderStatusFilter: '',
        
        // Data - initialize all arrays
        restaurants: [],
        orders: [],
        subscriptions: [],
        selectedRestaurant: null,
        selectedOrder: null,
        selectedSubscription: null,
        mealTypes: @json($mealTypes ?? []),
        
        // UI State - initialize all boolean flags
        isLoading: false,
        showMenuModal: false,
        showSubscriptionModal: false,
        showOrderDetailsModal: false,
        showReviewsModal: false,
        
        // Cart - initialize
        cart: {},
        cartItems: [],
        cartTotal: 0,
        
        // Selected items - initialize
        selectedMealTypeId: {{ $mealTypes->first()->id ?? 'null' }},
        orderType: 'PAY_PER_EAT',
        
        // Pagination
        currentPage: 1,
        lastPage: 1,
        
        // Reviews - initialize
        selectedRestaurantForReviews: null,
        reviewsData: null,
        isLoadingReviews: false,
        
        // Initialize
        init() {
            console.log('Food services initialized', this);
            this.setupEventListeners();
        },
        
        setupEventListeners() {
            // Listen for restaurant selection
            window.addEventListener('view-restaurant', (event) => {
                this.viewRestaurant(event.detail.restaurantId);
            });
            
            // Listen for review view
            window.addEventListener('view-reviews', (event) => {
                this.viewReviews(event.detail.restaurantId);
            });
        },
        
        // API Methods
        async loadRestaurants(page = 1) {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    meal_type: this.selectedMealType,
                    sort: this.sortBy,
                    page: page
                });
                
                const response = await fetch(`/food/api/restaurants?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load restaurants');
                
                const data = await response.json();
                if (data.success) {
                    if (page === 1) {
                        this.restaurants = data.restaurants;
                    } else {
                        this.restaurants = [...this.restaurants, ...data.restaurants];
                    }
                    this.currentPage = data.current_page || page;
                    this.lastPage = data.last_page || 1;
                }
            } catch (error) {
                console.error('Error loading restaurants:', error);
                this.showError('Failed to load restaurants');
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadOrders() {
            this.isLoading = true;
            try {
                const params = new URLSearchParams();
                if (this.orderStatusFilter) {
                    params.append('status', this.orderStatusFilter);
                }
                
                const response = await fetch(`/food/api/orders?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load orders');
                
                const data = await response.json();
                if (data.success) {
                    this.orders = data.orders;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                this.showError('Failed to load orders');
            } finally {
                this.isLoading = false;
            }
        },
        
        async loadSubscriptions() {
            this.isLoading = true;
            try {
                const response = await fetch('/food/api/subscriptions', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load subscriptions');
                
                const data = await response.json();
                if (data.success) {
                    this.subscriptions = data.subscriptions;
                }
            } catch (error) {
                console.error('Error loading subscriptions:', error);
                this.showError('Failed to load subscriptions');
            } finally {
                this.isLoading = false;
            }
        },
        
        async viewRestaurant(restaurantId) {
            console.log('========== VIEW RESTAURANT CALLED ==========');
            console.log('Restaurant ID:', restaurantId);
            console.log('Current showMenuModal:', this.showMenuModal);
            
            this.isLoading = true;
            try {
                const url = `/food/api/restaurant/${restaurantId}/menu`;
                console.log('Fetching from URL:', url);
                
                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Restaurant menu data:', data);
                
                if (data.success) {
                    this.selectedRestaurant = data.restaurant;
                    this.selectedRestaurant.menu_items = data.menu_items;
                    this.selectedMealTypeId = this.mealTypes && this.mealTypes.length > 0 ? this.mealTypes[0].id : null;
                    
                    console.log('Setting showMenuModal to true');
                    this.showMenuModal = true;
                    
                    this.resetCart();
                    console.log('Menu modal opened, showMenuModal =', this.showMenuModal);
                    console.log('Selected restaurant:', this.selectedRestaurant);
                } else {
                    console.error('Failed to load menu:', data.message);
                    this.showError(data.message || 'Failed to load restaurant menu');
                }
            } catch (error) {
                console.error('Error loading restaurant:', error);
                this.showError('Failed to load restaurant menu: ' + error.message);
            } finally {
                this.isLoading = false;
                console.log('viewRestaurant completed, isLoading =', this.isLoading);
            }
        },
        
        async viewOrderDetails(orderId) {
            const order = this.orders.find(o => o.id === orderId);
            if (order) {
                this.selectedOrder = order;
                this.showOrderDetailsModal = true;
            }
        },
        
        // Reviews Method
        async viewReviews(restaurantId) {
            console.log('Viewing reviews for restaurant:', restaurantId);
            
            // Find restaurant from either initial or loaded restaurants
            const initialRestaurants = @json($initialRestaurants);
            this.selectedRestaurantForReviews = this.restaurants.find(r => r.id === restaurantId) || 
                                                initialRestaurants.find(r => r.id === restaurantId);
            
            this.showReviewsModal = true;
            this.isLoadingReviews = true;
            this.reviewsData = null;
            
            try {
                const response = await fetch(`/food/api/restaurant/${restaurantId}/ratings`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Reviews data:', data);
                
                if (data.success) {
                    this.reviewsData = {
                        ratings: data.ratings || [],
                        stats: data.stats || {
                            average: 0,
                            total: 0,
                            quality_avg: 0,
                            delivery_avg: 0,
                            value_avg: 0,
                            breakdown: {5:0, 4:0, 3:0, 2:0, 1:0}
                        }
                    };
                } else {
                    console.error('Failed to load reviews:', data.message);
                    this.showError('Failed to load reviews');
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
                this.showError('Failed to load reviews. Please try again.');
            } finally {
                this.isLoadingReviews = false;
            }
        },
        
        // Cart Methods
        addToCart(item) {
            if (!this.cart[item.id]) {
                this.cart[item.id] = {
                    ...item,
                    quantity: 0
                };
            }
            this.increaseQuantity(item.id);
        },
        
        increaseQuantity(itemId) {
            const item = this.cart[itemId] || this.findMenuItem(itemId);
            if (!item) return;
            
            if (item.daily_quantity && (item.sold_today + (this.cart[itemId]?.quantity || 0) + 1) > item.daily_quantity) {
                this.showError(`Only ${item.daily_quantity - item.sold_today} items available`);
                return;
            }
            
            if (!this.cart[itemId]) {
                this.cart[itemId] = { ...item, quantity: 0 };
            }
            
            this.cart[itemId].quantity++;
            this.calculateCartTotal();
            this.updateCartItems();
        },
        
        decreaseQuantity(itemId) {
            if (this.cart[itemId] && this.cart[itemId].quantity > 0) {
                this.cart[itemId].quantity--;
                if (this.cart[itemId].quantity === 0) {
                    delete this.cart[itemId];
                }
                this.calculateCartTotal();
                this.updateCartItems();
            }
        },
        
        getCartQuantity(itemId) {
            return this.cart[itemId]?.quantity || 0;
        },
        
        findMenuItem(itemId) {
            return this.selectedRestaurant?.menu_items?.find(i => i.id == itemId);
        },
        
        calculateCartTotal() {
            let total = 0;
            for (const item of Object.values(this.cart)) {
                total += parseFloat(item.total_price) * item.quantity;
            }
            this.cartTotal = total;
        },
        
        updateCartItems() {
            this.cartItems = Object.values(this.cart);
        },
        
        resetCart() {
            this.cart = {};
            this.cartItems = [];
            this.cartTotal = 0;
        },
        
        // Order Methods
async placeOrder() {
    console.log('========== PLACE ORDER CALLED ==========');
    console.log('Cart Total:', this.cartTotal);
    console.log('Order Type:', this.orderType);
    console.log('Cart Items:', this.cartItems);
    console.log('Selected Restaurant:', this.selectedRestaurant);
    
    if (this.cartTotal == 0) {
        this.showError('Please add items to cart');
        return;
    }
    
    if (this.orderType === 'SUBSCRIPTION') {
        this.showSubscriptionModal = true;
        return;
    }
    
    // Validate that we have a selected restaurant
    if (!this.selectedRestaurant || !this.selectedRestaurant.id) {
        this.showError('Restaurant information is missing');
        return;
    }
    
    this.isLoading = true;
    
    try {
        // Prepare order items
        const orderItems = [];
        for (const item of Object.values(this.cart)) {
            if (item.quantity > 0) {
                orderItems.push({
                    food_item_id: parseInt(item.id),
                    quantity: item.quantity
                });
            }
        }
        
        console.log('Order Items:', orderItems);
        
        // Get user location
        const location = await this.getUserLocation();
        console.log('User Location:', location);
        
        // Get today's date in YYYY-MM-DD format
        const today = new Date();
        const mealDate = today.toISOString().split('T')[0];
        
        const orderData = {
            service_provider_id: this.selectedRestaurant.id,
            meal_type_id: this.selectedMealTypeId,
            meal_date: mealDate,
            delivery_address: location.address || 'Current Location',
            delivery_latitude: location.latitude,
            delivery_longitude: location.longitude,
            items: orderItems
        };
        
        console.log('Order Data:', orderData);
        
        const response = await fetch('/food/api/order/place', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(orderData)
        });
        
        console.log('Response status:', response.status);
        
        const result = await response.json();
        console.log('Order Result:', result);
        
        if (result.success) {
            this.showMenuModal = false;
            this.showSuccess('Order placed successfully!');
            this.resetCart();
            await this.loadOrders();
            this.activeTab = 'orders';
        } else {
            this.showError(result.message || 'Failed to place order');
        }
    } catch (error) {
        console.error('Error placing order:', error);
        this.showError('Failed to place order: ' + error.message);
    } finally {
        this.isLoading = false;
    }
},
        async cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) return;
            
            this.isLoading = true;
            try {
                const response = await fetch(`/food/api/order/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Order cancelled successfully');
                    await this.loadOrders();
                } else {
                    this.showError(result.message || 'Failed to cancel order');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                this.showError('Failed to cancel order');
            } finally {
                this.isLoading = false;
            }
        },
        
        async reorderItems(orderId) {
            try {
                const response = await fetch(`/food/api/order/${orderId}/reorder`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Items added to cart!');
                    this.viewRestaurant(result.restaurant_id);
                    setTimeout(() => {
                        result.items.forEach(item => {
                            for(let i = 0; i < item.quantity; i++) {
                                this.increaseQuantity(item.food_item_id);
                            }
                        });
                    }, 500);
                } else {
                    this.showError(result.message || 'Failed to reorder');
                }
            } catch (error) {
                console.error('Error reordering:', error);
                this.showError('Failed to reorder');
            }
        },
        
        // Subscription Methods
async createSubscription(event) {
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    
    // Format delivery days as integer
    let deliveryDays = 0;
    const dayCheckboxes = event.target.querySelectorAll('input[name="delivery_days[]"]:checked');
    dayCheckboxes.forEach(checkbox => {
        deliveryDays |= parseInt(checkbox.value);
    });
    data.delivery_days = deliveryDays;
    
    // Add items from cart
    data.items = this.cartItems.map(item => ({
        food_item_id: item.id,
        quantity: item.quantity
    }));
    
    this.isLoading = true;
    try {
        const response = await fetch('/food/api/subscription/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            this.showSubscriptionModal = false;
            this.showSuccess('Subscription created successfully!');
            await this.loadSubscriptions();
            this.activeTab = 'subscriptions';
        } else {
            this.showError(result.message || 'Failed to create subscription');
        }
    } catch (error) {
        console.error('Error creating subscription:', error);
        this.showError('Failed to create subscription');
    } finally {
        this.isLoading = false;
    }
},
        async cancelSubscription(subscriptionId) {
            if (!confirm('Are you sure you want to cancel this subscription?')) return;
            
            this.isLoading = true;
            try {
                const response = await fetch(`/food/api/subscription/${subscriptionId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Subscription cancelled successfully');
                    await this.loadSubscriptions();
                } else {
                    this.showError(result.message || 'Failed to cancel subscription');
                }
            } catch (error) {
                console.error('Error cancelling subscription:', error);
                this.showError('Failed to cancel subscription');
            } finally {
                this.isLoading = false;
            }
        },
        
        async pauseSubscription(subscriptionId) {
            this.isLoading = true;
            try {
                const response = await fetch(`/food/api/subscription/${subscriptionId}/pause`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Subscription paused successfully');
                    await this.loadSubscriptions();
                } else {
                    this.showError(result.message || 'Failed to pause subscription');
                }
            } catch (error) {
                console.error('Error pausing subscription:', error);
                this.showError('Failed to pause subscription');
            } finally {
                this.isLoading = false;
            }
        },
        
        async resumeSubscription(subscriptionId) {
            this.isLoading = true;
            try {
                const response = await fetch(`/food/api/subscription/${subscriptionId}/resume`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Subscription resumed successfully');
                    await this.loadSubscriptions();
                } else {
                    this.showError(result.message || 'Failed to resume subscription');
                }
            } catch (error) {
                console.error('Error resuming subscription:', error);
                this.showError('Failed to resume subscription');
            } finally {
                this.isLoading = false;
            }
        },
        
        async markHelpful(reviewId) {
            try {
                const response = await fetch(`/food/api/rating/${reviewId}/helpful`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showSuccess('Thank you for your feedback!');
                }
            } catch (error) {
                console.error('Error marking helpful:', error);
            }
        },
        
        // Helper Methods
        getUserLocation() {
            return new Promise((resolve) => {
                @if(isset($defaultAddress) && $defaultAddress)
                    resolve({
                        latitude: {{ $defaultAddress->latitude ?? 'null' }},
                        longitude: {{ $defaultAddress->longitude ?? 'null' }},
                        address: "{{ $defaultAddress->full_address ?? 'Current Location' }}"
                    });
                @else
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                resolve({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude,
                                    address: 'Current Location'
                                });
                            },
                            () => {
                                resolve({
                                    latitude: 23.8103,
                                    longitude: 90.4125,
                                    address: 'Dhaka, Bangladesh'
                                });
                            }
                        );
                    } else {
                        resolve({
                            latitude: 23.8103,
                            longitude: 90.4125,
                            address: 'Dhaka, Bangladesh'
                        });
                    }
                @endif
            });
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'PENDING': 'bg-yellow-100 text-yellow-800',
                'ACCEPTED': 'bg-blue-100 text-blue-800',
                'PREPARING': 'bg-purple-100 text-purple-800',
                'OUT_FOR_DELIVERY': 'bg-indigo-100 text-indigo-800',
                'DELIVERED': 'bg-green-100 text-green-800',
                'CANCELLED': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getSubscriptionStatusBadgeClass(status) {
            const classes = {
                'ACTIVE': 'bg-green-100 text-green-800',
                'PAUSED': 'bg-yellow-100 text-yellow-800',
                'CANCELLED': 'bg-red-100 text-red-800',
                'COMPLETED': 'bg-blue-100 text-blue-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        getMealTypeName(mealTypeId) {
            const mealType = this.mealTypes.find(m => m.id == mealTypeId);
            return mealType ? mealType.name : '';
        },
        
        get filteredMenuItems() {
            if (!this.selectedRestaurant?.menu_items) return [];
            return this.selectedRestaurant.menu_items.filter(item => 
                item.meal_type_id == this.selectedMealTypeId
            );
        },
        
        selectMealType(mealTypeId) {
            this.selectedMealTypeId = mealTypeId;
        },
        
        loadMore() {
            if (this.currentPage < this.lastPage) {
                this.loadRestaurants(this.currentPage + 1);
            }
        },
        
        showSuccess(message) {
            alert('Success: ' + message);
        },
        
        showError(message) {
            alert('Error: ' + message);
        }
    };
}

// Make the Alpine component globally accessible for debugging
document.addEventListener('alpine:init', () => {
    console.log('Alpine initialized');
});
</script>

<style>
[x-cloak] {
    display: none !important;
}

.loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #4f46e5;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
    margin: 0 auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Custom scrollbar for modals */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection