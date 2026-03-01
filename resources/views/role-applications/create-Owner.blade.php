@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Apply as Property Owner</h1>
                <p class="mt-2 text-gray-600">Fill out the form below to become a property owner on RMS.</p>
            </div>
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-home text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Display -->
    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-circle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">
                        Please fix the following errors:
                    </h3>
                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

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
                        <li>Property ownership proof (Deed/Tax Receipt) for each property</li>
                        <li>Valid identification document</li>
                        <li>Property location details with coordinates</li>
                        <li>Contact information</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('role.apply.store', 'OWNER') }}" enctype="multipart/form-data" id="ownerApplicationForm">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700">
                                Business/Company Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('business_name') border-red-500 @enderror"
                                       placeholder="e.g., Green Valley Properties">
                            </div>
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Type -->
                        <div>
                            <label for="property_type" class="block text-sm font-medium text-gray-700">
                                Primary Property Type <span class="text-red-500">*</span>
                            </label>
                            <select id="property_type" name="property_type" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('property_type') border-red-500 @enderror">
                                <option value="">Select Property Type</option>
                                <option value="HOSTEL" {{ old('property_type') == 'HOSTEL' ? 'selected' : '' }}>Hostel</option>
                                <option value="APARTMENT" {{ old('property_type') == 'APARTMENT' ? 'selected' : '' }}>Apartment Building</option>
                                <option value="BOTH" {{ old('property_type') == 'BOTH' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('property_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Count -->
                        <div>
                            <label for="property_count" class="block text-sm font-medium text-gray-700">
                                Number of Properties <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="property_count" name="property_count" value="{{ old('property_count', 1) }}" required min="1" max="20"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('property_count') border-red-500 @enderror">
                            <p class="mt-1 text-xs text-gray-500">Maximum 20 properties per application</p>
                            @error('property_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Years of Experience -->
                        <div>
                            <label for="years_experience" class="block text-sm font-medium text-gray-700">
                                Years of Experience <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="years_experience" name="years_experience" value="{{ old('years_experience', 0) }}" required min="0"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('years_experience') border-red-500 @enderror">
                            @error('years_experience')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_person') border-red-500 @enderror"
                                       placeholder="Full name of contact person">
                            </div>
                            @error('contact_person')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_email') border-red-500 @enderror"
                                       placeholder="contact@example.com">
                            </div>
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('contact_phone') border-red-500 @enderror"
                                       placeholder="+959XXXXXXXXX">
                            </div>
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('alt_phone') border-red-500 @enderror"
                                       placeholder="+959XXXXXXXXX">
                            </div>
                            @error('alt_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Primary Business Location</h3>
                    
                    <!-- Business Address -->
                    <div class="mb-4">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Business Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" name="business_address" rows="2" required
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('business_address') border-red-500 @enderror">{{ old('business_address') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- City/Town -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City/Town <span class="text-red-500">*</span>
                            </label>
                            <select id="city" name="city" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('city') border-red-500 @enderror">
                                <option value="">Select City/Town</option>
                                <option value="Yangon" {{ old('city') == 'Yangon' ? 'selected' : '' }}>Yangon</option>
                                <option value="Mandalay" {{ old('city') == 'Mandalay' ? 'selected' : '' }}>Mandalay</option>
                                <option value="Naypyidaw" {{ old('city') == 'Naypyidaw' ? 'selected' : '' }}>Naypyidaw</option>
                                <option value="Bago" {{ old('city') == 'Bago' ? 'selected' : '' }}>Bago</option>
                                <option value="Mawlamyine" {{ old('city') == 'Mawlamyine' ? 'selected' : '' }}>Mawlamyine</option>
                                <option value="Pathein" {{ old('city') == 'Pathein' ? 'selected' : '' }}>Pathein</option>
                                <option value="Pyay" {{ old('city') == 'Pyay' ? 'selected' : '' }}>Pyay</option>
                                <option value="Meiktila" {{ old('city') == 'Meiktila' ? 'selected' : '' }}>Meiktila</option>
                                <option value="Myeik" {{ old('city') == 'Myeik' ? 'selected' : '' }}>Myeik</option>
                                <option value="Taunggyi" {{ old('city') == 'Taunggyi' ? 'selected' : '' }}>Taunggyi</option>
                                <option value="Sittwe" {{ old('city') == 'Sittwe' ? 'selected' : '' }}>Sittwe</option>
                                <option value="Hpa-An" {{ old('city') == 'Hpa-An' ? 'selected' : '' }}>Hpa-An</option>
                                <option value="Lashio" {{ old('city') == 'Lashio' ? 'selected' : '' }}>Lashio</option>
                                <option value="Monywa" {{ old('city') == 'Monywa' ? 'selected' : '' }}>Monywa</option>
                                <option value="Myitkyina" {{ old('city') == 'Myitkyina' ? 'selected' : '' }}>Myitkyina</option>
                                <option value="Dawei" {{ old('city') == 'Dawei' ? 'selected' : '' }}>Dawei</option>
                                <option value="Magway" {{ old('city') == 'Magway' ? 'selected' : '' }}>Magway</option>
                                <option value="Pakokku" {{ old('city') == 'Pakokku' ? 'selected' : '' }}>Pakokku</option>
                                <option value="other_city">Other</option>
                            </select>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Other City (shown when "Other" selected) -->
                        <div id="other_city_container" class="hidden">
                            <label for="other_city" class="block text-sm font-medium text-gray-700">
                                Enter City/Town Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="other_city" name="other_city" value="{{ old('other_city') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Enter city/town name">
                        </div>

                        <!-- State/Region -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">
                                State/Region <span class="text-red-500">*</span>
                            </label>
                            <select id="state" name="state" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('state') border-red-500 @enderror">
                                <option value="">Select State/Region</option>
                                <option value="Yangon Region" {{ old('state') == 'Yangon Region' ? 'selected' : '' }}>Yangon Region</option>
                                <option value="Mandalay Region" {{ old('state') == 'Mandalay Region' ? 'selected' : '' }}>Mandalay Region</option>
                                <option value="Naypyidaw Union Territory" {{ old('state') == 'Naypyidaw Union Territory' ? 'selected' : '' }}>Naypyidaw Union Territory</option>
                                <option value="Ayeyarwady Region" {{ old('state') == 'Ayeyarwady Region' ? 'selected' : '' }}>Ayeyarwady Region</option>
                                <option value="Bago Region" {{ old('state') == 'Bago Region' ? 'selected' : '' }}>Bago Region</option>
                                <option value="Magway Region" {{ old('state') == 'Magway Region' ? 'selected' : '' }}>Magway Region</option>
                                <option value="Sagaing Region" {{ old('state') == 'Sagaing Region' ? 'selected' : '' }}>Sagaing Region</option>
                                <option value="Tanintharyi Region" {{ old('state') == 'Tanintharyi Region' ? 'selected' : '' }}>Tanintharyi Region</option>
                                <option value="Chin State" {{ old('state') == 'Chin State' ? 'selected' : '' }}>Chin State</option>
                                <option value="Kachin State" {{ old('state') == 'Kachin State' ? 'selected' : '' }}>Kachin State</option>
                                <option value="Kayah State" {{ old('state') == 'Kayah State' ? 'selected' : '' }}>Kayah State</option>
                                <option value="Kayin State" {{ old('state') == 'Kayin State' ? 'selected' : '' }}>Kayin State</option>
                                <option value="Mon State" {{ old('state') == 'Mon State' ? 'selected' : '' }}>Mon State</option>
                                <option value="Rakhine State" {{ old('state') == 'Rakhine State' ? 'selected' : '' }}>Rakhine State</option>
                                <option value="Shan State" {{ old('state') == 'Shan State' ? 'selected' : '' }}>Shan State</option>
                                <option value="other_state">Other</option>
                            </select>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Other State (shown when "Other" selected) -->
                        <div id="other_state_container" class="hidden">
                            <label for="other_state" class="block text-sm font-medium text-gray-700">
                                Enter State/Region Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="other_state" name="other_state" value="{{ old('other_state') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Enter state/region name">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Township -->
                        <div>
                            <label for="township" class="block text-sm font-medium text-gray-700">
                                Township <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="township" name="township" value="{{ old('township') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('township') border-red-500 @enderror"
                                   placeholder="e.g., Hlaing, Bahan, Chanayethazan">
                            @error('township')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Ward/Quarter -->
                        <div>
                            <label for="ward" class="block text-sm font-medium text-gray-700">
                                Ward/Quarter <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="ward" name="ward" value="{{ old('ward') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('ward') border-red-500 @enderror"
                                   placeholder="e.g., Ward 5, Quarter A">
                            @error('ward')
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
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('postal_code') border-red-500 @enderror"
                                   placeholder="e.g., 11011">
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
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('country') border-red-500 @enderror">
                                <option value="Myanmar" {{ old('country', 'Myanmar') == 'Myanmar' ? 'selected' : '' }}>Myanmar</option>
                                <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                                <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                                <option value="Bangladesh" {{ old('country') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                <option value="Sri Lanka" {{ old('country') == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka</option>
                                <option value="other_country">Other</option>
                            </select>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Other Country (shown when "Other" selected) -->
                        <div id="other_country_container" class="hidden">
                            <label for="other_country" class="block text-sm font-medium text-gray-700">
                                Enter Country Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="other_country" name="other_country" value="{{ old('other_country') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="Enter country name">
                        </div>
                    </div>

                    <!-- Latitude & Longitude Fields (ADD THESE) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">
                                Latitude <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.000001" id="latitude" name="latitude" 
                                       value="{{ old('latitude') }}" required
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('latitude') border-red-500 @enderror"
                                       placeholder="e.g., 16.8661">
                                <p class="mt-1 text-xs text-gray-500">e.g., 16.8661 for Yangon</p>
                            </div>
                            @error('latitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="longitude" class="block text-sm font-medium text-gray-700">
                                Longitude <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.000001" id="longitude" name="longitude" 
                                       value="{{ old('longitude') }}" required
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('longitude') border-red-500 @enderror"
                                       placeholder="e.g., 96.1951">
                                <p class="mt-1 text-xs text-gray-500">e.g., 96.1951 for Yangon</p>
                            </div>
                            @error('longitude')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Map Link (Optional) -->
                    <div>
                        <label for="map_link" class="block text-sm font-medium text-gray-700">
                            Google Maps Link (Optional)
                        </label>
                        <input type="url" id="map_link" name="map_link" value="{{ old('map_link') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('map_link') border-red-500 @enderror"
                               placeholder="https://maps.google.com/?q=...">
                        <p class="mt-1 text-xs text-gray-500">Share your exact location on Google Maps for better navigation</p>
                        @error('map_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dynamic Property Documents Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                        Property Ownership Documents
                        <span class="text-sm font-normal text-gray-500 ml-2">(Upload proof for each property)</span>
                    </h3>
                    
                    <div id="property_documents_container">
                        <!-- Documents will be dynamically added here -->
                    </div>

                    <div class="mt-4 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                        <span class="font-medium">Important:</span> You need to upload ownership proof for each property you own. 
                        Accepted documents: Property Deed, Tax Receipt, Utility Bill (with ownership proof), or Municipal License.
                    </div>
                </div>

                <!-- Document Upload for Identification (ADD THIS) -->
<!-- Document Upload for Identification (FIXED: Changed name from id_document to document) -->
<div>
    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Identification Document</h3>
    <div class="mb-6">
        <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
            Upload Valid ID (NID/Passport/Driver's License) <span class="text-red-500">*</span>
        </label>
        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
            <div class="space-y-1 text-center">
                <i class="fas fa-id-card text-gray-400 text-3xl mx-auto"></i>
                <div class="flex text-sm text-gray-600">
                    <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                        <span>Upload ID document</span>
                        <input id="document" name="document" type="file" 
                               accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                    </label>
                    <p class="pl-1">or drag and drop</p>
                </div>
                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
            </div>
        </div>
        @error('document')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

                <!-- Commission Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Commission Information
                    </h4>
                    <p class="text-sm text-blue-700">
                        As a Property Owner, you'll pay <strong>3% commission</strong> on apartment bookings and 
                        <strong>5% commission</strong> on hostel bookings. This commission will be automatically deducted 
                        from your earnings.
                    </p>
                </div>

                <!-- Terms Agreement -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="terms" name="terms" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error('terms') border-red-500 @enderror">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I confirm that I own all the properties listed and the documents provided are authentic
                            </label>
                            <p class="text-gray-500">By submitting this application, you confirm that all information provided is accurate and you agree to follow RMS guidelines for property owners.</p>
                        </div>
                    </div>
                    @error('terms')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
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
document.addEventListener('DOMContentLoaded', function() {
    const propertyCountInput = document.getElementById('property_count');
    const documentsContainer = document.getElementById('property_documents_container');
    const oldPropertyNames = @json(old('property_names', []));
    const oldPropertyDocTypes = @json(old('property_doc_types', []));
    const oldPropertyNotes = @json(old('property_notes', []));

    function generatePropertyDocuments(count) {
        let html = '';
        for (let i = 1; i <= count; i++) {
            const oldName = oldPropertyNames[i-1] || '';
            const oldDocType = oldPropertyDocTypes[i-1] || '';
            const oldNote = oldPropertyNotes[i-1] || '';
            
            html += `
                <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <h4 class="text-md font-medium text-gray-800 mb-3">
                        Property #${i} Documents
                        <span class="text-sm font-normal text-gray-500 ml-2">(Upload ownership proof)</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <!-- Property Name/Address -->
                        <div class="md:col-span-2">
                            <label for="property_name_${i}" class="block text-sm font-medium text-gray-700">
                                Property Name/Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="property_name_${i}" name="property_names[]" value="${oldName.replace(/"/g, '&quot;')}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Green Valley Apartment - House 12, Road 5, Hlaing Township">
                        </div>
                        
                        <!-- Document Type -->
                        <div>
                            <label for="property_doc_type_${i}" class="block text-sm font-medium text-gray-700">
                                Document Type <span class="text-red-500">*</span>
                            </label>
                            <select id="property_doc_type_${i}" name="property_doc_types[]" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Document Type</option>
                                <option value="DEED" ${oldDocType === 'DEED' ? 'selected' : ''}>Property Deed</option>
                                <option value="TAX_RECEIPT" ${oldDocType === 'TAX_RECEIPT' ? 'selected' : ''}>Tax Receipt</option>
                                <option value="UTILITY_BILL" ${oldDocType === 'UTILITY_BILL' ? 'selected' : ''}>Utility Bill (with ownership proof)</option>
                                <option value="MUNICIPAL_LICENSE" ${oldDocType === 'MUNICIPAL_LICENSE' ? 'selected' : ''}>Municipal License</option>
                                <option value="OTHER" ${oldDocType === 'OTHER' ? 'selected' : ''}>Other Ownership Proof</option>
                            </select>
                        </div>
                        
                        <!-- Document Upload -->
                        <div>
                            <label for="property_doc_${i}" class="block text-sm font-medium text-gray-700">
                                Upload Document <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="file" id="property_doc_${i}" name="property_documents[]" 
                                       accept=".pdf,.jpg,.jpeg,.png" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                        </div>
                    </div>
                    
                    <!-- Additional Notes (Optional) -->
                    <div>
                        <label for="property_notes_${i}" class="block text-sm font-medium text-gray-700">
                            Additional Notes (Optional)
                        </label>
                        <textarea id="property_notes_${i}" name="property_notes[]" rows="1"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Any additional information about this property">${oldNote.replace(/"/g, '&quot;')}</textarea>
                    </div>
                </div>
            `;
        }
        return html;
    }

    function updateDocuments() {
        const count = parseInt(propertyCountInput.value) || 1;
        documentsContainer.innerHTML = generatePropertyDocuments(count);
    }

    propertyCountInput.addEventListener('input', updateDocuments);
    
    // Initialize with default value
    updateDocuments();

    // Handle "Other" city selection
    const citySelect = document.getElementById('city');
    const otherCityContainer = document.getElementById('other_city_container');
    const otherCityInput = document.getElementById('other_city');

    if (citySelect) {
        citySelect.addEventListener('change', function() {
            if (this.value === 'other_city') {
                otherCityContainer.classList.remove('hidden');
                otherCityInput.required = true;
            } else {
                otherCityContainer.classList.add('hidden');
                otherCityInput.required = false;
            }
        });

        // Trigger on page load if "other" was selected
        if (citySelect.value === 'other_city') {
            otherCityContainer.classList.remove('hidden');
            otherCityInput.required = true;
        }
    }

    // Handle "Other" state selection
    const stateSelect = document.getElementById('state');
    const otherStateContainer = document.getElementById('other_state_container');
    const otherStateInput = document.getElementById('other_state');

    if (stateSelect) {
        stateSelect.addEventListener('change', function() {
            if (this.value === 'other_state') {
                otherStateContainer.classList.remove('hidden');
                otherStateInput.required = true;
            } else {
                otherStateContainer.classList.add('hidden');
                otherStateInput.required = false;
            }
        });

        // Trigger on page load if "other" was selected
        if (stateSelect.value === 'other_state') {
            otherStateContainer.classList.remove('hidden');
            otherStateInput.required = true;
        }
    }

    // Handle "Other" country selection
    const countrySelect = document.getElementById('country');
    const otherCountryContainer = document.getElementById('other_country_container');
    const otherCountryInput = document.getElementById('other_country');

    if (countrySelect) {
        countrySelect.addEventListener('change', function() {
            if (this.value === 'other_country') {
                otherCountryContainer.classList.remove('hidden');
                otherCountryInput.required = true;
            } else {
                otherCountryContainer.classList.add('hidden');
                otherCountryInput.required = false;
            }
        });

        // Trigger on page load if "other" was selected
        if (countrySelect.value === 'other_country') {
            otherCountryContainer.classList.remove('hidden');
            otherCountryInput.required = true;
        }
    }

    // File validation
    document.addEventListener('change', function(e) {
        if (e.target.type === 'file') {
            const file = e.target.files[0];
            if (file) {
                const fileSize = file.size / 1024 / 1024; // in MB
                const maxSize = 2;
                
                if (fileSize > maxSize) {
                    alert(`File size must be less than ${maxSize}MB`);
                    e.target.value = '';
                }
                
                const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Only PDF, JPG, JPEG, and PNG files are allowed');
                    e.target.value = '';
                }
            }
        }
    });

    // Form submission loading state
    const form = document.getElementById('ownerApplicationForm');
    const submitBtn = form?.querySelector('button[type="submit"]');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            // Disable submit button to prevent double submission
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
            return true;
        });
    }
});
</script>
@endpush
@endsection