@extends('dashboard')

@section('title', $provider->business_name)
@section('subtitle', 'Laundry Service Provider')

@section('content')
<div class="space-y-6">
    {{-- Provider Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="flex-shrink-0">
                    @if($provider->avatar_url)
                        <img src="{{ Storage::url($provider->avatar_url) }}" 
                             alt="{{ $provider->business_name }}"
                             class="w-20 h-20 rounded-lg object-cover">
                    @else
                        <div class="w-20 h-20 rounded-lg bg-[#174455] flex items-center justify-center">
                            <i class="fas fa-tshirt text-white text-3xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-[#174455]">{{ $provider->business_name }}</h2>
                    <div class="flex items-center mt-2">
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= round($provider->rating))
                                    <i class="fas fa-star text-yellow-400"></i>
                                @else
                                    <i class="far fa-star text-gray-300"></i>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600 ml-2">
                            {{ number_format($provider->rating, 1) }} ({{ $provider->total_orders }} orders)
                        </span>
                    </div>
                </div>
            </div>
            
            @if($userAddress && $provider->latitude && $provider->longitude)
                @php
                    $distance = $this->calculateDistance(
                        $userAddress->latitude, 
                        $userAddress->longitude,
                        $provider->latitude, 
                        $provider->longitude
                    );
                @endphp
                <div class="text-right">
                    <span class="text-sm text-gray-500">Distance</span>
                    <p class="text-xl font-bold text-[#174455]">{{ number_format($distance, 1) }} km</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Provider Info Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Provider Details --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Contact Card --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Contact Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-phone text-[#174455] w-6"></i>
                        <span>{{ $provider->contact_phone }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-envelope text-[#174455] w-6"></i>
                        <span>{{ $provider->contact_email }}</span>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-map-marker-alt text-[#174455] w-6 mt-1"></i>
                        <span>{{ $provider->address }}</span>
                    </div>
                </div>
            </div>

            {{-- Business Hours Card --}}
            @if($provider->laundryServiceConfig)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Business Hours</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Times</span>
                        <span class="font-medium">
                            {{ \Carbon\Carbon::parse($provider->laundryServiceConfig->pickup_start_time)->format('g:i A') }} - 
                            {{ \Carbon\Carbon::parse($provider->laundryServiceConfig->pickup_end_time)->format('g:i A') }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Turnaround (Normal)</span>
                        <span class="font-medium">{{ $provider->laundryServiceConfig->normal_turnaround_hours }} hours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Turnaround (Rush)</span>
                        <span class="font-medium">{{ $provider->laundryServiceConfig->rush_turnaround_hours }} hours</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Fee</span>
                        <span class="font-medium">৳{{ number_format($provider->laundryServiceConfig->pickup_fee, 2) }}</span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Description Card --}}
            @if($provider->description)
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">About</h3>
                <p class="text-gray-600">{{ $provider->description }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column - Items and Order Form --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Service Mode Selection --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Select Service Mode</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:border-[#174455] transition-colors">
                        <input type="radio" name="service_mode" value="NORMAL" class="mt-1 mr-3" checked>
                        <div class="flex-1">
                            <span class="font-medium text-gray-900">Normal Service</span>
                            <p class="text-sm text-gray-500 mt-1">
                                Standard turnaround: {{ $provider->laundryServiceConfig->normal_turnaround_hours ?? 120 }} hours
                            </p>
                        </div>
                    </label>
                    
                    <label class="relative flex items-start p-4 border rounded-lg cursor-pointer hover:border-[#174455] transition-colors">
                        <input type="radio" name="service_mode" value="RUSH" class="mt-1 mr-3">
                        <div class="flex-1">
                            <span class="font-medium text-gray-900">Rush Service</span>
                            <p class="text-sm text-gray-500 mt-1">
                                Fast turnaround: {{ $provider->laundryServiceConfig->rush_turnaround_hours ?? 48 }} hours
                            </p>
                            <span class="inline-block mt-2 text-xs px-2 py-1 bg-orange-100 text-orange-800 rounded-full">
                                +{{ $provider->items->first()->rush_surcharge_percent ?? 30 }}% surcharge
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            {{-- Items Selection --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Select Items</h3>
                
                <div class="space-y-4">
                    @foreach($provider->items as $item)
                    <div class="flex items-center justify-between p-4 border rounded-lg hover:bg-gray-50">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <h4 class="font-medium text-gray-900">{{ $item->item_name }}</h4>
                                @if(!$item->is_active)
                                    <span class="ml-2 px-2 py-0.5 bg-gray-100 text-gray-600 rounded-full text-xs">Unavailable</span>
                                @endif
                            </div>
                            @if($item->description)
                                <p class="text-sm text-gray-500 mt-1">{{ $item->description }}</p>
                            @endif
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <div class="text-right">
                                <p class="text-sm text-gray-500">Base Price</p>
                                <p class="font-medium">MMK {{ number_format($item->base_price, 2) }}</p>
                            </div>
                            
                            <div class="w-24">
                                <input type="number" 
                                       class="item-quantity w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                                       data-item-id="{{ $item->id }}"
                                       data-price="{{ $item->base_price }}"
                                       data-rush="{{ $item->rush_surcharge_percent }}"
                                       min="0" max="99" value="0"
                                       {{ !$item->is_active ? 'disabled' : '' }}>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pickup Details --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Pickup Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pickup Address</label>
                    <select id="address_id" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        @foreach($userAddresses as $address)
                            <option value="{{ $address->id }}" {{ $address->is_default ? 'selected' : '' }}>
                                {{ $address->address_line1 }}, {{ $address->city }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Special Instructions (Optional)</label>
                    <textarea id="instructions" rows="2" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                              placeholder="Any special instructions for pickup..."></textarea>
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-[#174455] mb-4">Order Summary</h3>
                
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium" id="subtotal">MMK 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Rush Surcharge</span>
                        <span class="font-medium" id="surcharge">MMK 0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Pickup Fee</span>
                        <span class="font-medium" id="pickup_fee">MMK {{ number_format($provider->laundryServiceConfig->pickup_fee ?? 0, 2) }}</span>
                    </div>
                    <div class="border-t pt-3">
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-xl text-[#174455]" id="total">MMK 0.00</span>
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

@push('scripts')
<script>
    const items = [];
    let selectedMode = 'NORMAL';
    
    document.querySelectorAll('input[name="service_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedMode = this.value;
            updateSummary();
        });
    });
    
    document.querySelectorAll('.item-quantity').forEach(input => {
        input.addEventListener('change', updateSummary);
    });
    
    function updateSummary() {
        let subtotal = 0;
        let rushSurcharge = 0;
        
        document.querySelectorAll('.item-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const price = parseFloat(input.dataset.price);
                const rushPercent = parseFloat(input.dataset.rush);
                
                subtotal += price * qty;
                
                if (selectedMode === 'RUSH') {
                    rushSurcharge += (price * qty * rushPercent / 100);
                }
            }
        });
        
        const pickupFee = parseFloat('{{ $provider->laundryServiceConfig->pickup_fee ?? 0 }}');
        const total = subtotal + rushSurcharge + pickupFee;
        
        document.getElementById('subtotal').textContent = '৳' + subtotal.toFixed(2);
        document.getElementById('surcharge').textContent = '৳' + rushSurcharge.toFixed(2);
        document.getElementById('total').textContent = '৳' + total.toFixed(2);
    }
    
    function placeOrder() {
        const items = [];
        document.querySelectorAll('.item-quantity').forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                items.push({
                    item_id: input.dataset.itemId,
                    quantity: qty
                });
            }
        });
        
        if (items.length === 0) {
            alert('Please select at least one item');
            return;
        }
        
        const pickupDate = document.getElementById('pickup_date').value;
        const pickupTime = document.getElementById('pickup_time').value;
        const addressId = document.getElementById('address_id').value;
        const instructions = document.getElementById('instructions').value;
        
        if (!pickupDate || !pickupTime) {
            alert('Please select pickup date and time');
            return;
        }
        
        const formData = {
            service_mode: selectedMode,
            items: items,
            pickup_date: pickupDate,
            pickup_time: pickupTime,
            address_id: addressId,
            instructions: instructions,
            provider_id: {{ $provider->id }}
        };
        
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
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Error placing order');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error placing order');
        });
    }
</script>
@endpush
@endsection