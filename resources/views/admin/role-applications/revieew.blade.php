@extends('layouts.admin')

@section('title', 'Review Application')

@section('header', 'Review Application')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center space-x-4">
        <a href="{{ route('admin.role-applications.show', $application) }}" class="text-gray-400 hover:text-gray-500">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <h2 class="text-2xl font-bold text-gray-900">
            Review Application #{{ $application->id }}
        </h2>
        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
            {{ $application->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Application Summary -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Applicant Info -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Applicant Summary</h3>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-16 w-16 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-800 font-medium text-xl">
                                    {{ substr($application->user->name ?? 'NA', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <dl class="grid grid-cols-2 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->user->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->user->email }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $application->user->phone ?? 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Applied For</dt>
                                    <dd class="mt-1">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($application->role_type == 'OWNER') bg-blue-100 text-blue-800
                                            @elseif($application->role_type == 'FOOD') bg-green-100 text-green-800
                                            @else bg-purple-100 text-purple-800
                                            @endif">
                                            {{ $application->role_type }}
                                        </span>
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Details -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Business Details</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->business_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->contact_person }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->contact_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->contact_phone }}</dd>
                        </div>
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Business Address</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $application->business_address }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Role Specific Details -->
            @if($application->additional_data)
                @foreach($application->additional_data as $roleType => $data)
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">
                                {{ ucfirst(strtolower($roleType)) }} Details
                            </h3>
                            <dl class="grid grid-cols-2 gap-4">
                                @foreach($data as $key => $value)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            @if(is_array($value))
                                                {{ implode(', ', $value) }}
                                            @elseif(is_bool($value))
                                                {{ $value ? 'Yes' : 'No' }}
                                            @else
                                                {{ $value }}
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                @endforeach
            @endif

            @if($application->document_path)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Supporting Document</h3>
                    <iframe src="{{ Storage::url($application->document_path) }}" 
                            class="w-full h-96 border border-gray-300 rounded-lg"
                            title="Supporting Document"></iframe>
                </div>
            </div>
            @endif
        </div>

        <!-- Review Form -->
        <div class="space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Review Decision</h3>
                    
                    <form id="reviewForm" class="space-y-6">
                        @csrf
                        
                        <!-- Decision Buttons -->
                        <div class="space-y-3">
                            <button type="button"
                                    onclick="submitDecision('approve')"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                <i class="fas fa-check mr-2"></i>
                                Approve Application
                            </button>
                            
                            <button type="button"
                                    onclick="showRejectionForm()"
                                    class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                <i class="fas fa-times mr-2"></i>
                                Reject Application
                            </button>
                        </div>

                        <!-- Rejection Reason (Hidden by default) -->
                        <div id="rejectionSection" class="hidden space-y-4">
                            <div>
                                <label for="rejection_reason" class="block text-sm font-medium text-gray-700">
                                    Rejection Reason <span class="text-red-500">*</span>
                                </label>
                                <textarea name="rejection_reason" 
                                          id="rejection_reason" 
                                          rows="4"
                                          class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          placeholder="Please provide a reason for rejection..."></textarea>
                            </div>
                            
                            <div class="flex space-x-3">
                                <button type="button"
                                        onclick="submitDecision('reject')"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                                    Confirm Rejection
                                </button>
                                <button type="button"
                                        onclick="hideRejectionForm()"
                                        class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Notes -->
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Review Guidelines</h4>
                        <ul class="text-xs text-gray-500 space-y-1">
                            <li>• Verify all business details are accurate</li>
                            <li>• Check supporting documents for authenticity</li>
                            <li>• Ensure contact information is valid</li>
                            <li>• Consider location and service radius</li>
                            <li>• Provide clear reason if rejecting</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-sm font-medium text-gray-900 mb-3">Quick Links</h3>
                    <div class="space-y-2">
                        <a href="#" class="block text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-user mr-2"></i>
                            View User Profile
                        </a>
                        @if($application->document_path)
                        <a href="{{ route('admin.role-applications.download-document', $application) }}" 
                           class="block text-sm text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-download mr-2"></i>
                            Download Document
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showRejectionForm() {
    document.getElementById('rejectionSection').classList.remove('hidden');
}

function hideRejectionForm() {
    document.getElementById('rejectionSection').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

function submitDecision(action) {
    if (action === 'reject') {
        const reason = document.getElementById('rejection_reason').value.trim();
        if (!reason) {
            alert('Please provide a rejection reason');
            return;
        }
        
        if (!confirm('Are you sure you want to reject this application?')) {
            return;
        }
        
        submitReview('reject', { rejection_reason: reason });
        
    } else if (action === 'approve') {
        if (!confirm('Are you sure you want to approve this application?')) {
            return;
        }
        
        submitReview('approve', {});
    }
}

function submitReview(action, data) {
    const url = action === 'approve' 
        ? '{{ route("admin.role-applications.approve", $application) }}'
        : '{{ route("admin.role-applications.reject", $application) }}';
    
    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.href = '{{ route("admin.role-applications.index") }}';
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error processing request');
    });
}
</script>
@endpush
@endsection