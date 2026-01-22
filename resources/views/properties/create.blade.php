@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900">Add New Property</h1>
        <p class="mt-2 text-gray-600">Fill out the form below to list your property on RMS.</p>
    </div>

    <!-- Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('properties.store') }}" id="propertyForm">
            @csrf
            
            <div class="p-6 space-y-8">
                <!-- Property Type Selection -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Property Type</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ propertyType: '{{ old('type', 'HOSTEL') }}' }">
                        <!-- Hostel Option -->
                        <div class="relative">
                            <input type="radio" id="type_hostel" name="type" value="HOSTEL" 
                                   x-model="propertyType" class="sr-only" required>
                            <label for="type_hostel" 
                                   class="cursor-pointer flex flex-col p-6 border-2 rounded-lg transition-all duration-200"
                                   :class="propertyType === 'HOSTEL' ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-bed text-indigo-600 text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">Hostel</h4>
                                        <p class="text-sm text-gray-500">Room-based rental with shared facilities</p>
                                    </div>
                                </div>
                                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Rent individual rooms
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Gender policy enforced
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        5% commission rate
                                    </li>
                                </ul>
                            </label>
                        </div>

                        <!-- Apartment Option -->
                        <div class="relative">
                            <input type="radio" id="type_apartment" name="type" value="APARTMENT" 
                                   x-model="propertyType" class="sr-only">
                            <label for="type_apartment" 
                                   class="cursor-pointer flex flex-col p-6 border-2 rounded-lg transition-all duration-200"
                                   :class="propertyType === 'APARTMENT' ? 'border-green-500 bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-home text-green-600 text-xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-medium text-gray-900">Apartment</h4>
                                        <p class="text-sm text-gray-500">Whole-unit rental</p>
                                    </div>
                                </div>
                                <ul class="mt-4 space-y-2 text-sm text-gray-600">
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        Rent entire unit
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        No gender restriction
                                    </li>
                                    <li class="flex items-center">
                                        <i class="fas fa-check text-green-500 mr-2"></i>
                                        3% commission rate
                                    </li>
                                </ul>
                            </label>
                        </div>
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Property Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                Property Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g., Green Valley Hostel">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <textarea id="description" name="description" rows="3" required
                                      class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="Describe your property...">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Gender Policy -->
                        <div>
                            <label for="gender_policy" class="block text-sm font-medium text-gray-700">
                                Gender Policy <span class="text-red-500">*</span>
                            </label>
                            <select id="gender_policy" name="gender_policy" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Gender Policy</option>
                                <option value="MALE_ONLY" {{ old('gender_policy') == 'MALE_ONLY' ? 'selected' : '' }}>Male Only</option>
                                <option value="FEMALE_ONLY" {{ old('gender_policy') == 'FEMALE_ONLY' ? 'selected' : '' }}>Female Only</option>
                                <option value="MIXED" {{ old('gender_policy') == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                            </select>
                            @error('gender_policy')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Base Price -->
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700">
                                Base Price (৳) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">৳</span>
                                </div>
                                <input type="number" step="0.01" id="base_price" name="base_price" 
                                       value="{{ old('base_price') }}" required min="0"
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="0.00">
                            </div>
                            @error('base_price')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission Rate -->
                        <div>
                            <label for="commission_rate" class="block text-sm font-medium text-gray-700">
                                Commission Rate (%) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.01" id="commission_rate" name="commission_rate" 
                                       value="{{ old('commission_rate', 5) }}" required min="0" max="100"
                                       class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500" id="commissionPreview">
                                Total price for customer: ৳<span id="totalPrice">0.00</span>
                            </p>
                            @error('commission_rate')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Apartment Specific Fields (Conditional) -->
                <div x-data="{ propertyType: '{{ old('type', 'HOSTEL') }}' }" x-show="propertyType === 'APARTMENT'">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Apartment Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Unit Size -->
                        <div>
                            <label for="unit_size" class="block text-sm font-medium text-gray-700">
                                Unit Size (sqft) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="unit_size" name="unit_size" 
                                       value="{{ old('unit_size') }}" min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">sqft</span>
                                </div>
                            </div>
                            @error('unit_size')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bedrooms -->
                        <div>
                            <label for="bedrooms" class="block text-sm font-medium text-gray-700">
                                Bedrooms <span class="text-red-500">*</span>
                            </label>
                            <select id="bedrooms" name="bedrooms"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select bedrooms</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('bedrooms') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'bedroom' : 'bedrooms' }}</option>
                                @endfor
                            </select>
                            @error('bedrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label for="bathrooms" class="block text-sm font-medium text-gray-700">
                                Bathrooms <span class="text-red-500">*</span>
                            </label>
                            <select id="bathrooms" name="bathrooms"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select bathrooms</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('bathrooms') == $i ? 'selected' : '' }}>{{ $i }} {{ $i == 1 ? 'bathroom' : 'bathrooms' }}</option>
                                @endfor
                            </select>
                            @error('bathrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Furnishing Status -->
                        <div>
                            <label for="furnishing_status" class="block text-sm font-medium text-gray-700">
                                Furnishing Status <span class="text-red-500">*</span>
                            </label>
                            <select id="furnishing_status" name="furnishing_status"
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select furnishing</option>
                                <option value="FURNISHED" {{ old('furnishing_status') == 'FURNISHED' ? 'selected' : '' }}>Furnished</option>
                                <option value="SEMI_FURNISHED" {{ old('furnishing_status') == 'SEMI_FURNISHED' ? 'selected' : '' }}>Semi-Furnished</option>
                                <option value="UNFURNISHED" {{ old('furnishing_status') == 'UNFURNISHED' ? 'selected' : '' }}>Unfurnished</option>
                            </select>
                            @error('furnishing_status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Minimum Stay -->
                        <div>
                            <label for="min_stay_months" class="block text-sm font-medium text-gray-700">
                                Minimum Stay <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="min_stay_months" name="min_stay_months" 
                                       value="{{ old('min_stay_months', 1) }}" min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">months</span>
                                </div>
                            </div>
                            @error('min_stay_months')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Security Deposit -->
                        <div>
                            <label for="deposit_months" class="block text-sm font-medium text-gray-700">
                                Security Deposit <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="deposit_months" name="deposit_months" 
                                       value="{{ old('deposit_months', 1) }}" min="0"
                                       class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">months rent</span>
                                </div>
                            </div>
                            @error('deposit_months')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Location Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="city" name="city" value="{{ old('city') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g., Dhaka">
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Area -->
                        <div>
                            <label for="area" class="block text-sm font-medium text-gray-700">
                                Area/Location <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="area" name="area" value="{{ old('area') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g., Dhanmondi">
                            @error('area')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700">
                                Full Address <span class="text-red-500">*</span>
                            </label>
                            <textarea id="address" name="address" rows="2" required
                                      class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                      placeholder="House no, Road no, Block, etc.">{{ old('address') }}</textarea>
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">
                                Latitude <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="number" step="any" id="latitude" name="latitude" 
                                       value="{{ old('latitude') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g., 23.8103">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Use Google Maps to find coordinates</p>
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Longitude -->
                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">
                                Longitude <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="number" step="any" id="longitude" name="longitude" 
                                       value="{{ old('longitude') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="e.g., 90.4125">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Use Google Maps to find coordinates</p>
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Amenities & Facilities</h3>
                    <div id="amenities-container">
                        <div class="space-y-3" id="amenities-list">
                            <!-- Dynamic amenities will be added here -->
                        </div>
                        <button type="button" onclick="addAmenity()" 
                                class="mt-3 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <i class="fas fa-plus mr-2"></i>
                            Add Amenity
                        </button>
                        <input type="hidden" name="amenities[]" id="amenities-input">
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('properties.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i>
                        Save Property
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for dynamic form -->
<script>
// Initialize Alpine.js for property type toggle
document.addEventListener('alpine:init', () => {
    Alpine.data('propertyForm', () => ({
        propertyType: '{{ old('type', 'HOSTEL') }}',
        init() {
            // Calculate initial total price
            this.calculateTotalPrice();
        },
        calculateTotalPrice() {
            const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
            const commissionRate = parseFloat(document.getElementById('commission_rate').value) || 0;
            const totalPrice = basePrice + (basePrice * commissionRate / 100);
            document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
        }
    }));
});

// Calculate total price on input
document.getElementById('base_price').addEventListener('input', calculateTotal);
document.getElementById('commission_rate').addEventListener('input', calculateTotal);

function calculateTotal() {
    const basePrice = parseFloat(document.getElementById('base_price').value) || 0;
    const commissionRate = parseFloat(document.getElementById('commission_rate').value) || 0;
    const totalPrice = basePrice + (basePrice * commissionRate / 100);
    document.getElementById('totalPrice').textContent = totalPrice.toFixed(2);
}

// Amenities management
let amenityCount = 0;
function addAmenity(value = '') {
    const amenitiesList = document.getElementById('amenities-list');
    const amenityId = `amenity-${amenityCount++}`;
    
    const amenityDiv = document.createElement('div');
    amenityDiv.className = 'flex items-center';
    amenityDiv.innerHTML = `
        <div class="flex-1">
            <input type="text" 
                   class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                   placeholder="e.g., WiFi, AC, Parking, etc."
                   value="${value}"
                   oninput="updateAmenities()">
        </div>
        <button type="button" 
                onclick="this.parentElement.remove(); updateAmenities();"
                class="ml-3 inline-flex items-center p-1 border border-transparent rounded-full text-red-600 hover:text-red-800 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    amenitiesList.appendChild(amenityDiv);
    updateAmenities();
}

function updateAmenities() {
    const inputs = document.querySelectorAll('#amenities-list input[type="text"]');
    const amenities = Array.from(inputs).map(input => input.value.trim()).filter(value => value !== '');
    document.getElementById('amenities-input').value = JSON.stringify(amenities);
}

// Initialize with old amenities if any
@if(old('amenities') && is_array(old('amenities')))
    @foreach(old('amenities') as $amenity)
        addAmenity('{{ $amenity }}');
    @endforeach
@else
    // Add one empty amenity by default
    addAmenity();
@endif
</script>
@endsection