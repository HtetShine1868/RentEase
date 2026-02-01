@extends('owner.layout.owner-layout')

@section('title', 'Booking Management - RentEase')
@section('page-title', 'Booking Management')
@section('page-subtitle', 'Manage all property bookings')

@section('content')
<div class="space-y-6">


    <!-- Header with Stats -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Booking Management</h1>
                <p class="text-gray-600 mt-1">View and manage all bookings across your properties</p>
            </div>
            
            <!-- Quick Stats -->
            <div class="flex gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-500">Today</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $dayCount ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $monthCount ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Revenue</p>
                    <p class="text-xl font-bold text-green-600 mt-1">${{ number_format($monthRevenue ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">All Bookings</h2>
            
            <div class="flex items-center gap-3">
                <!-- Export Button -->
                <button class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download mr-2"></i> Export
                </button>
                
                <!-- Refresh Button -->
                <button onclick="refreshBookings()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Filter Controls -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Property Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="checked_in">Checked In</option>
                    <option value="checked_out">Checked Out</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Date Range -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" 
                       placeholder="Search bookings by user, property, or booking ID..." 
                       class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
            </div>
        </div>

        <!-- Bookings Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Booking ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            User
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Property / Room
                        </th>
                        <th class="px 6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Dates
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Booking 1 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#BK-001</div>
                            <div class="text-xs text-gray-500">Jan 15, 2024</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-purple-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">John Doe</div>
                                    <div class="text-xs text-gray-500">john@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">Sunshine Apartments</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-building mr-1"></i> Apartment • Unit 302
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Feb 1 - Mar 1, 2024</div>
                            <div class="text-xs text-gray-500">30 days</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">$1,250.00</div>
                            <div class="text-xs text-gray-500">Commission: $37.50</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Confirmed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="viewBooking(1)" 
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="editBooking(1)" 
                                        class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button onclick="sendReminder(1)" 
                                        class="px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors"
                                        title="Send Reminder">
                                    <i class="fas fa-bell"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Booking 2 (Pending) -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#BK-002</div>
                            <div class="text-xs text-gray-500">Jan 16, 2024</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Sarah Smith</div>
                                    <div class="text-xs text-gray-500">sarah@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">City Hostel</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-bed mr-1"></i> Hostel • Room 101
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Feb 15 - Mar 15, 2024</div>
                            <div class="text-xs text-gray-500">30 days</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">$450.00</div>
                            <div class="text-xs text-gray-500">Commission: $22.50</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="viewBooking(2)" 
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="confirmBooking(2)" 
                                        class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
                                        title="Confirm Booking">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button onclick="cancelBooking(2)" 
                                        class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors"
                                        title="Cancel">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Booking 3 (Checked In) -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#BK-003</div>
                            <div class="text-xs text-gray-500">Jan 10, 2024</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-green-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Mike Johnson</div>
                                    <div class="text-xs text-gray-500">mike@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">City Hostel</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-bed mr-1"></i> Hostel • Room 102
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">Jan 20 - Feb 20, 2024</div>
                            <div class="text-xs text-gray-500">31 days • Ongoing</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-900">$465.00</div>
                            <div class="text-xs text-gray-500">Commission: $23.25</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-sign-in-alt mr-1"></i> Checked In
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="viewBooking(3)" 
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="checkOut(3)" 
                                        class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors"
                                        title="Check Out">
                                    <i class="fas fa-sign-out-alt"></i>
                                </button>
                                <button onclick="sendMessage(3)" 
                                        class="px-3 py-1.5 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors"
                                        title="Message">
                                    <i class="fas fa-envelope"></i>
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- Booking 4 (Cancelled) -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#BK-004</div>
                            <div class="text-xs text-gray-500">Jan 5, 2024</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 bg-gray-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-gray-600 text-sm"></i>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Emma Wilson</div>
                                    <div class="text-xs text-gray-500">emma@example.com</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 font-medium">Luxury Villa</div>
                            <div class="text-xs text-gray-500">
                                <i class="fas fa-home mr-1"></i> Apartment • Whole Unit
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-500 line-through">Mar 1 - Apr 1, 2024</div>
                            <div class="text-xs text-gray-500">Cancelled on Jan 7</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-gray-500 line-through">$2,800.00</div>
                            <div class="text-xs text-gray-500">Refund: $2,800.00</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1"></i> Cancelled
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center gap-2">
                                <button onclick="viewBooking(4)" 
                                        class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="restoreBooking(4)" 
                                        class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors"
                                        title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div id="empty-bookings" class="hidden">
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">No bookings found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters or come back later.</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-gray-200">
            <div class="text-sm text-gray-600">
                Showing <span class="font-semibold">1 to 4</span> of <span class="font-semibold">24</span> bookings
            </div>
            <div class="flex items-center gap-1">
                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-chevron-left text-sm"></i>
                </button>
                <button class="w-8 h-8 flex items-center justify-center rounded bg-purple-600 text-white">1</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">2</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">3</button>
                <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">
                    <i class="fas fa-chevron-right text-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Booking Management Functions
function viewBooking(bookingId) {
    Loading.show('Loading booking details...');
    setTimeout(() => {
        window.location.href = `/owner/bookings/${bookingId}`;
    }, 500);
}

function editBooking(bookingId) {
    Toast.info('Edit Booking', `Opening booking #${bookingId} for editing...`);
    // In real app, redirect to edit page
}

function confirmBooking(bookingId) {
    ConfirmationModal.show(
        'Confirm Booking',
        `Are you sure you want to confirm booking #${bookingId}?`,
        'Confirm Booking',
        () => {
            Loading.show('Confirming booking...');
            setTimeout(() => {
                Toast.success('Booking Confirmed', `Booking #${bookingId} has been confirmed.`);
                refreshBookings();
            }, 1000);
        }
    );
}

function cancelBooking(bookingId) {
    ConfirmationModal.show(
        'Cancel Booking',
        `Are you sure you want to cancel booking #${bookingId}? A cancellation fee may apply.`,
        'Cancel Booking',
        () => {
            Loading.show('Cancelling booking...');
            setTimeout(() => {
                Toast.warning('Booking Cancelled', `Booking #${bookingId} has been cancelled.`);
                refreshBookings();
            }, 1000);
        }
    );
}

function checkOut(bookingId) {
    ConfirmationModal.show(
        'Check Out Guest',
        `Mark booking #${bookingId} as checked out? This will make the room available for new bookings.`,
        'Check Out',
        () => {
            Loading.show('Processing check out...');
            setTimeout(() => {
                Toast.success('Guest Checked Out', `Booking #${bookingId} marked as completed.`);
                refreshBookings();
            }, 1000);
        }
    );
}

function sendReminder(bookingId) {
    Toast.info('Reminder Sent', `Payment reminder sent for booking #${bookingId}`);
}

function sendMessage(bookingId) {
    Toast.info('Message', `Opening chat for booking #${bookingId}`);
}

function restoreBooking(bookingId) {
    ConfirmationModal.show(
        'Restore Booking',
        `Restore cancelled booking #${bookingId}?`,
        'Restore',
        () => {
            Loading.show('Restoring booking...');
            setTimeout(() => {
                Toast.success('Booking Restored', `Booking #${bookingId} has been restored.`);
                refreshBookings();
            }, 1000);
        }
    );
}

function refreshBookings() {
    Loading.show('Refreshing bookings...');
    setTimeout(() => {
        Toast.success('Refreshed', 'Bookings list updated.');
        Loading.hide();
    }, 1000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const searchInput = document.querySelector('input[type="search"]');
    const filters = document.querySelectorAll('select, input[type="date"]');
    
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            if (rowText.includes(searchTerm)) {
                row.classList.remove('hidden');
            } else {
                row.classList.add('hidden');
            }
        });
    }
    
    searchInput.addEventListener('input', applyFilters);
    filters.forEach(filter => filter.addEventListener('change', applyFilters));
});
</script>

<style>
/* Booking Status Badges */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Table row hover effect */
tbody tr {
    transition: background-color 0.15s ease;
}

/* Action buttons styling */
.action-btn {
    transition: all 0.2s ease;
}

.action-btn:hover {
    transform: translateY(-1px);
}

/* Responsive table */
@media (max-width: 768px) {
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    table {
        min-width: 800px;
    }
}
</style>
@endsection