<header class="bg-white shadow-sm border-b border-gray-200">
    <div class="flex justify-between items-center px-4 sm:px-6 md:px-8 h-16">
        <!-- Mobile menu button -->
        <div class="md:hidden">
            <button @click="sidebarOpen = !sidebarOpen" 
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                <i class="fas fa-bars h-6 w-6"></i>
            </button>
        </div>
        
        <!-- Restaurant Name & Welcome -->
        <div class="flex-1 flex items-center">
            <div>
                <h2 class="text-lg font-medium text-gray-900" id="restaurant-name">
                    {{ auth()->user()->restaurant->name ?? 'Restaurant Name' }}
                </h2>
                <p class="text-sm text-gray-500 flex items-center">
                    <i class="fas fa-circle text-xs mr-1 text-green-500"></i>
                    <span id="restaurant-status">Online</span>
                    <span class="mx-2">•</span>
                    <span id="coverage-radius">5 km coverage</span>
                </p>
            </div>
        </div>
        
        <!-- Right side buttons -->
        <div class="flex items-center space-x-4">
            <!-- Quick Stats -->
            <div class="hidden md:flex items-center space-x-4 text-sm">
                <div class="text-center">
                    <div class="font-medium text-gray-900" id="today-orders">0</div>
                    <div class="text-xs text-gray-500">Today</div>
                </div>
                <div class="h-6 w-px bg-gray-300"></div>
                <div class="text-center">
                    <div class="font-medium text-green-600" id="today-earnings">₹0</div>
                    <div class="text-xs text-gray-500">Earnings</div>
                </div>
            </div>
            
            <!-- Notification Bell -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                    <i class="fas fa-bell h-6 w-6"></i>
                    <span class="absolute top-1 right-1 h-2 w-2 bg-red-500 rounded-full" id="notification-badge"></span>
                </button>
                
                <!-- Notification Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                    <div class="px-4 py-2 border-b border-gray-100">
                        <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                        <p class="text-xs text-gray-500 mt-1">You have <span class="font-medium">5</span> unread</p>
                    </div>
                    <div class="max-h-60 overflow-y-auto">
                        <!-- Notification items would go here -->
                        <div class="px-4 py-3 hover:bg-gray-50 border-b border-gray-100">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shopping-cart text-blue-500"></i>
                                </div>
                                <div class="ml-3 flex-1">
                                    <p class="text-sm text-gray-800">
                                        New order received
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">
                                        10 minutes ago
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="border-t border-gray-100 px-4 py-2">
                        <a href="{{ route('food-provider.notifications.index') }}" 
                           class="text-xs font-medium text-indigo-600 hover:text-indigo-500">
                            View all notifications →
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Profile Dropdown -->
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" 
                        class="flex items-center space-x-2 focus:outline-none">
                    <div class="h-8 w-8 rounded-full bg-indigo-600 flex items-center justify-center">
                        <span class="text-white font-medium text-sm">
                            {{ substr(auth()->user()->name ?? 'FP', 0, 2) }}
                        </span>
                    </div>
                    <div class="hidden md:block text-left">
                        <p class="text-sm font-medium text-gray-700">{{ auth()->user()->name ?? 'Food Provider' }}</p>
                        <p class="text-xs text-gray-500">Food Provider</p>
                    </div>
                    <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">
                    <a href="{{ route('food-provider.profile.index') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-user-circle mr-2"></i> Your Profile
                    </a>
                    <a href="{{ route('food-provider.settings.index') }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-cog mr-2"></i> Settings
                    </a>
                    <div class="border-t border-gray-100 my-1"></div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            <i class="fas fa-sign-out-alt mr-2"></i> Sign out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>