{{-- resources/views/laundry-provider/components/header.blade.php --}}
<header class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
    <div class="flex items-center">
        <button id="mobile-menu-toggle" class="md:hidden mr-4 text-gray-500">
            <i class="fas fa-bars text-xl"></i>
        </button>
        <h2 class="text-xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
    </div>
    
    <div class="flex items-center space-x-4">
        {{-- Date Display --}}
        <div class="hidden md:block text-gray-500">
            <i class="far fa-calendar mr-2"></i>
            {{ now()->format('l, F j, Y') }}
        </div>
        
        {{-- Notifications --}}
        <div class="relative">
            <button id="notification-btn" class="text-gray-500 hover:text-gray-700">
                <i class="far fa-bell text-xl"></i>
                @php $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count(); @endphp
                @if($unreadCount > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-4 w-4 flex items-center justify-center">{{ $unreadCount }}</span>
                @endif
            </button>
        </div>
        
        {{-- User Menu --}}
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center space-x-2">
                <img src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" 
                     alt="Avatar" class="w-8 h-8 rounded-full">
                <span class="hidden md:inline text-gray-700">{{ auth()->user()->name }}</span>
                <i class="fas fa-chevron-down text-xs text-gray-500"></i>
            </button>
            
            <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 z-50">
                <a href="{{ route('laundry-provider.profile.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="far fa-user mr-2"></i> Profile
                </a>
                <a href="{{ route('laundry-provider.settings.index') }}" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-cog mr-2"></i> Settings
                </a>
                <hr class="my-2">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>