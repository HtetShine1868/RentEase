@extends('layouts.apps')

@section('title', 'Request to Rent ' . $property->name)

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Breadcrumb -->
    <nav class="bg-white shadow" aria-label="Breadcrumb">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center space-x-2">
                <a href="{{ route('properties.search') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    Search
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <a href="{{ route('properties.show', $property) }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                    {{ Str::limit($property->name, 20) }}
                </a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-sm font-medium text-gray-900">Request to Rent</span>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Messages -->
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Progress Steps -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">
                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center font-medium">
                            1
                        </div>
                        <span class="ml-2 font-medium text-gray-900">Request Details</span>
                    </div>
                    
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    
                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-medium">
                            2
                        </div>
                        <span class="ml-2 font-medium text-gray-500">Owner Approval</span>
                    </div>
                    
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    
                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-medium">
                            3
                        </div>
                        <span class="ml-2 font-medium text-gray-500">Payment</span>
                    </div>

                    <i class="fas fa-chevron-right text-gray-400"></i>
                    
                    <!-- Step 4 -->
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700 flex items-center justify-center font-medium">
                            4
                        </div>
                        <span class="ml-2 font-medium text-gray-500">Confirmation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- How it works notice -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">How it works</h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <p>1. Submit your rental request with the details below.</p>
                        <p>2. The property owner will review your request.</p>
                        <p>3. If approved, you'll receive a notification to make payment.</p>
                        <p>4. Complete payment within 24 hours to confirm your booking.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Request Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900">Request to Rent</h2>
                <p class="text-gray-600 mt-1">{{ $property->name }}</p>
            </div>
            
            <form method="POST" action="{{ route('rental.apartment.request', $property) }}" class="p-6" id="rentalForm">
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
                                    {{ $property->bedrooms }} Beds
                                </span>
                                <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                    {{ $property->bathrooms }} Baths
                                </span>
                            </div>
                        </div>
                        <div class="mt-4 md:mt-0 text-right">
                            <div class="text-2xl font-bold text-gray-900">MMK{{ number_format($property->total_price) }}</div>
                            <div class="text-sm text-gray-500">per month</div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-6">
                    <!-- Check-in Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Check-in Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="check_in"
                               id="check_in"
                               value="{{ old('check_in') }}"
                               min="{{ date('Y-m-d') }}"
                               required
                               class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('check_in') border-red-500 @enderror">
                        @error('check_in')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Check-out Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Check-out Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" 
                               name="check_out"
                               id="check_out"
                               value="{{ old('check_out') }}"
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                               required
                               class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('check_out') border-red-500 @enderror">
                        @error('check_out')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Duration Display -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 block">Total Days:</span>
                                <span class="text-xl font-bold text-indigo-600" id="total_days">-</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 block">Total Months:</span>
                                <span class="text-xl font-bold text-indigo-600" id="total_months">-</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 block">Total Price:</span>
                                <span class="text-xl font-bold text-green-600" id="total_price">MMK-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Guests -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Number of Guests <span class="text-red-500">*</span>
                        </label>
                        <input type="number" 
                               name="guest_count"
                               id="guest_count"
                               value="{{ old('guest_count', 1) }}"
                               min="1"
                               max="{{ $property->bedrooms * 2 }}"
                               required
                               class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('guest_count') border-red-500 @enderror">
                        @error('guest_count')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-sm text-gray-500">Maximum: {{ $property->bedrooms * 2 }} persons</p>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Phone Number <span class="text-red-500">*</span>
                        </label>
                        <div class="flex">
                            <select name="phone_country_code" 
                                    class="w-24 rounded-l-lg border-r-0 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 bg-gray-50">
                                <option value="+95">+95</option>
                                <option value="+880" selected>+880</option>
                                <option value="+1">+1</option>
                                <option value="+44">+44</option>
                                <option value="+65">+65</option>
                            </select>
                            <input type="tel" 
                                   name="phone"
                                   value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                   required
                                   class="flex-1 rounded-r-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 @error('phone') border-red-500 @enderror"
                                   placeholder="XXXXXXXXX">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Special Requests (Optional)
                        </label>
                        <textarea name="special_requests" 
                                  rows="3"
                                  class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Any special requirements...">{{ old('special_requests') }}</textarea>
                    </div>

                    <!-- Terms -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <input type="checkbox" 
                                   name="agree_terms"
                                   id="agree_terms"
                                   value="1"
                                   required
                                   class="mt-1 mr-3">
                            <label for="agree_terms" class="text-sm text-gray-700">
                                I understand that this is a request only. The booking is not confirmed until approved by the owner and payment is made.
                            </label>
                        </div>
                        @error('agree_terms')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between pt-6 border-t">
                        <a href="{{ route('properties.show', $property) }}" 
                           class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium flex items-center">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Request
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkIn = document.getElementById('check_in');
    const checkOut = document.getElementById('check_out');
    const totalDaysSpan = document.getElementById('total_days');
    const totalMonthsSpan = document.getElementById('total_months');
    const totalPriceSpan = document.getElementById('total_price');
    const monthlyPrice = {{ $property->total_price }};
    
    function calculateDates() {
        if (checkIn.value && checkOut.value) {
            const start = new Date(checkIn.value);
            const end = new Date(checkOut.value);
            
            if (end > start) {
                const diffTime = end - start;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                const diffMonths = Math.round(diffDays / 30);
                const totalPrice = monthlyPrice * (diffMonths || 1);
                
                totalDaysSpan.textContent = diffDays + ' days';
                totalMonthsSpan.textContent = diffMonths + ' months';
                totalPriceSpan.textContent = 'MMK' + totalPrice.toLocaleString();
            }
        }
    }
    
    function updateCheckOutMin() {
        if (checkIn.value) {
            const nextDay = new Date(checkIn.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.min = nextDay.toISOString().split('T')[0];
            
            if (checkOut.value && new Date(checkOut.value) <= new Date(checkIn.value)) {
                checkOut.value = '';
            }
        }
        calculateDates();
    }
    
    checkIn.addEventListener('change', updateCheckOutMin);
    checkOut.addEventListener('change', calculateDates);
    
    // Set default check-in to today
    if (!checkIn.value) {
        checkIn.value = new Date().toISOString().split('T')[0];
        updateCheckOutMin();
    }
    
    // Form submission
    document.getElementById('rentalForm').addEventListener('submit', function() {
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
    });
});
</script>
@endsection