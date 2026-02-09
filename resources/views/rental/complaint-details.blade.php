{{-- resources/views/rental/complaint-details.blade.php --}}
@extends('layouts.app')

@section('title', 'Complaint Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Complaint Details</h1>
                    <p class="mt-2 text-gray-600">Complaint #{{ $complaint->complaint_reference }}</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('complaints.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Complaints
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Complaint Details Card -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Complaint Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Title & Status -->
                            <div>
                                <h2 class="text-xl font-bold text-gray-900 mb-2">{{ $complaint->title }}</h2>
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                                        @if($complaint->status === 'OPEN') bg-yellow-100 text-yellow-800
                                        @elseif($complaint->status === 'IN_PROGRESS') bg-blue-100 text-blue-800
                                        @elseif($complaint->status === 'RESOLVED') bg-green-100 text-green-800
                                        @elseif($complaint->status === 'CLOSED') bg-gray-100 text-gray-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ $complaint->status }}
                                    </span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                                        @if($complaint->priority === 'URGENT') bg-red-100 text-red-800
                                        @elseif($complaint->priority === 'HIGH') bg-orange-100 text-orange-800
                                        @elseif($complaint->priority === 'MEDIUM') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800 @endif">
                                        {{ $complaint->priority }} Priority
                                    </span>
                                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-indigo-100 text-indigo-800">
                                        {{ $complaint->complaint_type }}
                                    </span>
                                </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Description</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <p class="text-gray-700 whitespace-pre-line">{{ $complaint->description }}</p>
                                </div>
                            </div>

                            <!-- Complaint Metadata -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Complaint Details</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Reference:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $complaint->complaint_reference }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Submitted:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($complaint->created_at)->format('M d, Y H:i') }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Last Updated:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($complaint->updated_at)->format('M d, Y H:i') }}</dd>
                                        </div>
                                        @if($complaint->assigned_to_user)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Assigned To:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $complaint->assigned_to_user->name }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                                
                                <div>
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Related Information</h4>
                                    <dl class="space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Type:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $complaint->complaint_type }}</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Related To:</dt>
                                            <dd class="text-sm font-medium text-gray-900">{{ $complaint->related_type }}</dd>
                                        </div>
                                        @if($complaint->related_type === 'PROPERTY' && $complaint->property)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Property:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $complaint->property->name }}</dd>
                                            </div>
                                        @endif
                                        @if($complaint->booking)
                                            <div class="flex justify-between">
                                                <dt class="text-sm text-gray-600">Booking:</dt>
                                                <dd class="text-sm font-medium text-gray-900">{{ $complaint->booking->booking_reference }}</dd>
                                            </div>
                                        @endif
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resolution Section -->
                @if($complaint->resolution || $complaint->resolved_at)
                    <div class="bg-white shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-green-50">
                            <div class="flex items-center">
                                <svg class="h-5 w-5 text-green-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-semibold text-green-900">Resolution</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                @if($complaint->resolved_at)
                                    <div class="flex justify-between items-center">
                                        <div class="text-sm text-gray-600">Resolved On:</div>
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($complaint->resolved_at)->format('M d, Y H:i') }}</div>
                                    </div>
                                @endif
                                
                                @if($complaint->resolution)
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-700 mb-2">Resolution Details</h4>
                                        <div class="bg-green-50 rounded-lg p-4 border border-green-100">
                                            <p class="text-green-800 whitespace-pre-line">{{ $complaint->resolution }}</p>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($complaint->assigned_to_user)
                                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                        <div class="text-sm text-gray-600">Resolved By:</div>
                                        <div class="text-sm font-medium text-gray-900">{{ $complaint->assigned_to_user->name }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Activity Timeline -->
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">Activity Timeline</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-6">
                            <!-- Complaint Created -->
                            <div class="relative pl-8">
                                <div class="absolute left-0 top-0 h-4 w-4 bg-green-500 rounded-full"></div>
                                <div class="ml-2">
                                    <div class="text-sm font-medium text-gray-900">Complaint Submitted</div>
                                    <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($complaint->created_at)->format('M d, Y H:i') }}</div>
                                    <p class="mt-1 text-sm text-gray-700">You submitted this complaint with {{ $complaint->priority }} priority.</p>
                                </div>
                            </div>

                            <!-- Status Updates -->
                            @if($complaint->status === 'IN_PROGRESS')
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-0 h-4 w-4 bg-blue-500 rounded-full"></div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">In Progress</div>
                                        <div class="text-sm text-gray-600">Currently being reviewed</div>
                                    </div>
                                </div>
                            @endif

                            @if($complaint->status === 'RESOLVED')
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-0 h-4 w-4 bg-green-500 rounded-full"></div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">Resolved</div>
                                        @if($complaint->resolved_at)
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($complaint->resolved_at)->format('M d, Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($complaint->status === 'CLOSED')
                                <div class="relative pl-8">
                                    <div class="absolute left-0 top-0 h-4 w-4 bg-gray-500 rounded-full"></div>
                                    <div class="ml-2">
                                        <div class="text-sm font-medium text-gray-900">Closed</div>
                                        @if($complaint->updated_at)
                                            <div class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($complaint->updated_at)->format('M d, Y H:i') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @if($complaint->booking)
                                <a href="{{ route('bookings.show', $complaint->booking) }}" 
                                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    View Booking
                                </a>
                            @endif
                            
                            @if($complaint->related_type === 'PROPERTY' && $complaint->property)
                                <a href="{{ route('properties.show', $complaint->property) }}" 
                                   class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    View Property
                                </a>
                            @endif
                            
                            <a href="{{ route('complaints.index') }}" 
                               class="w-full flex items-center justify-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                                All Complaints
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Status Info -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Status Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div>
                                <div class="text-sm text-gray-600">Current Status</div>
                                <div class="text-lg font-semibold mt-1
                                    @if($complaint->status === 'OPEN') text-yellow-600
                                    @elseif($complaint->status === 'IN_PROGRESS') text-blue-600
                                    @elseif($complaint->status === 'RESOLVED') text-green-600
                                    @else text-gray-600 @endif">
                                    {{ $complaint->status }}
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Priority</div>
                                <div class="text-lg font-semibold mt-1
                                    @if($complaint->priority === 'URGENT') text-red-600
                                    @elseif($complaint->priority === 'HIGH') text-orange-600
                                    @elseif($complaint->priority === 'MEDIUM') text-yellow-600
                                    @else text-green-600 @endif">
                                    {{ $complaint->priority }}
                                </div>
                            </div>
                            
                            <div>
                                <div class="text-sm text-gray-600">Days Open</div>
                                <div class="text-lg font-semibold mt-1 text-gray-900">
                                    {{ \Carbon\Carbon::parse($complaint->created_at)->diffInDays(now()) }} days
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Support Contact -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Need Help?</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                +880 1234 567890
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                support@rentalsystem.com
                            </div>
                            <div class="pt-3 border-t border-gray-200">
                                <p class="text-xs text-gray-500">Our support team typically responds within 24 hours for urgent complaints and 48 hours for regular complaints.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>



</script>
@endsection