@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Apply as Laundry Provider</h1>
                <p class="mt-2 text-gray-600">Fill out the form below to become a laundry service provider on RMS.</p>
            </div>
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-tshirt text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Requirements</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Business registration certificate</li>
                        <li>Laundry facility photos</li>
                        <li>List of laundry services with pricing</li>
                        <li>Pickup and delivery coverage area</li>
                        <li>Contact information</li>
                        <li>Equipment and capacity details</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('role.apply.store', 'LAUNDRY') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div class="md:col-span-2">
                            <label for="business_name" class="block text-sm font-medium text-gray-700">
                                Laundry Business Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-tshirt text-gray-400"></i>
                                </div>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g., Fresh & Clean Laundry">
                            </div>
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Types -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Services Offered <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $serviceTypes = [
                                        'WASH_DRY' => 'Wash & Dry',
                                        'DRY_CLEAN' => 'Dry Cleaning', 
                                        'PRESS_ONLY' => 'Press Only',
                                        'FOLD_ONLY' => 'Fold Only',
                                        'STAIN_REMOVAL' => 'Stain Removal',
                                        'WASH_FOLD' => 'Wash & Fold',
                                        'IRONING' => 'Ironing',
                                        'CURTAIN' => 'Curtain Cleaning'
                                    ];
                                @endphp
                                @foreach($serviceTypes as $value => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="service_type_{{ $value }}" name="service_types[]" 
                                               value="{{ $value }}" 
                                               {{ in_array($value, old('service_types', [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="service_type_{{ $value }}" class="ml-2 text-sm text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('service_types')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Item Types Accepted -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Item Types Accepted <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $itemTypes = [
                                        'CLOTHING' => 'Clothing',
                                        'BEDDING' => 'Bedding', 
                                        'CURTAIN' => 'Curtains',
                                        'LINENS' => 'Linens',
                                        'TOWELS' => 'Towels',
                                        'TRADITIONAL' => 'Traditional Wear',
                                        'SUITS' => 'Suits',
                                        'OTHER' => 'Other'
                                    ];
                                @endphp
                                @foreach($itemTypes as $value => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="item_type_{{ $value }}" name="item_types[]" 
                                               value="{{ $value }}" 
                                               {{ in_array($value, old('item_types', [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="item_type_{{ $value }}" class="ml-2 text-sm text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('item_types')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Hours -->
                        <div>
                            <label for="opening_time" class="block text-sm font-medium text-gray-700">
                                Opening Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time', '09:00') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('opening_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="closing_time" class="block text-sm font-medium text-gray-700">
                                Closing Time <span class="text-red-500">*</span>
                            </label>
                            <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time', '20:00') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('closing_time')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Maximum Daily Capacity -->
                        <div>
                            <label for="daily_capacity" class="block text-sm font-medium text-gray-700">
                                Daily Capacity (kg) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="daily_capacity" name="daily_capacity" value="{{ old('daily_capacity', 50) }}" required min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">kg</span>
                                </div>
                            </div>
                            @error('daily_capacity')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Turnaround Time -->
                        <div>
                            <label for="turnaround_hours" class="block text-sm font-medium text-gray-700">
                                Standard Turnaround Time <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="turnaround_hours" name="turnaround_hours" value="{{ old('turnaround_hours', 48) }}" required min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">hours</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Standard time to complete laundry (in hours)</p>
                            @error('turnaround_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rush Service Available -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="rush_service_available" name="rush_service_available" value="1" 
                                       {{ old('rush_service_available') ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="rush_service_available" class="ml-2 text-sm text-gray-700">
                                    Offer Rush/Express Service (additional charges apply)
                                </label>
                            </div>
                        </div>

                        <!-- Rush Turnaround Time -->
                        <div id="rush_time_container" class="md:col-span-2 {{ old('rush_service_available') ? '' : 'hidden' }}">
                            <label for="rush_turnaround_hours" class="block text-sm font-medium text-gray-700">
                                Rush Service Turnaround Time
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="rush_turnaround_hours" name="rush_turnaround_hours" 
                                       value="{{ old('rush_turnaround_hours', 24) }}" min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">hours</span>
                                </div>
                            </div>
                        </div>

                        <!-- Service Radius -->
                        <div class="md:col-span-2">
                            <label for="service_radius_km" class="block text-sm font-medium text-gray-700">
                                Pickup & Delivery Coverage Radius (km) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" id="service_radius_km" name="service_radius_km" 
                                       value="{{ old('service_radius_km', 5) }}" required min="1" max="50"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">km</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum distance you can provide pickup and delivery services</p>
                            @error('service_radius_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pickup Service -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="provides_pickup" name="provides_pickup" value="1" 
                                       {{ old('provides_pickup', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="provides_pickup" class="ml-2 text-sm text-gray-700">
                                    Provide Free Pickup & Delivery Service
                                </label>
                            </div>
                        </div>

                        <!-- Pickup Fee -->
                        <div id="pickup_fee_container" class="md:col-span-2 {{ old('provides_pickup', true) ? 'hidden' : '' }}">
                            <label for="pickup_fee" class="block text-sm font-medium text-gray-700">
                                Pickup & Delivery Fee
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">৳</span>
                                </div>
                                <input type="number" step="0.01" id="pickup_fee" name="pickup_fee" value="{{ old('pickup_fee', 50) }}"
                                       class="pl-7 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Fee charged for pickup and delivery (if not free)</p>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Contact Email -->
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                Contact Email <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Contact Phone -->
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                                Contact Phone <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>

                        <!-- Alternative Phone -->
                        <div>
                            <label for="alt_phone" class="block text-sm font-medium text-gray-700">
                                Alternative Phone
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone-alt text-gray-400"></i>
                                </div>
                                <input type="text" id="alt_phone" name="alt_phone" value="{{ old('alt_phone') }}"
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information (Updated) -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Location Information</h3>
                    
                    <!-- Business Address -->
                    <div class="mb-4">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Street Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" name="business_address" rows="2" required
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="House/Building No, Road/Street, Area">{{ old('business_address') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City <span class="text-red-500">*</span>
                            </label>
                            <select id="city" name="city" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select City</option>
                                <option value="Dhaka" {{ old('city') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ old('city') == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                <option value="Rajshahi" {{ old('city') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ old('city') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Sylhet" {{ old('city') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Barisal" {{ old('city') == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                                <option value="Rangpur" {{ old('city') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ old('city') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                                <option value="Comilla" {{ old('city') == 'Comilla' ? 'selected' : '' }}>Comilla</option>
                                <option value="Narayanganj" {{ old('city') == 'Narayanganj' ? 'selected' : '' }}>Narayanganj</option>
                                <option value="Gazipur" {{ old('city') == 'Gazipur' ? 'selected' : '' }}>Gazipur</option>
                                <option value="other">Other</option>
                            </select>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State/Division -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">
                                Division <span class="text-red-500">*</span>
                            </label>
                            <select id="state" name="state" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Division</option>
                                <option value="Dhaka" {{ old('state') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ old('state') == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                <option value="Rajshahi" {{ old('state') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ old('state') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Sylhet" {{ old('state') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Barisal" {{ old('state') == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                                <option value="Rangpur" {{ old('state') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ old('state') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                            </select>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., 1205">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select id="country" name="country" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="Bangladesh" {{ old('country', 'Bangladesh') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                                <option value="Pakistan" {{ old('country') == 'Pakistan' ? 'selected' : '' }}>Pakistan</option>
                                <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal</option>
                                <option value="Sri Lanka" {{ old('country') == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka</option>
                                <option value="Maldives" {{ old('country') == 'Maldives' ? 'selected' : '' }}>Maldives</option>
                                <option value="other">Other</option>
                            </select>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Area/Locality -->
                    <div class="mb-4">
                        <label for="area" class="block text-sm font-medium text-gray-700">
                            Area/Locality <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="area" name="area" value="{{ old('area') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., Gulshan, Banani, Dhanmondi">
                        @error('area')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map Link (Optional) -->
                    <div>
                        <label for="map_link" class="block text-sm font-medium text-gray-700">
                            Google Maps Link (Optional)
                        </label>
                        <input type="url" id="map_link" name="map_link" value="{{ old('map_link') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="https://maps.google.com/?q=...">
                        <p class="mt-1 text-xs text-gray-500">Share your exact location on Google Maps for better navigation</p>
                        @error('map_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Document Upload -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Document Upload</h3>
                    
                    <!-- Business Registration -->
                    <div class="mb-6">
                        <label for="business_registration" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Registration Certificate <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-pdf text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="business_registration" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload a file</span>
                                        <input id="business_registration" name="business_registration" type="file" 
                                               accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Price List -->
                    <div class="mb-6">
                        <label for="price_list" class="block text-sm font-medium text-gray-700 mb-2">
                            Price List / Rate Card <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-excel text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="price_list" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload price list</span>
                                        <input id="price_list" name="price_list" type="file" 
                                               accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx" required class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PDF, Excel, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Facility Photos -->
                    <div>
                        <label for="facility_photos" class="block text-sm font-medium text-gray-700 mb-2">
                            Facility Photos <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-images text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="facility_photos" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload photos</span>
                                        <input id="facility_photos" name="facility_photos[]" type="file" multiple
                                               accept=".jpg,.jpeg,.png" required class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">JPEG, PNG up to 5MB each (max 5 photos)</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Commission Information
                    </h4>
                    <p class="text-sm text-blue-700">
                        As a Laundry Service Provider, you'll pay <strong>10% commission</strong> on all laundry orders. 
                        This commission will be automatically deducted from your earnings.
                    </p>
                </div>

                <!-- Terms Agreement -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="terms" name="terms" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I agree to the terms and conditions
                            </label>
                            <p class="text-gray-500">By submitting this application, you confirm that all information provided is accurate and you agree to follow RMS guidelines for laundry service providers.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('role.apply.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Toggle rush time input based on checkbox
    document.getElementById('rush_service_available').addEventListener('change', function() {
        const rushContainer = document.getElementById('rush_time_container');
        if (this.checked) {
            rushContainer.classList.remove('hidden');
        } else {
            rushContainer.classList.add('hidden');
        }
    });

    // Toggle pickup fee input based on checkbox
    document.getElementById('provides_pickup').addEventListener('change', function() {
        const feeContainer = document.getElementById('pickup_fee_container');
        if (this.checked) {
            feeContainer.classList.add('hidden');
        } else {
            feeContainer.classList.remove('hidden');
        }
    });

    // Show/hide other city input if needed (optional enhancement)
    document.getElementById('city').addEventListener('change', function() {
        // You can add logic here to show a text input if "other" is selected
    });
</script>
@endpush
@endsection