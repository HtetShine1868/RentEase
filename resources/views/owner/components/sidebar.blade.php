<aside id="owner-sidebar" 
       x-data="{
           sidebarOpen: false,
           mobileOpen: false,
           activeMenu: @php
               if (request()->routeIs('owner.dashboard')) {
                   echo "'dashboard'";
               } elseif (request()->routeIs('owner.properties.*')) {
                   echo "'properties'";
               } elseif (request()->routeIs('owner.bookings.*')) {
                   echo "'bookings'";
               } elseif (request()->routeIs('owner.earnings.*')) {
                   echo "'earnings'";
               } elseif (request()->routeIs('owner.complaints.*')) {
                   echo "'complaints'";
               } else {
                   echo "''";
               }
           @endphp,
           stats: {
               activeProperties: 8,
               monthlyEarnings: 2450,
               pendingBookings: 4,
               notifications: 5,
               complaints: 3
           },
           init() {
               // Set active menu based on current route using Blade
               this.setActiveMenuFromPath();
               
               // Listen for route changes
               window.addEventListener('popstate', () => {
                   this.setActiveMenuFromPath();
               });
           },
           setActiveMenuFromPath() {
               const path = window.location.pathname;
               
               // Check for patterns
               if (path.includes('/owner/properties')) {
                   this.activeMenu = 'properties';
               } else if (path.includes('/owner/bookings')) {
                   this.activeMenu = 'bookings';
               } else if (path.includes('/owner/earnings')) {
                   this.activeMenu = 'earnings';
               } else if (path.includes('/owner/complaints')) {
                   this.activeMenu = 'complaints';
               } else if (path.includes('/owner/dashboard') || path === '/owner' || path === '/owner/') {
                   this.activeMenu = 'dashboard';
               }
           },
           loadStats() {
               // Fetch real stats from API
               fetch('/api/owner/stats')
                   .then(response => response.json())
                   .then(data => this.stats = data);
           },
           toggleSidebar() {
               this.sidebarOpen = !this.sidebarOpen;
               localStorage.setItem('sidebarCollapsed', this.sidebarOpen);
           },
           isActive(menu) {
               return this.activeMenu === menu;
           }
       }"
       :class="{ 
           '-translate-x-full md:translate-x-0': !mobileOpen,
           'translate-x-0': mobileOpen,
           'w-20 md:w-20': sidebarOpen && window.innerWidth >= 768,
           'w-64 md:w-72': !sidebarOpen || window.innerWidth < 768
       }"
       class="bg-gradient-to-b from-gray-900 via-gray-900 to-gray-800 text-white flex-shrink-0 transform transition-all duration-300 z-50 fixed md:relative h-screen overflow-y-auto shadow-2xl"
       style="scrollbar-width: thin; scrollbar-color: #4b5563 #1f2937;">
    
    <!-- Animated Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 to-purple-900/10 pointer-events-none"></div>
    
    <!-- Sidebar Content -->
    <div class="relative z-10">
        <!-- Sidebar Header with Toggle -->
        <div class="p-6 border-b border-gray-800/50">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3 transition-all duration-300" :class="{ 'justify-center': sidebarOpen && window.innerWidth >= 768 }">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg group hover:shadow-blue-500/25 transition-all duration-300">
                        <i class="fas fa-home text-white text-lg group-hover:scale-110 transition-transform"></i>
                    </div>
                    <div x-show="!sidebarOpen || window.innerWidth < 768" x-transition:enter="transition ease-out duration-300" 
                         x-transition:enter-start="opacity-0 transform -translate-x-4" 
                         x-transition:enter-end="opacity-100 transform translate-x-0">
                        <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent">RentEase</h2>
                        <p class="text-gray-400 text-sm flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            Owner Dashboard
                        </p>
                    </div>
                </div>
                
                <!-- Desktop Toggle Button -->
                <button @click="toggleSidebar()" 
                        x-show="window.innerWidth >= 768 && !sidebarOpen" 
                        class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all duration-200 group"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:leave="transition ease-in duration-200">
                    <i class="fas fa-chevron-left text-sm group-hover:scale-110"></i>
                </button>
                
                <!-- Mobile Close Button -->
                <button @click="mobileOpen = false" 
                        x-show="mobileOpen" 
                        class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <!-- Collapsed Logo -->
            <div x-show="sidebarOpen && window.innerWidth >= 768" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="mt-4 flex justify-center">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg">
                    <i class="fas fa-home text-white text-xl"></i>
                </div>
            </div>
        </div>
        
        <!-- User Profile (Expanded only) -->
        <div x-show="!sidebarOpen || window.innerWidth < 768" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:leave="transition ease-in duration-200"
             class="p-4 border-b border-gray-800/50">
            <div class="flex items-center space-x-3 group cursor-pointer">
                <div class="relative">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center shadow-lg group-hover:shadow-blue-500/25 transition-all">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-900"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-medium truncate text-sm">{{ Auth::user()->name ?? 'Owner' }}</p>
                    <p class="text-xs text-gray-400 truncate">Property Manager</p>
                </div>
                <i class="fas fa-chevron-right text-gray-500 text-xs group-hover:text-white transition-colors"></i>
            </div>
        </div>
        
        <!-- Navigation Menu -->
        <div class="p-4 sidebar-menu">
            <ul class="space-y-1">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('owner.dashboard') }}" 
                       @click="activeMenu = 'dashboard'; mobileOpen = false"
                       :class="{
                           'bg-gradient-to-r from-blue-900/50 to-purple-900/30 border-l-4 border-blue-500 shadow-lg': isActive('dashboard'),
                           'hover:bg-gray-800/50': !isActive('dashboard')
                       }"
                       class="flex items-center p-3 rounded-xl transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-tachometer-alt w-6 mr-3 text-gray-400 group-hover:text-blue-400 transition-colors duration-200"
                               :class="{ 'text-blue-400': isActive('dashboard') }"></i>
                            <div x-show="isActive('dashboard')" 
                                 class="absolute -inset-1 bg-blue-500/20 blur-md rounded-full"></div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 -translate-x-4"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              class="font-medium"
                              :class="{ 'text-white': isActive('dashboard'), 'text-gray-300': !isActive('dashboard') }">
                            Dashboard
                        </span>
                    </a>
                </li>
                
                <!-- My Properties -->
                <li>
                    <a href="{{ route('owner.properties.index') }}" 
                       @click="activeMenu = 'properties'; mobileOpen = false"
                       :class="{
                           'bg-gradient-to-r from-blue-900/50 to-purple-900/30 border-l-4 border-blue-500 shadow-lg': isActive('properties'),
                           'hover:bg-gray-800/50': !isActive('properties')
                       }"
                       class="flex items-center p-3 rounded-xl transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-building w-6 mr-3 text-gray-400 group-hover:text-green-400 transition-colors duration-200"
                               :class="{ 'text-green-400': isActive('properties') }"></i>
                            <div x-show="isActive('properties')" 
                                 class="absolute -inset-1 bg-green-500/20 blur-md rounded-full"></div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              class="font-medium flex-1"
                              :class="{ 'text-white': isActive('properties'), 'text-gray-300': !isActive('properties') }">
                            My Properties
                        </span>
                        <span x-show="(!sidebarOpen || window.innerWidth < 768) && stats.activeProperties > 0"
                              class="ml-auto bg-gradient-to-r from-green-500 to-emerald-600 text-xs px-2 py-1 rounded-full font-bold shadow-lg">
                            <span x-text="stats.activeProperties"></span>
                        </span>
                    </a>
                </li>
                
                <!-- Bookings -->
                <li>
                    <a href="{{ route('owner.bookings.index') }}" 
                       @click="activeMenu = 'bookings'; mobileOpen = false"
                       :class="{
                           'bg-gradient-to-r from-blue-900/50 to-purple-900/30 border-l-4 border-blue-500 shadow-lg': isActive('bookings'),
                           'hover:bg-gray-800/50': !isActive('bookings')
                       }"
                       class="flex items-center p-3 rounded-xl transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-calendar-check w-6 mr-3 text-gray-400 group-hover:text-yellow-400 transition-colors duration-200"
                               :class="{ 'text-yellow-400': isActive('bookings') }"></i>
                            <div x-show="isActive('bookings')" 
                                 class="absolute -inset-1 bg-yellow-500/20 blur-md rounded-full"></div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              class="font-medium flex-1"
                              :class="{ 'text-white': isActive('bookings'), 'text-gray-300': !isActive('bookings') }">
                            Bookings
                        </span>
                        <span x-show="(!sidebarOpen || window.innerWidth < 768) && stats.pendingBookings > 0"
                              class="ml-auto bg-gradient-to-r from-yellow-500 to-amber-600 text-xs px-2 py-1 rounded-full font-bold shadow-lg">
                                <span x-show="(!sidebarOpen || window.innerWidth < 768) && stats.pendingBookings > 0"
                                    class="ml-auto bg-gradient-to-r from-yellow-500 to-amber-600 text-xs px-2 py-1 rounded-full font-bold shadow-lg">
                                    <span x-text="stats.pendingBookings"></span>
                                </span>
                        </span>
                    </a>
                </li>
                
                <!-- Earnings -->
                <li>
                    <a href="{{ route('owner.earnings.index') }}" 
                       @click="activeMenu = 'earnings'; mobileOpen = false"
                       :class="{
                           'bg-gradient-to-r from-blue-900/50 to-purple-900/30 border-l-4 border-blue-500 shadow-lg': isActive('earnings'),
                           'hover:bg-gray-800/50': !isActive('earnings')
                       }"
                       class="flex items-center p-3 rounded-xl transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-chart-line w-6 mr-3 text-gray-400 group-hover:text-emerald-400 transition-colors duration-200"
                               :class="{ 'text-emerald-400': isActive('earnings') }"></i>
                            <div x-show="isActive('earnings')" 
                                 class="absolute -inset-1 bg-emerald-500/20 blur-md rounded-full"></div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              class="font-medium flex-1"
                              :class="{ 'text-white': isActive('earnings'), 'text-gray-300': !isActive('earnings') }">
                            Earnings
                        </span>
                        <span x-show="!sidebarOpen || window.innerWidth < 768"
                              class="ml-auto text-xs px-2 py-1 rounded-full font-bold bg-gradient-to-r from-emerald-500 to-green-600 shadow-lg">
                          $<span x-text="stats.monthlyEarnings"></span>
                        </span>
                    </a>
                </li>
                
                <!-- Complaints -->
                <li>
                    <a href="{{ route('owner.complaints.index') }}" 
                       @click="activeMenu = 'complaints'; mobileOpen = false"
                       :class="{
                           'bg-gradient-to-r from-blue-900/50 to-purple-900/30 border-l-4 border-blue-500 shadow-lg': isActive('complaints'),
                           'hover:bg-gray-800/50': !isActive('complaints')
                       }"
                       class="flex items-center p-3 rounded-xl transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-exclamation-circle w-6 mr-3 text-gray-400 group-hover:text-red-400 transition-colors duration-200"
                               :class="{ 'text-red-400': isActive('complaints') }"></i>
                            <div x-show="isActive('complaints')" 
                                 class="absolute -inset-1 bg-red-500/20 blur-md rounded-full"></div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              class="font-medium flex-1"
                              :class="{ 'text-white': isActive('complaints'), 'text-gray-300': !isActive('complaints') }">
                            Complaints
                        </span>
                        <span x-show="(!sidebarOpen || window.innerWidth < 768) && stats.complaints > 0"
                              class="ml-auto bg-gradient-to-r from-red-500 to-pink-600 text-xs px-2 py-1 rounded-full font-bold shadow-lg">
                            <span x-text="stats.complaints"></span>
                        </span>
                    </a>
                </li>
                
                <!-- Notifications -->
                <li>
                    <a href="{{ route('owner.notifications') }}" 
                       @click="mobileOpen = false"
                       class="flex items-center p-3 rounded-xl hover:bg-gray-800/50 transition-all duration-200 group">
                        <div class="relative">
                            <i class="fas fa-bell w-6 mr-3 text-gray-400 group-hover:text-purple-400 transition-colors duration-200"></i>
                            <div x-show="stats.notifications > 0" 
                                 class="absolute -top-1 -right-1 w-5 h-5 bg-gradient-to-r from-purple-500 to-pink-600 rounded-full flex items-center justify-center text-xs animate-pulse">
                                <span x-text="stats.notifications"></span>
                            </div>
                        </div>
                        <span x-show="!sidebarOpen || window.innerWidth < 768" 
                              x-transition:enter="transition ease-out duration-300"
                              class="font-medium text-gray-300">
                            Notifications
                        </span>
                        <span x-show="(!sidebarOpen || window.innerWidth < 768) && stats.notifications > 0"
                              class="ml-auto bg-gradient-to-r from-purple-500 to-pink-600 text-xs px-2 py-1 rounded-full font-bold shadow-lg animate-pulse">
                            <span x-text="stats.notifications"></span>
                        </span>
                    </a>
                </li>
        
            </ul>
            
            <!-- Statistics Card -->
            <div x-show="!sidebarOpen || window.innerWidth < 768" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="mt-8 p-4 bg-gradient-to-br from-gray-800/80 to-gray-900/80 backdrop-blur-sm rounded-2xl border border-gray-700/50 shadow-2xl">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-semibold text-gray-300 flex items-center">
                        <i class="fas fa-chart-bar mr-2 text-blue-400"></i>
                        QUICK STATS
                    </h3>
                    <button @click="loadStats()" 
                            class="text-gray-400 hover:text-white transition-colors"
                            title="Refresh stats">
                        <i class="fas fa-sync-alt text-xs"></i>
                    </button>
                </div>
                <div class="space-y-3">
                    <!-- Active Properties -->
                    <div class="flex justify-between items-center group cursor-pointer" 
                         @click="activeMenu = 'properties'; window.location.href = '{{ route('owner.properties.index') }}'">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-blue-500 mr-2 group-hover:animate-pulse"></div>
                            <span class="text-gray-300 group-hover:text-white transition-colors">Active Properties</span>
                        </div>
                        <span class="font-bold text-blue-400 group-hover:scale-110 transition-transform">
                            <span x-text="stats.activeProperties"></span>
                        </span>
                    </div>
                    
                    <!-- Monthly Earnings -->
                    <div class="flex justify-between items-center group cursor-pointer"
                         @click="activeMenu = 'earnings'; window.location.href = '{{ route('owner.earnings.index') }}'">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-green-500 mr-2 group-hover:animate-pulse"></div>
                            <span class="text-gray-300 group-hover:text-white transition-colors">Monthly Earnings</span>
                        </div>
                        <span class="font-bold text-green-400 group-hover:scale-110 transition-transform">
                            $<span x-text="stats.monthlyEarnings"></span>
                        </span>
                    </div>
                    
                    <!-- Pending Bookings -->
                    <div class="flex justify-between items-center group cursor-pointer"
                         @click="activeMenu = 'bookings'; window.location.href = '{{ route('owner.bookings.index') }}'">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-yellow-500 mr-2 group-hover:animate-pulse"></div>
                            <span class="text-gray-300 group-hover:text-white transition-colors">Pending Bookings</span>
                        </div>
                        <span class="font-bold text-yellow-400 group-hover:scale-110 transition-transform">
                           <span x-text="stats.pendingBookings"></span>
                        </span>
                    </div>
                    
                    <!-- Occupancy Rate -->
                    <div class="flex justify-between items-center">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full bg-purple-500 mr-2"></div>
                            <span class="text-gray-300">Occupancy Rate</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-16 h-2 bg-gray-700 rounded-full overflow-hidden mr-2">
                                <div class="h-full bg-gradient-to-r from-purple-500 to-pink-500 rounded-full" style="width: 83%"></div>
                            </div>
                            <span class="font-bold text-purple-400">83%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Logout Button -->
            <div class="mt-6 pt-4 border-t border-gray-800/50">
                <a href="#" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
                   class="flex items-center p-3 rounded-xl hover:bg-gradient-to-r from-red-900/30 to-pink-900/20 transition-all duration-200 group">
                    <div class="relative">
                        <i class="fas fa-sign-out-alt w-6 mr-3 text-red-400 group-hover:text-red-300 transition-colors duration-200"></i>
                        <div class="absolute -inset-1 bg-red-500/10 blur-md rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </div>
                    <span x-show="!sidebarOpen || window.innerWidth < 768" 
                          x-transition:enter="transition ease-out duration-300"
                          class="font-medium text-red-400 group-hover:text-red-300 transition-colors">
                        Logout
                    </span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" 
     x-show="mobileOpen"
     @click="mobileOpen = false"
     class="fixed inset-0 bg-black bg-opacity-70 backdrop-blur-sm z-40 md:hidden transition-opacity duration-300"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     style="display: none;"></div>

<!-- Add Alpine.js CDN if not already included -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<script>
    // Initialize sidebar state from localStorage
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        const sidebarElement = document.querySelector('#owner-sidebar');
        if (sidebarElement && sidebarElement.__x) {
            const app = sidebarElement.__x;
            if (window.innerWidth >= 768) {
                app.$data.sidebarOpen = sidebarCollapsed;
            }
        }
        
        // Add resize listener
        window.addEventListener('resize', function() {
            const sidebarElement = document.querySelector('#owner-sidebar');
            if (sidebarElement && sidebarElement.__x) {
                const app = sidebarElement.__x;
                if (window.innerWidth < 768) {
                    app.$data.sidebarOpen = false;
                }
            }
        });
        
        // Close mobile sidebar on navigation
        document.querySelectorAll('#owner-sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 768) {
                    const sidebarElement = document.querySelector('#owner-sidebar');
                    if (sidebarElement && sidebarElement.__x) {
                        const app = sidebarElement.__x;
                        app.$data.mobileOpen = false;
                    }
                }
            });
        });
    });
    
    // Function to toggle sidebar (call from header)
    function toggleOwnerSidebar() {
        const sidebarElement = document.querySelector('#owner-sidebar');
        if (sidebarElement && sidebarElement.__x) {
            const app = sidebarElement.__x;
            if (window.innerWidth < 768) {
                app.$data.mobileOpen = !app.$data.mobileOpen;
            } else {
                app.$data.sidebarOpen = !app.$data.sidebarOpen;
                localStorage.setItem('sidebarCollapsed', app.$data.sidebarOpen);
            }
        }
    }
</script>

<style>
    /* Custom scrollbar */
    #owner-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #owner-sidebar::-webkit-scrollbar-track {
        background: rgba(31, 41, 55, 0.5);
        border-radius: 3px;
    }
    
    #owner-sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        border-radius: 3px;
    }
    
    #owner-sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #7c3aed);
    }
    
    /* Glow effects */
    .glow {
        box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
    }
    
    /* Smooth transitions */
    .transition-all {
        transition-property: all;
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    /* Hover effects */
    .hover-lift:hover {
        transform: translateY(-2px);
    }
    
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(to right, #60a5fa, #a78bfa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>