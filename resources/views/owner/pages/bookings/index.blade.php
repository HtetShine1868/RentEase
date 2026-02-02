@extends('owner.layout.owner-layout')

@section('title', 'Booking Management - RentEase')
@section('page-title', 'Booking Management')
@section('page-subtitle', 'Manage all property bookings')

@push('styles')
<style>
    /* Day 3: Status Badge Styles */
    .status-badge {
        @apply px-3 py-1 rounded-full text-xs font-semibold transition-all duration-200;
    }
    .status-pending {
        @apply bg-yellow-100 text-yellow-800 border border-yellow-200;
    }
    .status-confirmed {
        @apply bg-green-100 text-green-800 border border-green-200;
    }
    .status-cancelled {
        @apply bg-red-100 text-red-800 border border-red-200;
    }
    .status-completed {
        @apply bg-blue-100 text-blue-800 border border-blue-200;
    }
    .status-checked_in {
        @apply bg-indigo-100 text-indigo-800 border border-indigo-200;
    }
    .status-checked_out {
        @apply bg-purple-100 text-purple-800 border border-purple-200;
    }
    
    /* Day 3: Timeline Styles */
    .timeline-step {
        @apply flex flex-col items-center relative;
    }
    .timeline-step.active {
        @apply text-purple-600;
    }
    .timeline-step.completed {
        @apply text-green-600;
    }
    .timeline-connector {
        @apply h-0.5 flex-grow bg-gray-200 mx-2;
    }
    .timeline-connector.active {
        @apply bg-purple-500;
    }
    .timeline-connector.completed {
        @apply bg-green-500;
    }
    
    /* Day 4: Filter Styles */
    .filter-dropdown {
        @apply w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700;
    }
    
    /* Day 5: Mobile Card View */
    .mobile-card-view {
        display: none;
    }
    .booking-card {
        @apply bg-white rounded-xl border border-gray-200 p-4 mb-4 shadow-sm hover:shadow-md transition-shadow duration-200;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .mobile-card-view {
            display: block;
        }
        .desktop-table-view {
            display: none;
        }
    }
    @media (min-width: 769px) {
        .mobile-card-view {
            display: none;
        }
        .desktop-table-view {
            display: block;
        }
    }
    
    /* Table responsiveness */
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
@endpush

@section('content')
<div class="space-y-6">
    <!-- Day 1 & 2: Header with Stats (Enhanced) -->
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
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $dayCount ?? 8 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $monthCount ?? 24 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Revenue</p>
                    <p class="text-xl font-bold text-green-600 mt-1">${{ number_format($monthRevenue ?? 4280, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Day 3: Status Statistics -->
        <div class="mt-6 pt-6 border-t border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Booking Status Overview</h3>
                <div class="flex flex-wrap gap-2">
                    <div class="text-center px-3">
                        <span class="status-badge status-pending">Pending</span>
                        <p class="text-lg font-bold mt-1">8</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <p class="text-lg font-bold mt-1">12</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-checked_in">Checked In</span>
                        <p class="text-lg font-bold mt-1">3</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-completed">Completed</span>
                        <p class="text-lg font-bold mt-1">2</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-cancelled">Cancelled</span>
                        <p class="text-lg font-bold mt-1">2</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Day 3: Timeline Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-6">Booking Status Timeline</h2>
        <div class="flex flex-col md:flex-row items-center justify-between">
            <!-- Timeline steps -->
            <div class="flex items-center w-full md:w-auto mb-4 md:mb-0 overflow-x-auto pb-2">
                <div class="timeline-step completed">
                    <div class="w-10 h-10 rounded-full bg-green-100 border-2 border-green-500 flex items-center justify-center mb-2">
                        <i class="fas fa-calendar-plus text-green-600"></i>
                    </div>
                    <span class="text-xs font-medium">Created</span>
                </div>
                <div class="timeline-connector completed"></div>
                
                <div class="timeline-step active">
                    <div class="w-10 h-10 rounded-full bg-purple-100 border-2 border-purple-500 flex items-center justify-center mb-2">
                        <i class="fas fa-clock text-purple-600"></i>
                    </div>
                    <span class="text-xs font-medium">Pending</span>
                </div>
                <div class="timeline-connector"></div>
                
                <div class="timeline-step">
                    <div class="w-10 h-10 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center mb-2">
                        <i class="fas fa-check text-gray-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Confirmed</span>
                </div>
                <div class="timeline-connector"></div>
                
                <div class="timeline-step">
                    <div class="w-10 h-10 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center mb-2">
                        <i class="fas fa-home text-gray-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Checked In</span>
                </div>
                <div class="timeline-connector"></div>
                
                <div class="timeline-step">
                    <div class="w-10 h-10 rounded-full bg-gray-100 border-2 border-gray-300 flex items-center justify-center mb-2">
                        <i class="fas fa-flag-checkered text-gray-400"></i>
                    </div>
                    <span class="text-xs font-medium text-gray-500">Completed</span>
                </div>
            </div>
            
            <!-- Current Status Info -->
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Current Status Distribution</p>
                <div class="flex items-center mt-2">
                    <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                    <span class="text-sm">40% Pending</span>
                    <div class="w-2 h-2 bg-green-500 rounded-full mx-4 ml-6"></div>
                    <span class="text-sm">50% Confirmed</span>
                    <div class="w-2 h-2 bg-red-500 rounded-full mx-4 ml-6"></div>
                    <span class="text-sm">10% Cancelled</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Day 4: Enhanced Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">All Bookings</h2>
            
            <div class="flex items-center gap-3">
                <!-- Reset Filters Button -->
                <button onclick="resetFilters()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-rotate-left mr-2"></i> Reset
                </button>
                
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

        <!-- Enhanced Filter Controls (Day 4) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Property Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                <select class="filter-dropdown" id="propertyFilter">
                    <option value="">All Properties</option>
                    @foreach($properties as $property)
                    <option value="{{ $property->id }}">{{ $property->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select class="filter-dropdown" id="statusFilter">
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
                <input type="date" class="filter-dropdown" id="dateFrom">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                <input type="date" class="filter-dropdown" id="dateTo">
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="search" 
                       id="searchInput"
                       placeholder="Search bookings by user, property, or booking ID..." 
                       class="filter-dropdown pl-10">
            </div>
        </div>

        <!-- Bookings Table (Desktop View) -->
        <div class="desktop-table-view">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                        <tr class="hover:bg-gray-50 transition-colors booking-row">
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
                                <span class="status-badge status-confirmed">
                                    <i class="fas fa-check-circle mr-1"></i> Confirmed
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewBooking(1)" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors action-btn"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editBooking(1)" 
                                            class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors action-btn"
                                            title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="sendReminder(1)" 
                                            class="px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg hover:bg-yellow-100 transition-colors action-btn"
                                            title="Send Reminder">
                                        <i class="fas fa-bell"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Booking 2 (Pending) -->
                        <tr class="hover:bg-gray-50 transition-colors booking-row">
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
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock mr-1"></i> Pending
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewBooking(2)" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors action-btn">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="confirmBooking(2)" 
                                            class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors action-btn"
                                            title="Confirm Booking">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button onclick="cancelBooking(2)" 
                                            class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors action-btn"
                                            title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Booking 3 (Checked In) -->
                        <tr class="hover:bg-gray-50 transition-colors booking-row">
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
                                <span class="status-badge status-checked_in">
                                    <i class="fas fa-sign-in-alt mr-1"></i> Checked In
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewBooking(3)" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors action-btn">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="checkOut(3)" 
                                            class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors action-btn"
                                            title="Check Out">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                    <button onclick="sendMessage(3)" 
                                            class="px-3 py-1.5 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors action-btn"
                                            title="Message">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        <!-- Booking 4 (Cancelled) -->
                        <tr class="hover:bg-gray-50 transition-colors booking-row">
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
                                <span class="status-badge status-cancelled">
                                    <i class="fas fa-times-circle mr-1"></i> Cancelled
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <button onclick="viewBooking(4)" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors action-btn">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="restoreBooking(4)" 
                                            class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors action-btn"
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

        <!-- Day 5: Mobile Card View -->
        <div class="mobile-card-view">
            <!-- Card 1 -->
            <div class="booking-card">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">#BK-001</h3>
                        <p class="text-sm text-gray-500">Sunshine Apartments • Unit 302</p>
                    </div>
                    <span class="status-badge status-confirmed">Confirmed</span>
                </div>
                
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold mr-3">JD</div>
                    <div>
                        <p class="font-medium text-gray-900">John Doe</p>
                        <p class="text-xs text-gray-500">john@example.com</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Check-in</p>
                        <p class="font-medium">Feb 1, 2024</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Check-out</p>
                        <p class="font-medium">Mar 1, 2024</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Amount</p>
                        <p class="font-bold text-gray-900">$1,250.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nights</p>
                        <p class="font-medium text-gray-900">30</p>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="viewBooking(1)" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-eye mr-1"></i> View
                    </button>
                    <button onclick="sendReminder(1)" class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-bell mr-1"></i> Remind
                    </button>
                </div>
            </div>
            
            <!-- Card 2 (Pending) -->
            <div class="booking-card">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="font-bold text-gray-900">#BK-002</h3>
                        <p class="text-sm text-gray-500">City Hostel • Room 101</p>
                    </div>
                    <span class="status-badge status-pending">Pending</span>
                </div>
                
                <div class="flex items-center mb-3">
                    <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold mr-3">SS</div>
                    <div>
                        <p class="font-medium text-gray-900">Sarah Smith</p>
                        <p class="text-xs text-gray-500">sarah@example.com</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Check-in</p>
                        <p class="font-medium">Feb 15, 2024</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Check-out</p>
                        <p class="font-medium">Mar 15, 2024</p>
                    </div>
                </div>
                
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <p class="text-xs text-gray-500">Amount</p>
                        <p class="font-bold text-gray-900">$450.00</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Nights</p>
                        <p class="font-medium text-gray-900">30</p>
                    </div>
                </div>
                
                <div class="flex space-x-2">
                    <button onclick="confirmBooking(2)" class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-check mr-1"></i> Confirm
                    </button>
                    <button onclick="cancelBooking(2)" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2 rounded-lg text-sm font-medium">
                        <i class="fas fa-times mr-1"></i> Cancel
                    </button>
                </div>
            </div>
            
            <!-- Mobile Pagination -->
            <div class="flex justify-center space-x-2 mt-6">
                <button class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium">1</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg font-medium">2</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg font-medium">3</button>
                <button class="px-4 py-2 border border-gray-300 rounded-lg font-medium">Next</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Enhanced Booking Management Functions
function viewBooking(bookingId) {
    window.location.href = `/owner/bookings/${bookingId}`;
}

function editBooking(bookingId) {
    alert(`Edit booking #${bookingId}?`);
    // In real app: redirect to edit page or open modal
}

function confirmBooking(bookingId) {
    if(confirm(`Confirm booking #${bookingId}?`)) {
        // Show loading
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Confirming...';
        button.disabled = true;
        
        // Simulate API call
        setTimeout(() => {
            alert(`Booking #${bookingId} confirmed!`);
            button.innerHTML = originalText;
            button.disabled = false;
            // Refresh or update UI
        }, 1000);
    }
}

function cancelBooking(bookingId) {
    if(confirm(`Cancel booking #${bookingId}? A cancellation fee may apply.`)) {
        // Show loading
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Cancelling...';
        button.disabled = true;
        
        setTimeout(() => {
            alert(`Booking #${bookingId} cancelled.`);
            button.innerHTML = originalText;
            button.disabled = false;
        }, 1000);
    }
}

function checkOut(bookingId) {
    if(confirm(`Check out booking #${bookingId}?`)) {
        setTimeout(() => {
            alert(`Booking #${bookingId} checked out successfully.`);
        }, 500);
    }
}

function sendReminder(bookingId) {
    alert(`Payment reminder sent for booking #${bookingId}`);
}

function sendMessage(bookingId) {
    alert(`Opening chat for booking #${bookingId}`);
}

function restoreBooking(bookingId) {
    if(confirm(`Restore cancelled booking #${bookingId}?`)) {
        setTimeout(() => {
            alert(`Booking #${bookingId} restored.`);
        }, 500);
    }
}

function refreshBookings() {
    // Show loading
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Refreshing...';
    button.disabled = true;
    
    setTimeout(() => {
        alert('Bookings list refreshed!');
        button.innerHTML = originalText;
        button.disabled = false;
    }, 1000);
}

function resetFilters() {
    document.getElementById('propertyFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFrom').value = '';
    document.getElementById('dateTo').value = '';
    document.getElementById('searchInput').value = '';
    
    // Show all rows
    document.querySelectorAll('.booking-row').forEach(row => {
        row.classList.remove('hidden');
    });
    
    // Hide empty state
    document.getElementById('empty-bookings').classList.add('hidden');
    
    alert('Filters reset to default');
}

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const filters = document.querySelectorAll('.filter-dropdown');
    
    function applyFilters() {
        const searchTerm = searchInput.value.toLowerCase();
        const propertyFilter = document.getElementById('propertyFilter').value;
        const statusFilter = document.getElementById('statusFilter').value;
        
        let visibleCount = 0;
        const rows = document.querySelectorAll('.booking-row');
        
        rows.forEach(row => {
            const rowText = row.textContent.toLowerCase();
            const propertyMatch = !propertyFilter || row.dataset.property === propertyFilter;
            const statusMatch = !statusFilter || row.dataset.status === statusFilter;
            const searchMatch = !searchTerm || rowText.includes(searchTerm);
            
            if (propertyMatch && statusMatch && searchMatch) {
                row.classList.remove('hidden');
                visibleCount++;
            } else {
                row.classList.add('hidden');
            }
        });
        
        // Show/hide empty state
        const emptyState = document.getElementById('empty-bookings');
        if (visibleCount === 0) {
            emptyState.classList.remove('hidden');
        } else {
            emptyState.classList.add('hidden');
        }
    }
    
    searchInput.addEventListener('input', applyFilters);
    filters.forEach(filter => filter.addEventListener('change', applyFilters));
    
    // Add view toggle button for mobile testing
    const viewToggleBtn = document.createElement('button');
    viewToggleBtn.innerHTML = '<i class="fas fa-mobile-alt mr-2"></i>Toggle View';
    viewToggleBtn.className = 'fixed bottom-4 right-4 bg-purple-600 text-white px-4 py-2 rounded-full shadow-lg z-50 md:hidden';
    viewToggleBtn.onclick = function() {
        const mobileView = document.querySelector('.mobile-card-view');
        const desktopView = document.querySelector('.desktop-table-view');
        
        if(window.getComputedStyle(mobileView).display === 'none') {
            mobileView.style.display = 'block';
            desktopView.style.display = 'none';
        } else {
            mobileView.style.display = 'none';
            desktopView.style.display = 'block';
        }
    };
    document.body.appendChild(viewToggleBtn);
});
</script>
@endpush