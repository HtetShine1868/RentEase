<!-- resources/views/partials/sidebar.blade.php -->
<aside x-show="sidebarOpen" @keydown.escape.window="sidebarOpen = false"
       class="fixed lg:static inset-y-0 left-0 z-30 w-64 bg-white border-r border-gray-200 transform lg:translate-x-0 transition-transform duration-300 ease-in-out"
       :class="{ '-translate-x-full': !sidebarOpen }"
       x-transition:enter="transition ease-out duration-300"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full">
    
    <!-- Logo -->
    <div class="flex items-center justify-between h-16 px-4 border-b">
        <div class="flex items-center space-x-3">
            <div class="w-8 h-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                <i class="fas fa-home text-white text-sm"></i>
            </div>
            <span class="text-xl font-bold text-gray-800">RMS</span>
        </div>
        
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="lg:hidden text-gray-500 hover:text-gray-700">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4">
        <div class="px-2 space-y-1">
            <!-- Home -->
            <a href="{{ route('dashboard') }}" 
               class="{{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-home mr-3 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Home</span>
            </a>
            
            <!-- Search Properties -->
            <a href="{{ route('properties.index') }}" 
               class="{{ request()->routeIs('properties.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-search mr-3 {{ request()->routeIs('properties.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Search Properties</span>
            </a>
            
            <!-- Food Services -->
            <a href="{{ route('food.index') }}" 
               class="{{ request()->routeIs('food.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-utensils mr-3 {{ request()->routeIs('food.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Food Services</span>
            </a>
            
            <!-- Laundry Services -->
            <a href="{{ route('laundry.index') }}" 
               class="{{ request()->routeIs('laundry.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-tshirt mr-3 {{ request()->routeIs('laundry.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Laundry Services</span>
            </a>
            
            <!-- Applications with dropdown -->
            <div x-data="{ open: false }" class="space-y-1">
                <button @click="open = !open" 
                        class="{{ request()->routeIs('applications.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                               w-full group flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                    <div class="flex items-center">
                        <i class="fas fa-briefcase mr-3 {{ request()->routeIs('applications.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                        <span>Applications</span>
                    </div>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
                       :class="{ 'rotate-180': open }"></i>
                </button>
                
                <!-- Dropdown items -->
                <div x-show="open" x-collapse 
                     class="ml-8 space-y-1 border-l border-gray-200 pl-3">
                    <a href="{{ route('applications.owner') }}" 
                       class="{{ request()->routeIs('applications.owner') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} 
                              block py-2 text-sm font-medium transition-colors duration-200">
                        Owner Application
                    </a>
                    <a href="{{ route('applications.food') }}" 
                       class="{{ request()->routeIs('applications.food') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} 
                              block py-2 text-sm font-medium transition-colors duration-200">
                        Food Service
                    </a>
                    <a href="{{ route('applications.laundry') }}" 
                       class="{{ request()->routeIs('applications.laundry') ? 'text-indigo-600' : 'text-gray-600 hover:text-gray-900' }} 
                              block py-2 text-sm font-medium transition-colors duration-200">
                        Laundry Service
                    </a>
                </div>
            </div>
            
            <!-- Profile -->
            <a href="{{ route('profile.show') }}" 
               class="{{ request()->routeIs('profile.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-user mr-3 {{ request()->routeIs('profile.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Profile</span>
            </a>
            
            <!-- Payments -->
            <a href="{{ route('payments.index') }}" 
               class="{{ request()->routeIs('payments.*') ? 'bg-indigo-50 text-indigo-600' : 'text-gray-700 hover:bg-gray-100' }} 
                      group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-credit-card mr-3 {{ request()->routeIs('payments.*') ? 'text-indigo-600' : 'text-gray-400' }} text-lg"></i>
                <span>Payments</span>
            </a>
            
            <!-- Divider -->
            <div class="border-t border-gray-200 my-4"></div>
            
            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" 
                        class="w-full text-left text-red-600 hover:bg-red-50 group flex items-center px-4 py-3 text-sm font-medium rounded-lg transition-colors duration-200">
                    <i class="fas fa-sign-out-alt mr-3 text-red-500 text-lg"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>