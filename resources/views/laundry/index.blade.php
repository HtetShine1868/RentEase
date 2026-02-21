@extends('layouts.app')

@section('title', 'Laundry Services')
@section('subtitle', 'Find laundry services near you')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #map { height: 400px; width: 100%; border-radius: 8px; z-index: 1; }
    .provider-card {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .provider-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        border-color: #174455;
    }
    .distance-badge {
        background: rgba(23, 68, 85, 0.1);
        color: #174455;
    }
    .leaflet-container {
        z-index: 1;
    }
    .leaflet-popup-content {
        min-width: 200px;
    }
    .order-card {
        transition: all 0.2s ease;
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    .tab-btn.active {
        background-color: #174455;
        color: white;
    }
    .tab-btn:not(.active):hover {
        background-color: #f3f4f6;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header Card with Dashboard Button --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-[#174455]">Laundry Services</h2>
                <p class="text-gray-600">Find and book laundry services near you</p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Location Display --}}
                <div class="flex items-center space-x-2 bg-gray-50 px-4 py-2 rounded-lg">
                    <i class="fas fa-map-marker-alt text-[#174455]"></i>
                    <span class="text-sm text-gray-700 font-medium">
                        @if($userAddress)
                            {{ $userAddress->city }}, {{ $userAddress->state ?? '' }}
                        @else
                            <a href="{{ route('profile.address') }}" class="text-[#174455] hover:underline">
                                Add your location
                            </a>
                        @endif
                    </span>
                </div>
                
                {{-- Dashboard Button --}}
                <a href="{{ route('dashboard') }}" 
                   class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors inline-flex items-center">
                    <i class="fas fa-tachometer-alt mr-2"></i>
                    Go to Dashboard
                </a>
            </div>
        </div>
    </div>

    {{-- Tab Navigation --}}
    <div class="bg-white rounded-lg shadow-sm p-1 flex space-x-1">
        <button onclick="switchTab('find')" 
                class="tab-btn active flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors"
                id="tab-find">
            <i class="fas fa-search mr-2"></i> Find Services
        </button>
        <button onclick="switchTab('orders')" 
                class="tab-btn flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors"
                id="tab-orders">
            <i class="fas fa-shopping-bag mr-2"></i> My Orders
        </button>
        <button onclick="switchTab('rate')" 
                class="tab-btn flex-1 py-3 px-4 rounded-lg text-center font-medium transition-colors"
                id="tab-rate">
            <i class="fas fa-star mr-2"></i> Rate Orders
        </button>
    </div>

    {{-- Tab Content: Find Services --}}
    <div id="tab-content-find" class="tab-content">
        {{-- Search and Filter Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search by provider name..." 
                               class="w-full rounded-lg border-gray-300 pl-10 pr-4 py-2 focus:border-[#174455] focus:ring-[#174455]">
                        <div class="absolute left-3 top-2.5">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Type</label>
                    <select id="service-type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="all">All Services</option>
                        <option value="CLOTHING">Clothing</option>
                        <option value="BEDDING">Bedding</option>
                        <option value="CURTAIN">Curtain</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                    <select id="sort-by" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="rating">Highest Rated</option>
                        <option value="distance" {{ !$userAddress ? 'disabled' : '' }}>Nearest First</option>
                        <option value="orders">Most Popular</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Map and Providers Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Providers List --}}
            <div class="lg:col-span-1 space-y-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-[#174455]">Available Providers</h3>
                    <span class="text-sm text-gray-500">{{ $providers->total() }} found</span>
                </div>
                
                <div id="providers-list" class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                    @forelse($providers as $provider)
                    <div class="provider-card bg-white rounded-lg shadow-sm p-4 border border-gray-200 hover:border-[#174455]"
                         onclick="window.location.href='{{ route('laundry.provider.show', $provider->id) }}'">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                @if($provider->avatar_url)
                                    <img src="{{ Storage::url($provider->avatar_url) }}" 
                                         alt="{{ $provider->business_name }}"
                                         class="w-16 h-16 rounded-lg object-cover">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-[#174455] flex items-center justify-center">
                                        <i class="fas fa-tshirt text-white text-2xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900">{{ $provider->business_name }}</h4>
                                
                                <div class="flex items-center mt-1">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= round($provider->rating))
                                                <i class="fas fa-star text-yellow-400 text-xs"></i>
                                            @else
                                                <i class="far fa-star text-gray-300 text-xs"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2">
                                        ({{ number_format($provider->rating, 1) }})
                                    </span>
                                    <span class="text-xs text-gray-500 ml-2">
                                        • {{ $provider->total_orders ?? 0 }} orders
                                    </span>
                                </div>
                                
                                @if($userAddress && $provider->latitude && $provider->longitude && isset($provider->distance))
                                <div class="flex items-center mt-2">
                                    <span class="distance-badge text-xs px-2 py-1 rounded-full">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ number_format($provider->distance, 1) }} km away
                                    </span>
                                </div>
                                @endif
                                
                                <p class="text-xs text-gray-500 mt-2 line-clamp-2">
                                    {{ $provider->description ?? 'Professional laundry service' }}
                                </p>
                                
                                <div class="flex items-center justify-between mt-3">
                                    <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                        Open Now
                                    </span>
                                    <span class="text-sm font-semibold text-[#174455]">
                                        From ৳{{ $provider->laundryItems->min('base_price') ?? 0 }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="bg-white rounded-lg shadow-sm p-8 text-center text-gray-500">
                        <i class="fas fa-tshirt text-4xl text-gray-300 mb-3"></i>
                        <p>No laundry providers found</p>
                        <p class="text-sm mt-1">Try adjusting your filters</p>
                    </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-4">
                    {{ $providers->links() }}
                </div>
            </div>

            {{-- Map --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm p-4 sticky top-4">
                    <div id="map" style="height: 500px;"></div>
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Click on markers to view provider details
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab Content: My Orders --}}
    <div id="tab-content-orders" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-[#174455]">My Laundry Orders</h3>
                <a href="{{ route('dashboard') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
            </div>
            
            @php
                $userOrders = App\Models\LaundryOrder::with('serviceProvider')
                    ->where('user_id', Auth::id())
                    ->orderBy('created_at', 'desc')
                    ->get();
                    
                $statusColors = [
                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                    'PICKUP_SCHEDULED' => 'bg-blue-100 text-blue-800',
                    'PICKED_UP' => 'bg-purple-100 text-purple-800',
                    'IN_PROGRESS' => 'bg-indigo-100 text-indigo-800',
                    'READY' => 'bg-green-100 text-green-800',
                    'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                    'DELIVERED' => 'bg-gray-100 text-gray-800',
                    'CANCELLED' => 'bg-red-100 text-red-800'
                ];
            @endphp
            
            @if($userOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($userOrders as $order)
                    <div class="order-card bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <span class="font-bold text-[#174455]">#{{ $order->order_reference }}</span>
                                    @if($order->is_rush)
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">⚡ RUSH</span>
                                    @endif
                                    <span class="px-2 py-1 rounded-full text-xs {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                    @if($order->is_overdue)
                                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Overdue</span>
                                    @endif
                                    @if($order->urgency_level == 'urgent')
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs">Urgent</span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-500">Provider</p>
                                        <p class="font-medium">{{ $order->serviceProvider->business_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pickup</p>
                                        <p class="font-medium">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('M d, g:i A') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Total</p>
                                        <p class="font-bold text-[#174455]">৳{{ number_format($order->total_amount, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Progress</p>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-1">
                                            <div class="bg-[#174455] h-1.5 rounded-full" style="width: {{ $order->progress_percentage }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex gap-2">
                                <a href="{{ route('laundry.order.show', $order->id) }}" 
                                   class="px-3 py-1.5 bg-[#174455] text-white text-sm rounded-lg hover:bg-[#1f556b]">
                                    View
                                </a>
                                @if(in_array($order->status, ['PENDING', 'PICKUP_SCHEDULED']))
                                    <button onclick="cancelOrder({{ $order->id }})" 
                                            class="px-3 py-1.5 bg-red-50 text-red-600 text-sm rounded-lg hover:bg-red-100">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-shopping-bag text-4xl text-gray-300 mb-3"></i>
                    <p>No orders yet</p>
                    <p class="text-sm mt-1">Go to "Find Services" to place your first order</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Tab Content: Rate Orders --}}
    <div id="tab-content-rate" class="tab-content hidden">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-[#174455]">Orders Awaiting Your Rating</h3>
                <a href="{{ route('dashboard') }}" class="text-sm text-[#174455] hover:text-[#1f556b]">
                    <i class="fas fa-tachometer-alt mr-1"></i> Dashboard
                </a>
            </div>
            
            @php
                // Get delivered orders
                $deliveredOrders = App\Models\LaundryOrder::with('serviceProvider')
                    ->where('user_id', Auth::id())
                    ->where('status', 'DELIVERED')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Get orders that already have ratings from service_ratings table
                $ratedOrderIds = App\Models\ServiceRating::where('order_type', 'LAUNDRY')
                    ->whereIn('order_id', $deliveredOrders->pluck('id')->toArray())
                    ->pluck('order_id')
                    ->toArray();
                    
                $pendingRatings = $deliveredOrders->filter(function($order) use ($ratedOrderIds) {
                    return !in_array($order->id, $ratedOrderIds);
                });
            @endphp
            
            @if($pendingRatings->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingRatings as $order)
                    <div class="order-card bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2 flex-wrap">
                                    <span class="font-bold text-[#174455]">#{{ $order->order_reference }}</span>
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Delivered</span>
                                    @if($order->is_rush)
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded-full text-xs font-bold">⚡ RUSH</span>
                                    @endif
                                </div>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
                                    <div>
                                        <p class="text-gray-500">Provider</p>
                                        <p class="font-medium">{{ $order->serviceProvider->business_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Pickup Date</p>
                                        <p class="font-medium">{{ $order->pickup_time ? \Carbon\Carbon::parse($order->pickup_time)->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Delivery Date</p>
                                        <p class="font-medium">{{ $order->actual_return_date ? \Carbon\Carbon::parse($order->actual_return_date)->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">Total Amount</p>
                                        <p class="font-bold text-[#174455]">৳{{ number_format($order->total_amount, 2) }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <a href="{{ route('laundry.rate.show', $order->id) }}" 
                               class="px-4 py-2 bg-[#ffdb9f] text-[#174455] text-sm rounded-lg hover:bg-[#f8c570] transition-colors text-center whitespace-nowrap">
                                <i class="fas fa-star mr-1"></i> Rate Now
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-star text-4xl text-gray-300 mb-3"></i>
                    <p>No orders pending rating</p>
                    <p class="text-sm mt-1">When you receive your orders, you can rate them here</p>
                    @if($deliveredOrders->count() > 0)
                        <p class="text-xs text-green-600 mt-2">You've rated all your delivered orders. Thank you!</p>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let map;
    let markers = [];
    let userLat = {{ $userAddress->latitude ?? 23.8103 }};
    let userLng = {{ $userAddress->longitude ?? 90.4125 }};
    
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        loadProviders();
        setupFilters();
        setupTabs();
    });
    
    function initMap() {
        map = L.map('map').setView([userLat, userLng], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Add user location marker if address exists
        @if($userAddress)
        L.marker([userLat, userLng], {
            icon: L.divIcon({
                className: 'custom-div-icon',
                html: '<div style="background-color: #174455; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                iconSize: [20, 20],
                popupAnchor: [0, -10]
            })
        }).addTo(map).bindPopup('<strong>Your Location</strong>');
        @endif
    }
    
    function loadProviders() {
        // Clear existing markers
        markers.forEach(marker => map.removeLayer(marker));
        markers = [];
        
        @foreach($providers as $provider)
            @if($provider->latitude && $provider->longitude)
            (function() {
                const marker = L.marker([{{ $provider->latitude }}, {{ $provider->longitude }}]).addTo(map);
                
                let distanceText = '';
                @if($userAddress && isset($provider->distance))
                    distanceText = `<p class="text-sm text-gray-600 mt-1"><i class="fas fa-map-marker-alt text-[#174455] text-xs mr-1"></i>{{ number_format($provider->distance, 1) }} km away</p>`;
                @endif
                
                marker.bindPopup(`
                    <div class="p-2" style="min-width: 220px;">
                        <h4 class="font-bold text-[#174455] text-base">{{ $provider->business_name }}</h4>
                        <div class="flex items-center mt-1">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= round($provider->rating))
                                        <i class="fas fa-star text-yellow-400 text-xs"></i>
                                    @else
                                        <i class="far fa-star text-gray-300 text-xs"></i>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-xs text-gray-500 ml-2">({{ number_format($provider->rating, 1) }})</span>
                        </div>
                        ${distanceText}
                        <p class="text-xs text-gray-500 mt-2">{{ Str::limit($provider->description, 60) }}</p>
                        <div class="flex items-center justify-between mt-3">
                            <span class="text-xs px-2 py-1 bg-green-100 text-green-800 rounded-full">Open</span>
                            <span class="text-sm font-semibold text-[#174455]">From ৳{{ $provider->laundryItems->min('base_price') ?? 0 }}</span>
                        </div>
                        <a href="{{ route('laundry.provider.show', $provider->id) }}" 
                           class="mt-3 inline-block w-full text-center px-3 py-2 bg-[#174455] text-white text-sm rounded-lg hover:bg-[#1f556b] transition-colors no-underline">
                            View Details
                        </a>
                    </div>
                `);
                
                markers.push(marker);
            })();
            @endif
        @endforeach
    }
    
    function setupFilters() {
        const searchInput = document.getElementById('search');
        const serviceType = document.getElementById('service-type');
        const sortBy = document.getElementById('sort-by');
        
        let timeoutId;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => filterProviders(), 500);
        });
        
        serviceType.addEventListener('change', filterProviders);
        sortBy.addEventListener('change', filterProviders);
    }
    
    function filterProviders() {
        const search = document.getElementById('search').value;
        const type = document.getElementById('service-type').value;
        const sort = document.getElementById('sort-by').value;
        
        // Show loading state
        document.getElementById('providers-list').innerHTML = '<div class="text-center py-8"><i class="fas fa-spinner fa-spin text-[#174455] text-2xl"></i><p class="mt-2">Loading...</p></div>';
        
        fetch(`/laundry/api/providers?lat=${userLat}&lng=${userLng}&search=${search}&type=${type}&sort=${sort}`)
            .then(response => response.json())
            .then(data => {
                console.log(data);
                window.location.href = `?search=${search}&type=${type}&sort=${sort}`;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }
    
    function setupTabs() {
        // Check URL hash for initial tab
        const hash = window.location.hash.substring(1);
        if (hash === 'orders') {
            switchTab('orders');
        } else if (hash === 'rate') {
            switchTab('rate');
        } else {
            switchTab('find');
        }
    }
    
    function switchTab(tab) {
        // Update URL hash
        window.location.hash = tab;
        
        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active', 'bg-[#174455]', 'text-white');
        });
        document.getElementById(`tab-${tab}`).classList.add('active', 'bg-[#174455]', 'text-white');
        
        // Show/hide tab content
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        document.getElementById(`tab-content-${tab}`).classList.remove('hidden');
        
        // Refresh map if switching to find tab
        if (tab === 'find' && map) {
            setTimeout(() => {
                map.invalidateSize();
            }, 100);
        }
    }
    
    function cancelOrder(orderId) {
        const reason = prompt('Please provide a reason for cancellation:');
        if (!reason) return;
        
        fetch(`/laundry/order/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ reason: reason })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Order cancelled successfully');
                location.reload();
            } else {
                alert(data.message || 'Error cancelling order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error cancelling order');
        });
    }
</script>
@endpush
@endsection