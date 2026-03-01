@extends('owner.layout.owner-layout')

@section('title', 'Booking Management - RentEase')
@section('page-title', 'Booking Management')
@section('page-subtitle', 'Manage all property bookings')

@push('styles')
<style>
    /* Status Badge Styles */
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
    .status-checked_in {
        @apply bg-indigo-100 text-indigo-800 border border-indigo-200;
    }
    .status-checked_out {
        @apply bg-purple-100 text-purple-800 border border-purple-200;
    }
    
    /* Timeline Styles */
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
    
    /* Filter Styles */
    .filter-dropdown {
        @apply w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white text-gray-700;
    }
    
    /* Mobile Card View */
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
    
    /* Chat badge animation */
    .chat-badge {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.1);
        }
        100% {
            transform: scale(1);
        }
    }
</style>
@endpush

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
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $stats['today_count'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">This Month</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">{{ $stats['month_count'] ?? 0 }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500">Revenue</p>
                    <p class="text-xl font-bold text-green-600 mt-1">${{ number_format($stats['month_revenue'] ?? 0, 2) }}</p>
                </div>
            </div>
        </div>
        
        <!-- Status Statistics -->
        <div class="mt-6 pt-6 border-t border-gray-100">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <h3 class="text-lg font-semibold text-gray-800">Booking Status Overview</h3>
                <div class="flex flex-wrap gap-2">
                    <div class="text-center px-3">
                        <span class="status-badge status-pending">Pending</span>
                        <p class="text-lg font-bold mt-1">{{ $stats['status_counts']['PENDING'] ?? 0 }}</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-confirmed">Confirmed</span>
                        <p class="text-lg font-bold mt-1">{{ $stats['status_counts']['CONFIRMED'] ?? 0 }}</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-checked_in">Checked In</span>
                        <p class="text-lg font-bold mt-1">{{ $stats['status_counts']['CHECKED_IN'] ?? 0 }}</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-checked_out">Checked Out</span>
                        <p class="text-lg font-bold mt-1">{{ $stats['status_counts']['CHECKED_OUT'] ?? 0 }}</p>
                    </div>
                    <div class="text-center px-3">
                        <span class="status-badge status-cancelled">Cancelled</span>
                        <p class="text-lg font-bold mt-1">{{ $stats['status_counts']['CANCELLED'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline Section -->
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
                <div class="flex items-center mt-2 flex-wrap gap-2">
                    <div class="flex items-center">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mr-2"></div>
                        <span class="text-sm">Pending: {{ $stats['status_counts']['PENDING'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center ml-4">
                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2"></div>
                        <span class="text-sm">Confirmed: {{ $stats['status_counts']['CONFIRMED'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center ml-4">
                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                        <span class="text-sm">Cancelled: {{ $stats['status_counts']['CANCELLED'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters Section -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-900">All Bookings</h2>
            
            <div class="flex items-center gap-3">
                <!-- Reset Filters Button -->
                <a href="{{ route('owner.bookings.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-rotate-left mr-2"></i> Reset
                </a>
                
                <!-- Export Button -->
                <a href="{{ route('owner.bookings.export') }}?{{ http_build_query(request()->query()) }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
                
                <!-- Refresh Button -->
                <button onclick="window.location.reload()" 
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                    <i class="fas fa-sync-alt mr-2"></i> Refresh
                </button>
            </div>
        </div>

        <!-- Enhanced Filter Controls -->
        <form method="GET" action="{{ route('owner.bookings.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Property Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Property</label>
                    <select name="property_id" class="filter-dropdown" id="propertyFilter">
                        <option value="">All Properties</option>
                        @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="filter-dropdown" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending</option>
                        <option value="CONFIRMED" {{ request('status') == 'CONFIRMED' ? 'selected' : '' }}>Confirmed</option>
                        <option value="CHECKED_IN" {{ request('status') == 'CHECKED_IN' ? 'selected' : '' }}>Checked In</option>
                        <option value="CHECKED_OUT" {{ request('status') == 'CHECKED_OUT' ? 'selected' : '' }}>Checked Out</option>
                        <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Date Range -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Date</label>
                    <input type="date" name="date_from" class="filter-dropdown" id="dateFrom" value="{{ request('date_from') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Date</label>
                    <input type="date" name="date_to" class="filter-dropdown" id="dateTo" value="{{ request('date_to') }}">
                </div>
            </div>

            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="search" 
                           name="search"
                           id="searchInput"
                           value="{{ request('search') }}"
                           placeholder="Search bookings by user, property, or booking ID..." 
                           class="filter-dropdown pl-10">
                    <button type="submit" class="hidden">Search</button>
                </div>
            </div>
        </form>

        <!-- Bookings Table (Desktop View) -->
        <div class="desktop-table-view">
            @if($bookings->count() > 0)
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
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors booking-row" 
                            data-property="{{ $booking->property_id }}"
                            data-status="{{ strtolower($booking->status) }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#{{ $booking->booking_reference }}</div>
                                <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        @if($booking->user->avatar_url)
                                            <img src="{{ $booking->user->avatar_url }}" alt="{{ $booking->user->name }}" class="h-8 w-8 rounded-full">
                                        @else
                                            <i class="fas fa-user text-purple-600 text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $booking->user->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $booking->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900 font-medium">{{ $booking->property->name }}</div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-{{ $booking->property->type == 'HOSTEL' ? 'bed' : 'home' }} mr-1"></i>
                                    {{ $booking->property->type }} 
                                    @if($booking->room)
                                    • {{ $booking->room->room_number }}
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $booking->check_in->format('M d, Y') }} - {{ $booking->check_out->format('M d, Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $booking->check_in->diffInDays($booking->check_out) }} days
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">${{ number_format($booking->total_amount, 2) }}</div>
                                <div class="text-xs text-gray-500">Commission: ${{ number_format($booking->commission_amount, 2) }}</div>
                            </td>
                 
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <!-- View Details Button -->
                                    <button onclick="viewBooking({{ $booking->id }})" 
                                            class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors action-btn"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <!-- CHAT WITH TENANT BUTTON -->
                                    <a href="{{ route('owner.chat.show', $booking) }}" 
                                       class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors action-btn relative"
                                       title="Chat with Tenant">
                                        <i class="fas fa-comment"></i>
                                        @if(isset($booking->unread_chat_count) && $booking->unread_chat_count > 0)
                                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center chat-badge">
                                                {{ $booking->unread_chat_count }}
                                            </span>
                                        @endif
                                    </a>
                                    
                                    <!-- Status Update Buttons -->
                                    @if($booking->status == 'PENDING')
                                    <button onclick="updateStatus({{ $booking->id }}, 'CONFIRMED')" 
                                            class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors action-btn"
                                            title="Confirm Booking">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    @elseif($booking->status == 'CONFIRMED')
                                    <button onclick="updateStatus({{ $booking->id }}, 'CHECKED_IN')" 
                                            class="px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 transition-colors action-btn"
                                            title="Check In">
                                        <i class="fas fa-sign-in-alt"></i>
                                    </button>
                                    @elseif($booking->status == 'CHECKED_IN')
                                    <button onclick="updateStatus({{ $booking->id }}, 'CHECKED_OUT')" 
                                            class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors action-btn"
                                            title="Check Out">
                                        <i class="fas fa-sign-out-alt"></i>
                                    </button>
                                    @endif
                                    
                                    @if(in_array($booking->status, ['PENDING', 'CONFIRMED']))
                                    <button onclick="cancelBooking({{ $booking->id }})" 
                                            class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors action-btn"
                                            title="Cancel">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6 pt-6 border-t border-gray-200">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold">{{ $bookings->firstItem() }} to {{ $bookings->lastItem() }}</span> 
                    of <span class="font-semibold">{{ $bookings->total() }}</span> bookings
                </div>
                {{ $bookings->links() }}
            </div>
            @else
            <!-- Empty State -->
            <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-700">No bookings found</h3>
                <p class="text-gray-500 mt-2">Try adjusting your filters or come back later.</p>
                <a href="{{ route('owner.bookings.index') }}" 
                   class="mt-4 px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 inline-block">
                    Clear Filters
                </a>
            </div>
            @endif
        </div>

        <!-- Mobile Card View -->
        <div class="mobile-card-view">
            @if($bookings->count() > 0)
                @foreach($bookings as $booking)
                <div class="booking-card">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="font-bold text-gray-900">#{{ $booking->booking_reference }}</h3>
                            <p class="text-sm text-gray-500">{{ $booking->property->name }}</p>
                        </div>
                        @php
                            $statusClasses = [
                                'PENDING' => 'status-pending',
                                'CONFIRMED' => 'status-confirmed',
                                'CHECKED_IN' => 'status-checked_in',
                                'CHECKED_OUT' => 'status-checked_out',
                                'CANCELLED' => 'status-cancelled'
                            ];
                        @endphp
                        <span class="status-badge {{ $statusClasses[$booking->status] ?? 'status-pending' }}">
                            {{ ucfirst(strtolower(str_replace('_', ' ', $booking->status))) }}
                        </span>
                    </div>
                    
                    <div class="flex items-center mb-3">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600 font-semibold mr-3">
                            {{ substr($booking->user->name, 0, 2) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Check-in</p>
                            <p class="font-medium">{{ $booking->check_in->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Check-out</p>
                            <p class="font-medium">{{ $booking->check_out->format('M d, Y') }}</p>
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <p class="text-xs text-gray-500">Amount</p>
                            <p class="font-bold text-gray-900">${{ number_format($booking->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Nights</p>
                            <p class="font-medium text-gray-900">{{ $booking->check_in->diffInDays($booking->check_out) }}</p>
                        </div>
                    </div>
                    
                    <div class="flex space-x-2">
                        <!-- View Button -->
                        <button onclick="viewBooking({{ $booking->id }})" 
                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-eye mr-1"></i> View
                        </button>
                        
                        <!-- CHAT BUTTON FOR MOBILE -->
                        <a href="{{ route('owner.chat.show', $booking) }}" 
                           class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium relative">
                            <i class="fas fa-comment mr-1"></i> Chat
                            @if(isset($booking->unread_chat_count) && $booking->unread_chat_count > 0)
                                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center chat-badge">
                                    {{ $booking->unread_chat_count }}
                                </span>
                            @endif
                        </a>
                        
                        @if($booking->status == 'PENDING')
                        <button onclick="updateStatus({{ $booking->id }}, 'CONFIRMED')" 
                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-check mr-1"></i> Confirm
                        </button>
                        @elseif($booking->status == 'CONFIRMED')
                        <button onclick="updateStatus({{ $booking->id }}, 'CHECKED_IN')" 
                                class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-2 rounded-lg text-sm font-medium">
                            <i class="fas fa-sign-in-alt mr-1"></i> Check In
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach

                <!-- Mobile Pagination -->
                {{ $bookings->links('pagination::simple-tailwind') }}
            @else
                <div class="text-center py-12">
                    <i class="fas fa-calendar-times text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-700">No bookings found</h3>
                    <p class="text-gray-500 mt-2">Try adjusting your filters or come back later.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Get CSRF token safely
function getCsrfToken() {
    // Try multiple ways to get the CSRF token
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    if (metaTag && metaTag.content) {
        return metaTag.content;
    }
    
    const tokenInput = document.querySelector('input[name="_token"]');
    if (tokenInput && tokenInput.value) {
        return tokenInput.value;
    }
    
    // Fallback - try to get from Laravel's global JS object
    if (window.Laravel && window.Laravel.csrfToken) {
        return window.Laravel.csrfToken;
    }
    
    console.error('CSRF token not found');
    return '';
}

// Enhanced Booking Management Functions
function viewBooking(bookingId) {
    window.location.href = `/owner/bookings/${bookingId}`;
}

function updateStatus(bookingId, status) {
    const statusText = {
        'CONFIRMED': 'Confirmed',
        'CHECKED_IN': 'Checked In',
        'CHECKED_OUT': 'Checked Out',
        'CANCELLED': 'Cancelled'
    }[status] || status;
    
    if(!confirm(`Are you sure you want to change status to "${statusText}"?`)) {
        return;
    }
    
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        alert('Security token missing. Please refresh the page and try again.');
        return;
    }
    
    showLoading('Updating status...');
    
    // Use the correct route - make sure it matches your web.php
    fetch(`/owner/bookings/${bookingId}/status`, {
        method: 'POST', // Try POST first (Laravel accepts PUT via POST with _method)
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            _method: 'PUT', // For Laravel method spoofing
            status: status,
            notes: ''
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('success', data.message || 'Status updated successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('error', data.message || 'Failed to update status');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('error', 'Failed to update status. Please try again.');
    });
}

function cancelBooking(bookingId) {
    if(!confirm('Cancel this booking? A cancellation fee may apply.')) {
        return;
    }
    
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        alert('Security token missing. Please refresh the page and try again.');
        return;
    }
    
    showLoading('Cancelling booking...');
    
    fetch(`/owner/bookings/${bookingId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ 
            _method: 'PUT',
            status: 'CANCELLED',
            notes: 'Cancelled by owner'
        })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('success', 'Booking cancelled successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast('error', data.message || 'Failed to cancel booking');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('error', 'Failed to cancel booking. Please try again.');
    });
}

function sendReminder(bookingId) {
    const csrfToken = getCsrfToken();
    
    if (!csrfToken) {
        alert('Security token missing. Please refresh the page and try again.');
        return;
    }
    
    showLoading('Sending reminder...');
    
    fetch(`/owner/bookings/${bookingId}/reminder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        hideLoading();
        if (data.success) {
            showToast('success', data.message);
        } else {
            showToast('error', data.message);
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showToast('error', 'Failed to send reminder');
    });
}

// Utility Functions
function showLoading(message = 'Loading...') {
    // Remove existing loading overlay
    hideLoading();
    
    // Create loading overlay
    const overlay = document.createElement('div');
    overlay.id = 'loading-overlay';
    overlay.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    overlay.innerHTML = `
        <div class="bg-white rounded-xl p-6 flex flex-col items-center min-w-64">
            <div class="loading-spinner mb-4"></div>
            <p class="text-gray-700 font-medium">${message}</p>
        </div>
    `;
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loading-overlay');
    if (overlay) {
        overlay.remove();
    }
}

function showToast(type, message) {
    // Remove existing toast
    const existingToast = document.querySelector('.toast-notification');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Create new toast
    const toast = document.createElement('div');
    toast.className = `toast-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 animate-toast-in ${
        type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200'
    }`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} mr-2"></i>
            <span class="font-medium">${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentNode) {
            toast.classList.add('animate-toast-out');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, 300);
        }
    }, 5000);
}

// Add CSS for animations
const style = document.createElement('style');
style.textContent = `
    .loading-spinner {
        width: 40px;
        height: 40px;
        border: 3px solid #f3f3f3;
        border-top: 3px solid #8b5cf6;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .animate-toast-in {
        animation: toastIn 0.3s ease-out;
    }
    
    .animate-toast-out {
        animation: toastOut 0.3s ease-in forwards;
    }
    
    @keyframes toastIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes toastOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Add CSRF token to all fetch requests globally (optional backup)
const originalFetch = window.fetch;
window.fetch = function(...args) {
    const [url, options = {}] = args;
    
    // Don't modify external URLs
    if (url.startsWith('http') && !url.includes(window.location.host)) {
        return originalFetch.call(window, url, options);
    }
    
    // Add CSRF token to headers if not already present
    if (options.headers && typeof options.headers === 'object') {
        const csrfToken = getCsrfToken();
        if (csrfToken && !options.headers['X-CSRF-TOKEN']) {
            options.headers['X-CSRF-TOKEN'] = csrfToken;
        }
    }
    
    return originalFetch.call(window, url, options);
};

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
</script>
@endpush