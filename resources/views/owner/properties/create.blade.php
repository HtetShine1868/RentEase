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
                        
                        <!-- Step 3: ROOMS (Changed from Pricing) -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                3
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Rooms</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                        
                        <!-- Step 4: PRICING (Changed from Rooms) -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                4
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Pricing</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                        
                        <!-- Step 5 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                5
                            </div>
                            <span class="text-sm font-medium text-gray-500 mt-2">Amenities</span>
                        </div>
                        <div class="flex-1 h-1 bg-gray-200 mx-4"></div>
                        
                        <!-- Step 6 -->
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold">
                                6
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
            
            <!-- Hidden field for property type to track step 4 content -->
            <input type="hidden" id="propertyTypeHidden" name="type" value="{{ old('type', 'HOSTEL') }}">
            
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
                                <input type="radio" name="type" value="HOSTEL" class="sr-only peer property-type" 
                                       data-type="hostel" required {{ old('type') == 'HOSTEL' ? 'checked' : 'checked' }}>
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
                                <input type="radio" name="type" value="APARTMENT" class="sr-only peer property-type" 
                                       data-type="apartment" required {{ old('type') == 'APARTMENT' ? 'checked' : '' }}>
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
                    <div id="unitSizeContainer" class="{{ old('type') == 'APARTMENT' ? '' : 'hidden' }}">
                        <label for="unit_size" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit Size (sqft) <span id="unitSizeRequired" class="text-red-500 hidden">*</span>
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
                               placeholder="e.g., Dhaka"
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
                               placeholder="e.g., Dhanmondi"
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
                               value="{{ old('latitude', 23.810331) }}"
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
                               value="{{ old('longitude', 90.412521) }}"
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
                                    Latitude: <span id="latDisplay">{{ old('latitude', '23.8103') }}</span> • Longitude: <span id="lngDisplay">{{ old('longitude', '90.4125') }}</span>
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

            <!-- Step 3: Rooms Configuration (Changed from step4 to step3) -->
            <div class="mb-10 hidden" id="step3">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Room Configuration</h3>
                
                <!-- Apartment Message -->
                <div id="apartmentMessage" class="hidden bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-info-circle text-blue-500 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h4 class="font-medium text-blue-900">Apartment Property</h4>
                            <p class="text-blue-700 mt-1">
                                For apartments, the property is rented as a whole unit. No room configuration is needed.
                                The pricing set in the next step applies to the entire apartment.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Hostel Rooms Configuration -->
                <div id="hostelRoomsSection" class="hidden">
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="font-medium text-gray-900">Room Types & Pricing</h4>
                        <button type="button" id="addRoomBtn" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                            <i class="fas fa-plus mr-2"></i> Add Room Type
                        </button>
                    </div>

                    <!-- Rooms Container -->
                    <div id="roomsContainer" class="space-y-6">
                        @if(old('rooms'))
                            @foreach(old('rooms') as $index => $room)
                                <div class="border border-gray-200 rounded-lg p-6 bg-white room-item">
                                    <div class="flex justify-between items-start mb-4">
                                        <h5 class="font-medium text-gray-900">Room Type <span class="room-index">{{ $index + 1 }}</span></h5>
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
                                                   name="rooms[{{ $index }}][room_number]"
                                                   value="{{ $room['room_number'] ?? '' }}"
                                                   class="room-number-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                                   placeholder="101"
                                                   required>
                                        </div>

                                        <!-- Room Type -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Room Type <span class="text-red-500">*</span>
                                            </label>
                                            <select name="rooms[{{ $index }}][room_type]" class="room-type-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
                                                <option value="">Select</option>
                                                <option value="SINGLE" {{ ($room['room_type'] ?? '') == 'SINGLE' ? 'selected' : '' }}>Single</option>
                                                <option value="DOUBLE" {{ ($room['room_type'] ?? '') == 'DOUBLE' ? 'selected' : '' }}>Double</option>
                                                <option value="TRIPLE" {{ ($room['room_type'] ?? '') == 'TRIPLE' ? 'selected' : '' }}>Triple</option>
                                                <option value="QUAD" {{ ($room['room_type'] ?? '') == 'QUAD' ? 'selected' : '' }}>Quad</option>
                                                <option value="DORM" {{ ($room['room_type'] ?? '') == 'DORM' ? 'selected' : '' }}>Dorm</option>
                                            </select>
                                        </div>

                                        <!-- Floor Number -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Floor Number
                                            </label>
                                            <input type="number" 
                                                   name="rooms[{{ $index }}][floor_number]"
                                                   value="{{ $room['floor_number'] ?? '' }}"
                                                   min="0"
                                                   class="floor-number-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                                   placeholder="1">
                                        </div>

                                        <!-- Capacity -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Capacity <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" 
                                                   name="rooms[{{ $index }}][capacity]"
                                                   value="{{ $room['capacity'] ?? '' }}"
                                                   min="1"
                                                   class="capacity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                                   placeholder="2"
                                                   required>
                                        </div>

                                        <!-- Base Price -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Monthly Price (৳) <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <span class="text-gray-500">৳</span>
                                                </div>
                                                <input type="number" 
                                                       name="rooms[{{ $index }}][base_price]"
                                                       value="{{ $room['base_price'] ?? '' }}"
                                                       step="0.01"
                                                       min="0"
                                                       class="room-price-input w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                                       placeholder="0.00"
                                                       required>
                                            </div>
                                        </div>

                                        <!-- Room Status -->
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                Room Status
                                            </label>
                                            <select name="rooms[{{ $index }}][status]" class="room-status-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                                                <option value="AVAILABLE" {{ ($room['status'] ?? 'AVAILABLE') == 'AVAILABLE' ? 'selected' : '' }}>Available</option>
                                                <option value="MAINTENANCE" {{ ($room['status'] ?? '') == 'MAINTENANCE' ? 'selected' : '' }}>Maintenance</option>
                                                <option value="RESERVED" {{ ($room['status'] ?? '') == 'RESERVED' ? 'selected' : '' }}>Reserved</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Hostel Total Price Display -->
                    <div id="hostelTotalPriceContainer" class="hidden mt-8 bg-green-50 border border-green-200 rounded-lg p-6">
                        <h4 class="font-medium text-green-900 mb-4">Hostel Room Summary</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Rooms Added:</span>
                                <span id="totalRoomsCount" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Monthly Revenue (all rooms):</span>
                                <span id="totalMonthlyRevenue" class="font-medium text-green-600">৳0.00</span>
                            </div>
                            <div class="pt-3 border-t border-green-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Proceed to Pricing to see commission details</span>
                                    <span class="text-purple-600 font-medium">Next Step →</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No rooms message -->
                    <div id="noRoomsMessage" class="{{ old('rooms') ? 'hidden' : 'block' }} text-center py-8 border-2 border-dashed border-gray-300 rounded-lg">
                        <i class="fas fa-bed text-gray-400 text-4xl mb-3"></i>
                        <p class="text-gray-600 font-medium">No rooms added yet</p>
                        <p class="text-gray-500">Click "Add Room Type" to start adding rooms</p>
                    </div>
                </div>
            </div>

            <!-- Step 4: Pricing (Changed from step3 to step4) -->
            <div class="mb-10 hidden" id="step4">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Pricing & Commission</h3>
                
                <!-- Apartment Pricing Section -->
                <div id="apartmentPricingSection" class="hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Monthly Price -->
                        <div>
                            <label for="base_price" class="block text-sm font-medium text-gray-700 mb-2">
                                Monthly Price (৳) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">৳</span>
                                </div>
                                <input type="number" 
                                       id="base_price"
                                       name="base_price"
                                       value="{{ old('base_price') }}"
                                       step="0.01"
                                       min="0"
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                       placeholder="0.00"
                                       required>
                            </div>
                            @error('base_price')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Commission Rate -->
                        <div>
                            <label for="commission_rate" class="block text-sm font-medium text-gray-700 mb-2">
                                Commission Rate (%)
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       id="commission_rate"
                                       name="commission_rate"
                                       value="{{ old('commission_rate') }}"
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
                                    <h4 class="font-medium text-purple-900">Apartment Commission Breakdown</h4>
                                    <p class="text-sm text-purple-600">Based on your property type</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Commission Rate</p>
                                    <p id="apartmentCommissionRateDisplay" class="text-xl font-bold text-purple-700">3%</p>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Monthly Price</span>
                                    <span id="apartmentMonthlyPriceDisplay" class="font-medium">৳0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">Commission Amount</span>
                                    <span id="apartmentCommissionAmountDisplay" class="font-medium text-red-600">৳0.00</span>
                                </div>
                                <div class="pt-3 border-t border-purple-200">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold text-gray-900">Total Price (with commission)</span>
                                        <span id="apartmentTotalPriceDisplay" class="text-2xl font-bold text-green-600">৳0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hostel Pricing Section -->
                <div id="hostelPricingSection" class="hidden">
                    <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-purple-900">Hostel Room Summary</h4>
                                <p class="text-sm text-purple-600">Based on rooms added in previous step</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">Commission Rate</p>
                                <p id="hostelCommissionRateDisplay" class="text-xl font-bold text-purple-700">5%</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Rooms</span>
                                <span id="hostelTotalRoomsFinal" class="font-medium">0</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Total Monthly Revenue (all rooms)</span>
                                <span id="hostelTotalRevenueFinal" class="font-medium text-green-600">৳0.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Commission Amount (5%)</span>
                                <span id="hostelCommissionFinal" class="font-medium text-red-600">৳0.00</span>
                            </div>
                            <div class="pt-3 border-t border-purple-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total With Commission</span>
                                    <span id="hostelTotalWithCommissionFinal" class="text-2xl font-bold text-green-600">৳0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field for hostel total price -->
                    <input type="hidden" id="hostel_total_revenue" name="hostel_total_revenue" value="0">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <!-- Minimum Stay (Apartment only) -->
                    <div id="minStayContainer" class="hidden">
                        <label for="min_stay_months" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Stay (Months)
                        </label>
                        <select id="min_stay_months" name="min_stay_months" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="1" {{ old('min_stay_months', 1) == 1 ? 'selected' : '' }}>1 Month</option>
                            <option value="3" {{ old('min_stay_months', 1) == 3 ? 'selected' : '' }}>3 Months</option>
                            <option value="6" {{ old('min_stay_months', 1) == 6 ? 'selected' : '' }}>6 Months</option>
                            <option value="12" {{ old('min_stay_months', 1) == 12 ? 'selected' : '' }}>12 Months</option>
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
                    <div class="md:col-span-2">
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

            <!-- Step 5: Amenities -->
            <div class="mb-10 hidden" id="step5">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Property Amenities</h3>
                
                <!-- Basic Amenities -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-900 mb-4">Basic Amenities</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $basicAmenities = [
                                ['icon' => 'wifi', 'name' => 'Wi-Fi', 'value' => 'Wi-Fi'],
                                ['icon' => 'snowflake', 'name' => 'Air Conditioning', 'value' => 'Air Conditioning'],
                                ['icon' => 'fan', 'name' => 'Fan', 'value' => 'Fan'],
                                ['icon' => 'tv', 'name' => 'TV', 'value' => 'Television'],
                                ['icon' => 'bed', 'name' => 'Bed', 'value' => 'Bed'],
                                ['icon' => 'soap', 'name' => 'Attached Bath', 'value' => 'Attached Bathroom'],
                                ['icon' => 'shower', 'name' => 'Geyser', 'value' => 'Geyser'],
                                ['icon' => 'chair', 'name' => 'Furniture', 'value' => 'Furniture'],
                                ['icon' => 'box', 'name' => 'Storage', 'value' => 'Storage Space'],
                            ];
                        @endphp
                        
                        @foreach($basicAmenities as $amenity)
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity['value'] }}" 
                                       class="amenity-checkbox h-5 w-5 text-purple-600 rounded focus:ring-purple-500 border-gray-300"
                                       {{ in_array($amenity['value'], old('amenities', [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $amenity['name'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Security Amenities -->
                <div class="mb-8">
                    <h4 class="font-medium text-gray-900 mb-4">Security Amenities</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @php
                            $securityAmenities = [
                                ['icon' => 'shield-alt', 'name' => 'CCTV', 'value' => 'CCTV'],
                                ['icon' => 'lock', 'name' => 'Secure Entry', 'value' => 'Secure Entry'],
                                ['icon' => 'user-shield', 'name' => 'Security Guard', 'value' => 'Security Guard'],
                                ['icon' => 'fire-extinguisher', 'name' => 'Fire Safety', 'value' => 'Fire Safety'],
                            ];
                        @endphp
                        
                        @foreach($securityAmenities as $amenity)
                            <label class="flex items-center p-4 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                <input type="checkbox" 
                                       name="amenities[]" 
                                       value="{{ $amenity['value'] }}" 
                                       class="amenity-checkbox h-5 w-5 text-purple-600 rounded focus:ring-purple-500 border-gray-300"
                                       {{ in_array($amenity['value'], old('amenities', [])) ? 'checked' : '' }}>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $amenity['name'] }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- Custom Amenity -->
                <div>
                    <h4 class="font-medium text-gray-900 mb-4">Add Custom Amenities</h4>
                    <div class="space-y-4" id="customAmenitiesContainer">
                        @if(old('custom_amenities'))
                            @foreach(old('custom_amenities') as $customAmenity)
                                <div class="flex gap-4">
                                    <input type="text" 
                                           name="custom_amenities[]" 
                                           value="{{ $customAmenity }}"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" 
                                           placeholder="Amenity name">
                                    <button type="button" class="text-red-500 hover:text-red-700 remove-amenity-btn">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="addCustomAmenityBtn" class="mt-4 px-4 py-2 border border-dashed border-gray-300 text-gray-600 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-plus mr-2"></i> Add Custom Amenity
                    </button>
                </div>
            </div>

            <!-- Step 6: Images -->
            <div class="mb-10 hidden" id="step6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Property Images</h3>
                
                <!-- Cover Image -->
                <div class="mb-8">
                    <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                        Cover Image <span class="text-red-500">*</span>
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
// Pass commission rates from PHP to JavaScript
const commissionRates = {
    'HOSTEL': {{ $commissionRates['HOSTEL'] ?? 5.00 }},
    'APARTMENT': {{ $commissionRates['APARTMENT'] ?? 3.00 }}
};

document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 6;
    let roomCounter = {{ old('rooms') ? count(old('rooms')) : 0 }};
    let customAmenityCounter = {{ old('custom_amenities') ? count(old('custom_amenities')) : 0 }};
    let selectedPropertyType = document.querySelector('input[name="type"]:checked')?.value || 'HOSTEL';
    let hostelTotalRevenue = 0;
    
    // Simple commission rate function
    async function fetchCommissionRate(type) {
        return commissionRates[type] || (type === 'HOSTEL' ? 5.00 : 3.00);
    }
    
    // Update step indicator
    function updateStepIndicator() {
        const steps = document.querySelectorAll('.flex-col.items-center');
        if (steps.length === 0) {
            console.warn('No step elements found');
            return;
        }
        
        steps.forEach((step, index) => {
            const stepNumber = index + 1;
            const circle = step.querySelector('.rounded-full');
            const text = step.querySelector('.text-sm');
            
            // Skip if elements don't exist
            if (!circle || !text) {
                console.warn(`Step ${stepNumber} elements not found`);
                return;
            }
            
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
        
        const connectors = document.querySelectorAll('.h-1');
        if (connectors.length > 0) {
            connectors.forEach((connector, index) => {
                if (index + 1 < currentStep) {
                    connector.className = 'flex-1 h-1 bg-purple-200 mx-4';
                } else {
                    connector.className = 'flex-1 h-1 bg-gray-200 mx-4';
                }
            });
        }
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
        
        // Update property type specific displays
        if (step === 3 || step === 4) {
            updatePropertyTypeSpecificDisplay(step);
        }
        
        // Update buttons
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        
        if (step === 1) {
            if (prevBtn) prevBtn.classList.add('hidden');
        } else {
            if (prevBtn) prevBtn.classList.remove('hidden');
        }
        
        if (step === totalSteps) {
            if (nextBtn) nextBtn.classList.add('hidden');
            if (submitBtn) submitBtn.classList.remove('hidden');
        } else {
            if (nextBtn) nextBtn.classList.remove('hidden');
            if (submitBtn) submitBtn.classList.add('hidden');
        }
        
        updateStepIndicator();
    }
    
    // Update property type specific displays
function updatePropertyTypeSpecificDisplay(step) {
    const isApartment = selectedPropertyType === 'APARTMENT';
    const isHostel = selectedPropertyType === 'HOSTEL';
    
    if (step === 3) { // Rooms step
        const apartmentMessage = document.getElementById('apartmentMessage');
        const hostelRoomsSection = document.getElementById('hostelRoomsSection');
        const hostelTotalPriceContainer = document.getElementById('hostelTotalPriceContainer');
        
        if (isApartment) {
            if (apartmentMessage) apartmentMessage.classList.remove('hidden');
            if (hostelRoomsSection) hostelRoomsSection.classList.add('hidden');
            if (hostelTotalPriceContainer) hostelTotalPriceContainer.classList.add('hidden');
        } else {
            if (apartmentMessage) apartmentMessage.classList.add('hidden');
            if (hostelRoomsSection) hostelRoomsSection.classList.remove('hidden');
            calculateHostelTotalRevenue();
        }
    }
    
    if (step === 4) { // Pricing step
        const apartmentPricingSection = document.getElementById('apartmentPricingSection');
        const hostelPricingSection = document.getElementById('hostelPricingSection');
        const minStayContainer = document.getElementById('minStayContainer');
        const basePriceInput = document.getElementById('base_price');
        
        if (isApartment) {
            if (apartmentPricingSection) apartmentPricingSection.classList.remove('hidden');
            if (hostelPricingSection) hostelPricingSection.classList.add('hidden');
            if (minStayContainer) minStayContainer.classList.remove('hidden');
            // Show and make base_price required for apartments
            if (basePriceInput) {
                basePriceInput.required = true;
                basePriceInput.parentElement.parentElement.classList.remove('hidden');
            }
            updateApartmentCommissionDisplay();
        } else {
            if (apartmentPricingSection) apartmentPricingSection.classList.add('hidden');
            if (hostelPricingSection) hostelPricingSection.classList.remove('hidden');
            if (minStayContainer) minStayContainer.classList.add('hidden');
            // Hide and make base_price NOT required for hostels
            if (basePriceInput) {
                basePriceInput.required = false;
                basePriceInput.parentElement.parentElement.classList.add('hidden');
            }
            updateHostelCommissionDisplay();
        }
        
        updateCommissionRate();
    }
}
    // Calculate hostel total revenue from rooms
    function calculateHostelTotalRevenue() {
        const rooms = document.querySelectorAll('.room-item');
        const hostelTotalPriceContainer = document.getElementById('hostelTotalPriceContainer');
        const totalRoomsCount = document.getElementById('totalRoomsCount');
        const totalMonthlyRevenue = document.getElementById('totalMonthlyRevenue');
        
        hostelTotalRevenue = 0;
        rooms.forEach(room => {
            const priceInput = room.querySelector('.room-price-input');
            if (priceInput && priceInput.value) {
                hostelTotalRevenue += parseFloat(priceInput.value) || 0;
            }
        });
        
        if (rooms.length === 0) {
            if (hostelTotalPriceContainer) hostelTotalPriceContainer.classList.add('hidden');
            return;
        }
        
        if (totalRoomsCount) totalRoomsCount.textContent = rooms.length;
        if (totalMonthlyRevenue) totalMonthlyRevenue.textContent = '৳' + hostelTotalRevenue.toFixed(2);
        if (hostelTotalPriceContainer) hostelTotalPriceContainer.classList.remove('hidden');
        
        const hiddenField = document.getElementById('hostel_total_revenue');
        if (hiddenField) hiddenField.value = hostelTotalRevenue;
    }
    
    // Update hostel commission display
    function updateHostelCommissionDisplay() {
        const totalRooms = document.querySelectorAll('.room-item').length;
        const commissionRateInput = document.getElementById('commission_rate');
        const commissionRate = commissionRateInput ? parseFloat(commissionRateInput.value) || 5.00 : 5.00;
        const commissionAmount = hostelTotalRevenue * (commissionRate / 100);
        const totalWithCommission = hostelTotalRevenue + commissionAmount;
        
        const totalRoomsEl = document.getElementById('hostelTotalRoomsFinal');
        const totalRevenueEl = document.getElementById('hostelTotalRevenueFinal');
        const commissionEl = document.getElementById('hostelCommissionFinal');
        const totalWithCommissionEl = document.getElementById('hostelTotalWithCommissionFinal');
        const commissionRateDisplay = document.getElementById('hostelCommissionRateDisplay');
        
        if (totalRoomsEl) totalRoomsEl.textContent = totalRooms;
        if (totalRevenueEl) totalRevenueEl.textContent = '৳' + hostelTotalRevenue.toFixed(2);
        if (commissionEl) commissionEl.textContent = '৳' + commissionAmount.toFixed(2);
        if (totalWithCommissionEl) totalWithCommissionEl.textContent = '৳' + totalWithCommission.toFixed(2);
        if (commissionRateDisplay) commissionRateDisplay.textContent = commissionRate + '%';
    }
    
    // Update apartment commission display
    function updateApartmentCommissionDisplay() {
        const priceInput = document.getElementById('base_price');
        const commissionRateInput = document.getElementById('commission_rate');
        const commissionRate = commissionRateInput ? parseFloat(commissionRateInput.value) || 3.00 : 3.00;
        const price = priceInput ? parseFloat(priceInput.value) || 0 : 0;
        const commissionAmount = price * (commissionRate / 100);
        const totalPrice = price + commissionAmount;
        
        const monthlyPriceEl = document.getElementById('apartmentMonthlyPriceDisplay');
        const commissionAmountEl = document.getElementById('apartmentCommissionAmountDisplay');
        const totalPriceEl = document.getElementById('apartmentTotalPriceDisplay');
        const commissionRateDisplay = document.getElementById('apartmentCommissionRateDisplay');
        
        if (monthlyPriceEl) monthlyPriceEl.textContent = '৳' + price.toFixed(2);
        if (commissionAmountEl) commissionAmountEl.textContent = '৳' + commissionAmount.toFixed(2);
        if (totalPriceEl) totalPriceEl.textContent = '৳' + totalPrice.toFixed(2);
        if (commissionRateDisplay) commissionRateDisplay.textContent = commissionRate + '%';
    }
    
    // Update commission rate from server
    async function updateCommissionRate() {
        const commissionRate = await fetchCommissionRate(selectedPropertyType);
        const commissionRateInput = document.getElementById('commission_rate');
        
        if (commissionRateInput) {
            commissionRateInput.value = commissionRate;
        }
        
        if (selectedPropertyType === 'APARTMENT') {
            updateApartmentCommissionDisplay();
        } else {
            updateHostelCommissionDisplay();
        }
    }
    
    // Calculate apartment commission on price change
    function calculateApartmentCommission() {
        if (selectedPropertyType === 'APARTMENT') {
            updateApartmentCommissionDisplay();
        }
    }
    
    // Next button click
    const nextBtn = document.getElementById('nextBtn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    showStep(currentStep);
                }
            }
        });
    }
    
    // Previous button click
    const prevBtn = document.getElementById('prevBtn');
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            if (currentStep > 1) {
                currentStep--;
                showStep(currentStep);
            }
        });
    }
    
    // Property type change
    document.querySelectorAll('.property-type').forEach(radio => {
        radio.addEventListener('change', function() {
            selectedPropertyType = this.value;
            const hiddenField = document.getElementById('propertyTypeHidden');
            if (hiddenField) hiddenField.value = selectedPropertyType;
            updatePropertyTypeDependencies();
            
            if (currentStep === 3 || currentStep === 4) {
                updatePropertyTypeSpecificDisplay(currentStep);
            }
        });
    });
    
    function updatePropertyTypeDependencies() {
        const isApartment = selectedPropertyType === 'APARTMENT';
        const isHostel = selectedPropertyType === 'HOSTEL';
        
        // Show/hide unit size for apartments
        const unitSizeContainer = document.getElementById('unitSizeContainer');
        const unitSizeInput = document.getElementById('unit_size');
        const unitSizeRequired = document.getElementById('unitSizeRequired');
        
        if (isApartment) {
            if (unitSizeContainer) unitSizeContainer.classList.remove('hidden');
            if (unitSizeRequired) unitSizeRequired.classList.remove('hidden');
            if (unitSizeInput) unitSizeInput.required = true;
        } else {
            if (unitSizeContainer) unitSizeContainer.classList.add('hidden');
            if (unitSizeRequired) unitSizeRequired.classList.add('hidden');
            if (unitSizeInput) unitSizeInput.required = false;
        }
        
        updateCommissionRate();
    }
    
    // Add event listeners for apartment price changes
    const priceInput = document.getElementById('base_price');
    if (priceInput) {
        priceInput.addEventListener('input', calculateApartmentCommission);
    }
    
    // Room management
    const addRoomBtn = document.getElementById('addRoomBtn');
    if (addRoomBtn) {
        addRoomBtn.addEventListener('click', function() {
            addRoom();
        });
    }
    
    function addRoom() {
        const container = document.getElementById('roomsContainer');
        const noRoomsMessage = document.getElementById('noRoomsMessage');
        
        if (!container) return;
        
        const roomDiv = document.createElement('div');
        roomDiv.className = 'border border-gray-200 rounded-lg p-6 bg-white room-item';
        roomDiv.innerHTML = `
            <div class="flex justify-between items-start mb-4">
                <h5 class="font-medium text-gray-900">Room Type <span class="room-index">${roomCounter + 1}</span></h5>
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
                           name="rooms[${roomCounter}][room_number]"
                           class="room-number-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                           placeholder="101"
                           required>
                </div>

                <!-- Room Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Room Type <span class="text-red-500">*</span>
                    </label>
                    <select name="rooms[${roomCounter}][room_type]" class="room-type-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors" required>
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
                           name="rooms[${roomCounter}][floor_number]"
                           min="0"
                           class="floor-number-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                           placeholder="1">
                </div>

                <!-- Capacity -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Capacity <span class="text-red-500">*</span>
                    </label>
                    <input type="number" 
                           name="rooms[${roomCounter}][capacity]"
                           min="1"
                           class="capacity-input w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                           placeholder="2"
                           required>
                </div>

                <!-- Base Price -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Monthly Price (৳) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">৳</span>
                        </div>
                        <input type="number" 
                               name="rooms[${roomCounter}][base_price]"
                               step="0.01"
                               min="0"
                               class="room-price-input w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="0.00"
                               required>
                    </div>
                </div>

                <!-- Room Status -->
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Room Status
                    </label>
                    <select name="rooms[${roomCounter}][status]" class="room-status-select w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        <option value="AVAILABLE">Available</option>
                        <option value="MAINTENANCE">Maintenance</option>
                        <option value="RESERVED">Reserved</option>
                    </select>
                </div>
            </div>
        `;
        
        // Add remove functionality
        const removeBtn = roomDiv.querySelector('.remove-room-btn');
        removeBtn.addEventListener('click', function() {
            roomDiv.remove();
            updateRoomsDisplay();
            renumberRooms();
            calculateHostelTotalRevenue();
            if (currentStep === 4 && selectedPropertyType === 'HOSTEL') {
                updateHostelCommissionDisplay();
            }
        });
        
        // Add price change listener for hostel total calculation
        const priceInput = roomDiv.querySelector('.room-price-input');
        priceInput.addEventListener('input', function() {
            calculateHostelTotalRevenue();
            if (currentStep === 4 && selectedPropertyType === 'HOSTEL') {
                updateHostelCommissionDisplay();
            }
        });
        
        container.appendChild(roomDiv);
        roomCounter++;
        
        updateRoomsDisplay();
        calculateHostelTotalRevenue();
    }
    
    // Add remove functionality to existing rooms (from old input)
    document.querySelectorAll('.remove-room-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const roomDiv = this.closest('.room-item');
            if (roomDiv) {
                roomDiv.remove();
                updateRoomsDisplay();
                renumberRooms();
                calculateHostelTotalRevenue();
            }
        });
    });
    
    // Add price change listeners to existing room inputs
    document.querySelectorAll('.room-price-input').forEach(input => {
        input.addEventListener('input', function() {
            calculateHostelTotalRevenue();
            if (currentStep === 4 && selectedPropertyType === 'HOSTEL') {
                updateHostelCommissionDisplay();
            }
        });
    });
    
    function updateRoomsDisplay() {
        const container = document.getElementById('roomsContainer');
        const noRoomsMessage = document.getElementById('noRoomsMessage');
        
        if (!container || !noRoomsMessage) return;
        
        const hasRooms = container.querySelectorAll('.room-item').length > 0;
        noRoomsMessage.classList.toggle('hidden', hasRooms);
    }
    
    function renumberRooms() {
        document.querySelectorAll('.room-item').forEach((room, index) => {
            const indexSpan = room.querySelector('.room-index');
            if (indexSpan) {
                indexSpan.textContent = index + 1;
            }
            
            room.querySelectorAll('input, select').forEach(input => {
                const name = input.getAttribute('name');
                if (name && name.includes('rooms[')) {
                    const newName = name.replace(/rooms\[\d+\]/, `rooms[${index}]`);
                    input.setAttribute('name', newName);
                }
            });
        });
        roomCounter = document.querySelectorAll('.room-item').length;
    }
    
    // Custom amenities management
    const addCustomAmenityBtn = document.getElementById('addCustomAmenityBtn');
    if (addCustomAmenityBtn) {
        addCustomAmenityBtn.addEventListener('click', function() {
            addCustomAmenity();
        });
    }
    
    function addCustomAmenity() {
        const container = document.getElementById('customAmenitiesContainer');
        if (!container) return;
        
        const amenityDiv = document.createElement('div');
        amenityDiv.className = 'flex gap-4';
        amenityDiv.innerHTML = `
            <input type="text" 
                   name="custom_amenities[]" 
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg" 
                   placeholder="Amenity name">
            <button type="button" class="text-red-500 hover:text-red-700 remove-amenity-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        const removeBtn = amenityDiv.querySelector('.remove-amenity-btn');
        removeBtn.addEventListener('click', function() {
            amenityDiv.remove();
        });
        
        container.appendChild(amenityDiv);
        customAmenityCounter++;
    }
    
    document.querySelectorAll('.remove-amenity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const amenityDiv = this.parentElement;
            if (amenityDiv) {
                amenityDiv.remove();
            }
        });
    });
    
    // Image upload functionality
    const coverImageUpload = document.getElementById('coverImageUpload');
    const coverImageInput = document.getElementById('cover_image');
    const coverImagePreview = document.getElementById('coverImagePreview');
    const coverImagePreviewImg = coverImagePreview ? coverImagePreview.querySelector('img') : null;
    
    if (coverImageUpload && coverImageInput) {
        coverImageUpload.addEventListener('click', () => coverImageInput.click());
        
        coverImageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (coverImagePreviewImg) coverImagePreviewImg.src = e.target.result;
                    if (coverImagePreview) coverImagePreview.classList.remove('hidden');
                    if (coverImageUpload) coverImageUpload.classList.add('hidden');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    }
    
    const removeCoverImageBtn = document.getElementById('removeCoverImage');
    if (removeCoverImageBtn) {
        removeCoverImageBtn.addEventListener('click', function() {
            if (coverImageInput) coverImageInput.value = '';
            if (coverImagePreview) coverImagePreview.classList.add('hidden');
            if (coverImageUpload) coverImageUpload.classList.remove('hidden');
        });
    }
    
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
        if (!galleryGrid) return;
        
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
    
    // Form validation
    function validateCurrentStep() {
        const currentStepElement = document.getElementById(`step${currentStep}`);
        if (!currentStepElement) {
            console.error(`Step ${currentStep} element not found`);
            return true;
        }
        
        // Skip validation for steps that are conditionally hidden
        if (currentStep === 3 && selectedPropertyType === 'APARTMENT') {
            return true; // Skip rooms validation for apartments
        }
        
        const inputs = currentStepElement.querySelectorAll('[required]');
        let isValid = true;
        
        // Clear previous errors
        currentStepElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
        });
        
        // Validate only visible required fields
        inputs.forEach(input => {
            // Skip validation for hidden elements
            if (input.offsetParent === null || input.closest('.hidden')) {
                return;
            }
            
            if (!input.value || input.value.trim() === '') {
                input.classList.add('border-red-500');
                isValid = false;
                
                // Show error message
                let errorElement = input.nextElementSibling;
                if (!errorElement || !errorElement.classList.contains('text-red-500')) {
                    const parent = input.parentNode;
                    errorElement = document.createElement('p');
                    errorElement.className = 'text-red-500 text-sm mt-1';
                    errorElement.textContent = 'This field is required';
                    if (parent) {
                        parent.appendChild(errorElement);
                    }
                }
            } else {
                // Remove error if field is now valid
                input.classList.remove('border-red-500');
                const errorElement = input.nextElementSibling;
                if (errorElement && errorElement.classList.contains('text-red-500')) {
                    errorElement.remove();
                }
            }
        });
        
        // Additional validation for step 3 (hostel rooms)
        if (currentStep === 3 && selectedPropertyType === 'HOSTEL') {
            const rooms = document.querySelectorAll('.room-item');
            if (rooms.length === 0) {
                alert('Please add at least one room for hostel properties');
                isValid = false;
            }
        }
        
        // Additional validation for step 4 (pricing)
        if (currentStep === 4) {
            if (selectedPropertyType === 'APARTMENT') {
                const priceInput = document.getElementById('base_price');
                // Check if the input is visible
                if (priceInput && priceInput.offsetParent !== null) {
                    if (!priceInput.value || parseFloat(priceInput.value) <= 0) {
                        alert('Please enter a valid monthly price for the apartment');
                        priceInput.classList.add('border-red-500');
                        isValid = false;
                    }
                }
            } else if (selectedPropertyType === 'HOSTEL') {
                const rooms = document.querySelectorAll('.room-item');
                if (rooms.length === 0) {
                    alert('Please add at least one room before proceeding to pricing');
                    isValid = false;
                }
            }
        }
        
        // Additional validation for step 6 (images)
        if (currentStep === 6) {
            const coverImage = document.getElementById('cover_image');
            if (coverImage && (!coverImage.files || coverImage.files.length === 0)) {
                alert('Cover image is required');
                isValid = false;
            }
        }
        
        return isValid;
    }
    
    // Save as draft
    const saveDraftBtn = document.getElementById('saveDraftBtn');
    if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
            const statusSelect = document.getElementById('status');
            if (statusSelect) statusSelect.value = 'DRAFT';
            
            let allValid = true;
            for (let i = 1; i <= totalSteps; i++) {
                if (!validateStep(i)) {
                    allValid = false;
                    currentStep = i;
                    showStep(i);
                    break;
                }
            }
            if (allValid) {
                document.getElementById('propertyForm').submit();
            }
        });
    }
    
    function validateStep(step) {
        const stepElement = document.getElementById(`step${step}`);
        if (!stepElement) return true;
        
        const inputs = stepElement.querySelectorAll('[required]');
        let isValid = true;
        
        inputs.forEach(input => {
            // Skip validation for hidden elements
            if (input.offsetParent === null || input.closest('.hidden')) {
                return;
            }
            
            if (!input.value.trim() && input.offsetParent !== null) {
                isValid = false;
            }
        });
        
        return isValid;
    }
    
    // Initialize first step
    showStep(1);
    updatePropertyTypeDependencies();
});
</script>
@endsection