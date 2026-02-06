@extends('layouts.app')

@section('title', 'Rent ' . $property->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('rental.search') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Search
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <a href="{{ route('rental.property.details', $property) }}" 
                   class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    {{ Str::limit($property->name, 20) }}
                </a>
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-medium text-gray-900">Rent Apartment</span>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Progress Steps -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-medium">
                            1
                        </div>
                        <span class="ml-2 font-medium text-gray-900">Rental Details</span>
                    </div>
                    
                    <!-- Arrow -->
                    <div class="h-1 w-12 bg-gray-300"></div>
                    
                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-medium">
                            2
                        </div>
                        <span class="ml-2 font-medium text-gray-500">Payment</span>
                    </div>
                    
                    <!-- Arrow -->
                    <div class="h-1 w-12 bg-gray-300"></div>
                    
                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-medium">
                            3
                        </div>
                        <span class="ml-2 font-medium text-gray-500">Confirmation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rental Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Rental Application</h2>
                <p class="text-gray-600 mt-1">Please fill in your rental details for {{ $property->name }}</p>
            </div>
            
            <!-- FORM FIX: Changed action to the correct POST route -->
            <form method="POST" action="{{ route('rental.apartment.book', $property) }}" class="p-6">
                @csrf
                
                <!-- Property Summary -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex flex-col md:flex-row md:items-center justify-between">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $property->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $property->area }}, {{ $property->city }}</p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                    Apartment
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $property->bedrooms }} Bedrooms
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $property->bathrooms }} Bathrooms
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0">
                            <div class="text-2xl font-bold text-gray-900">৳{{ number_format($property->total_price) }}</div>
                            <div class="text-sm text-gray-500">per month</div>
                        </div>
                    </div>
                </div>

                <!-- Rental Details -->
                <div class="space-y-6">
                    <!-- Move-in Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Move-in Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="move_in_date"
                               id="move_in_date"
                               value="{{ old('move_in_date') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               required
                               class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('move_in_date') border-red-500 @enderror">
                        @error('move_in_date')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Earliest move-in is tomorrow</p>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Duration of Stay (Months) <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <select name="duration_months" id="duration_months" required
                                        class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('duration_months') border-red-500 @enderror">
                                    <option value="">Select Duration</option>
                                    <option value="1" {{ old('duration_months') == '1' ? 'selected' : '' }}>1 Month</option>
                                    <option value="3" {{ old('duration_months') == '3' ? 'selected' : '' }}>3 Months</option>
                                    <option value="6" {{ old('duration_months') == '6' ? 'selected' : '' }}>6 Months</option>
                                    <option value="12" {{ old('duration_months') == '12' ? 'selected' : '' }}>12 Months</option>
                                    <option value="custom" {{ old('duration_months') == 'custom' ? 'selected' : '' }}>Custom Duration</option>
                                </select>
                                @error('duration_months')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div id="custom_duration_container" class="{{ old('duration_months') == 'custom' ? '' : 'hidden' }}">
                                <div class="relative">
                                    <input type="number" 
                                           name="custom_duration"
                                           id="custom_duration"
                                           value="{{ old('custom_duration') }}"
                                           min="{{ $property->min_stay_months }}"
                                           max="36"
                                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12 @error('custom_duration') border-red-500 @enderror">
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">months</span>
                                    </div>
                                </div>
                                @error('custom_duration')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">Minimum stay: {{ $property->min_stay_months }} month(s)</p>
                    </div>

                    <!-- Number of Occupants -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Occupants <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="occupants"
                                   id="occupants"
                                   value="{{ old('occupants', 1) }}"
                                   min="1"
                                   max="{{ $property->bedrooms * 2 }}"
                                   required
                                   class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12 @error('occupants') border-red-500 @enderror">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">persons</span>
                            </div>
                        </div>
                        @error('occupants')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum capacity: {{ $property->bedrooms * 2 }} persons</p>
                    </div>

                    <!-- Contact Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <input type="tel" 
                                       name="phone"
                                       id="phone"
                                       value="{{ old('phone', auth()->user()->phone) }}"
                                       required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror"
                                       placeholder="+8801XXXXXXXXX">
                            </div>
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Emergency Contact -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Emergency Contact (Optional)
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <input type="tel" 
                                       name="emergency_contact"
                                       id="emergency_contact"
                                       value="{{ old('emergency_contact') }}"
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                       placeholder="+8801XXXXXXXXX">
                            </div>
                        </div>
                    </div>

                    <!-- Special Requirements -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Special Requirements or Notes (Optional)
                        </label>
                        <textarea name="notes" 
                                  id="notes"
                                  rows="3"
                                  class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Any special requests or requirements...">{{ old('notes') }}</textarea>
                    </div>

                    <!-- Terms Agreement -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.732 0L4.342 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h4 class="text-sm font-medium text-yellow-800">Important Terms & Conditions</h4>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        <li>Security deposit: {{ $property->deposit_months }} month(s) rent</li>
                                        <li>Advance payment: 1 month rent required</li>
                                        <li>Rent payment due by 5th of each month</li>
                                        <li>No subletting allowed</li>
                                        <li>24-hour notice required for maintenance access</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Agreement Checkbox -->
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" 
                                   id="agree_terms"
                                   name="agree_terms"
                                   value="1"
                                   required
                                   {{ old('agree_terms') ? 'checked' : '' }}
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded @error('agree_terms') border-red-500 @enderror">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="agree_terms" class="font-medium text-gray-700">
                                I agree to all terms and conditions <span class="text-red-500">*</span>
                            </label>
                            <p class="text-gray-500">I have read and agree to abide by the property rules and rental agreement.</p>
                            @error('agree_terms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                    <a href="{{ route('rental.property.details', $property) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Property
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        Proceed to Payment
                        <svg class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durationSelect = document.getElementById('duration_months');
    const customDurationContainer = document.getElementById('custom_duration_container');
    const customDurationInput = document.getElementById('custom_duration');
    
    // Show/hide custom duration field
    durationSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDurationContainer.classList.remove('hidden');
            if (customDurationInput) {
                customDurationInput.value = '{{ $property->min_stay_months }}';
                customDurationInput.required = true;
            }
        } else {
            customDurationContainer.classList.add('hidden');
            if (customDurationInput) {
                customDurationInput.required = false;
            }
        }
    });
    
    // Set minimum date to tomorrow
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);
    
    const dd = String(tomorrow.getDate()).padStart(2, '0');
    const mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
    const yyyy = tomorrow.getFullYear();
    
    const minDate = yyyy + '-' + mm + '-' + dd;
    document.getElementById('move_in_date').min = minDate;
    
    // Trigger change event on page load if custom is selected
    if (durationSelect.value === 'custom') {
        durationSelect.dispatchEvent(new Event('change'));
    }
});
</script>
@endsection