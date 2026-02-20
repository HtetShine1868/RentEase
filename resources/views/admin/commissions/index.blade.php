@extends('layouts.admin')

@section('title', 'Commission Management')

@section('header', 'Commission Settings')

@section('subtitle', 'Configure commission rates for different services')

@section('content')
<div class="space-y-6">
    <!-- Header Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Commission Management</h2>
                <p class="text-indigo-100">Set and manage commission rates for all services on the platform</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-percentage text-6xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Commission Form -->
    <form action="{{ route('admin.commissions.update') }}" method="POST" class="space-y-6">
        @csrf
        
        <!-- Commission Cards Grid -->
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <!-- Hostel Commission -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <i class="fas fa-bed text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Hostel Commission</h3>
                        </div>
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-white">Hostels</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="HOSTEL" class="block text-sm font-medium text-gray-700 mb-2">
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
                                       class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-lg"
                                       placeholder="5.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-calculator text-blue-600 mt-1 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-blue-900">Example Calculation</p>
                                    <p class="text-sm text-blue-700 mt-1">
                                        ₹1000 booking = ₹{{ number_format(1000 * (old('HOSTEL', $commissions['HOSTEL']->rate ?? 5.00) / 100), 2) }} commission
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Apartment Commission -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <i class="fas fa-building text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Apartment Commission</h3>
                        </div>
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-white">Apartments</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="APARTMENT" class="block text-sm font-medium text-gray-700 mb-2">
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
                                       class="focus:ring-green-500 focus:border-green-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-lg"
                                       placeholder="3.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-calculator text-green-600 mt-1 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-green-900">Example Calculation</p>
                                    <p class="text-sm text-green-700 mt-1">
                                        ₹5000 rental = ₹{{ number_format(5000 * (old('APARTMENT', $commissions['APARTMENT']->rate ?? 3.00) / 100), 2) }} commission
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Food Commission -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <i class="fas fa-utensils text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Food Commission</h3>
                        </div>
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-white">Food Orders</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="FOOD" class="block text-sm font-medium text-gray-700 mb-2">
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
                                       class="focus:ring-yellow-500 focus:border-yellow-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-lg"
                                       placeholder="8.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-calculator text-yellow-600 mt-1 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-yellow-900">Example Calculation</p>
                                    <p class="text-sm text-yellow-700 mt-1">
                                        ₹500 food order = ₹{{ number_format(500 * (old('FOOD', $commissions['FOOD']->rate ?? 8.00) / 100), 2) }} commission
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Laundry Commission -->
            <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden hover:shadow-xl transition-shadow">
                <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                                <i class="fas fa-tshirt text-white text-xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-white">Laundry Commission</h3>
                        </div>
                        <span class="px-3 py-1 bg-white bg-opacity-20 rounded-full text-sm text-white">Laundry</span>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label for="LAUNDRY" class="block text-sm font-medium text-gray-700 mb-2">
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
                                       class="focus:ring-purple-500 focus:border-purple-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-lg"
                                       placeholder="10.00">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">%</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-start">
                                <i class="fas fa-calculator text-purple-600 mt-1 mr-3"></i>
                                <div>
                                    <p class="text-sm font-medium text-purple-900">Example Calculation</p>
                                    <p class="text-sm text-purple-700 mt-1">
                                        ₹300 laundry = ₹{{ number_format(300 * (old('LAUNDRY', $commissions['LAUNDRY']->rate ?? 10.00) / 100), 2) }} commission
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl shadow-lg border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>
                    Current Commission Summary
                </h3>
                <button type="button" 
                        onclick="resetToDefaults()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors text-sm font-medium">
                    <i class="fas fa-undo mr-2"></i>
                    Reset to Defaults
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Hostel</p>
                    <p class="text-2xl font-bold text-blue-600" id="summary-hostel">{{ old('HOSTEL', $commissions['HOSTEL']->rate ?? 5.00) }}%</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Apartment</p>
                    <p class="text-2xl font-bold text-green-600" id="summary-apartment">{{ old('APARTMENT', $commissions['APARTMENT']->rate ?? 3.00) }}%</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Food</p>
                    <p class="text-2xl font-bold text-yellow-600" id="summary-food">{{ old('FOOD', $commissions['FOOD']->rate ?? 8.00) }}%</p>
                </div>
                <div class="bg-white rounded-lg p-4 shadow-sm">
                    <p class="text-sm text-gray-600 mb-1">Laundry</p>
                    <p class="text-2xl font-bold text-purple-600" id="summary-laundry">{{ old('LAUNDRY', $commissions['LAUNDRY']->rate ?? 10.00) }}%</p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-3">
            <button type="button" 
                    onclick="window.location.reload()"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </form>

    <!-- Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    <strong>Note:</strong> Commission rates apply to all transactions on the platform. Changes will affect future transactions only.
                    The commission is calculated as a percentage of the base price and added to the total amount paid by customers.
                </p>
            </div>
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
        
        // Update example calculations
        updateExamples();
    });
});

// Update example calculations
function updateExamples() {
    const hostelRate = document.getElementById('HOSTEL').value;
    const apartmentRate = document.getElementById('APARTMENT').value;
    const foodRate = document.getElementById('FOOD').value;
    const laundryRate = document.getElementById('LAUNDRY').value;
    
    // Update example texts (optional)
    const examples = document.querySelectorAll('.bg-blue-50, .bg-green-50, .bg-yellow-50, .bg-purple-50');
    if (examples.length >= 4) {
        // Hostel example
        const hostelExample = examples[0].querySelector('.text-blue-700');
        if (hostelExample) {
            hostelExample.textContent = `₹1000 booking = ₹${(1000 * hostelRate / 100).toFixed(2)} commission`;
        }
        
        // Apartment example
        const apartmentExample = examples[1].querySelector('.text-green-700');
        if (apartmentExample) {
            apartmentExample.textContent = `₹5000 rental = ₹${(5000 * apartmentRate / 100).toFixed(2)} commission`;
        }
        
        // Food example
        const foodExample = examples[2].querySelector('.text-yellow-700');
        if (foodExample) {
            foodExample.textContent = `₹500 food order = ₹${(500 * foodRate / 100).toFixed(2)} commission`;
        }
        
        // Laundry example
        const laundryExample = examples[3].querySelector('.text-purple-700');
        if (laundryExample) {
            laundryExample.textContent = `₹300 laundry = ₹${(300 * laundryRate / 100).toFixed(2)} commission`;
        }
    }
}

// Reset to default values
function resetToDefaults() {
    if (confirm('Reset all commission rates to default values?')) {
        document.getElementById('HOSTEL').value = '5.00';
        document.getElementById('APARTMENT').value = '3.00';
        document.getElementById('FOOD').value = '8.00';
        document.getElementById('LAUNDRY').value = '10.00';
        
        // Trigger input events to update summary and examples
        ['HOSTEL', 'APARTMENT', 'FOOD', 'LAUNDRY'].forEach(id => {
            const event = new Event('input', { bubbles: true });
            document.getElementById(id).dispatchEvent(event);
        });
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateExamples();
});
</script>
@endpush
@endsection