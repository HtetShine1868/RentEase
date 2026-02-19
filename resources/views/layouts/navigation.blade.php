<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('rental.index')" :active="request()->routeIs('rental.*')">
                        {{ __('Rentals') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('food.index')" :active="request()->routeIs('food.*')">
                        {{ __('Food') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('laundry.index')" :active="request()->routeIs('laundry.*')">
                        {{ __('Laundry') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6 space-x-3">
                
                <!-- Notifications Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" 
                            class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">View notifications</span>
                        <i class="fas fa-bell text-xl"></i>
                        
                        <!-- Notification Badge -->
                        <span id="notification-badge" 
                              class="absolute top-0 right-0 block h-4 w-4 transform translate-x-1/2 -translate-y-1/2 rounded-full bg-red-500 text-white text-xs flex items-center justify-center hidden">
                            0
                        </span>
                    </button>

                    <!-- Notifications Dropdown Panel -->
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="hidden origin-top-right absolute right-0 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                         id="notifications-dropdown">
                        
                        <div class="py-2">
                            <div class="px-4 py-2 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                    <button onclick="markAllAsRead()" class="text-xs text-indigo-600 hover:text-indigo-800">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                            
                            <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                <!-- Notifications will be loaded here via AJAX -->
                                <div class="px-4 py-3 text-sm text-gray-500 text-center">
                                    Loading notifications...
                                </div>
                            </div>
                            
                            <div class="border-t border-gray-200 px-4 py-2">
                                <a href="{{ route('notifications.index') }}" 
                                   class="block text-center text-sm text-indigo-600 hover:text-indigo-800">
                                    View all notifications
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Dropdown -->
                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ml-1">
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                        
                        <div class="py-1">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            
                            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-bell mr-2"></i> All Notifications
                                <span id="mobile-notification-badge" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 hidden">
                                    0
                                </span>
                            </a>
                            
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('rental.index')" :active="request()->routeIs('rental.*')">
                {{ __('Rentals') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('food.index')" :active="request()->routeIs('food.*')">
                {{ __('Food') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('laundry.index')" :active="request()->routeIs('laundry.*')">
                {{ __('Laundry') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.show')">
                    <i class="fas fa-user mr-2"></i> Profile
                </x-responsive-nav-link>
                
                <x-responsive-nav-link :href="route('notifications.index')">
                    <i class="fas fa-bell mr-2"></i> Notifications
                    <span id="mobile-notification-badge-responsive" class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 hidden">
                        0
                    </span>
                </x-responsive-nav-link>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

@push('scripts')
<script>
// Notification functionality
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
    updateNotificationBadge();
    
    // Refresh notifications every 30 seconds
    setInterval(function() {
        loadNotifications();
        updateNotificationBadge();
    }, 30000);
});

function loadNotifications() {
    fetch('/notifications/recent')
        .then(response => response.json())
        .then(notifications => {
            const list = document.getElementById('notifications-list');
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="px-4 py-8 text-center">
                        <i class="fas fa-bell-slash text-gray-400 text-3xl mb-2"></i>
                        <p class="text-sm text-gray-500">No notifications</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            notifications.forEach(notification => {
                html += `
                    <a href="${notification.url}" 
                       class="block px-4 py-3 hover:bg-gray-50 ${!notification.is_read ? 'bg-indigo-50' : ''}"
                       onclick="markAsRead(${notification.id})">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center">
                                    <i class="fas ${notification.icon} ${notification.color} text-sm"></i>
                                </div>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-medium text-gray-900">${notification.title}</p>
                                <p class="text-xs text-gray-500 mt-1">${notification.message}</p>
                                <p class="text-xs text-gray-400 mt-1">${notification.time}</p>
                            </div>
                            ${!notification.is_read ? '<span class="h-2 w-2 bg-indigo-600 rounded-full"></span>' : ''}
                        </div>
                    </a>
                `;
            });
            
            list.innerHTML = html;
        });
}

function updateNotificationBadge() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notification-badge');
            const mobileBadge = document.getElementById('mobile-notification-badge');
            const mobileBadgeResp = document.getElementById('mobile-notification-badge-responsive');
            
            if (data.count > 0) {
                badge.classList.remove('hidden');
                badge.textContent = data.count > 9 ? '9+' : data.count;
                
                if (mobileBadge) {
                    mobileBadge.classList.remove('hidden');
                    mobileBadge.textContent = data.count;
                }
                
                if (mobileBadgeResp) {
                    mobileBadgeResp.classList.remove('hidden');
                    mobileBadgeResp.textContent = data.count;
                }
            } else {
                badge.classList.add('hidden');
                if (mobileBadge) mobileBadge.classList.add('hidden');
                if (mobileBadgeResp) mobileBadgeResp.classList.add('hidden');
            }
        });
}

function markAsRead(id) {
    fetch(`/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        updateNotificationBadge();
    });
}

function markAllAsRead() {
    fetch('/notifications/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        loadNotifications();
        updateNotificationBadge();
    });
}

function clearAllNotifications() {
    if (!confirm('Are you sure you want to clear all notifications?')) {
        return;
    }
    
    fetch('/notifications/clear-all', {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        }
    }).then(() => {
        window.location.reload();
    });
}
</script>
@endpush