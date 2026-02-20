@extends('layouts.admin')

@section('title', 'Role Applications')

@section('header', 'Role Applications Management')

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Role Applications
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Review and manage user applications for different roles
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.role-applications.export') }}?{{ http_build_query(request()->except('page')) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i>
                Export Current View
            </a>
            <button type="button"
                    onclick="bulkApprove()"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <i class="fas fa-check-double mr-2"></i>
                Bulk Approve
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Applications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <i class="fas fa-file-alt text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Applications</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Applications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Pending Review</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['pending'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Approved Applications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Approved</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['approved'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejected Applications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Rejected</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['rejected'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Tabs -->
    <div class="border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <a href="{{ route('admin.role-applications.index', ['tab' => 'all'] + request()->except('tab')) }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'all' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-layer-group mr-2"></i>
                All Applications
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $activeTab === 'all' ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-900' }}">
                    {{ $tabCounts['all'] }}
                </span>
            </a>

            <a href="{{ route('admin.role-applications.index', ['tab' => 'owner'] + request()->except('tab')) }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'owner' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-building mr-2"></i>
                Property Owners
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $activeTab === 'owner' ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900' }}">
                    {{ $tabCounts['owner'] }}
                </span>
            </a>

            <a href="{{ route('admin.role-applications.index', ['tab' => 'food'] + request()->except('tab')) }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'food' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-utensils mr-2"></i>
                Food Providers
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $activeTab === 'food' ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-900' }}">
                    {{ $tabCounts['food'] }}
                </span>
            </a>

            <a href="{{ route('admin.role-applications.index', ['tab' => 'laundry'] + request()->except('tab')) }}" 
               class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'laundry' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                <i class="fas fa-tshirt mr-2"></i>
                Laundry Providers
                <span class="ml-2 py-0.5 px-2 rounded-full text-xs {{ $activeTab === 'laundry' ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-900' }}">
                    {{ $tabCounts['laundry'] }}
                </span>
            </a>
        </nav>
    </div>

    <!-- Status Filters -->
    <div class="flex space-x-2">
        <a href="{{ route('admin.role-applications.index', ['status' => null] + request()->except('status')) }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ !request('status') ? 'bg-gray-800 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            All ({{ $applications->total() }})
        </a>
        <a href="{{ route('admin.role-applications.index', ['status' => 'PENDING'] + request()->except('status')) }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request('status') === 'PENDING' ? 'bg-yellow-500 text-white' : 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200' }}">
            <i class="fas fa-clock mr-1.5"></i>
            Pending ({{ $statusCounts['pending'] }})
        </a>
        <a href="{{ route('admin.role-applications.index', ['status' => 'APPROVED'] + request()->except('status')) }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request('status') === 'APPROVED' ? 'bg-green-500 text-white' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
            <i class="fas fa-check-circle mr-1.5"></i>
            Approved ({{ $statusCounts['approved'] }})
        </a>
        <a href="{{ route('admin.role-applications.index', ['status' => 'REJECTED'] + request()->except('status')) }}" 
           class="inline-flex items-center px-3 py-1.5 rounded-md text-sm font-medium {{ request('status') === 'REJECTED' ? 'bg-red-500 text-white' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
            <i class="fas fa-times-circle mr-1.5"></i>
            Rejected ({{ $statusCounts['rejected'] }})
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.role-applications.index') }}" class="space-y-4">
                <!-- Preserve tab parameter -->
                <input type="hidden" name="tab" value="{{ $activeTab }}">
                
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div class="col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   name="search" 
                                   id="search" 
                                   value="{{ request('search') }}"
                                   class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md"
                                   placeholder="Search by name, business, email...">
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Date Range</label>
                        <select name="date_range" 
                                id="date_range" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="yesterday" {{ request('date_range') == 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>

                    <!-- Per Page -->
                    <div>
                        <label for="per_page" class="block text-sm font-medium text-gray-700">Per Page</label>
                        <select name="per_page" 
                                id="per_page" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="15" {{ request('per_page', 15) == 15 ? 'selected' : '' }}>15</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.role-applications.index', ['tab' => $activeTab]) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-times mr-2"></i>
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        <i class="fas fa-filter mr-2"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($applications->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-file-alt text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No applications found</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($activeTab !== 'all')
                        There are no {{ ucfirst($activeTab) }} applications matching your criteria.
                    @else
                        There are no applications matching your criteria.
                    @endif
                </p>
                @if(request()->anyFilled(['search', 'status', 'date_range']))
                    <div class="mt-6">
                        <a href="{{ route('admin.role-applications.index', ['tab' => $activeTab]) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-times mr-2"></i>
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applicant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Business</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($applications as $app)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($app->status === 'PENDING')
                                    <input type="checkbox" name="application_ids[]" value="{{ $app->id }}" class="application-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium text-sm">
                                            {{ substr($app->user->name ?? 'NA', 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $app->user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $app->user->email ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $roleColors = [
                                        'OWNER' => 'bg-blue-100 text-blue-800',
                                        'FOOD' => 'bg-green-100 text-green-800',
                                        'LAUNDRY' => 'bg-purple-100 text-purple-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $roleColors[$app->role_type] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $app->role_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $app->business_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $app->contact_person }}</div>
                                <div class="text-xs text-gray-500">{{ $app->contact_phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $app->created_at->format('M d, Y') }}</div>
                                <div class="text-xs">{{ $app->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'PENDING' => 'bg-yellow-100 text-yellow-800',
                                        'APPROVED' => 'bg-green-100 text-green-800',
                                        'REJECTED' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$app->status] }}">
                                    {{ $app->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.role-applications.show', $app->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($app->status === 'PENDING')
                                        <a href="{{ route('admin.role-applications.review', $app->id) }}" 
                                           class="text-blue-600 hover:text-blue-900"
                                           title="Review">
                                            <i class="fas fa-clipboard-check"></i>
                                        </a>
                                    @endif

                                    @if($app->document_path)
                                        <a href="{{ route('admin.role-applications.download-document', $app->id) }}" 
                                           class="text-gray-600 hover:text-gray-900"
                                           title="Download Document"
                                           target="_blank">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($applications->previousPageUrl())
                        <a href="{{ $applications->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($applications->nextPageUrl())
                        <a href="{{ $applications->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $applications->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $applications->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $applications->total() }}</span>
                            applications
                        </p>
                    </div>
                    <div>
                        {{ $applications->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>

            <!-- Bulk Actions Bar -->
            <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-700">
                            <span id="selected-count">0</span> applications selected
                        </span>
                        <button type="button"
                                onclick="bulkApprove()"
                                class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                                id="bulk-approve-btn"
                                disabled>
                            <i class="fas fa-check mr-1"></i>
                            Approve Selected
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Select all checkboxes
document.getElementById('select-all')?.addEventListener('change', function(e) {
    document.querySelectorAll('.application-checkbox').forEach(checkbox => {
        checkbox.checked = e.target.checked;
    });
    updateSelectedCount();
});

// Update selected count
document.querySelectorAll('.application-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

function updateSelectedCount() {
    const selected = document.querySelectorAll('.application-checkbox:checked').length;
    document.getElementById('selected-count').textContent = selected;
    
    const bulkBtn = document.getElementById('bulk-approve-btn');
    if (selected > 0) {
        bulkBtn.disabled = false;
    } else {
        bulkBtn.disabled = true;
    }
}

function bulkApprove() {
    const selected = Array.from(document.querySelectorAll('.application-checkbox:checked'))
        .map(cb => cb.value);
    
    if (selected.length === 0) {
        alert('Please select at least one application');
        return;
    }
    
    if (!confirm(`Approve ${selected.length} selected ${selected.length === 1 ? 'application' : 'applications'}?`)) {
        return;
    }
    
    fetch('{{ route("admin.role-applications.bulk-approve") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ application_ids: selected })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error processing bulk approval');
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});
</script>
@endpush
@endsection