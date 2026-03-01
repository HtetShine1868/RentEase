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
        <form method="POST" action="{{ route('role.apply.store', 'LAUNDRY') }}" enctype="multipart/form-data" id="laundryApplicationForm">
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('business_name') border-red-500 @enderror"
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
                                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error('service_types') border-red-500 @enderror">
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

                        <!-- Has Pickup Service (Boolean field required by controller) -->
                        <div class="md:col-span-2">
                            <div class="flex items-center">
                                <input type="checkbox" id="has_pickup_service" name="has_pickup_service" value="1" 
                                       {{ old('has_pickup_service', true) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded @error('has_pickup_service') border-red-500 @enderror">
                                <label for="has_pickup_service" class="ml-2 text-sm text-gray-700">
                                    Provide Pickup & Delivery Service
                                </label>
                            </div>
                            @error('has_pickup_service')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Radius -->
                        <div>
                            <label for="service_radius_km" class="block text-sm font-medium text-gray-700">
                                Service Coverage Radius (km) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" id="service_radius_km" name="service_radius_km" 
                                       value="{{ old('service_radius_km', 5) }}" required min="1" max="50"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('service_radius_km') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">km</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum distance you can provide pickup and delivery services</p>
                            @error('service_radius_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Normal Turnaround Hours -->
                        <div>
                            <label for="normal_turnaround_hours" class="block text-sm font-medium text-gray-700">
                                Standard Turnaround Time <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="normal_turnaround_hours" name="normal_turnaround_hours" 
                                       value="{{ old('normal_turnaround_hours', 48) }}" required min="24"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('normal_turnaround_hours') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">hours</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimum 24 hours for standard service</p>
                            @error('normal_turnaround_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rush Turnaround Hours -->
                        <div>
                            <label for="rush_turnaround_hours" class="block text-sm font-medium text-gray-700">
                                Rush Service Turnaround Time <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="rush_turnaround_hours" name="rush_turnaround_hours" 
                                       value="{{ old('rush_turnaround_hours', 24) }}" required min="12"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('rush_turnaround_hours') border-red-500 @enderror">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">hours</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Minimum 12 hours for rush service</p>
                            @error('rush_turnaround_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Daily Orders -->
                        <div>
                            <label for="max_daily_orders" class="block text-sm font-medium text-gray-700">
                                Maximum Daily Orders <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" id="max_daily_orders" name="max_daily_orders" 
                                       value="{{ old('max_daily_orders', 20) }}" required min="1"
                                       class="block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('max_daily_orders') border-red-500 @enderror">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum number of orders you can handle per day</p>
                            @error('max_daily_orders')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Business Hours (Optional - can be stored in additional_data) -->
                        <div>
                            <label for="opening_time" class="block text-sm font-medium text-gray-700">
                                Opening Time
                            </label>
                            <input type="time" id="opening_time" name="opening_time" value="{{ old('opening_time', '09:00') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="closing_time" class="block text-sm font-medium text-gray-700">
                                Closing Time
                            </label>
                            <input type="time" id="closing_time" name="closing_time" value="{{ old('closing_time', '20:00') }}"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
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

                <!-- Location Information (Myanmar) -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Location Information</h3>
                    
                    <!-- Business Address -->
                    <div class="mb-4">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Business Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" name="business_address" rows="2" required
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500 @error('business_address') border-red-500 @enderror"
                                  placeholder="House/Building No, Road/Street, Ward/Quarter">{{ old('business_address') }}</textarea>
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

                    <!-- Latitude & Longitude Fields -->
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

                <!-- Document Upload for Identification - FIXED: Using 'document' field name -->
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

                <!-- Business Registration Document -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Business Registration</h3>
                    <div class="mb-6">
                        <label for="business_registration" class="block text-sm font-medium text-gray-700 mb-2">
                            Business Registration Certificate (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-pdf text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="business_registration" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload file</span>
                                        <input id="business_registration" name="business_registration" type="file" 
                                               accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                        @error('business_registration')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Price List -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Price List</h3>
                    <div class="mb-6">
                        <label for="price_list" class="block text-sm font-medium text-gray-700 mb-2">
                            Price List / Rate Card (Optional)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-excel text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="price_list" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload price list</span>
                                        <input id="price_list" name="price_list" type="file" 
                                               accept=".pdf,.jpg,.jpeg,.png,.xls,.xlsx" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PDF, Excel, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                        @error('price_list')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Facility Photos -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Facility Photos</h3>
                    <div class="mb-6">
                        <label for="facility_photos" class="block text-sm font-medium text-gray-700 mb-2">
                            Facility Photos (Optional - max 5 photos)
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-images text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="facility_photos" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload photos</span>
                                        <input id="facility_photos" name="facility_photos[]" type="file" multiple
                                               accept=".jpg,.jpeg,.png" class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">JPEG, PNG up to 5MB each (max 5 photos)</p>
                            </div>
                        </div>
                        @error('facility_photos')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('facility_photos.*')
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
                        As a Laundry Service Provider, you'll pay <strong>10% commission</strong> on all laundry orders. 
                        This commission will be automatically deducted from your earnings.
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
                                I confirm that the information provided is accurate
                            </label>
                            <p class="text-gray-500">By submitting this application, you confirm that all information provided is accurate and you agree to follow RMS guidelines for laundry service providers.</p>
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
            
            // Check for multiple files in facility photos
            if (e.target.id === 'facility_photos' && e.target.files.length > 5) {
                alert('Maximum 5 photos allowed');
                e.target.value = '';
            }
        }
    });

    // Form submission loading state
    const form = document.getElementById('laundryApplicationForm');
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