@extends('layouts.admin')

@section('title', 'Review Role Application')
@section('header', 'Review Application')
@section('subtitle', 'Review and process role application')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
        <!-- Header with Back Button -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.role-applications.index') }}" 
                       class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-arrow-left text-lg"></i>
                    </a>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">Application Review</h2>
                        <p class="text-sm text-gray-500 mt-1">Review the application details below</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 text-sm font-medium rounded-full 
                        @if($application->status === 'PENDING') bg-yellow-100 text-yellow-800
                        @elseif($application->status === 'APPROVED') bg-green-100 text-green-800
                        @elseif($application->status === 'REJECTED') bg-red-100 text-red-800
                        @endif">
                        {{ $application->status }}
                    </span>
                    <span class="text-sm text-gray-500">
                        Submitted: {{ $application->created_at->format('M d, Y') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Applicant Info -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Applicant Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Name</p>
                    <p class="font-medium">{{ $application->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Email</p>
                    <p class="font-medium">{{ $application->user->email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Phone</p>
                    <p class="font-medium">{{ $application->user->phone ?? 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Applied Role</p>
                    <p class="font-medium">
                        @if($application->role_type === 'OWNER')
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Property Owner</span>
                        @elseif($application->role_type === 'FOOD')
                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Food Provider</span>
                        @elseif($application->role_type === 'LAUNDRY')
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded-full text-xs">Laundry Provider</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Business Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Business Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-sm text-gray-500">Business Name</p>
                    <p class="font-medium">{{ $application->business_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Contact Person</p>
                    <p class="font-medium">{{ $application->contact_person }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Contact Email</p>
                    <p class="font-medium">{{ $application->contact_email }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Contact Phone</p>
                    <p class="font-medium">{{ $application->contact_phone }}</p>
                </div>
                @if($application->service_radius_km)
                <div>
                    <p class="text-sm text-gray-500">Service Radius</p>
                    <p class="font-medium">{{ $application->service_radius_km }} km</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Location Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Location Information</h3>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <p class="text-sm text-gray-500">Business Address</p>
                    <p class="font-medium">{{ $application->business_address }}</p>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">Latitude</p>
                        <p class="font-medium">{{ $application->latitude }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Longitude</p>
                        <p class="font-medium">{{ $application->longitude }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Role-Specific Information -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">
                @if($application->role_type === 'OWNER')
                    <i class="fas fa-home text-blue-500 mr-2"></i>
                @elseif($application->role_type === 'FOOD')
                    <i class="fas fa-utensils text-green-500 mr-2"></i>
                @elseif($application->role_type === 'LAUNDRY')
                    <i class="fas fa-tshirt text-purple-500 mr-2"></i>
                @endif
                {{ ucfirst(strtolower($application->role_type)) }} Provider Information
            </h3>
            
            @php
                $additionalData = $application->additional_data ?? [];
            @endphp

            @if($application->role_type === 'OWNER')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Property Type</p>
                        <p class="font-medium">{{ $additionalData['owner']['property_type'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Property Count</p>
                        <p class="font-medium">{{ $additionalData['owner']['property_count'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Years of Experience</p>
                        <p class="font-medium">{{ $additionalData['owner']['years_experience'] ?? 'N/A' }}</p>
                    </div>
                </div>

            @elseif($application->role_type === 'FOOD')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Cuisine Type</p>
                        <p class="font-medium">{{ $additionalData['food_provider']['cuisine_type'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Meal Types</p>
                        <p class="font-medium">
                            @if(isset($additionalData['food_provider']['meal_types']) && is_array($additionalData['food_provider']['meal_types']))
                                @foreach($additionalData['food_provider']['meal_types'] as $mealType)
                                    <span class="inline-block px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs mr-1 mb-1">
                                        {{ $mealType }}
                                    </span>
                                @endforeach
                            @else
                                N/A
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Delivery Hours</p>
                        <p class="font-medium">{{ $additionalData['food_provider']['delivery_hours'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Max Daily Orders</p>
                        <p class="font-medium">{{ $additionalData['food_provider']['max_daily_orders'] ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Service Radius</p>
                        <p class="font-medium">{{ $additionalData['food_provider']['service_radius_km'] ?? 'N/A' }} km</p>
                    </div>
                </div>

            @elseif($application->role_type === 'LAUNDRY')
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500">Has Pickup Service</p>
                        <p class="font-medium">
                            @if(isset($additionalData['laundry_provider']['has_pickup_service']) && $additionalData['laundry_provider']['has_pickup_service'])
                                <span class="text-green-600">Yes</span>
                            @else
                                <span class="text-gray-600">No</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Service Radius</p>
                        <p class="font-medium">{{ $additionalData['laundry_provider']['service_radius_km'] ?? 'N/A' }} km</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Normal Turnaround</p>
                        <p class="font-medium">{{ $additionalData['laundry_provider']['normal_turnaround_hours'] ?? 'N/A' }} hours</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Rush Turnaround</p>
                        <p class="font-medium">{{ $additionalData['laundry_provider']['rush_turnaround_hours'] ?? 'N/A' }} hours</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">Max Daily Orders</p>
                        <p class="font-medium">{{ $additionalData['laundry_provider']['max_daily_orders'] ?? 'N/A' }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Documents -->
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Uploaded Documents</h3>
            <div class="space-y-4">
                @if($application->document_path)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-file-pdf text-red-500 text-xl mr-3"></i>
                        <div>
                            <p class="font-medium">Identification Document</p>
                            <p class="text-xs text-gray-500">Uploaded: {{ $application->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.role-applications.download-document', $application->id) }}" target="_blank" 
                       class="px-3 py-1 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                        <i class="fas fa-eye mr-1"></i> View
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Review Actions -->
        @if($application->status === 'PENDING')
        <div class="p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Decision</h3>
            
            <!-- Approve Form -->
            <form id="approveForm" action="{{ route('admin.role-applications.approve', $application->id) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="notes" id="approve_notes">
            </form>

            <!-- Reject Form -->
            <form id="rejectForm" action="{{ route('admin.role-applications.reject', $application->id) }}" method="POST" class="hidden">
                @csrf
                <input type="hidden" name="rejection_reason" id="rejection_reason_input">
            </form>

            <!-- Decision Buttons -->
            <div class="flex items-center gap-4">
                <button type="button" onclick="showApproveModal()" 
                        class="flex-1 py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <i class="fas fa-check-circle mr-2"></i> Approve Application
                </button>
                <button type="button" onclick="showRejectModal()" 
                        class="flex-1 py-3 px-4 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                    <i class="fas fa-times-circle mr-2"></i> Reject Application
                </button>
            </div>
        </div>
        @else
        <!-- Already Reviewed Message -->
        <div class="p-6 bg-gray-50">
            <div class="text-center">
                <i class="fas fa-check-circle text-4xl text-gray-400 mb-3"></i>
                <h3 class="text-lg font-medium text-gray-900">Application Already Reviewed</h3>
                <p class="text-gray-600 mt-1">This application has already been {{ strtolower($application->status) }}.</p>
                @if($application->reviewed_by)
                    <p class="text-sm text-gray-500 mt-3">
                        Reviewed by: {{ $application->reviewer->name ?? 'Admin' }} on {{ $application->reviewed_at?->format('M d, Y g:i A') }}
                    </p>
                @endif
                @if($application->rejection_reason)
                    <div class="mt-4 p-3 bg-red-50 rounded-lg text-left">
                        <p class="text-sm font-medium text-red-800">Rejection Reason:</p>
                        <p class="text-sm text-red-600 mt-1">{{ $application->rejection_reason }}</p>
                    </div>
                @endif
                <div class="mt-6">
                    <a href="{{ route('admin.role-applications.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Applications
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-bold text-gray-900">Approve Application</h3>
            <button onclick="closeApproveModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <p class="text-gray-600">Are you sure you want to approve this application?</p>
            <p class="text-sm text-gray-500 mt-2">The user will be notified and the role will be assigned immediately.</p>
            
            <div class="mt-4">
                <label for="modal_approve_notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Additional Notes (Optional)
                </label>
                <textarea id="modal_approve_notes" rows="3" 
                          class="block w-full rounded-lg border-gray-300 focus:border-green-500 focus:ring-green-500"
                          placeholder="Any notes about this approval..."></textarea>
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
            <h3 class="text-lg font-bold text-gray-900">Reject Application</h3>
            <button onclick="closeRejectModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="mb-4">
            <label for="modal_rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                Reason for Rejection <span class="text-red-500">*</span>
            </label>
            <textarea id="modal_rejection_reason" rows="4" required
                      class="block w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500"
                      placeholder="Please provide a clear reason for rejection..."></textarea>
            <p class="text-xs text-gray-500 mt-1">This reason will be sent to the applicant.</p>
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
    <div class="bg-white rounded-lg p-6 shadow-xl flex flex-col items-center max-w-sm mx-auto">
        <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mb-4"></div>
        <p class="text-gray-700 text-center" id="loadingMessage">Processing...</p>
        <p class="text-xs text-gray-400 mt-2">Please wait, this may take a moment...</p>
    </div>
</div>

<style>
.loading-spinner {
    border: 3px solid #f3f3f3;
    border-top: 3px solid #3b82f6;
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
@endsection

@push('scripts')
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
    
    // Set the notes in the form
    document.getElementById('approve_notes').value = notes;
    
    // Show loading overlay
    showLoadingOverlay('Approving application...');
    
    // Submit the form
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
    
    // Set the reason in the form
    document.getElementById('rejection_reason_input').value = reason;
    
    // Show loading overlay
    showLoadingOverlay('Rejecting application...');
    
    // Submit the form
    document.getElementById('rejectForm').submit();
}

// Loading overlay functions
function showLoadingOverlay(message) {
    const overlay = document.getElementById('loadingOverlay');
    const messageEl = document.getElementById('loadingMessage');
    if (messageEl) {
        messageEl.textContent = message || 'Processing...';
    }
    overlay.classList.remove('hidden');
}

function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    overlay.classList.add('hidden');
}

// Close modals when clicking outside
window.onclick = function(event) {
    const approveModal = document.getElementById('approveModal');
    const rejectModal = document.getElementById('rejectModal');
    
    if (event.target === approveModal) {
        closeApproveModal();
    }
    if (event.target === rejectModal) {
        closeRejectModal();
    }
}

// Close modals with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeApproveModal();
        closeRejectModal();
    }
});

// Prevent accidental page refresh/navigation
window.addEventListener('beforeunload', function(e) {
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (!loadingOverlay.classList.contains('hidden')) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>
@endpush