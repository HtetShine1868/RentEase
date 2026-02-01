phoopyaepyaemaung, [2/1/2026 7:59 PM]
@extends('owner.layout.owner-layout')

@section('title', 'Edit Property - RentEase')
@section('page-title', 'Edit Property')
@section('page-subtitle', 'Update your property details')

@section('content')
<div class="space-y-6">
    <!-- Property Header -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Property</h1>
                <div class="flex items-center gap-4 mt-2">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-purple-100 text-purple-800">
                        <i class="fas fa-building"></i>
                        Apartment
                    </span>
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                        <i class="fas fa-check-circle"></i>
                        Active
                    </span>
                    <span class="text-sm text-gray-600">
                        <i class="fas fa-calendar-alt mr-1"></i>
                        Last updated: {{ date('M d, Y') }}
                    </span>
                </div>
            </div>
            
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-eye mr-2"></i> Preview
                </button>
                <button class="px-4 py-2 bg-red-50 text-red-700 rounded-lg font-medium hover:bg-red-100 transition-colors">
                    <i class="fas fa-trash mr-2"></i> Delete
                </button>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Total Bookings</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">8</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Monthly Revenue</p>
                <p class="text-2xl font-bold text-green-600 mt-1">$1,250</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Occupancy Rate</p>
                <p class="text-2xl font-bold text-purple-600 mt-1">83%</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-500">Rating</p>
                <p class="text-2xl font-bold text-yellow-600 mt-1">4.7/5</p>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <!-- Form Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="flex space-x-8">
                <button class="tab-button active" data-tab="basic">
                    <i class="fas fa-info-circle mr-2"></i> Basic Info
                </button>
                <button class="tab-button" data-tab="location">
                    <i class="fas fa-map-marker-alt mr-2"></i> Location
                </button>
                <button class="tab-button" data-tab="pricing">
                    <i class="fas fa-dollar-sign mr-2"></i> Pricing
                </button>
                <button class="tab-button" data-tab="amenities">
                    <i class="fas fa-star mr-2"></i> Amenities
                </button>
                <button class="tab-button" data-tab="images">
                    <i class="fas fa-images mr-2"></i> Images
                </button>
                <button class="tab-button" data-tab="rooms">
                    <i class="fas fa-door-closed mr-2"></i> Rooms
                </button>
            </nav>
        </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Tab Content -->
        <form id="editPropertyForm">
            <!-- Basic Info Tab (Visible by default) -->
            <div class="tab-content active" id="basic-tab">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Basic Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Property Type (Disabled for editing) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Property Type
                        </label>
                        <div class="p-3 bg-gray-50 border border-gray-200 rounded-lg">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-building text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900">Apartment</p>
                                    <p class="text-xs text-gray-500">Property type cannot be changed</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Property Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               value="Sunshine Apartments"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="draft">Draft</option>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <!-- Gender Policy (for hostels) -->
                    <div id="genderPolicyField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Gender Policy
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="mixed">Mixed (Both genders)</option>
                            <option value="male">Male Only</option>
                            <option value="female">Female Only</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea rows="4" 
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">Beautiful 3-bedroom apartment located in the heart of downtown. Recently renovated with modern amenities, hardwood floors, and stunning city views. Perfect for professionals or small families.</textarea>
                    </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Bedrooms & Bathrooms -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bedrooms <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   value="3"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <button type="button" class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Bathrooms <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-2">
                            <button type="button" class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" 
                                   value="2"
                                   class="flex-1 px-4 py-3 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            <button type="button" class="w-10 h-10 border border-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-50">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Tab -->
            <div class="tab-content hidden" id="location-tab">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Location Details</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Full Address <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               value="123 Sunshine Avenue, Apt 302"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>

                    <!-- City & State -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               value="New York"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            State/Province <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               value="NY"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>

                    <!-- Postal Code & Country -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Postal Code <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               value="10001"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Country <span class="text-red-500">*</span>
                        </label>
                        <select class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <option value="US" selected>United States</option>
                            <option value="UK">United Kingdom</option>
                            <option value="CA">Canada</option>
                            <option value="AU">Australia</option>
                        </select>
                    </div>

                    <!-- Map Preview -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Location on Map
                        </label>
                        <div class="border border-gray-200 rounded-lg h-64 bg-gray-100 flex items-center justify-center">
                            <div class="text-center">
                                <i class="fas fa-map-marked-alt text-gray-400 text-4xl mb-3"></i>
                                <p class="text-gray-600">Map preview</p>
                                <p class="text-sm text-gray-500 mt-1">Latitude: 40.7128 • Longitude: -74.0060</p>
                                <button type="button" class="mt-3 px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                                    Update Location
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Tab -->
            <div class="tab-content hidden" id="pricing-tab">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Pricing & Commission</h3>
                
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
                                   value="1250"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        </div>
                    </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
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
                                   value="500"
                                   class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                        </div>
                    </div>

                    <!-- Commission Display -->
                    <div class="md:col-span-2 bg-purple-50 border border-purple-200 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h4 class="font-medium text-purple-900">Commission Breakdown</h4>
                                <p class="text-sm text-purple-600">Based on apartment type (3%)</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-600">You Earn Monthly</p>
                                <p class="text-2xl font-bold text-green-600">$1,212.50</p>
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
                                    <span class="text-sm text-gray-600">Note: Commission is deducted from each payment</span>
                                </div>
                            </div>
                        </div>
                    </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Additional Pricing -->
                    <div class="md:col-span-2">
                        <h4 class="font-medium text-gray-900 mb-4">Additional Pricing</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Cleaning Fee
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" 
                                           value="50"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pet Fee (per month)
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" 
                                           value="25"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Late Payment Fee
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">$</span>
                                    </div>
                                    <input type="number" 
                                           value="25"
                                           class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Amenities Tab -->
            <div class="tab-content hidden" id="amenities-tab">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Amenities & Features</h3>
                
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
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
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
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
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

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Outdoor -->
                    <div class="space-y-3">
                        <h4 class="font-medium text-gray-900">Outdoor</h4>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Balcony</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Garden</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500" checked>
                                <span class="ml-2 text-gray-700">Parking</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                                <span class="ml-2 text-gray-700">Pool</span>
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
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">High-speed internet included. Fresh linens provided weekly. Cleaning service available upon request.</textarea>
                </div>
            </div>

            <!-- Images Tab -->
            <div class="tab-content hidden" id="images-tab">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Property Images</h3>
                
                <!-- Main Image -->
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Main Cover Image <span class="text-red-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg overflow-hidden">
                        <div class="h-64 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                            <i class="fas fa-home text-gray-400 text-4xl"></i>
                            <button type="button" class="absolute top-4 right-4 bg-white p-2 rounded-lg shadow hover:bg-gray-50">
                                <i class="fas fa-sync-alt text-purple-600"></i>
                            </button>
                        </div>
                        <div class="p-4 bg-white border-t border-gray-200">
                            <p class="text-sm text-gray-600">Current cover image</p>
                            <button type="button" class="mt-2 text-sm text-purple-600 hover:text-purple-700">
                                <i class="fas fa-upload mr-1"></i> Change Image
                            </button>
                        </div>
                    </div>
                </div>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Image Gallery -->
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="font-medium text-gray-900">Image Gallery</h4>
                        <button type="button" class="text-sm text-purple-600 hover:text-purple-700">
                            <i class="fas fa-plus mr-1"></i> Add More Images
                        </button>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        @for($i = 1; $i <= 4; $i++)
                        <div class="border border-gray-200 rounded-lg overflow-hidden group">
                            <div class="h-40 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center relative">
                                <i class="fas fa-image text-gray-300 text-2xl"></i>
                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all flex items-center justify-center opacity-0 group-hover:opacity-100">
                                    <div class="flex gap-2">
                                        <button type="button" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100">
                                            <i class="fas fa-eye text-gray-700"></i>
                                        </button>
                                        <button type="button" class="w-8 h-8 bg-white rounded-full flex items-center justify-center hover:bg-gray-100">
                                            <i class="fas fa-trash text-red-600"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3 bg-white">
                                <p class="text-sm text-gray-600">Image {{ $i }}</p>
                            </div>
                        </div>
                        @endfor
                        
                        <!-- Add Image Card -->
                        <div class="border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center h-40 cursor-pointer hover:border-purple-400 hover:bg-purple-50 transition-colors">
                            <div class="text-center">
                                <i class="fas fa-plus text-gray-400 text-2xl mb-2"></i>
                                <p class="text-sm text-gray-600">Add Image</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rooms Tab (for hostels) -->
            <div class="tab-content hidden" id="rooms-tab">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Room Management</h3>
                    <a href="{{ route('owner.properties.rooms.index', $propertyId ?? 1) }}" 
                       class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                        <i class="fas fa-door-closed mr-2"></i> Manage All Rooms
                    </a>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle text-yellow-600 mr-3"></i>
                        <div>
                            <p class="text-sm text-yellow-800">This property is an apartment. Room management is only available for hostel properties.</p>
                            <p class="text-sm text-yellow-700 mt-1">Switch to hostel type to manage individual rooms.</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<!-- Form Actions -->
        <div class="flex justify-between items-center pt-8 border-t border-gray-200 mt-8">
            <div>
                <button type="button" onclick="window.history.back()" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </button>
            </div>
            
            <div class="flex items-center gap-4">
                <button type="button" class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    Save as Draft
                </button>
                <button type="submit" form="editPropertyForm" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition-colors">
                    <i class="fas fa-save mr-2"></i> Update Property
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Tab styling */
.tab-button {
    padding: 12px 0;
    font-size: 14px;
    font-weight: 500;
    color: #6b7280;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    position: relative;
}

.tab-button:hover {
    color: #7c3aed;
}

.tab-button.active {
    color: #7c3aed;
    border-bottom-color: #7c3aed;
}

.tab-content {
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Number input buttons */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}

/* Image hover effects */
.group:hover .group-hover\:bg-opacity-40 {
    background-color: rgba(0, 0, 0, 0.4);
}

/* Checkbox styling */
input[type="checkbox"]:checked {
    background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
}
</style>

phoopyaepyaemaung, [2/1/2026 7:59 PM]
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabId = this.getAttribute('data-tab');
            
            // Update active tab button
            tabButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Show corresponding tab content
            tabContents.forEach(content => {
                content.classList.remove('active');
                content.classList.add('hidden');
            });
            
            const activeTab = document.getElementById(${tabId}-tab);
            if (activeTab) {
                activeTab.classList.remove('hidden');
                activeTab.classList.add('active');
            }
        });
    });
    
    // Number input increment/decrement
    document.querySelectorAll('button').forEach(button => {
        if (button.innerHTML.includes('fa-plus')  button.innerHTML.includes('fa-minus')) {
            button.addEventListener('click', function() {
                const input = this.parentNode.querySelector('input[type="number"]');
                if (input) {
                    const currentValue = parseInt(input.value)  0;
                    if (this.innerHTML.includes('fa-plus')) {
                        input.value = currentValue + 1;
                    } else {
                        if (currentValue > 0) {
                            input.value = currentValue - 1;
                        }
                    }
                    
                    // Trigger change event
                    input.dispatchEvent(new Event('change'));
                }
            });
        }
    });
    
    // Form submission
    document.getElementById('editPropertyForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Saving...';
        submitBtn.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            // Show success message
            alert('Property updated successfully!');
            
            // Reset button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            
            // In real app, redirect or update UI
            // window.location.href = "{{ route('owner.properties.index') }}";
        }, 1500);
    });
    
    // Image upload simulation
    document.querySelectorAll('.cursor-pointer').forEach(uploadArea => {
        if (uploadArea.innerHTML.includes('Add Image')) {
            uploadArea.addEventListener('click', function() {
                alert('Image upload dialog would open here in the real application.');
            });
        }
    });
    
    // Image deletion
    document.querySelectorAll('.fa-trash').forEach(deleteBtn => {
        deleteBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            if (confirm('Are you sure you want to delete this image?')) {
                const imageCard = this.closest('.group');
                if (imageCard) {
                    imageCard.style.opacity = '0.5';
                    setTimeout(() => {
                        imageCard.remove();
                    }, 300);
                }
            }
        });
    });
    
    // Update commission display when price changes
    const priceInput = document.querySelector('input[type="number"][value="1250"]');
    if (priceInput) {
        priceInput.addEventListener('input', function() {
            const price = parseFloat(this.value) || 0;
            const commission = price * 0.03;
            const earnings = price - commission;

phoopyaepyaemaung, [2/1/2026 7:59 PM]
// Update commission display (in real app, you'd update the DOM)
            console.log(Updated: Price: $${price}, Commission: $${commission.toFixed(2)}, You Earn: $${earnings.toFixed(2)});
        });
    }
});
</script>
@endsection 