@extends('dashboard')

@section('title', 'Book ' . $room->room_type_name . ' - ' . $property->name)

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li>
                <a href="{{ route('properties.search') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600">
                    <i class="fas fa-search mr-2"></i>
                    Search Rental
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <a href="{{ route('properties.show', $property) }}" 
                       class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        {{ $property->name }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <a href="{{ url('/properties/' . $property->id . '/rooms/' . $room->id) }}"
                       class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600">
                        {{ $room->room_type_name }}
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400"></i>
                    <span class="ml-1 text-sm font-medium text-gray-500">Book Now</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Room Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex flex-col md:flex-row md:items-start justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Book {{ $room->room_type_name }} Room</h1>
                <div class="flex items-center mt-2">
                    <i class="fas fa-home text-gray-400 mr-2"></i>
                    <span class="text-gray-600">{{ $property->name }}, {{ $property->area }}, {{ $property->city }}</span>
                </div>
                <div class="flex items-center mt-2 space-x-4">
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                        {{ $room->room_type_name }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Room No: {{ $room->room_number }}
                    </span>
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <div class="text-right">
                    <div class="text-2xl font-bold text-indigo-600">MMK{{ number_format($room->total_price) }}/month</div>
                    <div class="text-sm text-gray-500">Base: MMK{{ number_format($room->base_price) }} + {{ $room->commission_rate }}% commission</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Booking Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Complete Your Booking</h3>
                
                <form method="POST" action="{{ route('bookings.room.store', [$property, $room]) }}">
                    @csrf
                    
                    <!-- Move-in Date & Duration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Move-in Date</label>
                            <input type="date" 
                                   name="move_in_date" 
                                   id="move_in_date"
                                   min="{{ date('Y-m-d') }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                   value="{{ old('move_in_date') }}"
                                   required>
                            @error('move_in_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Duration of Stay</label>
                            <select name="months" 
                                    id="months"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                <option value="">Select duration</option>
                                @for($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}" {{ old('months') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i > 1 ? 'months' : 'month' }}
                                    </option>
                                @endfor
                            </select>
                            @error('months')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Date Display (Read-only) -->
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm text-gray-600 block">Move-in Date:</span>
                                <span class="font-medium" id="display_move_in">-</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600 block">Move-out Date:</span>
                                <span class="font-medium" id="display_move_out">-</span>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Contact Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" 
                                       name="phone" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('phone') }}"
                                       placeholder="01XXXXXXXXX"
                                       required>
                                @error('phone')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Emergency Contact (Optional)</label>
                                <input type="tel" 
                                       name="emergency_contact" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                       value="{{ old('emergency_contact') }}"
                                       placeholder="Emergency phone number">
                                @error('emergency_contact')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Additional Notes -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                        <textarea name="notes" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                  placeholder="Any special requirements or notes for the property owner?">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price Calculation -->
                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-gray-900 mb-3">Price Summary</h4>
                        <div class="space-y-2" id="price_summary">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Monthly Rent</span>
                                <span class="font-medium">MMK{{ number_format($room->base_price) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Monthly Commission ({{ $room->commission_rate }}%)</span>
                                <span class="font-medium">MMk{{ number_format($room->base_price * $room->commission_rate / 100) }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Total Monthly</span>
                                <span class="font-medium text-indigo-600">MMK{{ number_format($room->total_price) }}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-2 mt-2" id="total_section" style="display: none;">
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">Total for <span id="selected_months">0</span> months</span>
                                    <span class="text-lg font-bold text-indigo-600" id="total_amount">MMK0</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Terms and Conditions -->
                    <div class="mb-6">
                        <div class="flex items-start">
                            <input type="checkbox" 
                                   name="agree_terms" 
                                   id="agree_terms"
                                   class="mt-1 mr-3"
                                   {{ old('agree_terms') ? 'checked' : '' }}
                                   required>
                            <label for="agree_terms" class="text-sm text-gray-600">
                                I agree to the <a href="#" class="text-indigo-600 hover:text-indigo-800">terms and conditions</a> and confirm that the information provided is accurate.
                            </label>
                        </div>
                        @error('agree_terms')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium">
                        <i class="fas fa-check-circle mr-2"></i>
                        Confirm Booking
                    </button>
                </form>
            </div>
        </div>

        <!-- Right Column - Room Info -->
        <div class="space-y-6">
            <!-- Room Details Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Room Details</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-bed text-gray-400 w-5 mr-3"></i>
                        <span class="text-gray-600">{{ $room->room_type_name }}</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-door-open text-gray-400 w-5 mr-3"></i>
                        <span class="text-gray-600">Room {{ $room->room_number }}</span>
                    </div>
                    @if($room->floor_number)
                    <div class="flex items-center">
                        <i class="fas fa-layer-group text-gray-400 w-5 mr-3"></i>
                        <span class="text-gray-600">Floor {{ $room->floor_number }}</span>
                    </div>
                    @endif
                    <div class="flex items-center">
                        <i class="fas fa-users text-gray-400 w-5 mr-3"></i>
                        <span class="text-gray-600">Capacity: {{ $room->capacity }} persons</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-venus-mars text-gray-400 w-5 mr-3"></i>
                        <span class="text-gray-600">{{ $property->gender_policy_name ?? 'Mixed' }}</span>
                    </div>
                </div>
            </div>

            <!-- Property Rules -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Property Rules</h3>
                <ul class="space-y-2">
                    <li class="flex items-start">
                        <i class="fas fa-clock text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Check-in: After 2:00 PM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-door-closed text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Quiet hours: 10:00 PM to 7:00 AM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-user-friends text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">No visitors after 9:00 PM</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-ban text-gray-400 mt-1 mr-2"></i>
                        <span class="text-gray-600">Strictly no smoking inside rooms</span>
                    </li>
                </ul>
            </div>

            <!-- Other Available Rooms -->
            @if(isset($otherRooms) && $otherRooms->count() > 0)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Other Available Rooms</h3>
                    <div class="space-y-3">
                        @foreach($otherRooms as $otherRoom)
                            <a href="{{ url('/properties/' . $property->id . '/rooms/' . $otherRoom->id) }}" 
                               class="block p-3 border border-gray-200 rounded-lg hover:border-indigo-300 hover:bg-indigo-50 transition-colors">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $otherRoom->room_type_name }}</div>
                                        <div class="text-sm text-gray-600">Room {{ $otherRoom->room_number }}</div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-indigo-600">MMK{{ number_format($otherRoom->total_price) }}</div>
                                        <div class="text-xs text-gray-500">per month</div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Contact Info -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Property Owner</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Name</div>
                            <div class="font-medium">{{ $property->owner->name }}</div>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-phone text-gray-400 mr-3"></i>
                        <div>
                            <div class="text-sm text-gray-500">Contact</div>
                            <div class="font-medium">{{ $property->owner->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const moveInDate = document.getElementById('move_in_date');
    const monthsSelect = document.getElementById('months');
    const displayMoveIn = document.getElementById('display_move_in');
    const displayMoveOut = document.getElementById('display_move_out');
    const totalSection = document.getElementById('total_section');
    const selectedMonthsSpan = document.getElementById('selected_months');
    const totalAmountSpan = document.getElementById('total_amount');
    
    // Price data from PHP
    const monthlyTotal = {{ $room->total_price }};
    
    function updateDates() {
        if (moveInDate.value && monthsSelect.value) {
            const moveIn = new Date(moveInDate.value);
            const months = parseInt(monthsSelect.value);
            
            // Format display dates
            displayMoveIn.textContent = moveIn.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            // Calculate move-out date
            const moveOut = new Date(moveIn);
            moveOut.setMonth(moveOut.getMonth() + months);
            
            displayMoveOut.textContent = moveOut.toLocaleDateString('en-US', { 
                year: 'numeric', month: 'long', day: 'numeric' 
            });
            
            // Show total section
            totalSection.style.display = 'block';
            selectedMonthsSpan.textContent = months;
            
            // Calculate total amount
            const total = monthlyTotal * months;
            totalAmountSpan.textContent = '৳' + total.toLocaleString();
        } else {
            displayMoveIn.textContent = '-';
            displayMoveOut.textContent = '-';
            totalSection.style.display = 'none';
        }
    }
    
    moveInDate.addEventListener('change', updateDates);
    monthsSelect.addEventListener('change', updateDates);
    
    // Initial update if values are set (for old input)
    updateDates();
});
</script>
@endpush