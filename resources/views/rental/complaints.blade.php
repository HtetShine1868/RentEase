{{-- resources/views/rental/complaints.blade.php --}}
@extends('dashboard')

@section('title', 'My Complaints')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">My Complaints</h1>
                    <p class="mt-2 text-gray-600">View and manage all your submitted complaints</p>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ route('rental.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Dashboard
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

        <!-- Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Complaints</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $complaints->total() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Open</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $complaints->where('status', 'OPEN')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Resolved</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $complaints->where('status', 'RESOLVED')->count() }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-12 w-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Closed</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $complaints->where('status', 'CLOSED')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaints List -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">All Complaints</h3>
                    <div class="mt-2 md:mt-0">
                        <select id="statusFilter" class="block w-full md:w-auto rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="">All Status</option>
                            <option value="OPEN">Open</option>
                            <option value="IN_PROGRESS">In Progress</option>
                            <option value="RESOLVED">Resolved</option>
                            <option value="CLOSED">Closed</option>
                        </select>
                    </div>
                </div>
            </div>
            
            @if($complaints->count() > 0)
                <div class="divide-y divide-gray-200">
                    @foreach($complaints as $complaint)
                        <div class="px-6 py-4 hover:bg-gray-50 transition duration-150 complaint-item" data-status="{{ $complaint->status }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-start">
                                        <div class="flex-shrink-0">
                                            @switch($complaint->priority)
                                                @case('URGENT')
                                                    <div class="h-10 w-10 bg-red-100 rounded-lg flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.771-.833-2.502 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                    </div>
                                                    @break
                                                @case('HIGH')
                                                    <div class="h-10 w-10 bg-orange-100 rounded-lg flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.771-.833-2.502 0L4.232 16.5c-.77.833.192 2.5 1.732 2.5z" />
                                                        </svg>
                                                    </div>
                                                    @break
                                                @default
                                                    <div class="h-10 w-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                        <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                                        </svg>
                                                    </div>
                                            @endswitch
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center justify-between">
                                                <h4 class="font-semibold text-gray-900">{{ $complaint->title }}</h4>
                                                <div class="flex items-center space-x-2">
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($complaint->status === 'OPEN') bg-yellow-100 text-yellow-800
                                                        @elseif($complaint->status === 'IN_PROGRESS') bg-blue-100 text-blue-800
                                                        @elseif($complaint->status === 'RESOLVED') bg-green-100 text-green-800
                                                        @elseif($complaint->status === 'CLOSED') bg-gray-100 text-gray-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        {{ $complaint->status }}
                                                    </span>
                                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                        @if($complaint->priority === 'URGENT') bg-red-100 text-red-800
                                                        @elseif($complaint->priority === 'HIGH') bg-orange-100 text-orange-800
                                                        @elseif($complaint->priority === 'MEDIUM') bg-yellow-100 text-yellow-800
                                                        @else bg-green-100 text-green-800 @endif">
                                                        {{ $complaint->priority }}
                                                    </span>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{ Str::limit($complaint->description, 120) }}</p>
                                            <div class="mt-2 flex items-center text-sm text-gray-500">
                                                <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                {{ \Carbon\Carbon::parse($complaint->created_at)->format('M d, Y') }}
                                                @if($complaint->complaint_type)
                                                    <span class="mx-2">•</span>
                                                    <span>{{ $complaint->complaint_type }}</span>
                                                @endif
                                            </div>
                                            @if($complaint->resolution)
                                                <div class="mt-2 p-2 bg-green-50 rounded border border-green-100">
                                                    <div class="flex items-start">
                                                        <svg class="h-4 w-4 text-green-500 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        <div>
                                                            <div class="text-xs font-medium text-green-800">Resolution:</div>
                                                            <div class="text-xs text-green-700 mt-1">{{ Str::limit($complaint->resolution, 100) }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('complaints.show', $complaint) }}" 
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                @if($complaints->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $complaints->links() }}
                    </div>
                @endif
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="h-12 w-12 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-gray-900">No complaints found</h3>
                    <p class="mt-1 text-sm text-gray-500">You haven't submitted any complaints yet.</p>
                    <div class="mt-6">
                        <a href="{{ route('rental.index') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                            Go to Dashboard
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Filter complaints by status
document.getElementById('statusFilter').addEventListener('change', function() {
    const status = this.value;
    const complaintItems = document.querySelectorAll('.complaint-item');
    
    complaintItems.forEach(item => {
        if (!status || item.dataset.status === status) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>
@endsection