@extends('owner.layout.owner-layout')

@section('title', 'Booking #' . $booking->booking_reference . ' - RentEase')
@section('page-title', 'Booking Details')

@push('styles')
<style>
    .info-card {
        @apply bg-white rounded-xl border border-gray-200 p-6;
    }
    
    .info-label {
        @apply text-sm font-medium text-gray-700 mb-1;
    }
    
    .info-value {
        @apply text-gray-900 font-semibold;
    }
    
    .status-badge {
        @apply px-3 py-1 rounded-full text-sm font-medium;
    }
    
    .status-pending { @apply bg-yellow-100 text-yellow-800; }
    .status-confirmed { @apply bg-green-100 text-green-800; }
    .status-checked_in { @apply bg-blue-100 text-blue-800; }
    .status-checked_out { @apply bg-purple-100 text-purple-800; }
    .status-cancelled { @apply bg-red-100 text-red-800; }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="info-card">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold text-gray-900">Booking #{{ $booking->booking_reference }}</h1>
                    @php
                        $statusClasses = [
                            'PENDING' => 'status-pending',
                            'CONFIRMED' => 'status-confirmed',
                            'CHECKED_IN' => 'status-checked_in',
                            'CHECKED_OUT' => 'status-checked_out',
                            'CANCELLED' => 'status-cancelled'
                        ];
                        
                        $statusIcons = [
                            'PENDING' => 'clock',
                            'CONFIRMED' => 'check-circle',
                            'CHECKED_IN' => 'sign-in-alt',
                            'CHECKED_OUT' => 'sign-out-alt',
                            'CANCELLED' => 'times-circle'
                        ];
                    @endphp
                    <span class="status-badge {{ $statusClasses[$booking->status] }}">
                        <i class="fas fa-{{ $statusIcons[$booking->status] }} mr-1"></i>
                        {{ ucfirst(strtolower(str_replace('_', ' ', $booking->status))) }}
                    </span>
                </div>
                <p class="text-gray-600">
                    Created {{ $booking->created_at->format('M d, Y \\a\\t h:i A') }}
                    • Last updated {{ $booking->updated_at->diffForHumans() }}
                </p>
            </div>
            
            <div class="flex gap-3">
                <a href="{{ route('owner.bookings.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg font-medium hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
                
                @if($booking->status == 'PENDING')
                <button onclick="updateStatus('CONFIRMED')" 
                        class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700">
                    <i class="fas fa-check mr-2"></i> Confirm
                </button>
                @elseif($booking->status == 'CONFIRMED')
                <button onclick="updateStatus('CHECKED_IN')" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                    <i class="fas fa-sign-in-alt mr-2"></i> Check In
                </button>
                @elseif($booking->status == 'CHECKED_IN')
                <button onclick="updateStatus('CHECKED_OUT')" 
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700">
                    <i class="fas fa-sign-out-alt mr-2"></i> Check Out
                </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Guest Information -->
            <div class="info-card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Guest Information</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-purple-100 flex items-center justify-center mr-4">
                            @if($booking->user->avatar_url)
                                <img src="{{ $booking->user->avatar_url }}" alt="{{ $booking->user->name }}" 
                                     class="w-16 h-16 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-purple-600 text-2xl"></i>
                            @endif
                        </div>
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ $booking->user->name }}</p>
                            <p class="text-gray-600">{{ $booking->user->email }}</p>
                            @if($booking->user->phone)
                            <p class="text-gray-500 mt-1">
                                <i class="fas fa-phone mr-2"></i>{{ $booking->user->phone }}
                            </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property & Stay Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Property Info -->
                <div class="info-card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Property Information</h2>
                    <div class="space-y-4">
                        <div>
                            <p class="info-label">Property Name</p>
                            <p class="info-value">{{ $booking->property->name }}</p>
                            <p class="text-gray-600 text-sm mt-1">
                                <i class="fas fa-{{ $booking->property->type == 'HOSTEL' ? 'bed' : 'home' }} mr-1"></i>
                                {{ $booking->property->type }}
                            </p>
                        </div>
                        
                        <div>
                            <p class="info-label">Location</p>
                            <p class="info-value">{{ $booking->property->city }}, {{ $booking->property->area }}</p>
                            <p class="text-gray-600 text-sm mt-1">{{ $booking->property->address }}</p>
                        </div>
                        
                        @if($booking->room)
                        <div>
                            <p class="info-label">Room Details</p>
                            <p class="info-value">{{ $booking->room->room_number }} ({{ $booking->room->room_type }})</p>
                            <p class="text-gray-600 text-sm mt-1">Capacity: {{ $booking->room->capacity }} persons</p>
                        </div>
                        @else
                        <div>
                            <p class="info-label">Unit Type</p>
                            <p class="info-value">Full Apartment</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Stay Details -->
                <div class="info-card">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Stay Details</h2>
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="info-label">Check-in Date</p>
                                <p class="info-value">{{ $booking->check_in->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="info-label">Check-out Date</p>
                                <p class="info-value">{{ $booking->check_out->format('M d, Y') }}</p>
                            </div>
                        </div>
                        
                        <div>
                            <p class="info-label">Duration</p>
                            <p class="info-value">{{ $booking->check_in->diffInDays($booking->check_out) }} days</p>
                        </div>
                        
                        <div>
                            <p class="info-label">Booking Created</p>
                            <p class="info-value">{{ $booking->created_at->format('M d, Y \\a\\t h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-6">
            <!-- Financial Summary -->
            <div class="info-card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Financial Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Room Charges</span>
                        <span class="font-semibold">
                            ${{ number_format($booking->room_price_per_day * $booking->check_in->diffInDays($booking->check_out), 2) }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Commission</span>
                        <span class="font-semibold text-red-600">
                            ${{ number_format($booking->commission_amount, 2) }}
                        </span>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900">Total Amount</span>
                            <span class="font-bold text-lg text-gray-900">
                                ${{ number_format($booking->total_amount, 2) }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Status -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-500">Payment Status</p>
                            @php
                                $hasPaid = $booking->payments->where('status', 'COMPLETED')->count() > 0;
                            @endphp
                            <p class="font-semibold {{ $hasPaid ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $hasPaid ? 'Paid' : 'Pending Payment' }}
                            </p>
                        </div>
                        @if(!$hasPaid)
                        <button onclick="sendReminder()" 
                                class="px-3 py-1.5 bg-yellow-100 text-yellow-800 rounded-lg text-sm font-medium hover:bg-yellow-200">
                            <i class="fas fa-bell mr-1"></i> Remind
                        </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="info-card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="space-y-3">
                    <button onclick="sendMessage()" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg font-medium hover:bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-envelope mr-2"></i> Send Message
                    </button>
                    
                    <button onclick="printInvoice()" 
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg font-medium hover:bg-gray-50 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i> Print Invoice
                    </button>
                    
                    @if($booking->status !== 'CANCELLED' && $booking->status !== 'CHECKED_OUT')
                    <button onclick="cancelBooking()" 
                            class="w-full px-4 py-2.5 border border-red-300 text-red-700 rounded-lg font-medium hover:bg-red-50 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i> Cancel Booking
                    </button>
                    @endif
                </div>
            </div>

            <!-- Booking Timeline -->
            <div class="info-card">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Booking Timeline</h2>
                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-calendar-plus text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Booking Created</p>
                            <p class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y \\a\\t h:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($booking->status == 'CANCELLED')
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center mr-3">
                            <i class="fas fa-times-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-medium">Booking Cancelled</p>
                            <p class="text-sm text-gray-500">{{ $booking->updated_at->format('M d, Y \\a\\t h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Get CSRF token
function getCsrfToken() {
    const metaTag = document.querySelector('meta[name="csrf-token"]');
    return metaTag ? metaTag.content : '';
}

function updateStatus(status) {
    const statusText = {
        'CONFIRMED': 'Confirmed',
        'CHECKED_IN': 'Checked In',
        'CHECKED_OUT': 'Checked Out',
        'CANCELLED': 'Cancelled'
    }[status] || status;
    
    if(confirm(`Are you sure you want to change status to "${statusText}"?`)) {
        fetch(`/owner/bookings/{{ $booking->id }}/status`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ 
                status: status,
                notes: ''
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Status updated successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Failed to update status');
        });
    }
}

function sendReminder() {
    fetch(`/owner/bookings/{{ $booking->id }}/reminder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': getCsrfToken(),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Failed to send reminder');
    });
}

function sendMessage() {
    alert('Messaging feature coming soon!');
}

function printInvoice() {
    window.open(`/owner/bookings/{{ $booking->id }}/invoice`, '_blank');
}

function cancelBooking() {
    if(confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
        updateStatus('CANCELLED');
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Add any initialization code here
});
</script>
@endsection