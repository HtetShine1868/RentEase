@extends('layouts.admin')

@section('title', 'Application Details - #' . $application->id)

@section('header', 'Application Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.role-applications.index', ['tab' => strtolower($application->role_type)]) }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Application #{{ $application->id }}
            </h2>
            @php
                $roleColors = [
                    'OWNER' => 'bg-blue-100 text-blue-800',
                    'FOOD' => 'bg-green-100 text-green-800',
                    'LAUNDRY' => 'bg-purple-100 text-purple-800',
                ];
                $statusColors = [
                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                    'APPROVED' => 'bg-green-100 text-green-800',
                    'REJECTED' => 'bg-red-100 text-red-800',
                ];
            @endphp
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $roleColors[$application->role_type] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $application->role_type }}
            </span>
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$application->status] }}">
                {{ $application->status }}
            </span>
        </div>
        
        @if($application->status === 'PENDING')
            <div class="flex space-x-3">
                <a href="{{ route('admin.role-applications.review', $application->id) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-clipboard-check mr-2"></i>
                    Review Application
                </a>
            </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Left Column - Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Applicant Information Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-user-circle text-indigo-500 mr-2"></i>
                        Applicant Information
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="h-20 w-20 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white font-bold text-2xl">
                                    {{ substr($application->user->name ?? 'NA', 0, 2) }}
                                </span>
                            </div>
                        </div>
                        <div class="flex-1 grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-500">Full Name</p>
                                <p class="mt-1 text-base text-gray-900">{{ $application->user->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Email Address</p>
                                <p class="mt-1 text-base text-gray-900">{{ $application->user->email ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Phone Number</p>
                                <p class="mt-1 text-base text-gray-900">{{ $application->user->phone ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Member Since</p>
                                <p class="mt-1 text-base text-gray-900">{{ $application->user->created_at->format('F d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Information Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-building text-indigo-500 mr-2"></i>
                        Business Information
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <dl class="grid grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Business Name</dt>
                            <dd class="mt-1 text-lg font-medium text-gray-900">{{ $application->business_name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Person</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $application->contact_person }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Email</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $application->contact_email }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $application->contact_phone }}</dd>
                        </div>
                        @if($application->service_radius_km)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Service Radius</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $application->service_radius_km }} km</dd>
                        </div>
                        @endif
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-gray-500">Business Address</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $application->business_address }}</dd>
                            <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                                <span><i class="fas fa-map-pin mr-1"></i> Lat: {{ $application->latitude }}</span>
                                <span><i class="fas fa-map-pin mr-1"></i> Long: {{ $application->longitude }}</span>
                            </div>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Role Specific Details -->
            @if($application->additional_data)
                @foreach($application->additional_data as $roleType => $data)
                    <div class="bg-white shadow-sm sm:rounded-lg">
                        <div class="px-6 py-5 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">
                                <i class="fas {{ $roleType === 'owner' ? 'fa-building' : ($roleType === 'food' ? 'fa-utensils' : 'fa-tshirt') }} text-indigo-500 mr-2"></i>
                                {{ ucfirst($roleType) }} Provider Details
                            </h3>
                        </div>
                        <div class="px-6 py-5">
                            <dl class="grid grid-cols-2 gap-4">
                                @foreach($data as $key => $value)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">{{ ucwords(str_replace('_', ' ', $key)) }}</dt>
                                        <dd class="mt-1 text-base text-gray-900">
                                            @if(is_array($value))
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($value as $item)
                                                        <span class="px-2 py-1 bg-gray-100 rounded-md text-sm">{{ $item }}</span>
                                                    @endforeach
                                                </div>
                                            @elseif(is_bool($value))
                                                <span class="px-2 py-1 {{ $value ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-md text-sm">
                                                    {{ $value ? 'Yes' : 'No' }}
                                                </span>
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

            <!-- Document Viewer -->
            @if($application->document_path)
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="px-6 py-5 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">
                            <i class="fas fa-file-pdf text-indigo-500 mr-2"></i>
                            Supporting Document
                        </h3>
                    </div>
                    <div class="px-6 py-5">
                        <iframe src="{{ Storage::url($application->document_path) }}" 
                                class="w-full h-96 border border-gray-300 rounded-lg shadow-inner"
                                title="Supporting Document"></iframe>
                        <div class="mt-4 flex justify-end">
                            <a href="{{ route('admin.role-applications.download-document', $application->id) }}" 
                               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                <i class="fas fa-download mr-2"></i>
                                Download Document
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column - Sidebar -->
        <div class="space-y-6">
            <!-- Status Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-info-circle text-indigo-500 mr-2"></i>
                        Application Status
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Current Status</span>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$application->status] }}">
                                {{ $application->status }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Submitted On</span>
                            <span class="text-sm text-gray-900">{{ $application->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Submission Time</span>
                            <span class="text-sm text-gray-900">{{ $application->created_at->format('h:i A') }}</span>
                        </div>
                        @if($application->reviewed_at)
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-500">Reviewed On</span>
                            <span class="text-sm text-gray-900">{{ $application->reviewed_at->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions Card -->
            @if($application->status === 'PENDING')
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-bolt text-indigo-500 mr-2"></i>
                        Quick Actions
                    </h3>
                </div>
                <div class="px-6 py-5 space-y-3">
                    <a href="{{ route('admin.role-applications.review', $application->id) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-clipboard-check mr-2"></i>
                        Review Application
                    </a>
                    
                    @if($application->document_path)
                    <a href="{{ route('admin.role-applications.download-document', $application->id) }}" 
                       target="_blank"
                       class="w-full inline-flex justify-center items-center px-4 py-3 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-download mr-2"></i>
                        Download Document
                    </a>
                    @endif
                </div>
            </div>
            @endif

            <!-- Reviewer Information -->
            @if($application->reviewer)
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-user-check text-indigo-500 mr-2"></i>
                        Reviewed By
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-600 font-medium text-lg">
                                {{ substr($application->reviewer->name ?? 'AD', 0, 2) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $application->reviewer->name }}</p>
                            <p class="text-xs text-gray-500">{{ $application->reviewer->email }}</p>
                            @if($application->reviewed_at)
                            <p class="text-xs text-gray-400 mt-1">Reviewed on {{ $application->reviewed_at->format('M d, Y h:i A') }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Rejection Reason (if rejected) -->
            @if($application->status === 'REJECTED' && $application->rejection_reason)
            <div class="bg-red-50 shadow-sm sm:rounded-lg border border-red-200">
                <div class="px-6 py-5">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400 text-lg"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Rejection Reason</h3>
                            <p class="mt-2 text-sm text-red-700">{{ $application->rejection_reason }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Timeline Card -->
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-6 py-5 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        <i class="fas fa-history text-indigo-500 mr-2"></i>
                        Timeline
                    </h3>
                </div>
                <div class="px-6 py-5">
                    <div class="flow-root">
                        <ul class="-mb-8">
                            <li>
                                <div class="relative pb-8">
                                    <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                                                <i class="fas fa-file-alt text-white text-sm"></i>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium text-gray-900">Application Submitted</p>
                                            <p class="text-xs text-gray-500">{{ $application->created_at->format('M d, Y h:i A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            
                            @if($application->reviewed_at)
                            <li>
                                <div class="relative pb-8">
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full 
                                                @if($application->status === 'APPROVED') bg-green-500 
                                                @elseif($application->status === 'REJECTED') bg-red-500 
                                                @else bg-gray-400 
                                                @endif flex items-center justify-center ring-8 ring-white">
                                                @if($application->status === 'APPROVED')
                                                    <i class="fas fa-check text-white text-sm"></i>
                                                @elseif($application->status === 'REJECTED')
                                                    <i class="fas fa-times text-white text-sm"></i>
                                                @else
                                                    <i class="fas fa-clock text-white text-sm"></i>
                                                @endif
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1 pt-1.5">
                                            <p class="text-sm font-medium text-gray-900">Application {{ $application->status }}</p>
                                            <p class="text-xs text-gray-500">{{ $application->reviewed_at->format('M d, Y h:i A') }}</p>
                                            @if($application->status === 'APPROVED')
                                            <p class="text-xs text-green-600 mt-1">Role assigned successfully</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection