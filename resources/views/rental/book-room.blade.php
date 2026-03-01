@extends('layouts.apps')

@section('title', 'Request to Book ' . $room->room_type_name)

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
                <span class="text-sm font-medium text-gray-900">Request Room {{ $room->room_number }}</span>
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

        <!-- Progress Steps -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-indigo-600 text-white">1</div>
                        <span class="ml-2 font-medium">Request</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700">2</div>
                        <span class="ml-2 text-gray-500">Approval</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700">3</div>
                        <span class="ml-2 text-gray-500">Payment</span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-gray-300 text-gray-700">4</div>
                        <span class="ml-2 text-gray-500">Confirmed</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- How it works -->
        <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg mb-6">
            <div class="flex">
                <i class="fas fa-info-circle text-blue-400 mr-3"></i>
                <p class="text-sm text-blue-700">
                    Submit a request to the owner. You'll be notified when approved, then you can make payment.
                </p>
            </div>
        </div>

        <!-- Request Form -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h2 class="text-2xl font-bold">Request to Book</h2>
                <p class="text-gray-600">{{ $property->name }} - {{ $room->room_type_name }} (Room {{ $room->room_number }})</p>
            </div>
            
            <form method="POST" action="{{ route('rental.room.request', [$property, $room]) }}" class="p-6">
                @csrf
                
                <!-- Room Summary -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <div class="flex justify-between">
                        <div>
                            <h4 class="font-medium">Room {{ $room->room_number }} - {{ $room->room_type_name }}</h4>
                            <p class="text-sm text-gray-600">Capacity: {{ $room->capacity }} persons</p>
                        </div>
                        <div class="text-right">
                            <div class="text-2xl font-bold">MMK{{ number_format($room->total_price) }}/month</div>
                        </div>
                    </div>
                </div>

                <!-- Form Fields -->
                <div class="space-y-6">
                    <!-- Dates -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Check-in</label>
                            <input type="date" name="check_in" id="check_in" 
                                   min="{{ date('Y-m-d') }}" value="{{ old('check_in') }}"
                                   class="w-full rounded-lg border-gray-300" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Check-out</label>
                            <input type="date" name="check_out" id="check_out" 
                                   class="w-full rounded-lg border-gray-300" required>
                        </div>
                    </div>

                    <!-- Calculation -->
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <div class="text-sm text-gray-600">Days</div>
                                <div class="text-xl font-bold text-indigo-600" id="total_days">-</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Months</div>
                                <div class="text-xl font-bold text-indigo-600" id="total_months">-</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-600">Total</div>
                                <div class="text-xl font-bold text-green-600" id="total_price">MMK-</div>
                            </div>
                        </div>
                    </div>

                    <!-- Guests -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Guests</label>
                        <input type="number" name="guest_count" value="1" min="1" max="{{ $room->capacity }}"
                               class="w-full rounded-lg border-gray-300" required>
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Phone</label>
                        <div class="flex">
                            <select name="phone_country_code" class="w-24 rounded-l-lg border-r-0 bg-gray-50">
                                <option value="+95">+95</option>
                                <option value="+880" selected>+880</option>
                            </select>
                            <input type="tel" name="phone" value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                   class="flex-1 rounded-r-lg border-gray-300" required>
                        </div>
                    </div>

                    <!-- Special Requests -->
                    <div>
                        <label class="block text-sm font-medium mb-2">Special Requests</label>
                        <textarea name="special_requests" rows="3" 
                                  class="w-full rounded-lg border-gray-300">{{ old('special_requests') }}</textarea>
                    </div>

                    <!-- Terms -->
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <label class="flex items-start">
                            <input type="checkbox" name="agree_terms" value="1" required class="mt-1 mr-3">
                            <span class="text-sm">I understand this is a request only. Booking not confirmed until approved and payment made.</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex justify-between pt-6 border-t">
                        <a href="{{ route('properties.show', $property) }}" 
                           class="px-4 py-2 border rounded-lg hover:bg-gray-50">Cancel</a>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
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
    const monthlyPrice = {{ $room->total_price }};
    
    function calculate() {
        if (checkIn.value && checkOut.value) {
            const start = new Date(checkIn.value);
            const end = new Date(checkOut.value);
            const days = Math.ceil((end - start) / (1000*60*60*24));
            const months = Math.round(days / 30);
            const total = monthlyPrice * (months || 1);
            
            document.getElementById('total_days').textContent = days + ' days';
            document.getElementById('total_months').textContent = months + ' months';
            document.getElementById('total_price').textContent = 'MMK' + total.toLocaleString();
        }
    }
    
    function updateCheckOut() {
        if (checkIn.value) {
            const next = new Date(checkIn.value);
            next.setDate(next.getDate() + 1);
            checkOut.min = next.toISOString().split('T')[0];
        }
        calculate();
    }
    
    checkIn.addEventListener('change', updateCheckOut);
    checkOut.addEventListener('change', calculate);
    
    if (!checkIn.value) {
        checkIn.value = new Date().toISOString().split('T')[0];
        updateCheckOut();
    }
});
</script>
@endsection