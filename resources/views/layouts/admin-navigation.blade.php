<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center">
                        <i class="fas fa-crown text-indigo-600 text-2xl mr-2"></i>
                        <span class="font-bold text-xl text-gray-800">AdminPanel</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <a href="{{ route('admin.dashboard') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        Dashboard
                    </a>
                    
                    <a href="{{ route('admin.role-applications.index') }}" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.role-applications*') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                        <i class="fas fa-file-alt mr-2"></i>
                        Role Applications
                        @php $pendingCount = \App\Models\RoleApplication::where('status', 'PENDING')->count(); @endphp
                        @if($pendingCount > 0)
                            <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    
                    <a href="#" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-users mr-2"></i>
                        Users
                    </a>
                    
                    <a href="#" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-building mr-2"></i>
                        Properties
                    </a>
                    
                    <a href="#" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-chart-line mr-2"></i>
                        Reports
                    </a>
                    
                    <a href="#" 
                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <i class="fas fa-cog mr-2"></i>
                        Settings
                    </a>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- Notifications Dropdown -->
                <button class="p-2 text-gray-400 hover:text-gray-500 focus:outline-none relative mr-3">
                    <i class="fas fa-bell text-xl"></i>
                    @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute top-0 right-0 block h-4 w-4 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <div class="relative" x-data="{ open: false }" @click.away="open = false">
                    <button @click="open = !open" class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                        <div class="flex items-center">
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                <span class="text-indigo-800 font-medium text-sm">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </span>
                            </div>
                            <span class="ml-2">{{ Auth::user()->name }}</span>
                            <i class="fas fa-chevron-down ml-1 text-xs"></i>
                        </div>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                        
                        <div class="py-1">
                            <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profile
                            </a>
                            
                            <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-bell mr-2"></i> Notifications
                                @if($unreadCount > 0)
                                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                                @endif
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
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.dashboard') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
            </a>
            
            <a href="{{ route('admin.role-applications.index') }}" class="block px-4 py-2 text-base font-medium {{ request()->routeIs('admin.role-applications*') ? 'text-indigo-600 bg-indigo-50' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}">
                <i class="fas fa-file-alt mr-2"></i> Role Applications
                @if($pendingCount > 0)
                    <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>
            
            <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-users mr-2"></i> Users
            </a>
            
            <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-building mr-2"></i> Properties
            </a>
            
            <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-chart-line mr-2"></i> Reports
            </a>
            
            <a href="#" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                <i class="fas fa-cog mr-2"></i> Settings
            </a>
        </div>

        <!-- Responsive User Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="{{ route('profile.show') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                
                <a href="{{ route('notifications.index') }}" class="block px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                    <i class="fas fa-bell mr-2"></i> Notifications
                    @if($unreadCount > 0)
                        <span class="ml-2 bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $unreadCount }}</span>
                    @endif
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-2 text-base font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>