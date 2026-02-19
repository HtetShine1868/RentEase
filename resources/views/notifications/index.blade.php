@extends('dashboard')

@section('title', 'All Notifications')

@section('header', 'Notifications')

@section('content')
{{-- SCRIPTS FIRST - Define functions before HTML loads --}}
<script>
// Define all functions immediately when this part of the page loads
console.log('🟢 Defining notification functions...');

window.markSingleAsRead = function(id) {
    console.log('✅ markSingleAsRead called with id:', id);
    if (!id) return;
    
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-notification-id="${id}"]`);
            if (notification) {
                notification.classList.remove('bg-indigo-50');
                const newBadge = notification.querySelector('.bg-indigo-100.text-indigo-800');
                if (newBadge) newBadge.remove();
            }
            if (window.updateNotificationBadge) {
                window.updateNotificationBadge();
            }
        } else {
            alert('Error marking notification as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking notification as read');
    });
};

window.deleteNotification = function(id) {
    console.log('✅ deleteNotification called with id:', id);
    if (!id) return;
    
    if (!confirm('Are you sure you want to delete this notification?')) {
        return;
    }
    
    fetch(`/notifications/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            const notification = document.querySelector(`[data-notification-id="${id}"]`);
            if (notification) {
                notification.remove();
            }
            
            if (window.updateNotificationBadge) {
                window.updateNotificationBadge();
            }
            
            const notificationsList = document.querySelector('.divide-y.divide-gray-200');
            if (notificationsList && notificationsList.children.length === 0) {
                window.location.reload();
            }
        } else {
            alert('Error deleting notification');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error deleting notification');
    });
};

window.markAllAsRead = function() {
    console.log('✅ markAllAsRead called');
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error marking all as read');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error marking all as read');
    });
};

window.clearAllNotifications = function() {
    console.log('✅ clearAllNotifications called');
    if (!confirm('Are you sure you want to clear all notifications? This action cannot be undone.')) {
        return;
    }
    
    fetch('/notifications/clear-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('Error clearing notifications');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error clearing notifications');
    });
};

window.updateNotificationBadge = function() {
    console.log('✅ updateNotificationBadge called');
    fetch('/notifications/unread-count', {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        const badge = document.getElementById('notification-badge');
        const mobileBadge = document.getElementById('mobile-notification-badge');
        const mobileBadgeResp = document.getElementById('mobile-notification-badge-responsive');
        const sidebarBadge = document.getElementById('sidebar-notification-badge');
        
        const count = data.count || 0;
        const displayCount = count > 99 ? '99+' : count;
        
        if (badge) {
            if (count > 0) {
                badge.classList.remove('hidden');
                badge.textContent = displayCount;
            } else {
                badge.classList.add('hidden');
            }
        }
        
        if (mobileBadge) {
            if (count > 0) {
                mobileBadge.classList.remove('hidden');
                mobileBadge.textContent = count;
            } else {
                mobileBadge.classList.add('hidden');
            }
        }
        
        if (mobileBadgeResp) {
            if (count > 0) {
                mobileBadgeResp.classList.remove('hidden');
                mobileBadgeResp.textContent = count;
            } else {
                mobileBadgeResp.classList.add('hidden');
            }
        }
        
        if (sidebarBadge) {
            if (count > 0) {
                sidebarBadge.classList.remove('hidden');
                sidebarBadge.textContent = displayCount;
            } else {
                sidebarBadge.classList.add('hidden');
            }
        }
    })
    .catch(error => {
        console.error('Error updating badge:', error);
    });
};

// Verify functions are defined
console.log('✅ Functions defined:', {
    markSingleAsRead: typeof window.markSingleAsRead === 'function',
    deleteNotification: typeof window.deleteNotification === 'function',
    markAllAsRead: typeof window.markAllAsRead === 'function',
    clearAllNotifications: typeof window.clearAllNotifications === 'function',
    updateNotificationBadge: typeof window.updateNotificationBadge === 'function'
});

// Auto-refresh badge every 30 seconds
setInterval(function() {
    if (window.updateNotificationBadge) {
        window.updateNotificationBadge();
    }
}, 30000);
</script>

{{-- Page Content --}}
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                All Notifications
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Stay updated with all your activities and alerts
            </p>
        </div>
        <div class="mt-4 sm:mt-0 flex space-x-3">
            <button type="button"
                    onclick="window.markAllAsRead()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-check-double mr-2"></i>
                Mark All as Read
            </button>
            <button type="button"
                    onclick="window.clearAllNotifications()"
                    class="inline-flex items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <i class="fas fa-trash mr-2"></i>
                Clear All
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Total Notifications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                        <i class="fas fa-bell text-indigo-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Notifications</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['total'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unread Notifications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-envelope text-yellow-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Unread</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['unread'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <!-- Read Notifications -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Read</dt>
                            <dd class="text-2xl font-semibold text-gray-900">{{ $stats['read'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <form method="GET" action="{{ route('notifications.index') }}" class="space-y-4">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                    <!-- Type Filter -->
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                        <select name="type" 
                                id="type" 
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            <option value="">All Types</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst(strtolower($type)) }}
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
                            <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                            <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                        </select>
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
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('notifications.index') }}" 
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

    <!-- Notifications List -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        @if($notifications->isEmpty())
            <div class="text-center py-12">
                <div class="mx-auto h-24 w-24 text-gray-400">
                    <i class="fas fa-bell-slash text-4xl"></i>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No notifications</h3>
                <p class="mt-1 text-sm text-gray-500">You don't have any notifications yet.</p>
            </div>
        @else
            <div class="divide-y divide-gray-200">
                @foreach($notifications as $notification)
                    <div class="p-6 hover:bg-gray-50 transition-colors duration-150 {{ !$notification->is_read ? 'bg-indigo-50' : '' }}"
                         data-notification-id="{{ $notification->id }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4 flex-1">
                                <!-- Icon -->
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full 
                                        @if(!$notification->is_read) bg-indigo-100 @else bg-gray-100 @endif 
                                        flex items-center justify-center">
                                        @php
                                            $icon = match($notification->type) {
                                                'BOOKING' => 'fa-calendar-check',
                                                'ORDER' => 'fa-shopping-bag',
                                                'PAYMENT' => 'fa-credit-card',
                                                'COMPLAINT' => 'fa-exclamation-circle',
                                                'SYSTEM' => 'fa-cog',
                                                'MARKETING' => 'fa-tag',
                                                default => 'fa-bell'
                                            };
                                            $color = match($notification->type) {
                                                'BOOKING' => 'text-blue-600',
                                                'ORDER' => 'text-green-600',
                                                'PAYMENT' => 'text-purple-600',
                                                'COMPLAINT' => 'text-red-600',
                                                'SYSTEM' => 'text-gray-600',
                                                'MARKETING' => 'text-yellow-600',
                                                default => 'text-indigo-600'
                                            };
                                        @endphp
                                        <i class="fas {{ $icon }} {{ $color }}"></i>
                                    </div>
                                </div>
                                
                                <!-- Content -->
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="text-base font-medium text-gray-900">
                                            {{ $notification->title }}
                                        </h4>
                                        @if(!$notification->is_read)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                New
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $notification->message }}
                                    </p>
                                    
                                    <div class="flex items-center space-x-4 mt-2">
                                        <span class="text-xs text-gray-500">
                                            <i class="far fa-clock mr-1"></i>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        
                                        <span class="text-xs text-gray-500">
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $notification->created_at->format('M d, Y h:i A') }}
                                        </span>
                                        
                                        <span class="text-xs px-2 py-0.5 bg-gray-100 rounded-full text-gray-600">
                                            {{ ucfirst(strtolower($notification->type)) }}
                                        </span>
                                    </div>

                                    @if($notification->related_entity_type && $notification->related_entity_id)
                                        <div class="mt-3">
                                            @php
                                                $routeName = match($notification->related_entity_type) {
                                                    'booking' => 'bookings.show',
                                                    'food_order' => 'food.orders.show',
                                                    'laundry_order' => 'laundry.orders.show',
                                                    'payment' => 'payments.show',
                                                    'complaint' => 'complaints.show',
                                                    default => null
                                                };
                                            @endphp
                                            
                                            @if($routeName && Route::has($routeName))
                                                <a href="{{ route($routeName, $notification->related_entity_id) }}" 
                                                   class="text-sm text-indigo-600 hover:text-indigo-800">
                                                    <i class="fas fa-external-link-alt mr-1"></i>
                                                    View Details
                                                </a>
                                            @else
                                                <span class="text-sm text-gray-400">Details not available</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Actions -->
                            <div class="ml-4 flex items-start space-x-2">
                                @if(!$notification->is_read)
                                    <button onclick="window.markSingleAsRead({{ $notification->id }})"
                                            class="text-gray-400 hover:text-green-600"
                                            title="Mark as read">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                <button onclick="window.deleteNotification({{ $notification->id }})"
                                        class="text-gray-400 hover:text-red-600"
                                        title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    @if($notifications->previousPageUrl())
                        <a href="{{ $notifications->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    @endif
                    
                    @if($notifications->nextPageUrl())
                        <a href="{{ $notifications->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    @endif
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{ $notifications->firstItem() }}</span>
                            to
                            <span class="font-medium">{{ $notifications->lastItem() }}</span>
                            of
                            <span class="font-medium">{{ $notifications->total() }}</span>
                            notifications
                        </p>
                    </div>
                    <div>
                        {{ $notifications->appends(request()->except('page'))->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection