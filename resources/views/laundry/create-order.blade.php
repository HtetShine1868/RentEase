@extends('layouts.apps')

@section('title', 'Place Laundry Order')
@section('subtitle', 'Select items and pickup location')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #location-map { height: 300px; width: 100%; border-radius: 8px; z-index: 1; }
    .leaflet-container { z-index: 1; }
    .modal { z-index: 9999; }
    .item-card {
        transition: all 0.2s ease;
    }
    .item-card:hover {
        border-color: #174455;
        background-color: #f8f9fa;
    }
    .quantity-input {
        width: 80px;
        text-align: center;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-semibold text-[#174455]">Place Laundry Order</h2>
                <p class="text-gray-600">Select items and choose pickup location</p>
            </div>
            <div class="flex items-center gap-2">
                @if($provider->avatar_url)
                    <img src="{{ Storage::url($provider->avatar_url) }}" 
                         alt="{{ $provider->business_name }}"
                         class="w-12 h-12 rounded-lg object-cover">
                @else
                    <div class="w-12 h-12 rounded-lg bg-[#174455] flex items-center justify-center">
                        <i class="fas fa-tshirt text-white text-xl"></i>
                    </div>
                @endif
                <div>
                    <p class="font-medium text-gray-900">{{ $provider->business_name }}</p>
                    <div class="flex items-center">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($provider->rating))
                                    <i class="fas fa-star text-yellow-400 text-xs"></i>
                                @else
                                    <i class="far fa-star text-gray-300 text-xs"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-xs text-gray-500 ml-2">({{ $provider->total_orders ?? 0 }} orders)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Items Selection --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Service Mode Selection --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">1. Select Service Mode</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:border-[#174455] transition-colors">
                        <input type="radio" name="service_mode" value="NORMAL" class="mt-1 mr-3" checked>
                        <div class="flex-1">
                            <span class="font-medium text-gray-900">Normal Service</span>
                            <p class="text-sm text-gray-500 mt-1">
                                Standard turnaround: {{ $provider->laundryConfig->normal_turnaround_hours ?? 120 }} hours
                            </p>
                            <span class="inline-block mt-2 text-xs px-2 py-1 bg-blue-100 text-blue-800 rounded-full">
                                No surcharge
                            </span>
                        </div>
                    </label>
                    
                    <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:border-[#174455] transition-colors">
                        <input type="radio" name="service_mode" value="RUSH" class="mt-1 mr-3">
                        <div class="flex-1">
                            <span class="font-medium text-gray-900">Rush Service</span>
                            <p class="text-sm text-gray-500 mt-1">
                                Fast turnaround: {{ $provider->laundryConfig->rush_turnaround_hours ?? 48 }} hours
                            </p>
                            <span class="inline-block mt-2 text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">
                                +30% surcharge
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Items Selection --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">2. Select Items</h3>
                
                @php
                    $groupedItems = $items->groupBy('item_type');
                    $itemTypes = [
                        'CLOTHING' => 'Clothing',
                        'BEDDING' => 'Bedding',
                        'CURTAIN' => 'Curtain',
                        'OTHER' => 'Other'
                    ];
                @endphp

                @foreach($groupedItems as $type => $typeItems)
                <div class="mb-6 last:mb-0">
                    <h4 class="font-medium text-gray-700 mb-3 flex items-center">
                        @if($type == 'CLOTHING')
                            <i class="fas fa-tshirt text-[#174455] mr-2"></i>
                        @elseif($type == 'BEDDING')
                            <i class="fas fa-bed text-[#174455] mr-2"></i>
                        @elseif($type == 'CURTAIN')
                            <i class="fas fa-window text-[#174455] mr-2"></i>
                        @else
                            <i class="fas fa-tag text-[#174455] mr-2"></i>
                        @endif
                        {{ $itemTypes[$type] ?? $type }}
                    </h4>
                    
                    <div class="space-y-3">
                        @foreach($typeItems as $item)
                        <div class="item-card flex items-center justify-between p-4 border rounded-lg">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h5 class="font-medium text-gray-900">{{ $item->item_name }}</h5>
                                    @if(!$item->is_active)
                                        <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">Unavailable</span>
                                    @endif
                                </div>
                                @if($item->description)
                                    <p class="text-sm text-gray-500 mt-1">{{ $item->description }}</p>
                                @endif
                                <div class="flex items-center gap-4 mt-2">
                                    <span class="text-sm">
                                        <span class="text-gray-500">Base:</span>
                                        <span class="font-medium">৳{{ number_format($item->base_price, 2) }}</span>
                                    </span>
                                    @if($item->rush_surcharge_percent > 0)
                                    <span class="text-sm text-orange-600">
                                        Rush: +{{ $item->rush_surcharge_percent }}%
                                    </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <button type="button" 
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                                        onclick="decrementQuantity({{ $item->id }})">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                
                                <input type="number" 
                                       id="quantity-{{ $item->id }}"
                                       class="quantity-input w-16 text-center border-gray-300 rounded-lg focus:border-[#174455] focus:ring-[#174455]"
                                       value="0"
                                       min="0"
                                       max="99"
                                       data-item-id="{{ $item->id }}"
                                       data-price="{{ $item->base_price }}"
                                       data-rush="{{ $item->rush_surcharge_percent }}"
                                       {{ !$item->is_active ? 'disabled' : '' }}
                                       onchange="updateSummary()">
                                
                                <button type="button" 
                                        class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 hover:bg-gray-200 transition-colors"
                                        onclick="incrementQuantity({{ $item->id }})">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Right Column - Location & Summary --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Pickup Location --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">3. Pickup Location</h3>
                
                {{-- Saved Addresses Dropdown --}}
                @if($addresses && $addresses->count() > 0)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Select Saved Address</label>
                    <select id="saved-address" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="">-- Select an address --</option>
                        @foreach($addresses as $address)
                        <option value="{{ $address->id }}" 
                                data-line1="{{ $address->address_line1 }}"
                                data-line2="{{ $address->address_line2 }}"
                                data-city="{{ $address->city }}"
                                data-state="{{ $address->state }}"
                                data-postal="{{ $address->postal_code }}"
                                data-country="{{ $address->country }}"
                                data-lat="{{ $address->latitude }}"
                                data-lng="{{ $address->longitude }}"
                                {{ $address->is_default ? 'selected' : '' }}>
                            {{ $address->address_line1 }}, {{ $address->city }} {{ $address->is_default ? '(Default)' : '' }}
                        </option>
                        @endforeach
                        <option value="new">+ Add New Address</option>
                    </select>
                </div>
                @endif

                {{-- Map for location selection --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Or select on map</label>
                    <div id="location-map"></div>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-info-circle mr-1"></i>
                        Click on the map to set pickup location, or drag the marker
                    </p>
                </div>

                {{-- Selected Location Details --}}
                <div class="bg-gray-50 p-4 rounded-lg space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700">Selected Location</span>
                        <button type="button" onclick="openSaveAddressModal()" 
                                class="text-xs bg-[#174455] text-white px-3 py-1 rounded-lg hover:bg-[#1f556b]">
                            <i class="fas fa-save mr-1"></i> Save Address
                        </button>
                    </div>
                    
                    <div id="selected-location-details">
                        <p id="selected-address" class="text-sm text-gray-600">Click on map to set location</p>
                        <div class="grid grid-cols-2 gap-2 mt-2 text-xs">
                            <div>
                                <span class="text-gray-500">Latitude:</span>
                                <span id="selected-lat" class="font-medium ml-1">-</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Longitude:</span>
                                <span id="selected-lng" class="font-medium ml-1">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Pickup Time --}}
                <div class="mt-4 grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Date</label>
                        <input type="date" id="pickup_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Time</label>
                        <input type="time" id="pickup_time" 
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                </div>

                {{-- Special Instructions --}}
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions</label>
                    <textarea id="instructions" rows="2" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                              placeholder="Any special instructions for pickup..."></textarea>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Order Summary</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium" id="summary-subtotal">MMK 0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Rush Surcharge</span>
                        <span class="font-medium" id="summary-surcharge">MMK 0.00</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Pickup Fee</span>
                        <span class="font-medium" id="summary-pickup-fee">MMK {{ number_format($provider->laundryConfig->pickup_fee ?? 0, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Commission</span>
                        <span class="font-medium" id="summary-commission">MMK 0.00</span>
                    </div>
                    <div class="border-t pt-3 mt-3">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-xl text-[#174455]" id="summary-total">MMK 0.00</span>
                        </div>
                    </div>
                </div>

                <button onclick="placeOrder()" 
                        class="mt-6 w-full bg-[#174455] text-white py-3 rounded-lg hover:bg-[#1f556b] transition-colors font-medium">
                    <i class="fas fa-check mr-2"></i> Place Order
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Save New Address Modal --}}
<div id="saveAddressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#174455]" id="modal-title">Save New Address</h3>
            <button onclick="closeSaveAddressModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="saveAddressForm">
            @csrf
            <input type="hidden" name="_method" id="method-field" value="POST">
            <input type="hidden" id="address_id" name="address_id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                    <select name="address_type" id="modal_address_type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="HOME">Home</option>
                        <option value="WORK">Work</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" id="modal_country" value="Bangladesh"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                <input type="text" name="address_line1" id="modal_address_line1" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                       placeholder="Street address, P.O. box">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" id="modal_address_line2"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                       placeholder="Apartment, suite, unit, building, floor">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" id="modal_city" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="state" id="modal_state" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code" id="modal_postal_code"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
            </div>
            
            <input type="hidden" name="latitude" id="modal_latitude">
            <input type="hidden" name="longitude" id="modal_longitude">
            
            <div class="flex items-center mb-4">
                <input type="checkbox" name="is_default" id="modal_is_default" value="1"
                       class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]">
                <label for="modal_is_default" class="ml-2 text-sm text-gray-700">Set as default address</label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeSaveAddressModal()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Cancel
                </button>
                <button type="submit" id="save-address-btn"
                        class="px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#1f556b]">
                    Save Address
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    let map, marker;
    let selectedLat = null;
    let selectedLng = null;
    let selectedAddress = '';
    let lastGeocodeData = null;
    
    // Provider data
    const providerId = {{ $provider->id }};
    const pickupFee = {{ $provider->laundryConfig->pickup_fee ?? 0 }};
    const commissionRate = 10; // Default commission rate
    
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        setupEventListeners();
        updateSummary();
        
        // Set default date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        document.getElementById('pickup_date').valueAsDate = tomorrow;
    });
    
    function initMap() {
        const defaultLat = {{ $addresses->first()->latitude ?? 23.8103 }};
        const defaultLng = {{ $addresses->first()->longitude ?? 90.4125 }};
        
        map = L.map('location-map').setView([defaultLat, defaultLng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
        
        marker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateSelectedLocation(pos.lat, pos.lng);
        });
        
        map.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateSelectedLocation(e.latlng.lat, e.latlng.lng);
        });
        
        // Try to get user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const userLat = position.coords.latitude;
                const userLng = position.coords.longitude;
                map.setView([userLat, userLng], 14);
                marker.setLatLng([userLat, userLng]);
                updateSelectedLocation(userLat, userLng);
            }, function(error) {
                console.log('Geolocation error:', error);
            });
        }
    }
    
    function updateSelectedLocation(lat, lng) {
        selectedLat = lat;
        selectedLng = lng;
        
        document.getElementById('selected-lat').textContent = lat.toFixed(6);
        document.getElementById('selected-lng').textContent = lng.toFixed(6);
        
        // Reverse geocode to get address
        fetch('{{ route("profile.reverse-geocode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ lat: lat, lng: lng })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                selectedAddress = data.address;
                document.getElementById('selected-address').textContent = data.address.substring(0, 50) + '...';
                
                // Store the full geocode data for later use
                lastGeocodeData = data;
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function setupEventListeners() {
        // Service mode change
        document.querySelectorAll('input[name="service_mode"]').forEach(radio => {
            radio.addEventListener('change', updateSummary);
        });
        
        // Saved address selection
        const savedAddress = document.getElementById('saved-address');
        if (savedAddress) {
            savedAddress.addEventListener('change', function(e) {
                const selected = e.target.selectedOptions[0];
                if (selected.value === 'new') {
                    openSaveAddressModal();
                    e.target.value = '';
                } else if (selected.value) {
                    const lat = parseFloat(selected.dataset.lat);
                    const lng = parseFloat(selected.dataset.lng);
                    
                    if (lat && lng) {
                        map.setView([lat, lng], 15);
                        marker.setLatLng([lat, lng]);
                        updateSelectedLocation(lat, lng);
                    }
                    
                    // Construct address from selected data
                    const addressLine = selected.dataset.line1;
                    const city = selected.dataset.city;
                    document.getElementById('selected-address').textContent = addressLine + ', ' + city;
                }
            });
        }
    }
    
    function incrementQuantity(itemId) {
        const input = document.getElementById(`quantity-${itemId}`);
        input.value = parseInt(input.value) + 1;
        updateSummary();
    }
    
    function decrementQuantity(itemId) {
        const input = document.getElementById(`quantity-${itemId}`);
        const currentValue = parseInt(input.value);
        if (currentValue > 0) {
            input.value = currentValue - 1;
            updateSummary();
        }
    }
    
    function updateSummary() {
        let subtotal = 0;
        let rushSurcharge = 0;
        const serviceMode = document.querySelector('input[name="service_mode"]:checked').value;
        
        document.querySelectorAll('[id^="quantity-"]').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const price = parseFloat(input.dataset.price);
                const rushPercent = parseFloat(input.dataset.rush);
                
                subtotal += price * qty;
                
                if (serviceMode === 'RUSH') {
                    rushSurcharge += (price * qty * rushPercent / 100);
                }
            }
        });
        
        const commission = (subtotal + rushSurcharge + pickupFee) * (commissionRate / 100);
        const total = subtotal + rushSurcharge + pickupFee + commission;
        
        document.getElementById('summary-subtotal').textContent = '৳' + subtotal.toFixed(2);
        document.getElementById('summary-surcharge').textContent = '৳' + rushSurcharge.toFixed(2);
        document.getElementById('summary-commission').textContent = '৳' + commission.toFixed(2);
        document.getElementById('summary-total').textContent = '৳' + total.toFixed(2);
    }
    
    function openSaveAddressModal() {
        // Set coordinates
        document.getElementById('modal_latitude').value = selectedLat || '';
        document.getElementById('modal_longitude').value = selectedLng || '';
        
        // Parse and fill address fields from the selected location
        if (lastGeocodeData) {
            fillAddressFromGeocode(lastGeocodeData);
        } else if (selectedAddress) {
            parseAndFillAddress(selectedAddress);
        }
        
        document.getElementById('saveAddressModal').classList.remove('hidden');
    }
    
    function fillAddressFromGeocode(data) {
        // Address line 1 (first part of display name)
        if (data.address) {
            const parts = data.address.split(',');
            document.getElementById('modal_address_line1').value = parts[0].trim();
        }
        
        // City
        if (data.city) {
            document.getElementById('modal_city').value = data.city;
        }
        
        // State
        if (data.state) {
            document.getElementById('modal_state').value = data.state;
        }
        
        // Country
        if (data.country) {
            document.getElementById('modal_country').value = data.country;
        }
        
        // Postal code
        if (data.postal_code) {
            document.getElementById('modal_postal_code').value = data.postal_code;
        }
    }
    
    function parseAndFillAddress(fullAddress) {
        if (!fullAddress) return;
        
        // Split address into parts
        const parts = fullAddress.split(',');
        
        if (parts.length >= 1) {
            document.getElementById('modal_address_line1').value = parts[0].trim();
        }
        
        if (parts.length >= 2) {
            document.getElementById('modal_city').value = parts[1].trim();
        }
        
        if (parts.length >= 3) {
            document.getElementById('modal_state').value = parts[2].trim();
        }
        
        if (parts.length >= 4) {
            document.getElementById('modal_country').value = parts[parts.length - 1].trim();
        }
        
        // Try to extract postal code if present
        const postalCodeMatch = fullAddress.match(/\b\d{4,6}\b/);
        if (postalCodeMatch) {
            document.getElementById('modal_postal_code').value = postalCodeMatch[0];
        }
    }
    
    function closeSaveAddressModal() {
        document.getElementById('saveAddressModal').classList.add('hidden');
        document.getElementById('saveAddressForm').reset();
    }
    
    document.getElementById('saveAddressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        // Show loading state
        const submitBtn = document.getElementById('save-address-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        
        fetch('{{ route("profile.address.add") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Address saved successfully!');
                closeSaveAddressModal();
                location.reload(); // Refresh to show new address in dropdown
            } else {
                alert('Error saving address: ' + (data.message || 'Unknown error'));
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving address');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    function placeOrder() {
        // Collect items
        const items = [];
        document.querySelectorAll('[id^="quantity-"]').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                items.push({
                    id: input.dataset.itemId,
                    quantity: qty
                });
            }
        });
        
        if (items.length === 0) {
            alert('Please select at least one item');
            return;
        }
        
        if (!selectedLat || !selectedLng) {
            alert('Please select a pickup location on the map');
            return;
        }
        
        const pickupDate = document.getElementById('pickup_date').value;
        const pickupTime = document.getElementById('pickup_time').value;
        
        if (!pickupDate || !pickupTime) {
            alert('Please select pickup date and time');
            return;
        }
        
        const formData = {
            provider_id: providerId,
            service_mode: document.querySelector('input[name="service_mode"]:checked').value,
            pickup_latitude: selectedLat,
            pickup_longitude: selectedLng,
            pickup_address: selectedAddress,
            pickup_date: pickupDate,
            pickup_time: pickupTime,
            pickup_instructions: document.getElementById('instructions').value,
            items: items
        };
        
        // Show loading state
        const btn = event.target;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Placing Order...';
        
        fetch('{{ route("laundry.place-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = `/laundry/order/${data.order_id}/confirmation`;
            } else {
                alert(data.message || 'Error placing order');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error placing order');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('saveAddressModal');
        if (event.target == modal) {
            closeSaveAddressModal();
        }
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeSaveAddressModal();
        }
    });
</script>
@endpush
@endsection