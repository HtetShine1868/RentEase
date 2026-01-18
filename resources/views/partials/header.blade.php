<!-- resources/views/partials/header.blade.php -->
<header class="bg-white border-b border-gray-200">
    <div class="flex items-center justify-between px-4 lg:px-6 py-3">
        <!-- Left: Menu button and breadcrumb -->
        <div class="flex items-center space-x-4">
            <!-- Mobile menu button -->
            <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-gray-700">
                <i class="fas fa-bars text-xl"></i>
            </button>
            
            <!-- Breadcrumb -->
            <div class="hidden lg:flex items-center space-x-2 text-sm">
                <a href="{{ route('dashboard') }}" class="text-gray-500 hover:text-gray-700">Dashboard</a>
                <i class="fas fa-chevron-right text-gray-400 text-xs"></i>
                <span class="text-gray-700 font-medium">
                    @yield('page-title', 'Home')
                </span>
            </div>
            
            <!-- Mobile title -->
            <h1 class="lg:hidden text-lg font-semibold text-gray-800">
                @yield('page-title', 'RMS')
            </h1>
        </div>
        
        <!-- Right: User menu and notifications -->
        <div class="flex items-center space-x-4">
            <!-- Notifications -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <!-- Notifications dropdown -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                    <div class="px-4 py-2 border-b">
                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        <!-- Notification items will go here -->
                        <div class="px-4 py-3 hover:bg-gray-50 border-b">
                            <p class="text-sm text-gray-700">Welcome to RMS! Complete your profile.</p>
                            <span class="text-xs text-gray-500">Just now</span>
                        </div>
                    </div>
                    <div class="px-4 py-2 border-t text-center">
                        <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">View all notifications</a>
                    </div>
                </div>
            </div>
            
            <!-- User menu -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-9 h-9 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600"></i>
                    </div>
                    <div class="hidden lg:block text-left">
                        <p class="text-sm font-medium text-gray-700">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-500 capitalize">{{ Auth::user()->role ?? 'User' }}</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-500 text-xs"></i>
                </button>
                
                <!-- User dropdown -->
                <div x-show="open" @click.away="open = false" 
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50">
                    <a href="{{ route('profile.show') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user mr-2"></i> My Profile
                    </a>
                    <a href="{{ route('payments.index') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-credit-card mr-2"></i> Payment History
                    </a>
                    <div class="border-t border-gray-200 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>