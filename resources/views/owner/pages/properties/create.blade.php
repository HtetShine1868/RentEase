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
                            <span class="text-sm font-medium text-gray-500 mt-2">Amenities</span>
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
        <form id="propertyForm">
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
                                <input type="radio" name="property_type" value="hostel" class="sr-only peer" checked>
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
                                <input type="radio" name="property_type" value="apartment" class="sr-only peer">
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
                    </div>

                    <!-- Property Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Property Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="e.g., Sunshine Apartments">
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                  placeholder="Describe your property, features, and what makes it special..."></textarea>
                    </div>

                    <!-- Bedrooms & Bathrooms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Bedrooms <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Bedroom' : 'Bedrooms' }}</option>
                            @endfor
                            <option value="11">11+ Bedrooms</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Bathrooms <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="">Select</option>
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }} {{ $i == 1 ? 'Bathroom' : 'Bathrooms' }}</option>
                            @endfor
                            <option value="11">11+ Bathrooms</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 2: Location (Hidden by default) -->
            <div class="mb-10 hidden" id="step2">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Location Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="Street address, apartment, suite, etc.">
                    </div>

                    <!-- City & State -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="City name">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            State/Province <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="State or province">
                    </div>

                    <!-- Postal Code & Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Postal Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                               placeholder="ZIP or postal code">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="">Select Country</option>
                            <option value="US">United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                            <option value="IN">India</option>
                        </select>
                    </div>

                    <!-- Map Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select on Map
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg h-64 flex items-center justify-center bg-gray-50">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-600">Click to select location on map</p>
                                <p class="text-sm text-gray-500 mt-1">Latitude: 0.0000 • Longitude: 0.0000</p>
                                <button type="button" class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    Open Map
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pricing (Hidden by default) -->
            <div class="mb-10 hidden" id="step3">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Pricing & Commission</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Monthly Price -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Monthly Price ($) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" 
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                   placeholder="0.00">
                        </div>
                    </div>

                    <!-- Security Deposit -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Security Deposit ($)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">$</span>
                            </div>
                            <input type="number" 
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                   placeholder="Optional">
                        </div>
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
                                <p class="text-xl font-bold text-purple-700">3%</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Monthly Price</span>
                                <span class="font-medium">$1,250.00</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700">Commission (3%)</span>
                                <span class="font-medium text-red-600">-$37.50</span>
                            </div>
                            <div class="pt-3 border-t border-purple-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">You Earn</span>
                                    <span class="text-2xl font-bold text-green-600">$1,212.50</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Minimum Stay -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Stay
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="1">1 Month</option>
                            <option value="3" selected>3 Months</option>
                            <option value="6">6 Months</option>
                            <option value="12">12 Months</option>
                        </select>
                    </div>

                    <!-- Utilities Included -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Utilities Included
                        </label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Electricity</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Water</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Internet</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 4: Amenities (Hidden by default) -->
            <div class="mb-10 hidden" id="step4">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Amenities & Features</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Kitchen -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Kitchen</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Refrigerator</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Stove</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Microwave</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Dishwasher</span>
                            </label>
                        </div>
                    </div>

                    <!-- Living Area -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Living Area</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">TV</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Air Conditioning</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Heating</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Fireplace</span>
                            </label>
                        </div>
                    </div>

                    <!-- Outdoor -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Outdoor</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Balcony</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Garden</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Parking</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Pool</span>
                            </label>
                        </div>
                    </div>

                    <!-- Hostel Specific -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Hostel Facilities</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Common Kitchen</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Laundry Room</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Study Room</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Gym</span>
                            </label>
                        </div>
                    </div>

                    <!-- Safety -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Safety & Security</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Smoke Detector</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Security Cameras</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Safe Deposit Box</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">First Aid Kit</span>
                            </label>
                        </div>
                    </div>

                    <!-- Accessibility -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Accessibility</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Wheelchair Access</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Elevator</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Ramp Access</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Additional Notes -->
                <div class="mt-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Notes
                    </label>
                    <textarea rows="3" 
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                              placeholder="Any other important information about amenities..."></textarea>
                </div>
            </div>

            <!-- Step 5: Images (Hidden by default) -->
            <div class="mb-10 hidden" id="step5">
                <h3 class="text-lg font-semibold text-gray-900 mb-6 pb-4 border-b border-gray-200">Property Images</h3>
                
                <!-- Main Image Upload -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Main Cover Image <span class="text-red-500">*</span>
                        <span class="text-xs text-gray-500">(Recommended: 1200x800px)</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg h-64 flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                        <div class="text-center">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-3"></i>
                            <p class="text-gray-600 font-medium">Click to upload cover image</p>
                            <p class="text-sm text-gray-500 mt-1">or drag and drop</p>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, GIF up to 10MB</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Images -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Additional Images <span class="text-xs text-gray-500">(Up to 10 images)</span>
                    </label>
                    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                        <!-- Image Placeholders -->
                        @for($i = 1; $i <= 5; $i++)
                        <div class="border-2 border-dashed border-gray-300 rounded-lg aspect-square flex items-center justify-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer">
                            <div class="text-center">
                                <i class="fas fa-plus text-gray-400 text-xl"></i>
                                <p class="text-xs text-gray-500 mt-1">Image {{ $i }}</p>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>

                <!-- Image Gallery Preview -->
                <div class="mt-8">
                    <h4 class="font-medium text-gray-900 mb-4">Image Gallery Preview</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @for($i = 1; $i <= 3; $i++)
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <div class="h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                <i class="fas fa-home text-gray-400 text-3xl"></i>
                            </div>
                            <div class="p-3 bg-white">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Image {{ $i }}</span>
                                    <button type="button" class="text-red-500 hover:text-red-700">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endfor
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
                    <button type="button" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentStep = 1;
    const totalSteps = 5;
    
    // Update step indicator
    function updateStepIndicator() {
        // Reset all steps
        document.querySelectorAll('.flex-col.items-center').forEach((step, index) => {
            const stepNumber = index + 1;
            const circle = step.querySelector('.rounded-full');
            const text = step.querySelector('.text-sm');
            
            if (stepNumber < currentStep) {
                // Completed step
                circle.className = 'w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold step-completed';
                text.className = 'text-sm font-medium text-purple-600 mt-2';
            } else if (stepNumber === currentStep) {
                // Current step
                circle.className = 'w-10 h-10 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold';
                text.className = 'text-sm font-medium text-purple-600 mt-2';
            } else {
                // Future step
                circle.className = 'w-10 h-10 rounded-full bg-gray-200 text-gray-400 flex items-center justify-center font-bold';
                text.className = 'text-sm font-medium text-gray-500 mt-2';
            }
        });
        
        // Update connectors
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
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
        }
    });
    
    // Previous button click
    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Form submission
    document.getElementById('propertyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show success message
        alert('Property created successfully! Redirecting to properties page...');
        
        // In real app, this would submit the form
        // window.location.href = "{{ route('owner.properties.index') }}";
    });
    
    // Initialize first step
    showStep(1);
    
    // Image upload interaction
    document.querySelectorAll('.border-dashed').forEach(uploadArea => {
        uploadArea.addEventListener('click', function() {
            alert('Image upload functionality would open file picker in real application.');
        });
        
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('border-purple-500', 'bg-purple-50');
        });
        
        uploadArea.addEventListener('dragleave', function() {
            this.classList.remove('border-purple-500', 'bg-purple-50');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('border-purple-500', 'bg-purple-50');
            alert('Image dropped! In real app, this would upload the file.');
        });
    });
    
    // Commission calculation based on price
    const priceInput = document.querySelector('input[type="number"]');
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const commission = price * 0.03;
            const earnings = price - commission;
            
            // Update commission display (in real app)
            console.log(`Price: $${price}, Commission: $${commission.toFixed(2)}, Earnings: $${earnings.toFixed(2)}`);
        });
    }
});
</script>
@endsection