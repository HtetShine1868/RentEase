@extends('layouts.owner')

@section('title', 'Booking Requests')
@section('header', 'Booking Management')
@section('subheader', 'Manage rental requests for your properties')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Requests</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Approved</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Confirmed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['confirmed'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-check-double text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rejected</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('owner.bookings.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Booking #, Customer, Property..." 
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">All Status ({{ $statusCounts['all'] }})</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Pending ({{ $statusCounts['pending'] }})</option>
                    <option value="APPROVED" {{ request('status') == 'APPROVED' ? 'selected' : '' }}>Approved ({{ $statusCounts['approved'] }})</option>
                    <option value="CONFIRMED" {{ request('status') == 'CONFIRMED' ? 'selected' : '' }}>Confirmed ({{ $statusCounts['confirmed'] }})</option>
                    <option value="REJECTED" {{ request('status') == 'REJECTED' ? 'selected' : '' }}>Rejected ({{ $statusCounts['rejected'] }})</option>
                </select>
            </div>
            
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Property</label>
                <select name="property_id" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="all">All Properties</option>
                    @foreach($properties as $property)
                        <option value="{{ $property->id }}" {{ request('property_id') == $property->id ? 'selected' : '' }}>
                            {{ $property->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="w-40">
                <label class="block text-sm font-medium text-gray-700 mb-1">Date Range</label>
                <select name="date_range" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">All Time</option>
                    <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                    <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                    <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                </select>
            </div>
            
            <div class="flex items-end space-x-2">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i> Apply
                </button>
                <a href="{{ route('owner.bookings.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    <i class="fas fa-times mr-2"></i> Clear
                </a>
                <a href="{{ route('owner.bookings.export') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    <i class="fas fa-download mr-2"></i> Export
                </a>
            </div>
        </form>
        
        <!-- Active Filters Display -->
        @if(request()->anyFilled(['search', 'status', 'property_id', 'date_range']) && request('status') != 'all' && request('property_id') != 'all')
            <div class="flex flex-wrap gap-2 mt-3">
                <span class="text-sm text-gray-500">Active filters:</span>
                @if(request('search'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                        Search: "{{ request('search') }}"
                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="ml-1 text-blue-600 hover:text-blue-800">&times;</a>
                    </span>
                @endif
                @if(request('status') && request('status') != 'all')
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                        Status: {{ request('status') }}
                        <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="ml-1 text-green-600 hover:text-green-800">&times;</a>
                    </span>
                @endif
                @if(request('property_id') && request('property_id') != 'all')
                    @php $property = $properties->find(request('property_id')); @endphp
                    @if($property)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                            Property: {{ $property->name }}
                            <a href="{{ request()->fullUrlWithQuery(['property_id' => null]) }}" class="ml-1 text-purple-600 hover:text-purple-800">&times;</a>
                        </span>
                    @endif
                @endif
                @if(request('date_range'))
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                        Date: {{ str_replace('_', ' ', request('date_range')) }}
                        <a href="{{ request()->fullUrlWithQuery(['date_range' => null]) }}" class="ml-1 text-yellow-600 hover:text-yellow-800">&times;</a>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Booking #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Property</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dates</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($bookings as $booking)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <span class="font-medium">{{ $booking->booking_reference }}</span>
                            <div class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $booking->user->name }}</div>
                            <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-medium">{{ $booking->property->name }}</div>
                            @if($booking->room)
                                <div class="text-sm text-gray-500">Room {{ $booking->room->room_number }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</div>
                            <div class="text-xs text-gray-500">{{ $booking->duration_days }} days</div>
                        </td>
                        <td class="px-6 py-4 font-medium">MMK{{ number_format($booking->total_amount) }}</td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                    'APPROVED' => 'bg-green-100 text-green-800',
                                    'REJECTED' => 'bg-red-100 text-red-800',
                                    'PAYMENT_PENDING' => 'bg-blue-100 text-blue-800',
                                    'CONFIRMED' => 'bg-green-100 text-green-800',
                                    'CHECKED_IN' => 'bg-indigo-100 text-indigo-800',
                                    'CHECKED_OUT' => 'bg-gray-100 text-gray-800',
                                    'CANCELLED' => 'bg-red-100 text-red-800',
                                ];
                                $statusIcons = [
                                    'PENDING' => 'fa-clock',
                                    'APPROVED' => 'fa-check-circle',
                                    'REJECTED' => 'fa-times-circle',
                                    'PAYMENT_PENDING' => 'fa-credit-card',
                                    'CONFIRMED' => 'fa-check-double',
                                    'CHECKED_IN' => 'fa-door-open',
                                    'CHECKED_OUT' => 'fa-check-circle',
                                    'CANCELLED' => 'fa-ban',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100' }}">
                                <i class="fas {{ $statusIcons[$booking->status] ?? 'fa-info-circle' }} mr-1"></i>
                                {{ str_replace('_', ' ', $booking->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('owner.bookings.show', $booking->id) }}" 
                               class="inline-flex items-center px-3 py-1 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 transition">
                                <i class="fas fa-eye mr-1"></i>
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                            <p class="text-lg font-medium">No booking requests found</p>
                            <p class="text-sm mt-1">When customers request to book your properties, they'll appear here.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($bookings->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $bookings->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection