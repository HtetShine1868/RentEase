@extends('layouts.app')

@section('title', 'Commission Management')

@section('header', 'Commission Settings')

@section('subtitle', 'Configure commission rates for different services')

@section('content')
<div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-900">Commission Rates</h2>
            <p class="text-sm text-gray-600 mt-1">Set the commission percentage for each service type</p>
        </div>
    </div>

    <!-- Commission Form -->
    <form action="{{ route('commissions.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Commission Cards -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Hostel Commission -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-blue-100 rounded-lg">
                                <i class="fas fa-bed text-blue-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Hostel Commission</h3>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Commission for hostel bookings</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="HOSTEL" class="block text-sm font-medium text-gray-700 mb-1">
                            Commission Rate (%)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="HOSTEL" 
                                   id="HOSTEL"
                                   value="{{ old('HOSTEL', $commissions['HOSTEL']->rate ?? 5.00) }}"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="5.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 rounded-lg p-3">
                        <div class="flex items-center text-sm text-blue-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Example: ₹1000 booking = ₹{{ number_format(1000 * (old('HOSTEL', $commissions['HOSTEL']->rate ?? 5.00) / 100), 2) }} commission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apartment Commission -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-green-100 rounded-lg">
                                <i class="fas fa-building text-green-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Apartment Commission</h3>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Commission for apartment rentals</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="APARTMENT" class="block text-sm font-medium text-gray-700 mb-1">
                            Commission Rate (%)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="APARTMENT" 
                                   id="APARTMENT"
                                   value="{{ old('APARTMENT', $commissions['APARTMENT']->rate ?? 3.00) }}"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="3.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 rounded-lg p-3">
                        <div class="flex items-center text-sm text-green-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Example: ₹5000 rental = ₹{{ number_format(5000 * (old('APARTMENT', $commissions['APARTMENT']->rate ?? 3.00) / 100), 2) }} commission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Commission -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-yellow-100 rounded-lg">
                                <i class="fas fa-utensils text-yellow-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Food Commission</h3>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Commission for food orders</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="FOOD" class="block text-sm font-medium text-gray-700 mb-1">
                            Commission Rate (%)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="FOOD" 
                                   id="FOOD"
                                   value="{{ old('FOOD', $commissions['FOOD']->rate ?? 8.00) }}"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="8.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-yellow-50 rounded-lg p-3">
                        <div class="flex items-center text-sm text-yellow-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Example: ₹500 food order = ₹{{ number_format(500 * (old('FOOD', $commissions['FOOD']->rate ?? 8.00) / 100), 2) }} commission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laundry Commission -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <div class="flex items-center space-x-2">
                            <div class="p-2 bg-purple-100 rounded-lg">
                                <i class="fas fa-tshirt text-purple-600 text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Laundry Commission</h3>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Commission for laundry services</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label for="LAUNDRY" class="block text-sm font-medium text-gray-700 mb-1">
                            Commission Rate (%)
                        </label>
                        <div class="relative rounded-md shadow-sm">
                            <input type="number" 
                                   name="LAUNDRY" 
                                   id="LAUNDRY"
                                   value="{{ old('LAUNDRY', $commissions['LAUNDRY']->rate ?? 10.00) }}"
                                   step="0.01"
                                   min="0"
                                   max="100"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="10.00">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-purple-50 rounded-lg p-3">
                        <div class="flex items-center text-sm text-purple-700">
                            <i class="fas fa-info-circle mr-2"></i>
                            <span>Example: ₹300 laundry = ₹{{ number_format(300 * (old('LAUNDRY', $commissions['LAUNDRY']->rate ?? 10.00) / 100), 2) }} commission</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-indigo-50 rounded-lg border border-indigo-200 p-6">
            <h3 class="text-lg font-semibold text-indigo-900 mb-4">Commission Summary</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-indigo-600">Hostel</p>
                    <p class="text-2xl font-bold text-indigo-900" id="summary-hostel">{{ old('HOSTEL', $commissions['HOSTEL']->rate ?? 5.00) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-indigo-600">Apartment</p>
                    <p class="text-2xl font-bold text-indigo-900" id="summary-apartment">{{ old('APARTMENT', $commissions['APARTMENT']->rate ?? 3.00) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-indigo-600">Food</p>
                    <p class="text-2xl font-bold text-indigo-900" id="summary-food">{{ old('FOOD', $commissions['FOOD']->rate ?? 8.00) }}%</p>
                </div>
                <div>
                    <p class="text-sm text-indigo-600">Laundry</p>
                    <p class="text-2xl font-bold text-indigo-900" id="summary-laundry">{{ old('LAUNDRY', $commissions['LAUNDRY']->rate ?? 10.00) }}%</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <button type="reset" 
                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Reset
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Save Changes
            </button>
        </div>
    </form>

    <!-- History Section -->
    <div class="mt-8">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Changes</h3>
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Old Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">New Rate</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Changed By</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-500" colspan="5">
                            <div class="text-center py-4">
                                <i class="fas fa-history text-gray-400 text-2xl mb-2"></i>
                                <p class="text-gray-500">Change history will appear here</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Update summary when inputs change
document.querySelectorAll('input[type="number"]').forEach(input => {
    input.addEventListener('input', function() {
        const type = this.name.toLowerCase();
        document.getElementById(`summary-${type}`).textContent = this.value + '%';
    });
});

// Calculate commission example
function updateExamples() {
    const hostelRate = document.getElementById('HOSTEL').value;
    const apartmentRate = document.getElementById('APARTMENT').value;
    const foodRate = document.getElementById('FOOD').value;
    const laundryRate = document.getElementById('LAUNDRY').value;
    
    // Update example texts (optional)
}
</script>
@endpush
@endsection