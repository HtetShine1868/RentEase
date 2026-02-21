@extends('laundry-provider.layouts.provider')

@section('title', 'Order Management')
@section('subtitle', 'Manage all laundry orders')

@section('content')
<div class="space-y-6" x-data="orderManager()">
    {{-- Tab Navigation --}}
    <div class="bg-white rounded-lg shadow-sm p-1 flex space-x-1">
        <button @click="switchTab('normal')" 
                :class="activeTab === 'normal' ? 'bg-[#174455] text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="tab-btn flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors">
            <i class="fas fa-box mr-2"></i> NORMAL ORDERS
        </button>
        <button @click="switchTab('rush')" 
                :class="activeTab === 'rush' ? 'bg-[#174455] text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="tab-btn flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors">
            <i class="fas fa-bolt mr-2"></i> RUSH ORDERS
            @if(isset($rushCount) && $rushCount > 0)
                <span class="ml-2 bg-[#ffdb9f] text-[#174455] text-xs px-2 py-1 rounded-full">{{ $rushCount }}</span>
            @endif
        </button>
        <button @click="switchTab('all')" 
                :class="activeTab === 'all' ? 'bg-[#174455] text-white' : 'text-gray-600 hover:bg-gray-100'"
                class="tab-btn flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors">
            <i class="fas fa-list mr-2"></i> ALL ORDERS
        </button>
    </div>

    {{-- Date Filter --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">📅 Filter by Date</label>
                <input type="date" x-model="selectedDate" @change="filterOrders()" 
                       value="{{ request('date', date('Y-m-d')) }}"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">🔍 Search</label>
                <input type="text" x-model="searchTerm" @input="filterOrders()" placeholder="Order #, Customer..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="flex items-end">
                <button @click="resetFilters()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-undo mr-2"></i>Reset
                </button>
            </div>
        </div>
        <div class="mt-2 text-sm text-gray-500">
            📊 Showing orders for <span class="font-medium text-[#174455]">{{ \Carbon\Carbon::parse(request('date', now()))->format('F j, Y') }}</span>
        </div>
    </div>

    {{-- Loading Indicator --}}
    <div x-show="loading" x-cloak class="text-center py-4">
        <div class="loading-spinner rounded-full h-8 w-8 border-t-2 border-b-2 border-[#174455] mx-auto"></div>
        <p class="mt-2 text-gray-600">Loading orders...</p>
    </div>

    {{-- Normal Tab Content --}}
    <div x-show="activeTab === 'normal'" x-cloak id="normal-tab-content">
        @include('laundry-provider.orders.partials.normal-tab', ['orders' => $normalOrders])
    </div>

    {{-- Rush Tab Content --}}
    <div x-show="activeTab === 'rush'" x-cloak id="rush-tab-content">
        @include('laundry-provider.orders.partials.rush-tab', ['orders' => $rushOrders])
    </div>

    {{-- All Orders Tab Content --}}
    <div x-show="activeTab === 'all'" x-cloak id="all-tab-content">
        @include('laundry-provider.orders.partials.all-orders-tab', ['orders' => $allOrders])
    </div>
</div>

{{-- Order Details Modal --}}
@include('laundry-provider.orders.partials.order-details-modal')
@endsection

@push('scripts')
<script>
function orderManager() {
    return {
        activeTab: 'normal',
        selectedDate: '{{ request('date', date('Y-m-d')) }}',
        searchTerm: '',
        loading: false,
        
        init() {
            // Initialize event listeners for order actions
            this.initOrderActionListeners();
        },
        
        switchTab(tab) {
            this.activeTab = tab;
            this.filterOrders();
        },
        
        filterOrders() {
            this.loading = true;
            
            // Build URL with parameters
            const url = new URL('/laundry-provider/orders/filter', window.location.origin);
            url.searchParams.append('date', this.selectedDate);
            url.searchParams.append('tab', this.activeTab);
            if (this.searchTerm) {
                url.searchParams.append('search', this.searchTerm);
            }
            
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                // Update the active tab content
                if (this.activeTab === 'normal') {
                    document.getElementById('normal-tab-content').innerHTML = data.normal;
                } else if (this.activeTab === 'rush') {
                    document.getElementById('rush-tab-content').innerHTML = data.rush;
                } else {
                    document.getElementById('all-tab-content').innerHTML = data.all;
                }
                
                // Re-initialize event listeners for the new content
                this.initOrderActionListeners();
                this.loading = false;
            })
            .catch(error => {
                console.error('Error:', error);
                this.loading = false;
                alert('Failed to load orders. Please try again.');
            });
        },
        
        resetFilters() {
            this.selectedDate = '{{ date('Y-m-d') }}';
            this.searchTerm = '';
            this.filterOrders();
        },
        
        initOrderActionListeners() {
            // Remove existing listeners to prevent duplicates
            document.querySelectorAll('.accept-order-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleAcceptOrder);
                btn.addEventListener('click', this.handleAcceptOrder);
            });
            
            document.querySelectorAll('.mark-picked-up-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleMarkPickedUp);
                btn.addEventListener('click', this.handleMarkPickedUp);
            });
            
            document.querySelectorAll('.start-processing-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleStartProcessing);
                btn.addEventListener('click', this.handleStartProcessing);
            });
            
            document.querySelectorAll('.mark-ready-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleMarkReady);
                btn.addEventListener('click', this.handleMarkReady);
            });
            
            document.querySelectorAll('.out-for-delivery-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleOutForDelivery);
                btn.addEventListener('click', this.handleOutForDelivery);
            });
            
            document.querySelectorAll('.delivered-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleDelivered);
                btn.addEventListener('click', this.handleDelivered);
            });
            
            document.querySelectorAll('.cancel-order-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleCancelOrder);
                btn.addEventListener('click', this.handleCancelOrder);
            });
            
            document.querySelectorAll('.view-details-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleViewDetails);
                btn.addEventListener('click', this.handleViewDetails);
            });
            
            document.querySelectorAll('.call-customer-btn').forEach(btn => {
                btn.removeEventListener('click', this.handleCallCustomer);
                btn.addEventListener('click', this.handleCallCustomer);
            });
        },
        
        handleAcceptOrder(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            if (confirm('Accept this order?')) {
                // Disable button and show loading
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Accepting...';
                
                fetch(`/laundry-provider/orders/${orderId}/accept`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order accepted successfully');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to accept order');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error accepting order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        },
        
        handleMarkPickedUp(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            if (confirm('Mark this order as picked up?')) {
                // Disable button and show loading
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
                
                fetch(`/laundry-provider/orders/${orderId}/picked-up`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order marked as picked up');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update order');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        },
        
        handleStartProcessing(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            // Disable button and show loading
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Starting...';
            
            fetch(`/laundry-provider/orders/${orderId}/start-processing`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Processing started');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to update order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating order');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        },
        
        handleMarkReady(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            if (confirm('Mark this order as ready for delivery?')) {
                // Disable button and show loading
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
                
                fetch(`/laundry-provider/orders/${orderId}/mark-ready`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order marked as ready');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update order');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        },
        
        handleOutForDelivery(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            if (confirm('Mark this order as out for delivery?')) {
                // Disable button and show loading
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
                
                fetch(`/laundry-provider/orders/${orderId}/out-for-delivery`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order marked as out for delivery');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update order');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        },
        
        handleDelivered(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            if (confirm('Mark this order as delivered?')) {
                // Disable button and show loading
                btn.disabled = true;
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Updating...';
                
                fetch(`/laundry-provider/orders/${orderId}/deliver`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Order marked as delivered');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update order');
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                });
            }
        },
        
        handleCancelOrder(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (!orderId) {
                alert('Order ID not found');
                return;
            }
            
            const reason = prompt('Please provide a reason for cancellation:');
            if (!reason) return;
            
            // Disable button and show loading
            btn.disabled = true;
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Cancelling...';
            
            fetch(`/laundry-provider/orders/${orderId}/cancel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ reason: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Order cancelled successfully');
                    location.reload();
                } else {
                    alert(data.message || 'Failed to cancel order');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error cancelling order');
                btn.disabled = false;
                btn.innerHTML = originalText;
            });
        },
        
        handleViewDetails(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const orderId = btn.dataset.id;
            
            if (orderId) {
                openOrderModal(orderId);
            }
        },
        
        handleCallCustomer(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const phone = btn.dataset.phone;
            
            if (phone) {
                window.location.href = `tel:${phone}`;
            } else {
                alert('No phone number available');
            }
        }
    }
}

// Modal functions
function openOrderModal(orderId) {
    const modal = document.getElementById('orderDetailsModal');
    if (!modal) return;
    
    modal.classList.remove('hidden');
    
    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = `
        <div class="text-center py-8">
            <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-[#174455] mx-auto mb-3"></div>
            <p class="text-gray-600">Loading order details...</p>
        </div>
    `;
    
    fetch(`/laundry-provider/orders/${orderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.text())
    .then(html => {
        modalContent.innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        modalContent.innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                <p>Failed to load order details</p>
                <button onclick="closeOrderModal()" class="mt-3 px-4 py-2 bg-gray-100 rounded-lg">Close</button>
            </div>
        `;
    });
}

function closeOrderModal() {
    const modal = document.getElementById('orderDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target == modal) {
        closeOrderModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOrderModal();
    }
});
</script>
@endpush