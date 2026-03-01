@extends('layouts.owner')

@section('title', 'Booking Details')
@section('header', 'Booking Request Details')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('owner.bookings.index') }}" class="text-gray-400 hover:text-gray-600 mr-4">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Booking #{{ $booking->booking_reference }}</h2>
                    <p class="text-sm text-gray-500">Requested on {{ $booking->created_at->format('M d, Y g:i A') }}</p>
                </div>
            </div>
            <div class="flex items-center space-x-3">
                @php
                    $statusColors = [
                        'PENDING' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'APPROVED' => 'bg-green-100 text-green-800 border-green-200',
                        'REJECTED' => 'bg-red-100 text-red-800 border-red-200',
                        'PAYMENT_PENDING' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'CONFIRMED' => 'bg-green-100 text-green-800 border-green-200',
                        'CHECKED_IN' => 'bg-indigo-100 text-indigo-800 border-indigo-200',
                        'CHECKED_OUT' => 'bg-gray-100 text-gray-800 border-gray-200',
                        'CANCELLED' => 'bg-red-100 text-red-800 border-red-200',
                    ];
                @endphp
                <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$booking->status] ?? 'bg-gray-100' }}">
                    {{ str_replace('_', ' ', $booking->status) }}
                </span>
            </div>
        </div>

        <div class="p-6">
            <!-- Customer & Property Info Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Customer Info -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-user-circle text-gray-400 mr-2"></i>
                        Customer Information
                    </h3>
                    <div class="space-y-2">
                        <div class="flex items-start">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                <span class="text-indigo-700 font-medium">{{ substr($booking->user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $booking->user->name }}</p>
                                <p class="text-sm text-gray-600">{{ $booking->user->email }}</p>
                                @if($booking->user->phone)
                                    <p class="text-sm text-gray-600 mt-1">
                                        <i class="fas fa-phone mr-1 text-gray-400"></i> {{ $booking->user->phone }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Info -->
                <div class="border rounded-lg p-4">
                    <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                        <i class="fas fa-home text-gray-400 mr-2"></i>
                        Property Information
                    </h3>
                    <div class="space-y-2">
                        <p><span class="text-sm text-gray-500">Name:</span> <span class="font-medium">{{ $booking->property->name }}</span></p>
                        <p><span class="text-sm text-gray-500">Location:</span> <span class="font-medium">{{ $booking->property->area }}, {{ $booking->property->city }}</span></p>
                        @if($booking->room)
                            <p><span class="text-sm text-gray-500">Room:</span> <span class="font-medium">{{ $booking->room->room_type_name }} (No. {{ $booking->room->room_number }})</span></p>
                        @endif
                        <p><span class="text-sm text-gray-500">Type:</span> <span class="font-medium">{{ $booking->property->type }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Booking Details -->
            <div class="border rounded-lg p-4 mb-6">
                <h3 class="font-medium text-gray-900 mb-3 flex items-center">
                    <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                    Booking Details
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                    <div>
                        <p class="text-sm text-gray-500">Guests</p>
                        <p class="font-medium">{{ $booking->guest_count }} persons</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Monthly Rate</p>
                        <p class="font-medium">MMK{{ number_format($booking->property->base_price) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Total Amount</p>
                        <p class="text-xl font-bold text-indigo-600">MMK{{ number_format($booking->total_amount) }}</p>
                    </div>
                </div>
                
                <!-- Price Breakdown -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Room Price ({{ $booking->duration_days }} days)</span>
                        <span>MMK{{ number_format($booking->total_room_price) }}</span>
                    </div>
                    <div class="flex justify-between text-sm mt-1">
                        <span class="text-gray-600">Commission ({{ $booking->property->commission_rate }}%)</span>
                        <span>MMK{{ number_format($booking->commission_amount) }}</span>
                    </div>
                </div>

                @if($booking->special_requests)
                    <div class="mt-4 pt-4 border-t">
                        <p class="text-sm text-gray-500 mb-1">Special Requests:</p>
                        <div class="bg-gray-50 p-3 rounded-lg text-gray-700">
                            {{ $booking->special_requests }}
                        </div>
                    </div>
                @endif
            </div>

            <!-- Competing Requests (for PENDING status) -->
            @if($booking->status === 'PENDING' && $competingRequests->count() > 0)
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-yellow-800 mb-3 flex items-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                        Competing Requests ({{ $competingRequests->count() }})
                    </h3>
                    <p class="text-sm text-yellow-700 mb-3">There are other pending requests for the same dates. Approving this request will automatically reject others.</p>
                    <div class="space-y-2">
                        @foreach($competingRequests as $competing)
                            <div class="bg-white border border-yellow-200 rounded p-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-medium">{{ $competing->user->name }}</span>
                                        <span class="text-gray-500 text-xs ml-2">
                                            <i class="far fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($competing->created_at)->diffForHumans() }}
                                        </span>
                                    </div>
                                    <span class="text-gray-600 font-medium">MMK{{ number_format($competing->total_amount) }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Payment Information (if any) -->
            @if($booking->payments->count() > 0)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                    <h3 class="font-medium text-blue-800 mb-3 flex items-center">
                        <i class="fas fa-credit-card text-blue-600 mr-2"></i>
                        Payment Information
                    </h3>
                    @foreach($booking->payments as $payment)
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-sm font-medium text-blue-900">Payment #{{ $payment->payment_reference }}</p>
                                <p class="text-xs text-blue-700">{{ $payment->created_at->format('M d, Y g:i A') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-medium text-blue-900">MMK{{ number_format($payment->amount) }}</p>
                                <span class="px-2 py-0.5 text-xs rounded-full {{ $payment->status == 'COMPLETED' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $payment->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Action Buttons (for PENDING status) -->
            @if($booking->status === 'PENDING')
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Review Decision</h3>
                    
                    <form id="approveForm" action="{{ route('owner.bookings.approve', $booking->id) }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="owner_notes" id="approve_notes">
                    </form>
                    
                    <form id="rejectForm" action="{{ route('owner.bookings.reject', $booking->id) }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="rejection_reason" id="rejection_reason_input">
                    </form>
                    
                    <div class="flex gap-4">
                        <button onclick="showApproveModal()" 
                                class="flex-1 py-3 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition flex items-center justify-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            Approve Request
                        </button>
                        <button onclick="showRejectModal()" 
                                class="flex-1 py-3 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium transition flex items-center justify-center">
                            <i class="fas fa-times-circle mr-2"></i>
                            Reject Request
                        </button>
                    </div>
                </div>
            @elseif($booking->status === 'APPROVED')
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-green-800">Request Approved</h4>
                            <p class="text-sm text-green-700">Customer has been notified and will complete payment within 24 hours.</p>
                            @if($booking->approved_at)
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="far fa-clock mr-1"></i> Approved on {{ $booking->approved_at->format('M d, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($booking->status === 'REJECTED')
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex items-start">
                        <i class="fas fa-times-circle text-red-600 text-2xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-red-800">Request Rejected</h4>
                            @if($booking->rejection_reason)
                                <div class="mt-2 p-3 bg-white rounded border border-red-200">
                                    <p class="text-sm text-gray-700"><span class="font-medium">Reason:</span> {{ $booking->rejection_reason }}</p>
                                </div>
                            @endif
                            @if($booking->rejected_at)
                                <p class="text-xs text-red-600 mt-2">
                                    <i class="far fa-clock mr-1"></i> Rejected on {{ $booking->rejected_at->format('M d, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif(in_array($booking->status, ['CONFIRMED', 'CHECKED_IN', 'CHECKED_OUT']))
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-double text-green-600 text-2xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-green-800">Booking Confirmed</h4>
                            <p class="text-sm text-green-700">Payment received. Booking is confirmed.</p>
                            @if($booking->paid_at)
                                <p class="text-xs text-green-600 mt-1">
                                    <i class="far fa-clock mr-1"></i> Paid on {{ $booking->paid_at->format('M d, Y g:i A') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($booking->status === 'CANCELLED')
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-ban text-gray-600 text-2xl mr-3"></i>
                        <div>
                            <h4 class="font-medium text-gray-800">Booking Cancelled</h4>
                            @if($booking->cancellation_reason)
                                <p class="text-sm text-gray-600 mt-1">Reason: {{ $booking->cancellation_reason }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Approve Booking</h3>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-gray-600">Are you sure you want to approve this booking?</p>
            <p class="text-sm text-gray-500 mt-2">The customer will be notified and asked to make payment within 24 hours.</p>
            
            <div class="mt-4">
                <label for="modal_approve_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes to Customer (Optional)
                </label>
                <textarea id="modal_approve_notes" rows="3" 
                          class="w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                          placeholder="Any special instructions or welcome message..."></textarea>
            </div>
        </div>
        
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeApproveModal()" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Cancel
            </button>
            <button type="button" onclick="submitApprove()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                Confirm Approval
            </button>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Reject Booking</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <label for="modal_rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                Reason for Rejection <span class="text-red-500">*</span>
            </label>
            <textarea id="modal_rejection_reason" rows="4" required
                      class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                      placeholder="Please explain why this request is being rejected..."></textarea>
            <p class="text-xs text-gray-500 mt-1">This reason will be sent to the customer.</p>
        </div>
        
        <div class="flex justify-end gap-2">
            <button type="button" onclick="closeRejectModal()" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Cancel
            </button>
            <button type="button" onclick="submitReject()" 
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                Confirm Rejection
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 shadow-xl text-center max-w-sm">
        <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mx-auto mb-4"></div>
        <p class="text-gray-700 font-medium" id="loadingMessage">Processing...</p>
        <p class="text-xs text-gray-400 mt-2">Please wait, this may take a moment.</p>
    </div>
</div>

<style>
.loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #6366f1;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<script>
// ============ APPROVE FUNCTIONS ============
function showApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.getElementById('modal_approve_notes').value = '';
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function submitApprove() {
    const notes = document.getElementById('modal_approve_notes').value;
    document.getElementById('approve_notes').value = notes;
    
    showLoadingOverlay('Approving booking...');
    document.getElementById('approveForm').submit();
}

// ============ REJECT FUNCTIONS ============
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('modal_rejection_reason').value = '';
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}

function submitReject() {
    const reason = document.getElementById('modal_rejection_reason').value.trim();
    if (!reason) {
        alert('Please provide a reason for rejection');
        return;
    }
    
    document.getElementById('rejection_reason_input').value = reason;
    showLoadingOverlay('Rejecting booking...');
    document.getElementById('rejectForm').submit();
}

// ============ LOADING OVERLAY ============
function showLoadingOverlay(message) {
    const overlay = document.getElementById('loadingOverlay');
    const messageEl = document.getElementById('loadingMessage');
    if (messageEl) messageEl.textContent = message || 'Processing...';
    overlay.classList.remove('hidden');
}

function hideLoadingOverlay() {
    document.getElementById('loadingOverlay').classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    
    if (event.target === approveModal) closeApproveModal();
    if (event.target === rejectModal) closeRejectModal();
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
    }
});
</script>
@endsection