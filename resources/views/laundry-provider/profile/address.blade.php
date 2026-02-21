@extends('laundry-provider.layouts.provider')

@section('title', 'Manage Addresses')
@section('subtitle', '')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    #business-map, #address-map { height: 300px; width: 100%; border-radius: 8px; }
    .leaflet-container { z-index: 1; }
    .modal { z-index: 9999; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    {{-- Header Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-semibold text-[#174455]">Manage Addresses</h2>
                <p class="text-gray-600">Add and manage your addresses with exact map location</p>
            </div>
            <button onclick="openAddressModal()" 
                    class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors text-sm">
                <i class="fas fa-plus mr-2"></i> Add New Address
            </button>
        </div>
    </div>

    {{-- Business Location Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-[#174455] mb-4">Business Location</h3>
        <p class="text-sm text-gray-600 mb-4">Set your business location on the map. This is where customers will find you.</p>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Map --}}
            <div>
                <div id="business-map" style="height: 300px; width: 100%;" class="rounded-lg border border-gray-300"></div>
            </div>
            
            {{-- Business Location Details --}}
            <div>
                <form id="business-location-form" onsubmit="updateBusinessLocation(event)">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                            <input type="text" id="business-address" name="address" 
                                   value="{{ $businessLocation['address'] }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                                   placeholder="Click on map to set location">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                            <input type="text" id="business-city" name="city" 
                                   value="{{ $businessLocation['city'] }}"
                                   class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        </div>
                        
                        <input type="hidden" id="business-lat" name="latitude" value="{{ $businessLocation['latitude'] }}">
                        <input type="hidden" id="business-lng" name="longitude" value="{{ $businessLocation['longitude'] }}">
                        
                        <button type="submit" 
                                class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                            <i class="fas fa-save mr-2"></i> Save Business Location
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Saved Addresses Card --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-[#174455] mb-4">Saved Addresses</h3>
        
        @if($addresses && $addresses->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($addresses as $address)
                    <div class="border rounded-lg p-4 {{ $address->is_default ? 'border-[#174455] bg-blue-50' : 'border-gray-200' }}">
                        <div class="flex justify-between items-start">
                            <div class="flex items-start gap-3">
                                <div class="mt-1">
                                    @if($address->address_type == 'HOME')
                                        <i class="fas fa-home text-[#174455] text-xl"></i>
                                    @elseif($address->address_type == 'WORK')
                                        <i class="fas fa-briefcase text-[#174455] text-xl"></i>
                                    @else
                                        <i class="fas fa-map-marker-alt text-[#174455] text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold">{{ $address->address_type }}</span>
                                        @if($address->is_default)
                                            <span class="px-2 py-0.5 bg-green-100 text-green-800 rounded-full text-xs">Default</span>
                                        @endif
                                    </div>
                                    <p class="text-sm">{{ $address->address_line1 }}</p>
                                    @if($address->address_line2)
                                        <p class="text-sm">{{ $address->address_line2 }}</p>
                                    @endif
                                    <p class="text-sm">{{ $address->city }}, {{ $address->state }} {{ $address->postal_code }}</p>
                                    <p class="text-sm">{{ $address->country }}</p>
                                </div>
                            </div>
                            
                            <div class="flex flex-col gap-1">
                                @if(!$address->is_default)
                                    <button onclick="setDefaultAddress({{ $address->id }})" 
                                            class="text-green-600 hover:text-green-800 text-xs">
                                        Set Default
                                    </button>
                                @endif
                                <button onclick="editAddress({{ $address->id }})" 
                                        class="text-blue-600 hover:text-blue-800 text-xs">
                                    Edit
                                </button>
                                <button onclick="deleteAddress({{ $address->id }})" 
                                        class="text-red-600 hover:text-red-800 text-xs">
                                    Delete
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-map-marker-alt text-4xl text-gray-300 mb-3"></i>
                <p>No addresses saved yet</p>
                <p class="text-sm mt-1">Click "Add New Address" to add your first address</p>
            </div>
        @endif
    </div>
</div>

{{-- Address Modal --}}
<div id="addressModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-[#174455]" id="modal-title">Add New Address</h3>
            <button onclick="closeAddressModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="addressForm" method="POST">
            @csrf
            <input type="hidden" name="_method" id="method-field" value="POST">
            <input type="hidden" id="address_id" name="address_id" value="">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Type</label>
                    <select name="address_type" id="address_type" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="HOME">Home</option>
                        <option value="WORK">Work</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Country</label>
                    <input type="text" name="country" id="country" value="Bangladesh"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1</label>
                <input type="text" name="address_line1" id="address_line1" required
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                       placeholder="Street address, P.O. box">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2 (Optional)</label>
                <input type="text" name="address_line2" id="address_line2"
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]"
                       placeholder="Apartment, suite, unit, building, floor">
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                    <input type="text" name="city" id="city" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">State</label>
                    <input type="text" name="state" id="state" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                    <input type="text" name="postal_code" id="postal_code"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                </div>
            </div>
            
            {{-- Map for address selection --}}
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Location on Map</label>
                <div id="address-map" style="height: 250px; width: 100%;" class="rounded-lg border border-gray-300 mb-2"></div>
                <p class="text-xs text-gray-500">Click on the map to set exact location, or search below</p>
            </div>
            
            <div class="mb-4">
                <div class="flex gap-2">
                    <input type="text" id="location-search" placeholder="Search for a location..." 
                           class="flex-1 rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    <button type="button" onclick="searchLocation()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
            
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">
            
            <div class="flex items-center mb-4">
                <input type="checkbox" name="is_default" id="is_default" value="1"
                       class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]">
                <label for="is_default" class="ml-2 text-sm text-gray-700">Set as default address</label>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddressModal()" 
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
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
    // Copy ALL your existing JavaScript here exactly as it was
    // (All your map functions, form handlers, etc. - unchanged)
    let businessMap, addressMap, businessMarker, addressMarker;
    let currentAddressId = null;
    
    document.addEventListener('DOMContentLoaded', function() {
        initBusinessMap();
        initAddressMap();
    });
    
    function initBusinessMap() {
        const lat = parseFloat({{ $businessLocation['latitude'] }});
        const lng = parseFloat({{ $businessLocation['longitude'] }});
        
        businessMap = L.map('business-map').setView([lat, lng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(businessMap);
        
        businessMarker = L.marker([lat, lng], { draggable: true }).addTo(businessMap);
        
        businessMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            updateBusinessLocationFromCoords(pos.lat, pos.lng);
        });
        
        businessMap.on('click', function(e) {
            businessMarker.setLatLng(e.latlng);
            updateBusinessLocationFromCoords(e.latlng.lat, e.latlng.lng);
        });
    }
    
    function initAddressMap(lat = 23.8103, lng = 90.4125) {
        if (addressMap) {
            addressMap.remove();
        }
        
        addressMap = L.map('address-map').setView([lat, lng], 13);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(addressMap);
        
        addressMarker = L.marker([lat, lng], { draggable: true }).addTo(addressMap);
        
        addressMarker.on('dragend', function(e) {
            const pos = e.target.getLatLng();
            document.getElementById('latitude').value = pos.lat;
            document.getElementById('longitude').value = pos.lng;
            reverseGeocode(pos.lat, pos.lng);
        });
        
        addressMap.on('click', function(e) {
            addressMarker.setLatLng(e.latlng);
            document.getElementById('latitude').value = e.latlng.lat;
            document.getElementById('longitude').value = e.latlng.lng;
            reverseGeocode(e.latlng.lat, e.latlng.lng);
        });
    }
    
    function updateBusinessLocationFromCoords(lat, lng) {
        document.getElementById('business-lat').value = lat;
        document.getElementById('business-lng').value = lng;
        
        fetch('{{ route("laundry-provider.profile.reverse-geocode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lat: lat, lng: lng })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('business-address').value = data.address;
                document.getElementById('business-city').value = data.city;
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function updateBusinessLocation(event) {
        event.preventDefault();
        
        const formData = new FormData(document.getElementById('business-location-form'));
        
        fetch('{{ route("laundry-provider.profile.business.location") }}', {
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
                alert('Business location updated successfully!');
            } else {
                alert(data.message || 'Error updating business location');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating business location');
        });
    }
    
    function searchLocation() {
        const query = document.getElementById('location-search').value;
        if (!query) return;
        
        fetch('{{ route("laundry-provider.profile.geocode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ address: query })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const lat = parseFloat(data.lat);
                const lng = parseFloat(data.lon);
                
                addressMap.setView([lat, lng], 15);
                addressMarker.setLatLng([lat, lng]);
                
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lng;
                document.getElementById('address_line1').value = data.display_name.split(',')[0];
            } else {
                alert('Location not found');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error searching location');
        });
    }
    
    function reverseGeocode(lat, lng) {
        fetch('{{ route("laundry-provider.profile.reverse-geocode") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ lat: lat, lng: lng })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('address_line1').value = data.address.split(',')[0];
                document.getElementById('city').value = data.city;
                document.getElementById('state').value = data.state;
                document.getElementById('country').value = data.country;
                document.getElementById('postal_code').value = data.postal_code || '';
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    function openAddressModal() {
        currentAddressId = null;
        document.getElementById('modal-title').textContent = 'Add New Address';
        document.getElementById('addressForm').reset();
        document.getElementById('address_id').value = '';
        document.getElementById('method-field').value = 'POST';
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
        document.getElementById('addressModal').classList.remove('hidden');
        
        setTimeout(() => {
            initAddressMap();
        }, 100);
    }
    
    function closeAddressModal() {
        document.getElementById('addressModal').classList.add('hidden');
    }
    
    function editAddress(id) {
        currentAddressId = id;
        document.getElementById('modal-title').textContent = 'Edit Address';
        document.getElementById('method-field').value = 'PUT';
        
        fetch(`/laundry-provider/profile/address/${id}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                const addr = data.address;
                document.getElementById('address_id').value = addr.id;
                document.getElementById('address_type').value = addr.address_type;
                document.getElementById('address_line1').value = addr.address_line1;
                document.getElementById('address_line2').value = addr.address_line2 || '';
                document.getElementById('city').value = addr.city;
                document.getElementById('state').value = addr.state;
                document.getElementById('postal_code').value = addr.postal_code || '';
                document.getElementById('country').value = addr.country;
                document.getElementById('latitude').value = addr.latitude || '';
                document.getElementById('longitude').value = addr.longitude || '';
                document.getElementById('is_default').checked = addr.is_default;
                
                document.getElementById('addressModal').classList.remove('hidden');
                
                setTimeout(() => {
                    if (addr.latitude && addr.longitude) {
                        initAddressMap(parseFloat(addr.latitude), parseFloat(addr.longitude));
                    } else {
                        initAddressMap();
                    }
                }, 100);
            } else {
                alert('Error loading address');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading address details');
        });
    }
    
    function deleteAddress(id) {
        if (confirm('Are you sure you want to delete this address?')) {
            fetch(`/laundry-provider/profile/address/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error deleting address');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error deleting address');
            });
        }
    }
    
    function setDefaultAddress(id) {
        fetch(`/laundry-provider/profile/address/${id}/default`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Error setting default address');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error setting default address');
        });
    }
    
    document.getElementById('addressForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const addressId = document.getElementById('address_id').value;
        
        let url = '{{ route("laundry-provider.profile.address.add") }}';
        
        if (addressId) {
            url = `{{ url('laundry-provider/profile/address') }}/${addressId}`;
        }
        
        const submitBtn = document.getElementById('save-address-btn');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                closeAddressModal();
                location.reload();
            } else {
                alert(data.message || 'Error saving address');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving address. Please try again.');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
    
    window.onclick = function(event) {
        const modal = document.getElementById('addressModal');
        if (event.target == modal) {
            closeAddressModal();
        }
    }
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAddressModal();
        }
    });
</script>
@endpush