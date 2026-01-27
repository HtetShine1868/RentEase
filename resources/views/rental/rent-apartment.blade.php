@extends('dashboard')

@section('title', 'Rent ' . $property->name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('rental.search') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-search mr-2"></i>
                    Search Rental
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <a href="{{ route('rental.property.details', $property) }}" 
                       class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        {{ $property->name }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500">Rent Apartment</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Progress Steps -->
    <div class="bg-white rounded-lg shadow p-6">
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
            <h2 class="text-xl font-bold text-gray-900">Rental Application</h2>
            <p class="text-gray-600 mt-1">Please fill in your rental details for {{ $property->name }}</p>
        </div>
        
        <form method="POST" action="#" class="p-6">
            @csrf
            
            <!-- Property Summary -->
            <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-gray-900">{{ $property->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $property->area }}, {{ $property->city }}</p>
                        <div class="flex items-center mt-1">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                Apartment
                            </span>
                            <span class="ml-2 px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                {{ $property->bedrooms }} Bedrooms
                            </span>
                        </div>
                    </div>
                    <div class="text-right">
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
                           min="{{ date('Y-m-d') }}"
                           required
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-sm text-gray-500">Earliest move-in is tomorrow</p>
                </div>

                <!-- Duration -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Duration of Stay <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <select name="duration_months" required
                                    class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Duration</option>
                                <option value="1">1 Month</option>
                                <option value="3">3 Months</option>
                                <option value="6">6 Months</option>
                                <option value="12">12 Months</option>
                                <option value="custom">Custom Duration</option>
                            </select>
                        </div>
                        <div id="custom-duration" class="hidden">
                            <div class="relative">
                                <input type="number" 
                                       name="custom_duration"
                                       min="{{ $property->min_stay_months }}"
                                       class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500">months</span>
                                </div>
                            </div>
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
                               min="1"
                               max="{{ $property->bedrooms * 2 }}"
                               required
                               class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 pr-12">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">persons</span>
                        </div>
                    </div>
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
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="tel" 
                                   name="phone"
                                   value="{{ auth()->user()->phone }}"
                                   required
                                   class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="+8801XXXXXXXXX">
                        </div>
                    </div>

                    <!-- Emergency Contact -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Emergency Contact (Optional)
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="tel" 
                                   name="emergency_contact"
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
                              rows="3"
                              class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Any special requests or requirements..."></textarea>
                </div>

                <!-- Terms Agreement -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400"></i>
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
                               required
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="agree_terms" class="font-medium text-gray-700">
                            I agree to all terms and conditions
                        </label>
                        <p class="text-gray-500">I have read and agree to abide by the property rules and rental agreement.</p>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-between pt-8 border-t border-gray-200">
                <a href="{{ route('rental.property.details', $property) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Back to Property
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Proceed to Payment
                    <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const durationSelect = document.querySelector('select[name="duration_months"]');
    const customDurationDiv = document.getElementById('custom-duration');
    
    durationSelect.addEventListener('change', function() {
        if (this.value === 'custom') {
            customDurationDiv.classList.remove('hidden');
        } else {
            customDurationDiv.classList.add('hidden');
        }
    });
});
</script>
@endsection