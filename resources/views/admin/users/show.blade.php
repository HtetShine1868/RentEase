@extends('layouts.admin')

@section('title', 'User Details - ' . $user->name)

@section('header', 'User Profile')

@section('subtitle', 'View and manage user details')

@section('content')
<div class="space-y-6">
    <!-- Header with back button -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.users.index') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                User Profile
            </h2>
            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                @if($user->status == 'ACTIVE') bg-green-100 text-green-800
                @else bg-red-100 text-red-800
                @endif">
                {{ $user->status }}
            </span>
            @if(!$user->email_verified_at)
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                    Unverified
                </span>
            @endif
        </div>
        
        <div class="flex space-x-3">
            @if($user->status == 'ACTIVE')
                <button onclick="updateStatus({{ $user->id }}, 'BANNED')"
                        class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <i class="fas fa-ban mr-2"></i>
                    Ban User
                </button>
            @else
                <button onclick="updateStatus({{ $user->id }}, 'ACTIVE')"
                        class="inline-flex items-center px-4 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <i class="fas fa-check-circle mr-2"></i>
                    Activate User
                </button>
            @endif
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Personal Information</h3>
        </div>
        <div class="px-6 py-5">
            <div class="flex items-start space-x-6">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    @if($user->avatar_url)
                        <img src="{{ Storage::url($user->avatar_url) }}" 
                             alt="{{ $user->name }}"
                             class="h-24 w-24 rounded-full border-4 border-indigo-100 object-cover">
                    @else
                        <div class="h-24 w-24 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center">
                            <span class="text-white font-bold text-3xl">
                                {{ substr($user->name, 0, 2) }}
                            </span>
                        </div>
                    @endif
                </div>

                <!-- User Details -->
                <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Full Name</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Email Address</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                        @if($user->email_verified_at)
                            <p class="text-xs text-green-600 mt-1">
                                <i class="fas fa-check-circle mr-1"></i>
                                Verified on {{ $user->email_verified_at->format('M d, Y') }}
                            </p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Phone Number</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Gender</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->gender ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Member Since</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->created_at->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-500">{{ $user->created_at->format('h:i A') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Last Updated</p>
                        <p class="mt-1 text-lg text-gray-900">{{ $user->updated_at->format('F d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Role Management Card -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Role Management</h3>
        </div>
        <div class="px-6 py-5">
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($user->roles as $role)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                        @if($role->name == 'SUPERADMIN') bg-purple-100 text-purple-800
                        @elseif($role->name == 'OWNER') bg-blue-100 text-blue-800
                        @elseif($role->name == 'FOOD') bg-green-100 text-green-800
                        @elseif($role->name == 'LAUNDRY') bg-yellow-100 text-yellow-800
                        @else bg-gray-100 text-gray-800
                        @endif">
                        {{ $role->name }}
                        @if($role->name != 'USER' && $user->roles->count() > 1)
                            <button onclick="removeRole({{ $user->id }}, '{{ $role->name }}')" 
                                    class="ml-2 text-gray-500 hover:text-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </span>
                @endforeach
            </div>

            <!-- Add Role Form -->
            <div class="mt-4 flex items-center space-x-3">
                <select id="newRole" class="block w-64 pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Select role to assign</option>
                    @foreach(['OWNER', 'FOOD', 'LAUNDRY', 'USER'] as $role)
                        @if(!$user->roles->pluck('name')->contains($role))
                            <option value="{{ $role }}">{{ $role }}</option>
                        @endif
                    @endforeach
                </select>
                <button onclick="assignRole({{ $user->id }})"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>
                    Assign Role
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-calendar-check text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_bookings'] }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-xs text-gray-500">{{ $stats['active_bookings'] }} active</span>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-utensils text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Food Orders</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_food_orders'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-tshirt text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Laundry Orders</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total_laundry_orders'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-rupee-sign text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Spent</dt>
                            <dd class="text-2xl font-semibold text-gray-900">₹{{ number_format($stats['total_spent'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Recent Bookings -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                @forelse($recentBookings as $booking)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->property->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₹{{ number_format($booking->total_amount, 0) }}</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($booking->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'CONFIRMED') bg-green-100 text-green-800
                                @elseif($booking->status == 'CHECKED_IN') bg-blue-100 text-blue-800
                                @elseif($booking->status == 'CHECKED_OUT') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $booking->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                    <p>No bookings found</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Food Orders -->
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Recent Food Orders</h3>
            </div>
            <div class="divide-y divide-gray-200 max-h-80 overflow-y-auto">
                @forelse($recentFoodOrders as $order)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->order_reference }}</p>
                            <p class="text-xs text-gray-500">{{ $order->serviceProvider->business_name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₹{{ number_format($order->total_amount, 0) }}</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'DELIVERED') bg-green-100 text-green-800
                                @elseif($order->status == 'CANCELLED') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-utensils text-3xl mb-2"></i>
                    <p>No food orders found</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Role Applications -->
    @if($applications->isNotEmpty())
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Role Applications</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($applications as $app)
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="h-10 w-10 rounded-full 
                                @if($app->role_type == 'OWNER') bg-blue-100
                                @elseif($app->role_type == 'FOOD') bg-green-100
                                @else bg-purple-100
                                @endif flex items-center justify-center">
                                <i class="fas 
                                    @if($app->role_type == 'OWNER') fa-building text-blue-600
                                    @elseif($app->role_type == 'FOOD') fa-utensils text-green-600
                                    @else fa-tshirt text-purple-600
                                    @endif"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $app->business_name }}</p>
                            <p class="text-xs text-gray-500">Applied for: {{ $app->role_type }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($app->status == 'PENDING') bg-yellow-100 text-yellow-800
                            @elseif($app->status == 'APPROVED') bg-green-100 text-green-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ $app->status }}
                        </span>
                        <a href="{{ route('admin.role-applications.show', $app->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Properties (if owner) -->
    @if($user->isOwner() && $properties->isNotEmpty())
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900">Properties Owned</h3>
        </div>
        <div class="divide-y divide-gray-200">
            @foreach($properties as $property)
            <div class="px-6 py-4 hover:bg-gray-50">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $property->name }}</p>
                        <p class="text-xs text-gray-500">{{ $property->type }} • {{ $property->city }}</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($property->status == 'ACTIVE') bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $property->status }}
                        </span>
                        <span class="text-sm text-gray-600">{{ $property->bookings_count }} bookings</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
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

function assignRole(userId) {
    const role = document.getElementById('newRole').value;
    if (!role) {
        alert('Please select a role to assign');
        return;
    }
    
    fetch(`/admin/users/${userId}/assign-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ role: role })
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
        alert('Error assigning role');
    });
}

function removeRole(userId, role) {
    if (!confirm(`Are you sure you want to remove the ${role} role?`)) {
        return;
    }
    
    fetch(`/admin/users/${userId}/remove-role`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ role: role })
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
        alert('Error removing role');
    });
}
</script>
@endpush
@endsection