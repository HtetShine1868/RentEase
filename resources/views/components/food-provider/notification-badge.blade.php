<!-- resources/views/components/food-provider/notification-badge.blade.php -->
<div class="relative">
    <button type="button" 
            class="relative p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            onclick="toggleNotifications()">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        
        <!-- Unread badge -->
        @if($unreadCount > 0)
        <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-400 text-white text-xs flex items-center justify-center">
            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
        </span>
        @endif
    </button>
    
    <!-- Notification dropdown (simplified version) -->
    <div id="notification-dropdown" 
         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
        <div class="px-4 py-2 border-b">
            <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
            <p class="text-xs text-gray-500">{{ $unreadCount }} unread</p>
        </div>
        
        <div class="max-h-96 overflow-y-auto">
            <!-- Quick notification items -->
            @foreach($recentNotifications as $notification)
            <a href="#" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                <div class="flex items-start">
                    <div class="flex-shrink-0 mr-3">
                        <div class="h-8 w-8 rounded-full {{ $notification['bgColor'] }} flex items-center justify-center">
                            {!! $notification['icon'] !!}
                        </div>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">{{ $notification['title'] }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $notification['time'] }}</p>
                    </div>
                    @if(!$notification['read'])
                    <div class="flex-shrink-0">
                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                    </div>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        
        <div class="px-4 py-2 border-t">
            <a href="{{ route('food-provider.notifications.index') }}" 
               class="block text-center text-sm text-blue-600 hover:text-blue-800">
                View all notifications
            </a>
        </div>
    </div>
</div>

<script>
    function toggleNotifications() {
        const dropdown = document.getElementById('notification-dropdown');
        dropdown.classList.toggle('hidden');
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function closeDropdown(event) {
            if (!dropdown.contains(event.target) && !event.target.closest('.relative')) {
                dropdown.classList.add('hidden');
                document.removeEventListener('click', closeDropdown);
            }
        });
    }
</script>