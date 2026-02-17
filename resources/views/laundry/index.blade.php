@extends('dashboard')

@section('title', 'Laundry Services')

@section('content')
<div class="space-y-6" x-data="laundryApp()" x-init="init()">
    <!-- Header with Stats -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Laundry Services</h2>
                <p class="mt-2 text-gray-600">Professional laundry and dry cleaning services</p>
            </div>
            <div class="flex space-x-4">
                <div class="text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['totalOrders'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Total Orders</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['pendingOrders'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">In Progress</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['completedOrders'] ?? 0 }}</div>
                    <div class="text-sm text-gray-500">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button @click="activeTab = 'providers'; loadProviders()" 
                        :class="activeTab === 'providers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Browse Providers
                </button>
                <button @click="activeTab = 'orders'; loadOrders()" 
                        :class="activeTab === 'orders' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Orders
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

            <!-- Providers Tab -->
            <div x-show="activeTab === 'providers'" x-cloak>
                @include('laundry.partials.providers-tab')
            </div>

            <!-- Orders Tab -->
            <div x-show="activeTab === 'orders'" x-cloak>
                @include('laundry.partials.orders-tab')
            </div>
        </div>
    </div>
</div>

<!-- Provider Items Modal -->
@include('laundry.partials.items-modal')

<!-- Order Details Modal -->
@include('laundry.partials.order-details-modal')

<!-- Location Request Modal -->
@include('laundry.partials.location-modal')

<!-- Toast Notification -->
<div x-show="toast.show" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform translate-x-full"
     x-transition:enter-end="opacity-100 transform translate-x-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100 transform translate-x-0"
     x-transition:leave-end="opacity-0 transform translate-x-full"
     class="fixed bottom-4 right-4 z-50"
     @click="toast.show = false">
    <div :class="{
        'bg-green-500': toast.type === 'success',
        'bg-red-500': toast.type === 'error',
        'bg-blue-500': toast.type === 'info'
    }" class="px-6 py-3 rounded-lg shadow-lg text-white font-medium cursor-pointer">
        <div class="flex items-center space-x-2">
            <i :class="{
                'fas fa-check-circle': toast.type === 'success',
                'fas fa-exclamation-circle': toast.type === 'error',
                'fas fa-info-circle': toast.type === 'info'
            }"></i>
            <span x-text="toast.message"></span>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('laundryApp', () => ({
        // Tab state
        activeTab: 'providers',
        
        // Filters
        searchQuery: '',
        selectedItemType: '',
        sortBy: 'rating',
        orderStatusFilter: '',
        
        // Data
        providers: [],
        orders: @json($recentOrders),
        itemTypes: @json($itemTypes),
        selectedProvider: null,
        selectedOrder: null,
        
        // UI State
        isLoading: false,
        showItemsModal: false,
        showOrderDetailsModal: false,
        showLocationModal: false,
        
        // Cart
        cart: {},
        cartItems: [],
        cartTotal: 0,
        
        // Order details
        serviceMode: 'NORMAL',
        pickupDate: new Date().toISOString().split('T')[0],
        pickupTime: '10:00',
        pickupInstructions: '',
        
        // Pagination
        currentPage: 1,
        lastPage: 1,
        
        // Toast
        toast: {
            show: false,
            message: '',
            type: 'success'
        },
        
        // Initialize
        init() {
            console.log('Laundry app initialized');
            this.loadProviders();
            this.checkLocation();
        },
        
        // Location methods
        checkLocation() {
            @if(!isset($defaultAddress) || !$defaultAddress)
                setTimeout(() => {
                    this.showLocationModal = true;
                }, 1000);
            @endif
        },
        
        getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        this.saveLocation(
                            position.coords.latitude,
                            position.coords.longitude,
                            'Current Location'
                        );
                    },
                    (error) => {
                        console.error('Geolocation error:', error);
                        this.showLocationModal = false;
                        this.showToast('Unable to get your location. Using default location.', 'info');
                    }
                );
            } else {
                this.showLocationModal = false;
            }
        },
        
        saveLocation(latitude, longitude, address) {
            fetch('/user/save-location', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({
                    latitude: latitude,
                    longitude: longitude,
                    address: address
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showLocationModal = false;
                    this.showToast('Location saved successfully!', 'success');
                    this.loadProviders();
                }
            })
            .catch(error => {
                console.error('Error saving location:', error);
                this.showLocationModal = false;
            });
        },
        
        skipLocation() {
            this.showLocationModal = false;
            this.loadProviders();
        },
        
        // Toast methods
        showToast(message, type = 'success') {
            this.toast.message = message;
            this.toast.type = type;
            this.toast.show = true;
            
            setTimeout(() => {
                this.toast.show = false;
            }, 3000);
        },
        
        // API Methods
        async loadProviders(page = 1) {
            this.isLoading = true;
            try {
                const params = new URLSearchParams({
                    search: this.searchQuery,
                    item_type: this.selectedItemType,
                    sort: this.sortBy,
                    page: page
                });
                
                const response = await fetch(`/laundry/api/providers?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load providers');
                
                const data = await response.json();
                if (data.success) {
                    if (page === 1) {
                        this.providers = data.providers;
                    } else {
                        this.providers = [...this.providers, ...data.providers];
                    }
                    this.currentPage = data.current_page || page;
                    this.lastPage = data.last_page || 1;
                }
            } catch (error) {
                console.error('Error loading providers:', error);
                this.showToast('Failed to load providers', 'error');
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
                
                const response = await fetch(`/laundry/api/orders?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load orders');
                
                const data = await response.json();
                if (data.success) {
                    this.orders = data.orders;
                }
            } catch (error) {
                console.error('Error loading orders:', error);
                this.showToast('Failed to load orders', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        async viewProvider(providerId) {
            this.isLoading = true;
            try {
                const response = await fetch(`/laundry/api/provider/${providerId}/items`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                if (!response.ok) throw new Error('Failed to load provider items');
                
                const data = await response.json();
                if (data.success) {
                    this.selectedProvider = data.provider;
                    this.selectedProvider.items = data.items || [];
                    this.selectedProvider.grouped_items = data.grouped_items || {};
                    this.showItemsModal = true;
                    this.resetCart();
                }
            } catch (error) {
                console.error('Error loading provider:', error);
                this.showToast('Failed to load provider items', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        viewOrderDetails(orderId) {
            const order = this.orders.find(o => o.id === orderId);
            if (order) {
                this.selectedOrder = order;
                this.showOrderDetailsModal = true;
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
            const item = this.findItem(itemId);
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
        
        findItem(itemId) {
            return this.selectedProvider?.items?.find(i => i.id == itemId);
        },
        
        calculateCartTotal() {
            let total = 0;
            for (const item of Object.values(this.cart)) {
                let itemTotal = parseFloat(item.total_price) * item.quantity;
                
                // Add rush surcharge if applicable
                if (this.serviceMode === 'RUSH') {
                    itemTotal *= (1 + (item.rush_surcharge_percent / 100));
                }
                
                total += itemTotal;
            }
            
            // Add pickup fee
            if (this.selectedProvider?.pickup_fee > 0) {
                total += this.selectedProvider.pickup_fee;
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
            this.serviceMode = 'NORMAL';
            this.pickupInstructions = '';
        },
        
        // Calculate expected return date
        getExpectedReturnDate() {
            if (!this.selectedProvider || !this.pickupDate || !this.pickupTime) return 'N/A';
            
            const pickupDateTime = new Date(`${this.pickupDate}T${this.pickupTime}`);
            const turnaroundHours = this.serviceMode === 'RUSH' 
                ? this.selectedProvider.rush_turnaround_hours 
                : this.selectedProvider.normal_turnaround_hours;
            
            const returnDate = new Date(pickupDateTime.getTime() + (turnaroundHours * 60 * 60 * 1000));
            
            return returnDate.toLocaleDateString('en-US', { 
                month: 'short', 
                day: 'numeric', 
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        },
        
        // Order Methods
        async placeOrder() {
            if (this.cartTotal === 0) {
                this.showToast('Please add items to cart', 'error');
                return;
            }
            
            this.isLoading = true;
            
            try {
                const orderItems = this.cartItems.map(item => ({
                    laundry_item_id: parseInt(item.id),
                    quantity: item.quantity
                }));
                
                const pickupDateTime = `${this.pickupDate}T${this.pickupTime}:00`;
                
                const orderData = {
                    service_provider_id: this.selectedProvider.id,
                    service_mode: this.serviceMode,
                    pickup_address: 'Current Location',
                    pickup_latitude: 23.8103,
                    pickup_longitude: 90.4125,
                    pickup_time: pickupDateTime,
                    pickup_instructions: this.pickupInstructions,
                    items: orderItems
                };
                
                const response = await fetch('/laundry/api/order/place', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(orderData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showItemsModal = false;
                    this.showToast('Order placed successfully! Expected return: ' + this.getExpectedReturnDate(), 'success');
                    this.resetCart();
                    await this.loadOrders();
                    this.activeTab = 'orders';
                } else {
                    this.showToast(result.message || 'Failed to place order', 'error');
                }
            } catch (error) {
                console.error('Error placing order:', error);
                this.showToast('Failed to place order', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        async cancelOrder(orderId) {
            if (!confirm('Are you sure you want to cancel this order?')) return;
            
            this.isLoading = true;
            try {
                const response = await fetch(`/laundry/api/order/${orderId}/cancel`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    this.showToast('Order cancelled successfully', 'success');
                    await this.loadOrders();
                    this.showOrderDetailsModal = false;
                } else {
                    this.showToast(result.message || 'Failed to cancel order', 'error');
                }
            } catch (error) {
                console.error('Error cancelling order:', error);
                this.showToast('Failed to cancel order', 'error');
            } finally {
                this.isLoading = false;
            }
        },
        
        // Helper Methods
        get filteredItems() {
            if (!this.selectedProvider?.items) return [];
            if (!this.selectedItemType) return this.selectedProvider.items;
            return this.selectedProvider.items.filter(item => 
                item.item_type === this.selectedItemType
            );
        },
        
        get groupedItems() {
            return this.selectedProvider?.grouped_items || {};
        },
        
        get itemTypeNames() {
            return {
                'CLOTHING': 'Clothing',
                'BEDDING': 'Bedding',
                'CURTAIN': 'Curtain',
                'OTHER': 'Other'
            };
        },
        
        loadMore() {
            if (this.currentPage < this.lastPage) {
                this.loadProviders(this.currentPage + 1);
            }
        },
        
        getStatusBadgeClass(status) {
            const classes = {
                'PENDING': 'bg-yellow-100 text-yellow-800',
                'PICKUP_SCHEDULED': 'bg-blue-100 text-blue-800',
                'PICKED_UP': 'bg-purple-100 text-purple-800',
                'IN_PROGRESS': 'bg-indigo-100 text-indigo-800',
                'READY': 'bg-green-100 text-green-800',
                'OUT_FOR_DELIVERY': 'bg-orange-100 text-orange-800',
                'DELIVERED': 'bg-green-100 text-green-800',
                'CANCELLED': 'bg-red-100 text-red-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        
        // Button click handler with visual feedback
        handleButtonClick(event, callback) {
            const button = event.currentTarget;
            button.classList.add('btn-clicked');
            
            setTimeout(() => {
                button.classList.remove('btn-clicked');
            }, 300);
            
            if (callback) {
                callback();
            }
        },
        
        // Async button handler with loading state
        async withLoading(button, callback) {
            button.classList.add('btn-loading');
            button.disabled = true;
            
            try {
                await callback();
            } finally {
                button.classList.remove('btn-loading');
                button.disabled = false;
            }
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

.line-clamp-2 {
    overflow: hidden;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

/* Button Styles */
.btn-clicked {
    animation: button-pop 0.3s ease-in-out;
}

@keyframes button-pop {
    0% { transform: scale(1); }
    50% { transform: scale(0.95); }
    100% { transform: scale(1); }
}

.btn-loading {
    position: relative;
    color: transparent !important;
    pointer-events: none;
}

.btn-loading:after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    top: 50%;
    left: 50%;
    margin-left: -8px;
    margin-top: -8px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: #fff;
    animation: spin 0.6s linear infinite;
}

.hover-lift {
    transition: transform 0.2s ease;
}

.hover-lift:hover {
    transform: translateY(-2px);
}

.hover-lift:active {
    transform: translateY(0);
}
</style>
@endsection