@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Apply as Food Provider</h1>
                <p class="mt-2 text-gray-600">Fill out the form below to become a food service provider on RMS.</p>
            </div>
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-utensils text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-green-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Requirements</h3>
                <div class="mt-2 text-sm text-green-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Food business license/certificate</li>
                        <li>Kitchen facility photos</li>
                        <li>Menu details and pricing</li>
                        <li>Delivery coverage area</li>
                        <li>Contact information</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('role.apply.store', 'FOOD') }}" enctype="multipart/form-data">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div class="md:col-span-2">
                            <label for="business_name" class="block text-sm font-medium text-gray-700">
                                Restaurant/Kitchen Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-utensils text-gray-400"></i>
                                </div>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                       placeholder="e.g., Spicy Bites Restaurant">
                            </div>
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Cuisine Type -->
                        <div class="md:col-span-2">
                            <label for="cuisine_type" class="block text-sm font-medium text-gray-700">
                                Cuisine Type <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                   placeholder="e.g., Bengali, Chinese, Fast Food, etc.">
                            @error('cuisine_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meal Types -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Meal Types Offered <span class="text-red-500">*</span>
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @php
                                    $mealTypes = [
                                        'BREAKFAST' => 'Breakfast',
                                        'LUNCH' => 'Lunch', 
                                        'DINNER' => 'Dinner',
                                        'SNACKS' => 'Snacks'
                                    ];
                                @endphp
                                @foreach($mealTypes as $value => $label)
                                    <div class="flex items-center">
                                        <input type="checkbox" id="meal_type_{{ $value }}" name="meal_types[]" 
                                               value="{{ $value }}" 
                                               {{ in_array($value, old('meal_types', [])) ? 'checked' : '' }}
                                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                                        <label for="meal_type_{{ $value }}" class="ml-2 text-sm text-gray-700">
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                            @error('meal_types')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Daily Orders -->
                        <div>
                            <label for="max_daily_orders" class="block text-sm font-medium text-gray-700">
                                Maximum Daily Orders <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="max_daily_orders" name="max_daily_orders" value="{{ old('max_daily_orders', 50) }}" required min="1"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            @error('max_daily_orders')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Delivery Hours -->
                        <div>
                            <label for="delivery_hours" class="block text-sm font-medium text-gray-700">
                                Delivery Hours <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="delivery_hours" name="delivery_hours" value="{{ old('delivery_hours') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                                   placeholder="e.g., 8 AM - 10 PM">
                            @error('delivery_hours')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Service Radius -->
                        <div class="md:col-span-2">
                            <label for="service_radius_km" class="block text-sm font-medium text-gray-700">
                                Service Coverage Radius (km) <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <input type="number" step="0.1" id="service_radius_km" name="service_radius_km" 
                                       value="{{ old('service_radius_km', 5) }}" required min="1" max="50"
                                       class="block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">km</span>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Maximum distance you can deliver food (1-50 km)</p>
                            @error('service_radius_km')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information (Same as OWNER form) -->
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
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
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information (Similar to OWNER form) -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Location Information</h3>
                    
                    <!-- Business Address -->
                    <div class="mb-6">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Kitchen/Shop Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" name="business_address" rows="3" required
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">{{ old('business_address') }}</textarea>
                    </div>

                    <!-- Map Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Latitude -->
                        <div>
                            <label for="latitude" class="block text-sm font-medium text-gray-700">
                                Latitude <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-map-marker-alt text-gray-400"></i>
                                </div>
                                <input type="number" step="any" id="latitude" name="latitude" value="{{ old('latitude') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            </div>
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
                                <input type="number" step="any" id="longitude" name="longitude" value="{{ old('longitude') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Document Upload -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Document Upload</h3>
                    <div class="mb-6">
                        <label for="document" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Food Business License <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-file-upload text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="document" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500">
                                        <span>Upload a file</span>
                                        <input id="document" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Commission Information -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-green-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Commission Information
                    </h4>
                    <p class="text-sm text-green-700">
                        As a Food Provider, you'll pay <strong>8% commission</strong> on all food orders. 
                        This commission will be automatically deducted from your earnings.
                    </p>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('role.apply.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection