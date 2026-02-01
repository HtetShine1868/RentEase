@props([
    'showAdvanced' => false,
    'onFilter' => '',
])

<div class="bg-white shadow-sm sm:rounded-lg" x-data="{ showAdvanced: {{ $showAdvanced ? 'true' : 'false' }} }">
    <div class="px-4 py-5 sm:p-6">
        <!-- Filter Header -->
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Filter Orders</h3>
            <button type="button" 
                    @click="showAdvanced = !showAdvanced"
                    class="text-sm text-indigo-600 hover:text-indigo-500">
                <span x-show="!showAdvanced">
                    <i class="fas fa-filter mr-1"></i> Advanced Filters
                </span>
                <span x-show="showAdvanced" x-cloak>
                    <i class="fas fa-times mr-1"></i> Hide Filters
                </span>
            </button>
        </div>

        <!-- Basic Filters -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <!-- Date Range -->
            <div>
                <label for="date_range" class="block text-sm font-medium text-gray-700 mb-1">
                    Date Range
                </label>
                <select id="date_range" 
                        name="date_range" 
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="today">Today</option>
                    <option value="yesterday" selected>Yesterday</option>
                    <option value="last_7_days">Last 7 days</option>
                    <option value="last_30_days">Last 30 days</option>
                    <option value="this_month">This Month</option>
                    <option value="custom">Custom Range</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Status
                </label>
                <select id="status" 
                        name="status" 
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="accepted">Accepted</option>
                    <option value="preparing" selected>Preparing</option>
                    <option value="out_for_delivery">Out for Delivery</option>
                    <option value="delivered">Delivered</option>
                    <option value="delayed">Delayed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Order Type -->
            <div>
                <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Order Type
                </label>
                <select id="order_type" 
                        name="order_type" 
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">All Types</option>
                    <option value="pay_per_eat">Pay-per-eat</option>
                    <option value="subscription">Subscription</option>
                </select>
            </div>

            <!-- Amount Range -->
            <div>
                <label for="amount_range" class="block text-sm font-medium text-gray-700 mb-1">
                    Amount Range
                </label>
                <select id="amount_range" 
                        name="amount_range" 
                        class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Any Amount</option>
                    <option value="0-500">₹0 - ₹500</option>
                    <option value="500-1000">₹500 - ₹1000</option>
                    <option value="1000-2000">₹1000 - ₹2000</option>
                    <option value="2000+">Above ₹2000</option>
                </select>
            </div>
        </div>

        <!-- Custom Date Range (Conditional) -->
        <div id="custom_date_range" class="hidden mb-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                        Start Date
                    </label>
                    <input type="date" 
                           id="start_date" 
                           name="start_date" 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                        End Date
                    </label>
                    <input type="date" 
                           id="end_date" 
                           name="end_date" 
                           class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
            </div>
        </div>

        <!-- Advanced Filters -->
        <div x-show="showAdvanced" x-transition class="border-t pt-4 mt-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Advanced Filters</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <!-- Customer Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Customer Type
                    </label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input id="new_customer" 
                                   name="customer_type[]" 
                                   type="checkbox" 
                                   value="new"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="new_customer" class="ml-2 block text-sm text-gray-900">
                                New Customers
                            </label>
                        </div>
                        <div class="flex items-center">
                            <input id="regular_customer" 
                                   name="customer_type[]" 
                                   type="checkbox" 
                                   value="regular"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="regular_customer" class="ml-2 block text-sm text-gray-900">
                                Regular Customers
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Preparation Time -->
                <div>
                    <label for="prep_time" class="block text-sm font-medium text-gray-700 mb-1">
                        Max Prep Time (mins)
                    </label>
                    <select id="prep_time" 
                            name="prep_time" 
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Any Time</option>
                        <option value="15">15 mins or less</option>
                        <option value="30">30 mins or less</option>
                        <option value="45">45 mins or less</option>
                        <option value="60">60 mins or less</option>
                    </select>
                </div>

                <!-- Delivery Distance -->
                <div>
                    <label for="delivery_distance" class="block text-sm font-medium text-gray-700 mb-1">
                        Delivery Distance
                    </label>
                    <select id="delivery_distance" 
                            name="delivery_distance" 
                            class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Any Distance</option>
                        <option value="1">Within 1 km</option>
                        <option value="2">Within 2 km</option>
                        <option value="5">Within 5 km</option>
                        <option value="10">Within 10 km</option>
                    </select>
                </div>
            </div>

            <!-- Search by Customer/Order ID -->
            <div class="mb-4">
                <label for="search_term" class="block text-sm font-medium text-gray-700 mb-1">
                    Search (Customer Name/Order ID)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="search_term" 
                           name="search_term" 
                           class="block w-full pl-10 pr-10 border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="Search by customer name or order ID...">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <button type="button" 
                                class="text-gray-400 hover:text-gray-500"
                                onclick="clearSearch()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Payment Status
                </label>
                <div class="flex flex-wrap gap-3">
                    <div class="flex items-center">
                        <input id="payment_paid" 
                               name="payment_status[]" 
                               type="checkbox" 
                               value="paid"
                               class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded">
                        <label for="payment_paid" class="ml-2 block text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Paid
                            </span>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="payment_pending" 
                               name="payment_status[]" 
                               type="checkbox" 
                               value="pending"
                               class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                        <label for="payment_pending" class="ml-2 block text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        </label>
                    </div>
                    <div class="flex items-center">
                        <input id="payment_failed" 
                               name="payment_status[]" 
                               type="checkbox" 
                               value="failed"
                               class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                        <label for="payment_failed" class="ml-2 block text-sm text-gray-900">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Failed
                            </span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex justify-between items-center pt-4 border-t border-gray-200">
            <div>
                <button type="button" 
                        onclick="resetFilters()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-redo mr-2"></i>
                    Reset Filters
                </button>
            </div>
            <div class="flex space-x-3">
                <button type="button" 
                        onclick="saveFilterPreset()"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-save mr-2"></i>
                    Save Preset
                </button>
                <button type="button" 
                        onclick="{{ $onFilter ?? 'applyFilters()' }}"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-filter mr-2"></i>
                    Apply Filters
                </button>
            </div>
        </div>

        <!-- Active Filters -->
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <h4 class="text-sm font-medium text-gray-900">Active Filters</h4>
                <span class="text-xs text-gray-500" id="active-filter-count">0 filters applied</span>
            </div>
            <div class="mt-2 flex flex-wrap gap-2" id="active-filters-container">
                <!-- Active filters will be added here dynamically -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide custom date range
    document.getElementById('date_range').addEventListener('change', function() {
        const customRangeDiv = document.getElementById('custom_date_range');
        if (this.value === 'custom') {
            customRangeDiv.classList.remove('hidden');
        } else {
            customRangeDiv.classList.add('hidden');
        }
    });

    // Clear search input
    function clearSearch() {
        document.getElementById('search_term').value = '';
        updateActiveFilters();
    }

    // Reset all filters
    function resetFilters() {
        // Reset all form elements
        const form = document.querySelector('[x-data]').closest('form') || document;
        form.reset();
        
        // Hide custom date range
        document.getElementById('custom_date_range').classList.add('hidden');
        
        // Reset Alpine.js state
        if (typeof Alpine !== 'undefined') {
            const component = Alpine.$data(document.querySelector('[x-data]'));
            if (component) {
                component.showAdvanced = false;
            }
        }
        
        // Update active filters
        updateActiveFilters();
        
        // Show success message
        showToast('success', 'Filters have been reset');
    }

    // Apply filters
    function applyFilters() {
        // Collect filter values
        const filters = {
            dateRange: document.getElementById('date_range').value,
            status: document.getElementById('status').value,
            orderType: document.getElementById('order_type').value,
            amountRange: document.getElementById('amount_range').value,
            searchTerm: document.getElementById('search_term').value,
            // Add more filter values as needed
        };

        // Update active filters display
        updateActiveFilters();
        
        // Trigger filter event (for Livewire or other frameworks)
        const event = new CustomEvent('orders-filtered', { detail: filters });
        window.dispatchEvent(event);
        
        // Show success message
        showToast('success', 'Filters applied successfully');
        
        // Log filters (for debugging)
        console.log('Applied filters:', filters);
    }

    // Save filter preset
    function saveFilterPreset() {
        const presetName = prompt('Enter a name for this filter preset:');
        if (presetName) {
            // Save logic here (localStorage or API)
            localStorage.setItem('order_filter_preset_' + presetName, JSON.stringify(getCurrentFilters()));
            showToast('success', `Preset "${presetName}" saved successfully`);
        }
    }

    // Get current filter values
    function getCurrentFilters() {
        return {
            date_range: document.getElementById('date_range').value,
            status: document.getElementById('status').value,
            order_type: document.getElementById('order_type').value,
            amount_range: document.getElementById('amount_range').value,
            search_term: document.getElementById('search_term').value,
            prep_time: document.getElementById('prep_time')?.value || '',
            delivery_distance: document.getElementById('delivery_distance')?.value || '',
            start_date: document.getElementById('start_date')?.value || '',
            end_date: document.getElementById('end_date')?.value || ''
        };
    }

    // Update active filters display
    function updateActiveFilters() {
        const filters = getCurrentFilters();
        const container = document.getElementById('active-filters-container');
        const countElement = document.getElementById('active-filter-count');
        
        // Clear current display
        container.innerHTML = '';
        
        // Count active filters
        let activeCount = 0;
        
        // Add filter chips for active filters
        Object.entries(filters).forEach(([key, value]) => {
            if (value && value !== '') {
                activeCount++;
                
                let label = '';
                let displayValue = value;
                
                // Convert keys to readable labels
                switch(key) {
                    case 'date_range':
                        label = 'Date Range';
                        displayValue = document.getElementById('date_range').options[document.getElementById('date_range').selectedIndex].text;
                        break;
                    case 'status':
                        label = 'Status';
                        displayValue = document.getElementById('status').options[document.getElementById('status').selectedIndex].text;
                        break;
                    case 'order_type':
                        label = 'Order Type';
                        displayValue = document.getElementById('order_type').options[document.getElementById('order_type').selectedIndex].text;
                        break;
                    case 'amount_range':
                        label = 'Amount';
                        break;
                    case 'search_term':
                        label = 'Search';
                        break;
                    case 'prep_time':
                        label = 'Prep Time';
                        displayValue = value + ' mins or less';
                        break;
                    case 'delivery_distance':
                        label = 'Distance';
                        displayValue = 'Within ' + value + ' km';
                        break;
                    default:
                        label = key.replace('_', ' ').toUpperCase();
                }
                
                // Create filter chip
                const chip = document.createElement('span');
                chip.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800';
                chip.innerHTML = `
                    ${label}: ${displayValue}
                    <button type="button" 
                            onclick="removeFilter('${key}')"
                            class="ml-1.5 text-indigo-600 hover:text-indigo-900 focus:outline-none">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                `;
                container.appendChild(chip);
            }
        });
        
        // Update count
        countElement.textContent = `${activeCount} filter${activeCount !== 1 ? 's' : ''} applied`;
        
        // Show/hide container
        if (activeCount > 0) {
            container.parentElement.classList.remove('hidden');
        } else {
            container.parentElement.classList.add('hidden');
        }
    }

    // Remove specific filter
    function removeFilter(filterKey) {
        switch(filterKey) {
            case 'date_range':
                document.getElementById('date_range').value = '';
                break;
            case 'status':
                document.getElementById('status').value = '';
                break;
            case 'order_type':
                document.getElementById('order_type').value = '';
                break;
            case 'amount_range':
                document.getElementById('amount_range').value = '';
                break;
            case 'search_term':
                document.getElementById('search_term').value = '';
                break;
            case 'prep_time':
                document.getElementById('prep_time').value = '';
                break;
            case 'delivery_distance':
                document.getElementById('delivery_distance').value = '';
                break;
            case 'start_date':
                document.getElementById('start_date').value = '';
                break;
            case 'end_date':
                document.getElementById('end_date').value = '';
                break;
        }
        
        updateActiveFilters();
        applyFilters();
    }

    // Toast notification function
    function showToast(type, message) {
        // You can use your preferred toast library
        console.log(`[${type.toUpperCase()}] ${message}`);
        // Example with Alpine.js toast component
        // window.dispatchEvent(new CustomEvent('show-toast', { 
        //     detail: { type, message } 
        // }));
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateActiveFilters();
    });
</script>
@endpush