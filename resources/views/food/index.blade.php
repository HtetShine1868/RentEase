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

    <!-- Modals -->
    <template x-if="showMenuModal">
        @include('food.partials.menu-modal')
    </template>

    <template x-if="showSubscriptionModal">
        @include('food.partials.subscription-modal')
    </template>

    <template x-if="showOrderDetailsModal">
        @include('food.partials.order-details-modal')
    </template>

    <template x-if="showReviewsModal">
        @include('food.partials.reviews-modal')
    </template>

    <template x-if="showLocationModal">
        @include('food.partials.location-modal')
    </template>

    <template x-if="showMapModal">
        @include('food.partials.map-view')
    </template>

    <!-- Address Selection Modal -->
    <template x-if="showAddressSelectionModal">
        <div class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showAddressSelectionModal = false"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Select Saved Address</h3>
                        <div class="space-y-3 max-h-60 overflow-y-auto">
                            <template x-for="address in savedAddresses" :key="address.id">
                                <div @click="selectSavedAddress(address.id); showAddressSelectionModal = false;" 
                                     class="p-3 border rounded-lg cursor-pointer hover:bg-gray-50">
                                    <p class="font-medium" x-text="address.address_line1"></p>
                                    <p class="text-sm text-gray-600" x-text="address.city + ', ' + address.state"></p>
                                </div>
                            </template>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="showAddressSelectionModal = false" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('foodServices', () => ({
        // ============ TAB STATE ============
        activeTab: 'restaurants',
        
        // ============ FILTERS ============
        searchQuery: '',
        selectedMealType: '',
        sortBy: 'recommended',
        orderStatusFilter: '',
        openNow: false,
        showCuisineFilter: false,
        
        // ============ DATA ARRAYS ============
        restaurants: @json($initialRestaurants ?? []),
        orders: [],
        subscriptions: [],
        mealTypes: @json($mealTypes ?? []),
        savedAddresses: [],
        
        // ============ SELECTED ITEMS ============
        selectedRestaurant: null,
        selectedOrder: null,
        selectedSubscription: null,
        selectedRestaurantForReviews: null,
        reviewsData: null,
        selectedMealTypeId: {{ $mealTypes->first()->id ?? 'null' }},
        orderType: 'PAY_PER_EAT',
        
        // ============ LOCATION ============
        selectedLocation: (() => {
            @if(isset($defaultAddress) && $defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude)
            return {
                lat: {{ $defaultAddress->latitude }},
                lng: {{ $defaultAddress->longitude }},
                address: "{{ addslashes(($defaultAddress->address_line1 ?? '') . ', ' . ($defaultAddress->city ?? '')) }}",
                id: {{ $defaultAddress->id ?? 'null' }}
            };
            @else
            return null;
            @endif
        })(),
        selectedAddressId: (() => {
            @if(isset($defaultAddress) && $defaultAddress && $defaultAddress->id)
            return {{ $defaultAddress->id }};
            @else
            return null;
            @endif
        })(),
        deliveryDistance: null,
        deliveryFee: 0,
        estimatedDeliveryTime: null,
        locationSearch: '',
        searchResults: [],
        showSearchResults: false,
        
        // ============ MAP ============
        map: null,
        marker: null,
        mapInitialized: false,
        mapError: false,
        
        // ============ UI STATE ============
        isLoading: false,
        isLoadingReviews: false,
        showMenuModal: false,
        showSubscriptionModal: false,
        showOrderDetailsModal: false,
        showReviewsModal: false,
        showLocationModal: false,
        showMapModal: false,
        showAddressSelectionModal: false,
        
        // ============ CART ============
        cart: {},
        cartItems: [],
        cartTotal: 0,
        
        // ============ PAGINATION ============
        currentPage: 1,
        lastPage: 1,
        
        // ============ INITIALIZATION ============
        init() {
            console.log('Food services initialized');
            
            // Fix Leaflet icon paths
            if (typeof L !== 'undefined' && L.Icon && L.Icon.Default) {
                delete L.Icon.Default.prototype._getIconUrl;
                
                L.Icon.Default.mergeOptions({
                    iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                });
            }
            
            this.setupEventListeners();
            if (this.selectedLocation) {
                this.updateDeliveryLocation(this.selectedLocation);
            }
        },
        
        setupEventListeners() {
            window.addEventListener('view-restaurant', (event) => {
                this.viewRestaurant(event.detail.restaurantId);
            });
            
            window.addEventListener('view-reviews', (event) => {
                this.viewReviews(event.detail.restaurantId);
            });
            
            window.addEventListener('location-selected', (event) => {
                this.selectedLocation = event.detail;
                this.updateDeliveryLocation(this.selectedLocation);
            });
        },
        
        // ============ API METHODS ============
        async loadRestaurants(page = 1) {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    meal_type: this.selectedMealType,
                    sort: this.sortBy,
                    page: page
                });
                
                if (this.selectedLocation) {
                    params.append('latitude', this.selectedLocation.lat);
                    params.append('longitude', this.selectedLocation.lng);
                }
                
                if (this.openNow) {
                    params.append('open_now', '1');
                }
                
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
                    
                    if (this.selectedLocation) {
                        this.updateDeliveryLocation(this.selectedLocation);
                    }
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
            this.isLoading = true;
            try {
                const response = await fetch(`/food/api/restaurant/${restaurantId}/menu`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load menu');
                
                const data = await response.json();
                if (data.success) {
                    this.selectedRestaurant = data.restaurant;
                    this.selectedRestaurant.menu = data.menu;
                    this.selectedMealTypeId = this.mealTypes && this.mealTypes.length > 0 ? this.mealTypes[0].id : null;
                    this.showMenuModal = true;
                    this.resetCart();
                    
                    if (this.selectedLocation) {
                        const distance = this.calculateDistance(
                            this.selectedRestaurant.latitude,
                            this.selectedRestaurant.longitude,
                            this.selectedLocation.lat,
                            this.selectedLocation.lng
                        );
                        this.deliveryDistance = distance;
                        this.deliveryFee = distance <= 2 ? 0 : Math.round(distance * 10);
                        this.estimatedDeliveryTime = 30 + Math.ceil(distance * 5);
                    }
                }
            } catch (error) {
                console.error('Error loading restaurant:', error);
                this.showError('Failed to load restaurant menu');
            } finally {
                this.isLoading = false;
            }
        },
        
        async viewReviews(restaurantId) {
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
                
                if (!response.ok) throw new Error('Failed to load reviews');
                
                const data = await response.json();
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
                }
            } catch (error) {
                console.error('Error loading reviews:', error);
                this.showError('Failed to load reviews');
            } finally {
                this.isLoadingReviews = false;
            }
        },
        
        // ============ CART METHODS ============
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
            if (!this.selectedRestaurant?.menu) return null;
            for (const section of this.selectedRestaurant.menu) {
                const item = section.items.find(i => i.id == itemId);
                if (item) return item;
            }
            return null;
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
        
        get filteredMenuItems() {
            if (!this.selectedRestaurant?.menu) return [];
            const section = this.selectedRestaurant.menu.find(s => s.meal_type_id == this.selectedMealTypeId);
            return section ? section.items : [];
        },
        
        selectMealType(mealTypeId) {
            this.selectedMealTypeId = mealTypeId;
        },
        
        // ============ ORDER METHODS ============
        async placeOrder() {
            if (this.cartTotal == 0) {
                this.showError('Please add items to cart');
                return;
            }
            
            if (this.orderType === 'SUBSCRIPTION') {
                this.showSubscriptionModal = true;
                return;
            }
            
            if (!this.selectedLocation) {
                this.showError('Please select a delivery location');
                this.showLocationModal = true;
                return;
            }
            
            if (!this.selectedRestaurant || !this.selectedRestaurant.id) {
                this.showError('Restaurant information is missing');
                return;
            }
            
            this.isLoading = true;
            
            try {
                const orderItems = Object.values(this.cart).map(item => ({
                    food_item_id: parseInt(item.id),
                    quantity: item.quantity
                }));
                
                const today = new Date().toISOString().split('T')[0];
                
                const orderData = {
                    service_provider_id: this.selectedRestaurant.id,
                    meal_type_id: this.selectedMealTypeId,
                    meal_date: today,
                    delivery_address: this.selectedLocation.address,
                    delivery_latitude: this.selectedLocation.lat,
                    delivery_longitude: this.selectedLocation.lng,
                    items: orderItems
                };
                
                const response = await fetch('/food/api/order/place', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
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
        
        // ============ SUBSCRIPTION METHODS ============
        async createSubscription(event) {
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData.entries());
            
            let deliveryDays = 0;
            const dayCheckboxes = event.target.querySelectorAll('input[name="delivery_days[]"]:checked');
            dayCheckboxes.forEach(checkbox => {
                deliveryDays |= parseInt(checkbox.value);
            });
            data.delivery_days = deliveryDays;
            
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
        
        // ============ LOCATION METHODS ============
        openMapModal() {
            console.log('Opening map modal');
            this.showMapModal = true;
            this.mapError = false;
            this.mapInitialized = false;
            
            setTimeout(() => {
                this.initMap();
            }, 500);
        },
        
        async initMap() {
            console.log('Initializing map...');
            
            if (this.mapInitialized) {
                console.log('Map already initialized');
                return;
            }
            
            this.mapError = false;
            
            try {
                if (!document.querySelector('#leaflet-css')) {
                    console.log('Loading Leaflet CSS...');
                    const link = document.createElement('link');
                    link.id = 'leaflet-css';
                    link.rel = 'stylesheet';
                    link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
                    document.head.appendChild(link);
                }
                
                if (!window.L) {
                    console.log('Loading Leaflet JS...');
                    await new Promise((resolve, reject) => {
                        const script = document.createElement('script');
                        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
                        script.onload = () => {
                            console.log('Leaflet JS loaded successfully');
                            resolve();
                        };
                        script.onerror = (error) => {
                            console.error('Failed to load Leaflet JS:', error);
                            reject(error);
                        };
                        document.head.appendChild(script);
                    });
                }
                
                let centerLat = 23.8103;
                let centerLng = 90.4125;
                
                if (this.selectedLocation) {
                    centerLat = this.selectedLocation.lat;
                    centerLng = this.selectedLocation.lng;
                } else {
                    const location = await this.getUserLocation();
                    centerLat = location.latitude;
                    centerLng = location.longitude;
                }
                
                console.log('Map center:', centerLat, centerLng);
                
                setTimeout(() => {
                    const mapContainer = document.getElementById('map');
                    
                    if (!mapContainer) {
                        console.error('Map container not found');
                        this.mapError = true;
                        return;
                    }
                    
                    console.log('Map container found, creating map...');
                    
                    this.map = L.map('map', {
                        center: [centerLat, centerLng],
                        zoom: 13,
                        zoomControl: false,
                        fadeAnimation: true,
                        markerZoomAnimation: true
                    });
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                        maxZoom: 19,
                        minZoom: 5
                    }).addTo(this.map);
                    
                    this.marker = L.marker([centerLat, centerLng], {
                        draggable: true,
                        autoPan: true
                    }).addTo(this.map);
                    
                    this.marker.on('dragend', async (event) => {
                        const position = event.target.getLatLng();
                        console.log('Marker dragged to:', position);
                        
                        const address = await this.reverseGeocode(position.lat, position.lng);
                        
                        this.selectedLocation = {
                            lat: position.lat,
                            lng: position.lng,
                            address: address
                        };
                    });
                    
                    this.map.on('click', async (e) => {
                        console.log('Map clicked at:', e.latlng);
                        
                        this.marker.setLatLng(e.latlng);
                        
                        const address = await this.reverseGeocode(e.latlng.lat, e.latlng.lng);
                        
                        this.selectedLocation = {
                            lat: e.latlng.lat,
                            lng: e.latlng.lng,
                            address: address
                        };
                    });
                    
                    setTimeout(() => {
                        if (this.map) {
                            this.map.invalidateSize();
                            console.log('Map size invalidated');
                        }
                    }, 100);
                    
                    this.mapInitialized = true;
                    this.mapError = false;
                    console.log('Map initialized successfully');
                    
                }, 300);
                
            } catch (error) {
                console.error('Error initializing map:', error);
                this.mapError = true;
                this.mapInitialized = false;
            }
        },
        
        async getCurrentLocation() {
            console.log('Getting current location...');
            try {
                const position = await new Promise((resolve, reject) => {
                    navigator.geolocation.getCurrentPosition(resolve, reject, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    });
                });
                
                console.log('Location obtained:', position);
                
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                
                const address = await this.reverseGeocode(lat, lng);
                
                this.selectedLocation = {
                    lat: lat,
                    lng: lng,
                    address: address
                };
                
                if (this.map) {
                    this.map.setView([lat, lng], 15);
                    this.marker.setLatLng([lat, lng]);
                }
                
                this.showMapModal = false;
                this.showLocationModal = false;
                this.updateDeliveryLocation(this.selectedLocation);
                this.showSuccess('Location detected!');
                console.log('Location set successfully');
                
            } catch (error) {
                console.error('Error getting current location:', error);
                this.showError('Unable to get your current location. Please select manually.');
            }
        },
        
        async reverseGeocode(lat, lng) {
            try {
                const response = await fetch(`/food/api/reverse-geocode?latitude=${lat}&longitude=${lng}`);
                const data = await response.json();
                return data.address || 'Selected Location';
            } catch (error) {
                console.error('Reverse geocoding error:', error);
                return 'Selected Location';
            }
        },
        
        async searchLocation() {
            if (this.locationSearch.length < 3) {
                this.searchResults = [];
                return;
            }
            
            try {
                const response = await fetch(`/food/api/search-locations?query=${encodeURIComponent(this.locationSearch)}`);
                const data = await response.json();
                if (data.success) {
                    this.searchResults = data.results;
                    this.showSearchResults = true;
                }
            } catch (error) {
                console.error('Location search error:', error);
            }
        },
        
        selectSearchResult(result) {
            const lat = parseFloat(result.lat);
            const lng = parseFloat(result.lon);
            
            if (this.map) {
                this.map.setView([lat, lng], 15);
                this.marker.setLatLng([lat, lng]);
            }
            
            this.selectedLocation = {
                lat: lat,
                lng: lng,
                address: result.display_name
            };
            
            this.locationSearch = '';
            this.searchResults = [];
            this.showSearchResults = false;
        },
        
        confirmLocation() {
            if (this.selectedLocation) {
                window.dispatchEvent(new CustomEvent('location-selected', {
                    detail: this.selectedLocation
                }));
                this.updateDeliveryLocation(this.selectedLocation);
                this.showMapModal = false;
                this.showSuccess('Location confirmed!');
            }
        },
        
        useCurrentLocation() {
            this.getCurrentLocation();
        },
        
        useSavedAddress() {
            this.showLocationModal = false;
            this.fetchSavedAddresses();
        },
        
        async fetchSavedAddresses() {
            try {
                const response = await fetch('/food/api/user/addresses', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load addresses');
                
                const data = await response.json();
                if (data.success && data.addresses && data.addresses.length > 0) {
                    this.savedAddresses = data.addresses;
                    this.showAddressSelectionModal = true;
                } else {
                    this.showError('No saved addresses found. Please add an address first.');
                }
            } catch (error) {
                console.error('Error fetching addresses:', error);
                this.showError('Failed to load saved addresses');
            }
        },
        
        async selectSavedAddress(addressId) {
            try {
                const response = await fetch('/food/api/user/addresses');
                const data = await response.json();
                if (data.success) {
                    const address = data.addresses.find(a => a.id === addressId);
                    if (address) {
                        this.selectedLocation = {
                            lat: parseFloat(address.latitude),
                            lng: parseFloat(address.longitude),
                            address: address.full_address || address.address_line1 + ', ' + address.city,
                            id: address.id
                        };
                        this.selectedAddressId = addressId;
                        this.updateDeliveryLocation(this.selectedLocation);
                    }
                }
            } catch (error) {
                console.error('Error selecting saved address:', error);
            }
        },
        
        updateDeliveryLocation(location) {
            if (!location) return;
            
            this.restaurants = this.restaurants.map(restaurant => {
                if (restaurant.latitude && restaurant.longitude) {
                    const distance = this.calculateDistance(
                        restaurant.latitude,
                        restaurant.longitude,
                        location.lat,
                        location.lng
                    );
                    restaurant.distance = distance;
                    restaurant.distance_km = distance.toFixed(1) + ' km';
                    restaurant.estimated_delivery_minutes = 30 + Math.ceil(distance * 5);
                    restaurant.delivery_fee = distance <= 2 ? 0 : Math.round(distance * 10);
                }
                return restaurant;
            });
            
            if (this.selectedRestaurant) {
                const distance = this.calculateDistance(
                    this.selectedRestaurant.latitude,
                    this.selectedRestaurant.longitude,
                    location.lat,
                    location.lng
                );
                this.deliveryDistance = distance;
                this.deliveryFee = distance <= 2 ? 0 : Math.round(distance * 10);
                this.estimatedDeliveryTime = 30 + Math.ceil(distance * 5);
            }
        },
        
        calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371;
            const dLat = this.deg2rad(lat2 - lat1);
            const dLon = this.deg2rad(lon2 - lon1);
            const a = 
                Math.sin(dLat/2) * Math.sin(dLat/2) +
                Math.cos(this.deg2rad(lat1)) * Math.cos(this.deg2rad(lat2)) * 
                Math.sin(dLon/2) * Math.sin(dLon/2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
            return Math.round(R * c * 10) / 10;
        },
        
        deg2rad(deg) {
            return deg * (Math.PI/180);
        },
        
        getUserLocation() {
            return new Promise((resolve) => {
                @if(isset($defaultAddress) && $defaultAddress && $defaultAddress->latitude && $defaultAddress->longitude)
                    resolve({
                        latitude: {{ $defaultAddress->latitude }},
                        longitude: {{ $defaultAddress->longitude }},
                        address: "{{ addslashes($defaultAddress->address_line1 ?? 'Current Location') }}"
                    });
                @else
                    if (typeof navigator !== 'undefined' && navigator.geolocation) {
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
        
        // ============ HELPER METHODS ============
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
        
        loadMore() {
            if (this.currentPage < this.lastPage) {
                this.loadRestaurants(this.currentPage + 1);
            }
        },
        
        resetFilters() {
            this.searchQuery = '';
            this.selectedMealType = '';
            this.sortBy = 'recommended';
            this.openNow = false;
            this.loadRestaurants(1);
        },
        
        applyCuisineFilter() {
            this.loadRestaurants(1);
        },
        
        showSuccess(message) {
            alert('Success: ' + message);
        },
        
        showError(message) {
            alert('Error: ' + message);
        }
    }));
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

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Map container fixes */
#map {
    height: 400px;
    width: 100%;
    z-index: 1;
    background-color: #f0f0f0;
}

.leaflet-container {
    height: 100%;
    width: 100%;
    z-index: 1;
    border-radius: 0.5rem;
}

.leaflet-default-icon-path {
    background-image: url(https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png);
}

.restaurant-card {
    transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
}

.restaurant-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}
</style>
@endsection