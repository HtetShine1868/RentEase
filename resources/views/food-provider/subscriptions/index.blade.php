@extends('layouts.food-provider')

@section('title', 'Subscription Management')

@section('header', 'Subscriptions')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Subscription Management
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Manage all recurring meal subscriptions
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('food-provider.subscriptions.today') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                <i class="fas fa-calendar-day mr-2"></i>
                Today's Deliveries
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Active Subscriptions -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Active Subscriptions
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $activeCount }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Deliveries -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-truck text-blue-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Today's Deliveries
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    {{ $todayDeliveries }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Revenue -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-rupee-sign text-purple-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Monthly Revenue
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    MMK {{ number_format($monthlyRevenue, 2) }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Average Subscription Value -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-chart-line text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Avg. Subscription Value
                            </dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-semibold text-gray-900">
                                    MMK {{ $activeCount > 0 ? number_format($monthlyRevenue / $activeCount, 2) : '0.00' }}
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('food-provider.subscriptions.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Search -->
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700">Search</label>
                        <input type="text" 
                               name="search" 
                               id="search" 
                               value="{{ request('search') }}"
                               class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                               placeholder="Customer name or email">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select name="status" 
                                id="status" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Statuses</option>
                            <option value="ACTIVE" {{ request('status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                            <option value="PAUSED" {{ request('status') == 'PAUSED' ? 'selected' : '' }}>Paused</option>
                            <option value="CANCELLED" {{ request('status') == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                            <option value="COMPLETED" {{ request('status') == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>

                    <!-- Meal Type Filter -->
                    <div>
                        <label for="meal_type" class="block text-sm font-medium text-gray-700">Meal Type</label>
                        <select name="meal_type" 
                                id="meal_type" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Meal Types</option>
                            @foreach($mealTypes as $type)
                                <option value="{{ $type->id }}" {{ request('meal_type') == $type->id ? 'selected' : '' }}>
                                    {{ $type->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort -->
                    <div>
                        <label for="sort" class="block text-sm font-medium text-gray-700">Sort By</label>
                        <select name="sort" 
                                id="sort" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>Start Date</option>
                            <option value="end_date" {{ request('sort') == 'end_date' ? 'selected' : '' }}>End Date</option>
                            <option value="daily_price" {{ request('sort') == 'daily_price' ? 'selected' : '' }}>Price</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('food-provider.subscriptions.index') }}" 
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

    <!-- Subscriptions Table -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($subscriptions->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-calendar-alt text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No subscriptions yet</h3>
                <p class="mt-1 text-sm text-gray-500">You haven't received any subscription orders yet.</p>
            </div>
        @else
            <!-- Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Subscription ID
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Meal Type
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Schedule
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Period
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($subscriptions as $subscription)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    #SUB-{{ str_pad($subscription->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $subscription->created_at->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium text-xs">
                                            {{ substr($subscription->user->name ?? 'NA', 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $subscription->user->name ?? 'Unknown' }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $subscription->user->phone ?? 'No phone' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $subscription->mealType->name ?? 'N/A' }}
                                </span>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $subscription->delivery_time ? date('h:i A', strtotime($subscription->delivery_time)) : 'No time' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $days = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                                    $activeDays = [];
                                    for ($i = 0; $i < 7; $i++) {
                                        if ($subscription->delivery_days & pow(2, $i)) {
                                            $activeDays[] = $days[$i];
                                        }
                                    }
                                @endphp
                                <div class="text-sm text-gray-900">
                                    {{ implode(', ', $activeDays) }}
                                </div>
                                @if(count($activeDays) == 7)
                                    <span class="text-xs text-green-600">Every day</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $subscription->start_date->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    to {{ $subscription->end_date->format('d M Y') }}
                                </div>
                                @php
                                    $daysLeft = now()->diffInDays($subscription->end_date, false);
                                @endphp
                                @if($daysLeft > 0 && $subscription->status == 'ACTIVE')
                                    <span class="text-xs {{ $daysLeft < 7 ? 'text-orange-600' : 'text-green-600' }}">
                                        {{ $daysLeft }} days left
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    MMK {{ number_format($subscription->daily_price, 2) }}/day
                                </div>
                                <div class="text-xs text-gray-500">
                                    Total: MMK {{ number_format($subscription->total_price, 2) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'ACTIVE' => 'bg-green-100 text-green-800',
                                        'PAUSED' => 'bg-yellow-100 text-yellow-800',
                                        'CANCELLED' => 'bg-red-100 text-red-800',
                                        'COMPLETED' => 'bg-gray-100 text-gray-800'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$subscription->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $subscription->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-3">
                                    <a href="{{ route('food-provider.subscriptions.show', $subscription->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900"
                                       title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($subscription->status == 'ACTIVE')
                                        <button type="button" 
                                                onclick="updateStatus({{ $subscription->id }}, 'PAUSED')"
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Pause Subscription">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @endif
                                    
                                    @if($subscription->status == 'PAUSED')
                                        <button type="button" 
                                                onclick="updateStatus({{ $subscription->id }}, 'ACTIVE')"
                                                class="text-green-600 hover:text-green-900"
                                                title="Resume Subscription">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    
                                    @if(in_array($subscription->status, ['ACTIVE', 'PAUSED']))
                                        <button type="button" 
                                                onclick="updateStatus({{ $subscription->id }}, 'CANCELLED')"
                                                class="text-red-600 hover:text-red-900"
                                                title="Cancel Subscription">
                                            <i class="fas fa-times"></i>
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
                    @if($subscriptions->previousPageUrl())
                        <a href="{{ $subscriptions->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($subscriptions->nextPageUrl())
                        <a href="{{ $subscriptions->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $subscriptions->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $subscriptions->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $subscriptions->total() }}</span>
                            subscriptions
                        </p>
                    </div>
                    <div>
                        {{ $subscriptions->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function updateStatus(subscriptionId, newStatus) {
    let message = '';
    switch(newStatus) {
        case 'PAUSED':
            message = 'Are you sure you want to pause this subscription?';
            break;
        case 'ACTIVE':
            message = 'Are you sure you want to resume this subscription?';
            break;
        case 'CANCELLED':
            message = 'Are you sure you want to cancel this subscription? This action cannot be undone.';
            break;
        default:
            message = 'Are you sure?';
    }
    
    if (!confirm(message)) {
        return;
    }
    
    fetch(`/food-provider/subscriptions/${subscriptionId}/status`, {
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
        alert('Error updating subscription status');
    });
}
</script>
@endpush
@endsection