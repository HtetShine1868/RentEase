@extends('layouts.apps')

@section('title', 'Booking Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <a href="{{ route('rental.index') }}" class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Back to My Bookings
        </a>

        <!-- Status Banner -->
        <div class="mb-6">
            @php
                $statusColors = [
                    'PENDING' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'border' => 'border-yellow-200', 'icon' => 'fa-clock'],
                    'APPROVED' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => 'fa-check-circle'],
                    'REJECTED' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'fa-times-circle'],
                    'PAYMENT_PENDING' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'border' => 'border-blue-200', 'icon' => 'fa-credit-card'],
                    'CONFIRMED' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'border' => 'border-green-200', 'icon' => 'fa-check-double'],
                    'CHECKED_IN' => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-800', 'border' => 'border-indigo-200', 'icon' => 'fa-door-open'],
                    'CHECKED_OUT' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'icon' => 'fa-check-circle'],
                    'CANCELLED' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'border' => 'border-red-200', 'icon' => 'fa-ban'],
                ];
                $status = $booking->status;
                $statusConfig = $statusColors[$status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'border' => 'border-gray-200', 'icon' => 'fa-info-circle'];
                
                $statusMessages = [
                    'PENDING' => 'Your request is waiting for owner approval.',
                    'APPROVED' => 'Your request was approved! Please complete payment within 24 hours.',
                    'REJECTED' => 'Your request was declined by the owner.',
                    'PAYMENT_PENDING' => 'Payment initiated, waiting for confirmation.',
                    'CONFIRMED' => 'Booking confirmed! You\'re all set.',
                    'CHECKED_IN' => 'You are currently checked in.',
                    'CHECKED_OUT' => 'Your stay has ended. Thank you!',
                    'CANCELLED' => 'This booking was cancelled.',
                ];
            @endphp

            <div class="{{ $statusConfig['bg'] }} border {{ $statusConfig['border'] }} rounded-lg p-4 flex items-center justify-between">
                <div class="flex items-center">
                    <i class="fas {{ $statusConfig['icon'] }} text-2xl mr-3 {{ $statusConfig['text'] }}"></i>
                    <div>
                        <h3 class="font-bold text-lg {{ $statusConfig['text'] }}">{{ str_replace('_', ' ', $status) }}</h3>
                        <p class="text-sm opacity-75">{{ $statusMessages[$status] ?? '' }}</p>
                    </div>
                </div>
                <div class="text-sm">
                    Booking #: {{ $booking->booking_reference }}
                </div>
            </div>

            <!-- Payment Deadline for Approved Bookings -->
            @if($status === 'APPROVED' && isset($paymentDeadline))
                <div class="mt-3 bg-{{ $isExpiring ? 'red' : 'blue' }}-50 border border-{{ $isExpiring ? 'red' : 'blue' }}-200 rounded-lg p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="fas fa-hourglass-half text-{{ $isExpiring ? 'red' : 'blue' }}-500 mr-2"></i>
                            <span class="text-sm text-{{ $isExpiring ? 'red' : 'blue' }}-700">
                                <strong>Payment deadline:</strong> {{ $paymentDeadline->format('M d, Y g:i A') }}
                            </span>
                        </div>
                        <span class="text-sm font-medium {{ $isExpiring ? 'text-red-600' : 'text-blue-600' }}">
                            {{ $hoursLeft }} hours left
                        </span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column - Booking Info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Property Details -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Property Details</h3>
                    <div class="flex items-start">
                        @if($booking->property->primaryImage)
                            <img src="{{ Storage::url($booking->property->primaryImage->image_path) }}" 
                                 alt="{{ $booking->property->name }}"
                                 class="w-24 h-24 object-cover rounded-lg mr-4">
                        @else
                            <div class="w-24 h-24 bg-gray-200 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-building text-gray-400 text-3xl"></i>
                            </div>
                        @endif
                        <div>
                            <h4 class="font-semibold text-lg">{{ $booking->property->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ $booking->property->address }}, {{ $booking->property->city }}</p>
                            <div class="flex items-center mt-2 space-x-2">
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    {{ $booking->property->type }}
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ $booking->property->bedrooms }} Beds
                                </span>
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    {{ $booking->property->bathrooms }} Baths
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Timeline -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Timeline</h3>
                    <div class="relative">
                        <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-gray-200"></div>
                        
                        @php
                            $timelineSteps = [
                                'requested' => [
                                    'label' => 'Request Submitted',
                                    'time' => $booking->created_at,
                                    'icon' => 'fa-paper-plane',
                                    'color' => 'bg-blue-500'
                                ],
                                'approved' => [
                                    'label' => 'Owner Approved',
                                    'time' => $booking->approved_at,
                                    'icon' => 'fa-check-circle',
                                    'color' => 'bg-green-500'
                                ],
                                'payment' => [
                                    'label' => 'Payment Made',
                                    'time' => $booking->paid_at,
                                    'icon' => 'fa-credit-card',
                                    'color' => 'bg-purple-500'
                                ],
                                'confirmed' => [
                                    'label' => 'Booking Confirmed',
                                    'time' => $booking->paid_at ?? $booking->approved_at,
                                    'icon' => 'fa-check-double',
                                    'color' => 'bg-green-600'
                                ],
                                'checked_in' => [
                                    'label' => 'Checked In',
                                    'time' => null, // Would need check_in_time field
                                    'icon' => 'fa-door-open',
                                    'color' => 'bg-indigo-500'
                                ],
                                'checked_out' => [
                                    'label' => 'Checked Out',
                                    'time' => $booking->status === 'CHECKED_OUT' ? $booking->check_out : null,
                                    'icon' => 'fa-check-circle',
                                    'color' => 'bg-gray-500'
                                ],
                            ];
                        @endphp

                        @foreach($timelineSteps as $key => $step)
                            @if($step['time'] || ($key == 'requested') || 
                                ($key == 'approved' && $booking->status != 'PENDING') ||
                                ($key == 'payment' && in_array($booking->status, ['PAYMENT_PENDING', 'CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT'])) ||
                                ($key == 'confirmed' && in_array($booking->status, ['CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT'])) ||
                                ($key == 'checked_in' && $booking->status == 'CHECKED_IN') ||
                                ($key == 'checked_out' && $booking->status == 'CHECKED_OUT'))
                                <div class="relative pl-12 pb-6 last:pb-0">
                                    <div class="absolute left-2 -translate-x-1/2 w-8 h-8 rounded-full {{ $step['color'] }} flex items-center justify-center text-white">
                                        <i class="fas {{ $step['icon'] }} text-sm"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-medium">{{ $step['label'] }}</h4>
                                        @if($step['time'])
                                            <p class="text-sm text-gray-500">{{ $step['time']->format('M d, Y g:i A') }}</p>
                                        @elseif($key == 'approved' && $booking->status == 'PENDING')
                                            <p class="text-sm text-yellow-600">Waiting for owner approval</p>
                                        @elseif($key == 'payment' && $booking->status == 'APPROVED')
                                            <p class="text-sm text-blue-600">Payment required</p>
                                        @elseif($key == 'confirmed' && $booking->status == 'PAYMENT_PENDING')
                                            <p class="text-sm text-blue-600">Payment pending confirmation</p>
                                        @elseif($key == 'checked_in' && $booking->status == 'CONFIRMED')
                                            <p class="text-sm text-indigo-600">Check-in available on {{ Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</p>
                                        @else
                                            <p class="text-sm text-gray-400">Pending</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <!-- Special Requests -->
                @if($booking->special_requests)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">Special Requests</h3>
                        <p class="text-gray-700">{{ $booking->special_requests }}</p>
                    </div>
                @endif
            </div>

            <!-- Right Column - Booking Summary -->
            <div class="space-y-6">
                <!-- Booking Summary Card -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Booking Summary</h3>
                    
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500">Check-in</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Check-out</p>
                            <p class="font-medium">{{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Duration</p>
                            <p class="font-medium">{{ $booking->duration_days }} days</p>
                        </div>
                        @if($booking->room)
                            <div>
                                <p class="text-sm text-gray-500">Room</p>
                                <p class="font-medium">{{ $booking->room->room_type_name }} - Room {{ $booking->room->room_number }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Guests</p>
                            <p class="font-medium">{{ $booking->guest_count }} person(s)</p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 mt-4 pt-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Room Price</span>
                            <span>MMK{{ number_format($booking->total_room_price) }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Commission</span>
                            <span>MMK{{ number_format($booking->commission_amount) }}</span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>Total</span>
                            <span class="text-xl text-indigo-600">MMK{{ number_format($booking->total_amount) }}</span>
                        </div>
                    </div>

                    <!-- Payment Required Button -->
                    @if($booking->status === 'APPROVED')
                        <a href="{{ route('payments.create', $booking) }}" 
                           class="mt-6 w-full inline-flex justify-center items-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition">
                            <i class="fas fa-credit-card mr-2"></i>
                            Pay Now (MMK{{ number_format($booking->total_amount) }})
                        </a>
                        <p class="text-xs text-gray-500 text-center mt-2">You have 24 hours to complete payment</p>
                    @endif

                    @if($booking->status === 'PAYMENT_PENDING')
                        <div class="mt-6 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <p class="text-sm text-blue-700">Your payment is being processed. We'll notify you once confirmed.</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Contact Owner -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Contact Owner</h3>
                    <div class="space-y-2">
                        <p class="text-sm text-gray-600">{{ $booking->property->owner->name }}</p>
                        @if($booking->property->owner->phone)
                            <a href="tel:{{ $booking->property->owner->phone }}" 
                               class="flex items-center text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-phone mr-2"></i>
                                {{ $booking->property->owner->phone }}
                            </a>
                        @endif
                        
                        <!-- Chat Button -->
                        <a href="{{ route('rental.chat.show', $booking) }}" 
                           class="mt-3 w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                            <i class="fas fa-comment mr-2"></i>
                            Chat with Owner
                        </a>
                    </div>
                </div>

                <!-- Cancellation -->
                @if(in_array($booking->status, ['PENDING', 'APPROVED']))
                    <div class="bg-white rounded-lg shadow p-6">
                        <button onclick="showCancelModal()" 
                                class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            Cancel Request
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Cancel Booking</h3>
            <button onclick="closeCancelModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form action="{{ route('bookings.cancel', $booking) }}" method="POST">
            @csrf
            @method('POST')
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Reason for cancellation
                </label>
                <textarea name="cancellation_reason" rows="3" required
                          class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                          placeholder="Please tell us why you're cancelling..."></textarea>
            </div>
            
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeCancelModal()" 
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                    Close
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm Cancellation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCancelModal() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}

window.onclick = function(event) {
    const modal = document.getElementById('cancelModal');
    if (event.target == modal) {
        closeCancelModal();
    }
}
</script>
@endsection