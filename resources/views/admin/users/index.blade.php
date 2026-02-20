@extends('layouts.admin')

@section('title', 'User Management')

@section('header', 'Users')

@section('subtitle', 'Manage all users on the platform')

@section('content')
<div class="space-y-6">
    <!-- Header with actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                User Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                View and manage all registered users
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <a href="{{ route('admin.users.export') }}?{{ http_build_query(request()->except('page')) }}"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-download mr-2"></i>
                Export
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <i class="fas fa-users text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Active Users</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['active'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-red-100 rounded-md p-3">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Banned Users</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['banned'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-user-plus text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">New Today</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['new_today'] ?? 0 }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               placeholder="Name, email or phone...">
                    </div>

                    <!-- Role Filter -->
                    <div>
                        <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                        <select name="role" 
                                id="role" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Roles</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" 
                                id="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All</option>
                            <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                            <option value="BANNED" {{ request('status') == 'BANNED' ? 'selected' : '' }}>Banned</option>
                        </select>
                    </div>

                    <!-- Date Range -->
                    <div>
                        <label for="date_range" class="block text-sm font-medium text-gray-700">Joined</label>
                        <select name="date_range" 
                                id="date_range" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Time</option>
                            <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                            <option value="this_week" {{ request('date_range') == 'this_week' ? 'selected' : '' }}>This Week</option>
                            <option value="this_month" {{ request('date_range') == 'this_month' ? 'selected' : '' }}>This Month</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('admin.users.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                        Clear Filters
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($users->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-users text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">No users match your search criteria.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium text-sm">
                                            {{ substr($user->name ?? 'NA', 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $user->name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $user->email ?? 'No email' }}</div>
                                <div class="text-xs text-gray-500">{{ $user->phone ?? 'No phone' }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($user->roles as $role)
                                        <span class="px-2 py-1 text-xs rounded-full 
                                            @if($role->name == 'SUPERADMIN') bg-purple-100 text-purple-800
                                            @elseif($role->name == 'OWNER') bg-blue-100 text-blue-800
                                            @elseif($role->name == 'FOOD') bg-green-100 text-green-800
                                            @elseif($role->name == 'LAUNDRY') bg-yellow-100 text-yellow-800
                                            @else bg-gray-100 text-gray-800
                                            @endif">
                                            {{ $role->name }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500">No roles</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    @if($user->status == 'ACTIVE') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $user->status ?? 'ACTIVE' }}
                                </span>
                                @if(!$user->email_verified_at)
                                    <span class="ml-2 px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                        Unverified
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($user->created_at)
                                    <div>{{ $user->created_at->format('M d, Y') }}</div>
                                    <div class="text-xs">{{ $user->created_at->format('h:i A') }}</div>
                                @else
                                    <div class="text-gray-400">Unknown</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($user->status == 'ACTIVE')
                                        <button onclick="updateStatus({{ $user->id }}, 'BANNED')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Ban User">
                                            <i class="fas fa-ban"></i>
                                        </button>
                                    @else
                                        <button onclick="updateStatus({{ $user->id }}, 'ACTIVE')"
                                                class="text-green-600 hover:text-green-900"
                                                title="Activate User">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
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
                    @if($users->previousPageUrl())
                        <a href="{{ $users->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($users->nextPageUrl())
                        <a href="{{ $users->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $users->firstItem() ?? 0 }}</span>
                            to
                            <span class="font-medium">{{ $users->lastItem() ?? 0 }}</span>
                            of
                            <span class="font-medium">{{ $users->total() ?? 0 }}</span>
                            users
                        </p>
                    </div>
                    <div>
                        {{ $users->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateStatus(userId, newStatus) {
    const action = newStatus === 'BANNED' ? 'ban' : 'activate';
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
        return;
    }
    
    fetch(`/admin/users/${userId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: newStatus })
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
        alert('Error updating user status');
    });
}
</script>
@endpush
@endsection