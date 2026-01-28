@extends('layouts.food-provider')

@section('title', 'Edit Restaurant Profile')

@section('header', 'Edit Restaurant Profile')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm sm:rounded-lg">
        <!-- Form Header -->
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">
                Restaurant Information
            </h3>
            <p class="mt-1 text-sm text-gray-500">
                Update your restaurant details. All changes will be visible to customers.
            </p>
        </div>

        <!-- Form -->
        <form action="{{ route('food-provider.profile.update') }}" 
              method="POST" 
              enctype="multipart/form-data" 
              x-data="restaurantProfileForm()"
              data-coverage-radius="{{ auth()->user()->restaurant->coverage_radius ?? 5 }}">
            @csrf
            @method('PUT')
            
            <div class="px-4 py-5 sm:p-6 space-y-8">
                <!-- Restaurant Logo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Restaurant Logo
                    </label>
                    <div class="flex items-center space-x-6">
                        <div class="shrink-0">
                            <img class="h-24 w-24 rounded-lg object-cover border"
                                 :src="logoPreview || '{{ auth()->user()->restaurant->logo_url ?? 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=200&h=200&fit=crop' }}'"
                                 alt="Restaurant Logo">
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*"
                                   @change="updateLogoPreview($event)"
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">
                                PNG, JPG, GIF up to 2MB. Recommended: 200x200px
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Restaurant Name & Tagline -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Restaurant Name *
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="name" 
                                   id="name" 
                                   value="{{ auth()->user()->restaurant->name ?? '' }}"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   required>
                        </div>
                    </div>

                    <div>
                        <label for="tagline" class="block text-sm font-medium text-gray-700">
                            Tagline / Slogan
                        </label>
                        <div class="mt-1">
                            <input type="text" 
                                   name="tagline" 
                                   id="tagline" 
                                   value="{{ auth()->user()->restaurant->tagline ?? '' }}"
                                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                   placeholder="e.g., Best food in town!">
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Description *
                    </label>
                    <div class="mt-1">
                        <textarea id="description" 
                                  name="description" 
                                  rows="4" 
                                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                  placeholder="Describe your restaurant, cuisine type, specialties..."
                                  required>{{ auth()->user()->restaurant->description ?? '' }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">
                        Brief description about your restaurant (max 500 characters).
                    </p>
                    <div class="mt-1 text-right text-xs text-gray-500">
                        <span id="char-count">0</span>/500 characters
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Contact Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">
                                Phone Number *
                            </label>
                            <div class="mt-1">
                                <input type="tel" 
                                       name="phone" 
                                       id="phone" 
                                       value="{{ auth()->user()->phone ?? '' }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                Contact Email *
                            </label>
                            <div class="mt-1">
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       value="{{ auth()->user()->email ?? '' }}"
                                       class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                       required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location & Coverage -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Location & Service Area</h4>
                    
                    <!-- Address -->
                    <div class="mb-6">
                        <label for="address" class="block text-sm font-medium text-gray-700">
                            Full Address *
                        </label>
                        <div class="mt-1">
                            <textarea id="address" 
                                      name="address" 
                                      rows="2" 
                                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      required>{{ auth()->user()->restaurant->address ?? '' }}</textarea>
                        </div>
                    </div>

                    <!-- Map Location Picker -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Set Location on Map
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                            <div class="flex flex-col items-center justify-center py-8">
                                <i class="fas fa-map-marker-alt text-gray-400 text-4xl mb-4"></i>
                                <h4 class="text-lg font-medium text-gray-900">Select Restaurant Location</h4>
                                <p class="mt-2 text-sm text-gray-500 mb-4">
                                    Click the button below to select your restaurant location on the map
                                </p>
                                <button type="button" 
                                        onclick="openMapPicker()"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-map-pin mr-2"></i>
                                    Open Map Picker
                                </button>
                            </div>
                            <!-- Hidden fields for coordinates -->
                            <input type="hidden" name="latitude" id="latitude" value="{{ auth()->user()->restaurant->latitude ?? '' }}">
                            <input type="hidden" name="longitude" id="longitude" value="{{ auth()->user()->restaurant->longitude ?? '' }}">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Your location helps customers find you and determines delivery availability
                        </p>
                    </div>

                    <!-- Coverage Radius -->
                    <div>
                        <label for="coverage_radius" class="block text-sm font-medium text-gray-700">
                            Service Coverage Radius (km) *
                        </label>
                        <div class="mt-1">
                            <input type="range" 
                                   id="coverage_radius" 
                                   name="coverage_radius" 
                                   min="1" 
                                   max="20" 
                                   step="1"
                                   value="{{ auth()->user()->restaurant->coverage_radius ?? 5 }}"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer"
                                   x-model="coverageRadius"
                                   @input="updateCoverageEstimate">
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>1 km</span>
                                <span class="font-medium" x-text="coverageRadius + ' km'"></span>
                                <span>20 km</span>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-blue-50 rounded-md">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <span class="text-sm text-blue-700">
                                    Your restaurant will serve customers within 
                                    <span class="font-bold" x-text="coverageRadius"></span> km radius.
                                    Approx. <span class="font-bold" x-text="estimatedCustomers"></span> customers in your area.
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Service Configuration -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Service Configuration</h4>
                    
                    <!-- Meal Types -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Available Meal Types *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            @php
                                $restaurant = auth()->user()->restaurant ?? null;
                            @endphp
                            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="breakfast" 
                                           name="meal_types[]" 
                                           type="checkbox" 
                                           value="breakfast" 
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                           {{ ($restaurant && $restaurant->serves_breakfast) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <label for="breakfast" class="font-medium text-gray-700">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-sun text-yellow-500 mr-2"></i> Breakfast
                                        </span>
                                    </label>
                                    <p class="text-sm text-gray-500">7:00 AM - 11:00 AM</p>
                                </div>
                            </div>
                            
                            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="lunch" 
                                           name="meal_types[]" 
                                           type="checkbox" 
                                           value="lunch" 
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                           {{ ($restaurant && $restaurant->serves_lunch) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <label for="lunch" class="font-medium text-gray-700">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-utensils text-orange-500 mr-2"></i> Lunch
                                        </span>
                                    </label>
                                    <p class="text-sm text-gray-500">12:00 PM - 3:00 PM</p>
                                </div>
                            </div>
                            
                            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="dinner" 
                                           name="meal_types[]" 
                                           type="checkbox" 
                                           value="dinner" 
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                           {{ ($restaurant && $restaurant->serves_dinner) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3">
                                    <label for="dinner" class="font-medium text-gray-700">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-moon text-blue-500 mr-2"></i> Dinner
                                        </span>
                                    </label>
                                    <p class="text-sm text-gray-500">7:00 PM - 11:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Types -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Service Types Offered *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="subscription" 
                                           name="service_types[]" 
                                           type="checkbox" 
                                           value="subscription" 
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                           {{ ($restaurant && $restaurant->offers_subscription) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="subscription" class="font-medium text-gray-700">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i> Monthly Subscription
                                        </span>
                                    </label>
                                    <p class="text-sm text-gray-500">Recurring meal plans for customers</p>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Higher commission: 15%
                                    </div>
                                </div>
                            </div>
                            
                            <div class="relative flex items-start p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                <div class="flex items-center h-5">
                                    <input id="pay_per_eat" 
                                           name="service_types[]" 
                                           type="checkbox" 
                                           value="pay_per_eat" 
                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                           {{ ($restaurant && $restaurant->offers_pay_per_eat) ? 'checked' : '' }}>
                                </div>
                                <div class="ml-3 flex-1">
                                    <label for="pay_per_eat" class="font-medium text-gray-700">
                                        <span class="inline-flex items-center">
                                            <i class="fas fa-shopping-cart text-pink-500 mr-2"></i> Pay-Per-Eat
                                        </span>
                                    </label>
                                    <p class="text-sm text-gray-500">One-time orders</p>
                                    <div class="mt-2 text-xs text-gray-500">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        Standard commission: 12%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="border-t border-gray-200 pt-6">
                    <h4 class="text-md font-medium text-gray-900 mb-4">Operating Hours</h4>
                    <div class="space-y-4">
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        @php
                            $dayLower = strtolower($day);
                            $hours = $restaurant ? json_decode($restaurant->opening_hours, true) : [];
                            $dayHours = $hours[$dayLower] ?? ['open' => '08:00', 'close' => '22:00'];
                            $isClosed = isset($dayHours['open']) && $dayHours['open'] === 'closed';
                        @endphp
                        <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg">
                            <div class="flex items-center">
                                <span class="font-medium text-gray-700 w-24">{{ $day }}</span>
                                <div class="flex items-center space-x-2">
                                    <select name="opening_hours[{{ $dayLower }}][open]" 
                                            class="border-gray-300 rounded-md text-sm">
                                        <option value="closed" {{ $isClosed ? 'selected' : '' }}>Closed</option>
                                        @foreach(range(6, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ !$isClosed && $dayHours['open'] == sprintf('%02d:00', $hour) ? 'selected' : '' }}>{{ sprintf('%02d:00', $hour) }}</option>
                                            <option value="{{ sprintf('%02d:30', $hour) }}" {{ !$isClosed && $dayHours['open'] == sprintf('%02d:30', $hour) ? 'selected' : '' }}>{{ sprintf('%02d:30', $hour) }}</option>
                                        @endforeach
                                    </select>
                                    <span class="text-gray-500">to</span>
                                    <select name="opening_hours[{{ $dayLower }}][close]" 
                                            class="border-gray-300 rounded-md text-sm">
                                        <option value="closed" {{ $isClosed ? 'selected' : '' }}>Closed</option>
                                        @foreach(range(6, 23) as $hour)
                                            <option value="{{ sprintf('%02d:00', $hour) }}" {{ !$isClosed && $dayHours['close'] == sprintf('%02d:00', $hour) ? 'selected' : '' }}>{{ sprintf('%02d:00', $hour) }}</option>
                                            <option value="{{ sprintf('%02d:30', $hour) }}" {{ !$isClosed && $dayHours['close'] == sprintf('%02d:30', $hour) ? 'selected' : '' }}>{{ sprintf('%02d:30', $hour) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input id="{{ $dayLower }}-closed" 
                                       name="{{ $dayLower }}_closed" 
                                       type="checkbox" 
                                       value="1"
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                       {{ $isClosed ? 'checked' : '' }}>
                                <label for="{{ $dayLower }}-closed" class="ml-2 text-sm text-gray-700">
                                    Closed
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
                <a href="{{ route('food-provider.profile.index') }}" 
                   class="inline-flex justify-center py-2 px-4 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Cancel
                </a>
                <button type="submit" 
                        class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i> Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Alpine.js form logic
    function restaurantProfileForm() {
        return {
            logoPreview: '',
            coverageRadius: 5,
            estimatedCustomers: '100-150',
            
            init() {
                // Get coverage radius from data attribute
                const radiusData = this.$el.dataset.coverageRadius;
                this.coverageRadius = radiusData ? parseInt(radiusData) : 5;
                
                // Initialize estimated customers based on current radius
                this.updateCoverageEstimate();
            },
            
            updateLogoPreview(event) {
                const input = event.target;
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.logoPreview = e.target.result;
                    };
                    reader.readAsDataURL(input.files[0]);
                }
            },
            
            updateCoverageEstimate() {
                // Simple estimation based on radius
                const estimates = {
                    1: '20-30',
                    2: '40-60',
                    3: '60-90',
                    4: '80-120',
                    5: '100-150',
                    6: '120-180',
                    7: '140-210',
                    8: '160-240',
                    9: '180-270',
                    10: '200-300',
                    15: '300-450',
                    20: '400-600'
                };
                this.estimatedCustomers = estimates[this.coverageRadius] || '100-150';
            }
        };
    }

    // Character counter for description
    document.addEventListener('DOMContentLoaded', function() {
        const desc = document.getElementById('description');
        if (desc) {
            // Initialize character count
            document.getElementById('char-count').textContent = desc.value.length;
            
            // Add input event listener
            desc.addEventListener('input', function() {
                const count = this.value.length;
                const charCount = document.getElementById('char-count');
                charCount.textContent = count;
                
                if (count > 500) {
                    charCount.classList.add('text-red-600');
                } else {
                    charCount.classList.remove('text-red-600');
                }
            });
        }
    });

    // Map picker function
    function openMapPicker() {
        alert('Map picker would open here. Integration with Google Maps API would be implemented here.');
        // Implementation would use Google Maps API or similar
        // For now, we'll just set some default coordinates
        document.getElementById('latitude').value = '28.6139';
        document.getElementById('longitude').value = '77.2090';
        
        // Show success message
        showToast('Location set to default coordinates for demo purposes.', 'success');
    }

    // Toast notification function
    function showToast(message, type = 'success') {
        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}"
                 class="fixed top-4 right-4 z-50 max-w-sm w-full ${type === 'success' ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'} rounded-lg shadow-lg border p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas ${type === 'success' ? 'fa-check-circle text-green-400' : 'fa-exclamation-circle text-red-400'} h-5 w-5"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm font-medium ${type === 'success' ? 'text-green-800' : 'text-red-800'}">
                            ${message}
                        </p>
                    </div>
                    <button type="button" 
                            onclick="document.getElementById('${toastId}').remove()" 
                            class="ml-4 flex-shrink-0 inline-flex rounded-md p-1.5 ${type === 'success' ? 'text-green-500 hover:bg-green-100' : 'text-red-500 hover:bg-red-100'}">
                        <i class="fas fa-times h-4 w-4"></i>
                    </button>
                </div>
            </div>
        `;
        
        // Add to DOM
        const toastContainer = document.createElement('div');
        toastContainer.innerHTML = toastHtml;
        document.body.appendChild(toastContainer.firstElementChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            const toast = document.getElementById(toastId);
            if (toast && toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 5000);
    }
</script>
@endsection