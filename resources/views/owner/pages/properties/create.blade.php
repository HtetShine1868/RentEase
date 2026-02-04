@extends('owner.layout.owner-layout')

@section('title', 'Add New Property - RentEase')
@section('page-title', 'Add New Property')
@section('page-subtitle', 'Create a new hostel or apartment listing')

@section('content')
<div class="space-y-6">
    <!-- Progress Steps -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Create New Property</h2>
            <p class="text-gray-600">Fill in all required details to list your property</p>
        </div>
        
        <!-- Step Indicator -->
        <div class="mb-10">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <!-- Step 1 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold">
                                1
                            </div>
                            <span class="text-sm font-medium text-purple-600 mt-2">Basic Info</span>
                        </div>
                        <div class="flex-1 h-1 bg-purple-200 mx-4"></div>
                        
                        <!-- Step 2 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold">
                                2
                            </div>
                            <span class="text-sm font-medium text-purple-600 mt-2">Location</span>
                        </div>
                        <div class="flex-1 h-1 bg-purple-200 mx-4"></div>
                        
                        <!-- Step 3 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                3
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Pricing</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                        
                        <!-- Step 4 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                4
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Rooms</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                        
                        <!-- Step 5 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                5
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Images</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form id="propertyForm" action="{{ route('owner.properties.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Step 1: Basic Information (Visible) -->
            <div class="mb-10" id="step1">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Property Type <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="HOSTEL" class="sr-only peer property-type" data-type="hostel" required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                            <i class="fas fa-bed text-purple-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Hostel</p>
                                            <p class="text-xs text-gray-500">Room-based rental</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <label class="relative cursor-pointer">
                                <input type="radio" name="type" value="APARTMENT" class="sr-only peer property-type" data-type="apartment" required>
                                <div class="p-4 border-2 border-gray-200 rounded-lg peer-checked:border-purple-500 peer-checked:bg-purple-50 transition-all">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-building text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900">Apartment</p>
                                            <p class="text-xs text-gray-500">Whole unit rental</p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @error('type')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Property Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="e.g., Sunshine Apartments"
                               required>
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description"
                                  name="description"
                                  rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                  placeholder="Describe your property, features, and what makes it special..."
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Bedrooms & Bathrooms -->
                    <div>
                        <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Bedrooms <span class="text-red-500">*</span>
                        </label>
                        <select id="bedrooms" name="bedrooms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('bedrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i == 1 ? 'Bedroom' : 'Bedrooms' }}
                                </option>
                            @endfor
                            <option value="11" {{ old('bedrooms') == 11 ? 'selected' : '' }}>11+ Bedrooms</option>
                        </select>
                        @error('bedrooms')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Bathrooms <span class="text-red-500">*</span>
                        </label>
                        <select id="bathrooms" name="bathrooms" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}" {{ old('bathrooms') == $i ? 'selected' : '' }}>
                                    {{ $i }} {{ $i == 1 ? 'Bathroom' : 'Bathrooms' }}
                                </option>
                            @endfor
                            <option value="11" {{ old('bathrooms') == 11 ? 'selected' : '' }}>11+ Bathrooms</option>
                        </select>
                        @error('bathrooms')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender Policy -->
                    <div>
                        <label for="gender_policy" class="block text-sm font-medium text-gray-700 mb-2">
                            Gender Policy <span class="text-red-500">*</span>
                        </label>
                        <select id="gender_policy" name="gender_policy" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                            <option value="">Select</option>
                            <option value="MALE_ONLY" {{ old('gender_policy') == 'MALE_ONLY' ? 'selected' : '' }}>Male Only</option>
                            <option value="FEMALE_ONLY" {{ old('gender_policy') == 'FEMALE_ONLY' ? 'selected' : '' }}>Female Only</option>
                            <option value="MIXED" {{ old('gender_policy') == 'MIXED' ? 'selected' : '' }}>Mixed</option>
                        </select>
                        @error('gender_policy')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Unit Size (Apartment only) -->
                    <div id="unitSizeContainer" class="hidden">
                        <label for="unit_size" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit Size (sqft) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="unit_size"
                               name="unit_size"
                               value="{{ old('unit_size') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="e.g., 1200"
                               min="1">
                        @error('unit_size')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Furnishing Status -->
                    <div>
                        <label for="furnishing_status" class="block text-sm font-medium text-gray-700 mb-2">
                            Furnishing Status
                        </label>
                        <select id="furnishing_status" name="furnishing_status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="">Select</option>
                            <option value="FURNISHED" {{ old('furnishing_status') == 'FURNISHED' ? 'selected' : '' }}>Furnished</option>
                            <option value="SEMI_FURNISHED" {{ old('furnishing_status') == 'SEMI_FURNISHED' ? 'selected' : '' }}>Semi-Furnished</option>
                            <option value="UNFURNISHED" {{ old('furnishing_status') == 'UNFURNISHED' ? 'selected' : '' }}>Unfurnished</option>
                        </select>
                        @error('furnishing_status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 2: Location (Hidden by default) -->
            <div class="mb-10 hidden" id="step2">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Location Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                            Full Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="address"
                               name="address"
                               value="{{ old('address') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="Street address, apartment, suite, etc."
                               required>
                        @error('address')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- City & Area -->
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="city"
                               name="city"
                               value="{{ old('city') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="City name"
                               required>
                        @error('city')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="area" class="block text-sm font-medium text-gray-700 mb-2">
                            Area/Neighborhood <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="area"
                               name="area"
                               value="{{ old('area') }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="Area or neighborhood"
                               required>
                        @error('area')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Latitude & Longitude -->
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Latitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="latitude"
                               name="latitude"
                               step="0.0000001"
                               value="{{ old('latitude', 0.0) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="23.810331"
                               required>
                        @error('latitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                            Longitude <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               id="longitude"
                               name="longitude"
                               step="0.0000001"
                               value="{{ old('longitude', 0.0) }}"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="90.412521"
                               required>
                        @error('longitude')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select on Map
                        </label>
                        <div id="mapContainer" class="border-2 border-dashed border-gray-300 rounded-lg h-64 flex items-center justify-center bg-gray-50">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-600">Click to select location on map</p>
                                <p class="text-sm text-gray-500 mt-1" id="coordinatesDisplay">
                                    Latitude: <span id="latDisplay">0.0000</span> • Longitude: <span id="lngDisplay">0.0000</span>
                                </p>
                                <button type="button" id="openMapBtn" class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    Open Map
                                </button>
                            </div>
                        </div>
                        <div id="map" class="hidden h-64 rounded-lg"></div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pricing (Hidden by default) -->
            <div class="mb-10 hidden" id="step3">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Pricing & Commission</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Monthly Price -->
                    <div>
                        <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Price ($) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" 
                                   id="base_price"
                                   name="base_price"
                                   value="{{ old('base_price') }}"
                                   step="0.01"
                                   min="0"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                   placeholder="0.00"
                                   required>
                        </div>
                        @error('base_price')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commission Rate (Auto-filled based on property type) -->
                    <div>
                        <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                            Commission Rate (%)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   id="commission_rate"
                                   name="commission_rate"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   readonly
                                   class="w-full px-4 py-3 border border-gray-300 bg-gray-50 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                   placeholder="Auto-filled">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">%</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Based on property type</p>
                        @error('commission_rate')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Commission Display -->
                    <div class="md:col-span-2 bg-purple-50 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-purple-900">Commission Breakdown</h4>
                                <p class="text-sm text-purple-600">Based on your property type</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Commission Rate</p>
                                <p id="commissionRateDisplay" class="text-xl font-bold text-purple-700">0%</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Monthly Price</span>
                                <span id="monthlyPriceDisplay" class="font-medium">$0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Commission Amount</span>
                                <span id="commissionAmountDisplay" class="font-medium text-red-600">$0.00</span>
                            </div>
                            <div class="pt-3 border-t border-purple-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total Price (with commission)</span>
                                    <span id="totalPriceDisplay" class="text-2xl font-bold text-green-600">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Minimum Stay (Apartment only) -->
                    <div id="minStayContainer" class="hidden">
                        <label for="min_stay_months" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Stay (Months)
                        </label>
                        <select id="min_stay_months" name="min_stay_months" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="1" {{ old('min_stay_months', 3) == 1 ? 'selected' : '' }}>1 Month</option>
                            <option value="3" {{ old('min_stay_months', 3) == 3 ? 'selected' : '' }}>3 Months</option>
                            <option value="6" {{ old('min_stay_months', 3) == 6 ? 'selected' : '' }}>6 Months</option>
                            <option value="12" {{ old('min_stay_months', 3) == 12 ? 'selected' : '' }}>12 Months</option>
                        </select>
                        @error('min_stay_months')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deposit Months -->
                    <div>
                        <label for="deposit_months" class="block text-sm font-medium text-gray-700 mb-2">
                            Security Deposit (Months)
                        </label>
                        <select id="deposit_months" name="deposit_months" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="0" {{ old('deposit_months', 1) == 0 ? 'selected' : '' }}>No Deposit</option>
                            <option value="1" {{ old('deposit_months', 1) == 1 ? 'selected' : '' }}>1 Month</option>
                            <option value="2" {{ old('deposit_months', 1) == 2 ? 'selected' : '' }}>2 Months</option>
                            <option value="3" {{ old('deposit_months', 1) == 3 ? 'selected' : '' }}>3 Months</option>
                        </select>
                        @error('deposit_months')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            Property Status
                        </label>
                        <select id="status" name="status" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="DRAFT" {{ old('status', 'DRAFT') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                            <option value="PENDING" {{ old('status', 'DRAFT') == 'PENDING' ? 'selected' : '' }}>Submit for Review</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Step 4: Rooms (Hidden by default) -->
            <div class="mb-10 hidden" id="step4">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Room Configuration</h3>
                
                <!-- Apartment Pricing Section -->
                <div id="apartmentPricingSection" class="hidden">
                    <p class="text-gray-600 mb-4">For apartments, the property price is set in the previous step. No room configuration needed.</p>
                </div>

                <!-- Hostel Rooms Section -->
                <div id="hostelRoomsSection" class="hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-medium text-gray-900">Room Types & Pricing</h4>
                        <button type="button" id="addRoomBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Room Type
                        </button>
                    </div>

                    <!-- Rooms Container -->
                    <div id="roomsContainer" class="space-y-6">
                        <!-- Room template (will be cloned) -->
                        <div id="roomTemplate" class="hidden border border-gray-200 rounded-lg p-6 bg-white">
                            <div class="flex justify-between items-start mb-4">
                                <h5 class="font-medium text-gray-900">Room Type <span class="room-index">1</span></h5>
                                <button type="button" class="text-red-500 hover:text-red-700 remove-room-btn">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <!-- Room Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Room Number <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           name="rooms[][room_number]"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                           placeholder="101"
                                           required>
                                </div>

                                <!-- Room Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Room Type <span class="text-red-500">*</span>
                                    </label>
                                    <select name="rooms[][room_type]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                                        <option value="">Select</option>
                                        <option value="SINGLE">Single</option>
                                        <option value="DOUBLE">Double</option>
                                        <option value="TRIPLE">Triple</option>
                                        <option value="QUAD">Quad</option>
                                        <option value="DORM">Dorm</option>
                                    </select>
                                </div>

                                <!-- Floor Number -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Floor Number
                                    </label>
                                    <input type="number" 
                                           name="rooms[][floor_number]"
                                           min="0"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                           placeholder="1">
                                </div>

                                <!-- Capacity -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Capacity <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" 
                                           name="rooms[][capacity]"
                                           min="1"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                           placeholder="2"
                                           required>
                                </div>

                                <!-- Base Price -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Monthly Price ($) <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500">$</span>
                                        </div>
                                        <input type="number" 
                                               name="rooms[][base_price]"
                                               step="0.01"
                                               min="0"
                                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                               placeholder="0.00"
                                               required>
                                    </div>
                                </div>

                                <!-- Room Status -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Room Status
                                    </label>
                                    <select name="rooms[][status]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                        <option value="AVAILABLE">Available</option>
                                        <option value="MAINTENANCE">Maintenance</option>
                                        <option value="RESERVED">Reserved</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No rooms message -->
                    <div id="noRoomsMessage" class="text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fas fa-bed text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 font-medium">No rooms added yet</p>
                        <p class="text-gray-500">Click "Add Room Type" to start adding rooms</p>
                    </div>
                </div>
            </div>

            <!-- Step 5: Images (Hidden by default) -->
            <div class="mb-10 hidden" id="step5">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Property Images</h3>
                
                <!-- Main Image Upload -->
                <div class="mb-8">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Main Cover Image <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500">(Recommended: 1200x800px)</span>
                    </label>
                    <div id="coverImageUpload" class="border-2 border-dashed border-gray-300 rounded-lg h-64 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-600 font-medium">Click to upload cover image</p>
                            <p class="text-sm text-gray-500 mt-1">or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                    <input type="file" 
                           id="cover_image"
                           name="cover_image"
                           accept="image/*"
                           class="hidden"
                           required>
                    <div id="coverImagePreview" class="hidden mt-4">
                        <img src="" alt="Cover preview" class="max-h-64 rounded-lg">
                        <button type="button" id="removeCoverImage" class="mt-2 text-red-500 hover:text-red-700">
                            <i class="fas fa-times mr-1"></i> Remove
                        </button>
                    </div>
                    @error('cover_image')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Additional Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Images <span class="text-xs text-gray-500">(Up to 10 images)</span>
                    </label>
                    <div id="additionalImagesUpload" class="border-2 border-dashed border-gray-300 rounded-lg p-6 bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-images text-gray-400 text-3xl mb-3"></i>
                            <p class="text-gray-600 font-medium">Click to upload additional images</p>
                            <p class="text-sm text-gray-500 mt-1">or drag and drop multiple images</p>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 10MB each</p>
                        </div>
                    </div>
                    <input type="file" 
                           id="additional_images"
                           name="additional_images[]"
                           accept="image/*"
                           multiple
                           class="hidden">
                    
                    <!-- Image Gallery Preview -->
                    <div id="imageGallery" class="mt-6">
                        <h4 class="font-medium text-gray-900 mb-4">Image Gallery Preview</h4>
                        <div id="galleryGrid" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Images will be added here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between items-center pt-8 border-t border-gray-200">
                <div>
                    <button type="button" id="prevBtn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors hidden">
                        <i class="fas fa-arrow-left mr-2"></i> Previous
                    </button>
                </div>
                
                <div class="flex items-center gap-4">
                    <button type="button" id="saveDraftBtn" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                        Save as Draft
                    </button>
                    <button type="button" id="nextBtn" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        Next Step <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="submitBtn" class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors hidden">
                        <i class="fas fa-check mr-2"></i> Create Property
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
/* Form step transitions */
.form-step {
    transition: all 0.3s ease;
}

/* Custom checkbox styles */
input[type="checkbox"]:checked {
    background-color: #7c3aed;
    border-color: #7c3aed;
}

/* Image upload hover effect */
.border-dashed:hover {
    border-color: #7c3aed;
    background-color: #faf5ff;
}

/* Step indicator animation */
.step-completed {
    animation: stepComplete 0.5s ease;
}

@keyframes stepComplete {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Map styles */
#map {
    width: 100%;
    height: 256px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 5;
    let roomCounter = 0;
    let selectedPropertyType = null;
    const commissionRates = {
        'HOSTEL': 5.00,
        'APARTMENT': 3.00
    };
    
    // Initialize property type
    initializePropertyType();
    
    function initializePropertyType() {
        const selectedType = document.querySelector('input[name="type"]:checked');
        if (selectedType) {
            selectedPropertyType = selectedType.value;
            updatePropertyTypeDependencies();
        }
    }
    
    // Update step indicator
    function updateStepIndicator() {
        document.querySelectorAll('.flex-col.items-center').forEach((step, index) => {
            const stepNumber = index + 1;
            const circle = step.querySelector('.rounded-full');
            const text = step.querySelector('.text-sm');
            
            if (stepNumber < currentStep) {
                circle.className = 'w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold step-completed';
                text.className = 'text-sm font-medium text-purple-600 mt-2';
            } else if (stepNumber === currentStep) {
                circle.className = 'w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold';
                text.className = 'text-sm font-medium text-purple-600 mt-2';
            } else {
                circle.className = 'w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold';
                text.className = 'text-sm font-medium text-gray-500 mt-2';
            }
        });
        
        document.querySelectorAll('.h-1').forEach((connector, index) => {
            if (index + 1 < currentStep) {
                connector.className = 'flex-1 h-1 bg-purple-200 mx-4';
            } else {
                connector.className = 'flex-1 h-1 bg-gray-200 mx-4';
            }
        });
    }
    
    // Show current step
    function showStep(step) {
        // Hide all steps
        for (let i = 1; i <= totalSteps; i++) {
            const stepElement = document.getElementById(`step${i}`);
            if (stepElement) {
                stepElement.classList.add('hidden');
            }
        }
        
        // Show current step
        const currentStepElement = document.getElementById(`step${step}`);
        if (currentStepElement) {
            currentStepElement.classList.remove('hidden');
        }
        
        // Update buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (step === 1) {
            prevBtn.classList.add('hidden');
        } else {
            prevBtn.classList.remove('hidden');
        }
        
        if (step === totalSteps) {
            nextBtn.classList.add('hidden');
            submitBtn.classList.remove('hidden');
        } else {
            nextBtn.classList.remove('hidden');
            submitBtn.classList.add('hidden');
        }
        
        updateStepIndicator();
    }
    
    // Next button click
    document.getElementById('nextBtn').addEventListener('click', function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    // Previous button click
    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Property type change
    document.querySelectorAll('.property-type').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedPropertyType = this.value;
            updatePropertyTypeDependencies();
        });
    });
    
    function updatePropertyTypeDependencies() {
        const isApartment = selectedPropertyType === 'APARTMENT';
        const isHostel = selectedPropertyType === 'HOSTEL';
        
        // Show/hide unit size for apartments
        const unitSizeContainer = document.getElementById('unitSizeContainer');
        const unitSizeInput = document.getElementById('unit_size');
        if (isApartment) {
            unitSizeContainer.classList.remove('hidden');
            unitSizeInput.required = true;
        } else {
            unitSizeContainer.classList.add('hidden');
            unitSizeInput.required = false;
        }
        
        // Show/hide minimum stay for apartments
        const minStayContainer = document.getElementById('minStayContainer');
        if (isApartment) {
            minStayContainer.classList.remove('hidden');
        } else {
            minStayContainer.classList.add('hidden');
        }
        
        // Update commission rate
        const commissionRate = commissionRates[selectedPropertyType] || 0;
        const commissionRateInput = document.getElementById('commission_rate');
        const commissionRateDisplay = document.getElementById('commissionRateDisplay');
        
        if (commissionRateInput) {
            commissionRateInput.value = commissionRate;
        }
        if (commissionRateDisplay) {
            commissionRateDisplay.textContent = commissionRate + '%';
        }
        
        // Update step 4 based on property type
        const apartmentPricingSection = document.getElementById('apartmentPricingSection');
        const hostelRoomsSection = document.getElementById('hostelRoomsSection');
        
        if (currentStep >= 4) {
            if (isApartment) {
                apartmentPricingSection.classList.remove('hidden');
                hostelRoomsSection.classList.add('hidden');
            } else if (isHostel) {
                apartmentPricingSection.classList.add('hidden');
                hostelRoomsSection.classList.remove('hidden');
            }
        }
        
        // Recalculate commission
        calculateCommission();
    }
    
    // Commission calculation
    function calculateCommission() {
        const priceInput = document.getElementById('base_price');
        const commissionRateInput = document.getElementById('commission_rate');
        
        if (!priceInput || !commissionRateInput) return;
        
        const price = parseFloat(priceInput.value) || 0;
        const commissionRate = parseFloat(commissionRateInput.value) || 0;
        const commissionAmount = price * (commissionRate / 100);
        const totalPrice = price + commissionAmount;
        
        // Update displays
        document.getElementById('monthlyPriceDisplay').textContent = '$' + price.toFixed(2);
        document.getElementById('commissionAmountDisplay').textContent = '-$' + commissionAmount.toFixed(2);
        document.getElementById('totalPriceDisplay').textContent = '$' + totalPrice.toFixed(2);
    }
    
    // Add event listeners for price changes
    const priceInput = document.getElementById('base_price');
    if (priceInput) {
        priceInput.addEventListener('input', calculateCommission);
    }
    
    // Room management
    document.getElementById('addRoomBtn')?.addEventListener('click', function() {
        addRoom();
    });
    
    function addRoom() {
        const template = document.getElementById('roomTemplate');
        const container = document.getElementById('roomsContainer');
        const noRoomsMessage = document.getElementById('noRoomsMessage');
        
        if (!template || !container) return;
        
        // Clone template
        const newRoom = template.cloneNode(true);
        newRoom.id = 'room_' + roomCounter;
        newRoom.classList.remove('hidden');
        
        // Update room index
        const roomIndexSpan = newRoom.querySelector('.room-index');
        if (roomIndexSpan) {
            roomIndexSpan.textContent = roomCounter + 1;
        }
        
        // Add remove functionality
        const removeBtn = newRoom.querySelector('.remove-room-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                newRoom.remove();
                updateRoomsDisplay();
            });
        }
        
        // Add to container
        container.appendChild(newRoom);
        
        // Update display
        updateRoomsDisplay();
        roomCounter++;
    }
    
    function updateRoomsDisplay() {
        const container = document.getElementById('roomsContainer');
        const noRoomsMessage = document.getElementById('noRoomsMessage');
        
        if (!container || !noRoomsMessage) return;
        
        const hasRooms = container.children.length > 0;
        noRoomsMessage.classList.toggle('hidden', hasRooms);
    }
    
    // Image upload functionality
    const coverImageUpload = document.getElementById('coverImageUpload');
    const coverImageInput = document.getElementById('cover_image');
    const coverImagePreview = document.getElementById('coverImagePreview');
    const coverImagePreviewImg = coverImagePreview.querySelector('img');
    
    if (coverImageUpload && coverImageInput) {
        coverImageUpload.addEventListener('click', () => coverImageInput.click());
        
        coverImageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    coverImagePreviewImg.src = e.target.result;
                    coverImagePreview.classList.remove('hidden');
                    coverImageUpload.classList.add('hidden');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    document.getElementById('removeCoverImage')?.addEventListener('click', function() {
        coverImageInput.value = '';
        coverImagePreview.classList.add('hidden');
        coverImageUpload.classList.remove('hidden');
    });
    
    // Additional images upload
    const additionalImagesUpload = document.getElementById('additionalImagesUpload');
    const additionalImagesInput = document.getElementById('additional_images');
    const galleryGrid = document.getElementById('galleryGrid');
    
    if (additionalImagesUpload && additionalImagesInput) {
        additionalImagesUpload.addEventListener('click', () => additionalImagesInput.click());
        
        additionalImagesInput.addEventListener('change', function(e) {
            if (this.files) {
                Array.from(this.files).forEach(file => {
                    if (file.type.startsWith('image/')) {
                        addImageToGallery(file);
                    }
                });
            }
        });
    }
    
    function addImageToGallery(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const colDiv = document.createElement('div');
            colDiv.className = 'border border-gray-200 rounded-lg overflow-hidden';
            
            colDiv.innerHTML = `
                <div class="h-48 bg-gray-100 flex items-center justify-center">
                    <img src="${e.target.result}" alt="Preview" class="h-full w-full object-cover">
                </div>
                <div class="p-3 bg-white">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 truncate">${file.name}</span>
                        <button type="button" class="text-red-500 hover:text-red-700 remove-image-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            `;
            
            const removeBtn = colDiv.querySelector('.remove-image-btn');
            removeBtn.addEventListener('click', function() {
                colDiv.remove();
            });
            
            galleryGrid.appendChild(colDiv);
        };
        reader.readAsDataURL(file);
    }
    
    // Map functionality
    const openMapBtn = document.getElementById('openMapBtn');
    const mapContainer = document.getElementById('mapContainer');
    const mapDiv = document.getElementById('map');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const latDisplay = document.getElementById('latDisplay');
    const lngDisplay = document.getElementById('lngDisplay');
    
    if (openMapBtn && mapContainer && mapDiv) {
        openMapBtn.addEventListener('click', function() {
            mapContainer.classList.add('hidden');
            mapDiv.classList.remove('hidden');
            
            // Initialize map (using Leaflet as example)
            // In production, you would integrate with actual map API
            initializeMockMap();
        });
    }
    
    function initializeMockMap() {
        // This is a mock implementation. In production, use Leaflet or Google Maps
        mapDiv.innerHTML = `
            <div class="h-full flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-purple-50 rounded-lg">
                <i class="fas fa-map-marker-alt text-purple-600 text-4xl mb-4"></i>
                <p class="text-gray-700 font-medium mb-2">Map Integration</p>
                <p class="text-gray-600 text-sm mb-4">In production, this would show an interactive map</p>
                <div class="flex gap-2">
                    <button type="button" id="useCurrentLocation" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                        Use Current Location
                    </button>
                    <button type="button" id="closeMap" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close Map
                    </button>
                </div>
            </div>
        `;
        
        // Mock current location
        document.getElementById('useCurrentLocation')?.addEventListener('click', function() {
            // Mock coordinates (Dhaka, Bangladesh)
            const mockLat = 23.810331;
            const mockLng = 90.412521;
            
            latInput.value = mockLat;
            lngInput.value = mockLng;
            latDisplay.textContent = mockLat.toFixed(4);
            lngDisplay.textContent = mockLng.toFixed(4);
            
            // Close map
            mapDiv.classList.add('hidden');
            mapContainer.classList.remove('hidden');
        });
        
        // Close map
        document.getElementById('closeMap')?.addEventListener('click', function() {
            mapDiv.classList.add('hidden');
            mapContainer.classList.remove('hidden');
        });
    }
    
    // Form validation
    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step${currentStep}`);
        const inputs = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('border-red-500');
                isValid = false;
                
                // Show error message
                let errorElement = input.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('text-red-500')) {
                    errorElement = document.createElement('p');
                    errorElement.className = 'text-red-500 text-sm mt-1';
                    errorElement.textContent = 'This field is required';
                    input.parentNode.appendChild(errorElement);
                }
            } else {
                input.classList.remove('border-red-500');
                // Remove error message if exists
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('text-red-500')) {
                    errorElement.remove();
                }
            }
        });
        
        // Additional validation for step 4 (rooms)
        if (currentStep === 4 && selectedPropertyType === 'HOSTEL') {
            const roomsContainer = document.getElementById('roomsContainer');
            if (roomsContainer.children.length === 0) {
                alert('Please add at least one room for hostel properties');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Save as draft
    document.getElementById('saveDraftBtn').addEventListener('click', function() {
        document.getElementById('status').value = 'DRAFT';
        document.getElementById('propertyForm').submit();
    });
    
    // Initialize first step
    showStep(1);
    calculateCommission();
});
</script>

<!-- Include Leaflet CSS & JS for maps (example) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
@endsection