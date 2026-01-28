<header class="bg-white shadow-sm border-b border-gray-200 h-16 md:h-20 flex items-center justify-between px-4 md:px-6 z-20">
    <!-- Left Section: Mobile Menu Button & Title -->
    <div class="flex items-center space-x-4">
        <!-- Mobile Menu Button -->
        <button onclick="toggleSidebar()" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
            <i class="fas fa-bars text-xl"></i>
        </button>
        
        <!-- Page Title (Hidden on mobile when sidebar is open) -->
        <div>
            <h1 class="text-lg md:text-xl font-semibold text-gray-800">
                @hasSection('header-title')
                    @yield('header-title')
                @elseif (View::hasSection('page-title'))
                    @yield('page-title')
                @else
                    Dashboard
                @endif
            </h1>

        </div>
    </div>
    
    <!-- Right Section: Search, Notifications, Profile -->
    <div class="flex items-center space-x-4">
        <!-- Search Bar (Hidden on mobile) -->
        <div class="hidden md:block relative">
            <input type="text" 
                   placeholder="Search properties, bookings..." 
                   class="pl-10 pr-4 py-2 w-64 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
        </div>
        
        <!-- Notifications Dropdown -->
        <div class="relative">
            <button id="notifications-btn" class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none">
                <i class="fas fa-bell text-xl"></i>
                <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">5</span>
            </button>
            
            <!-- Notifications Dropdown Menu -->
            <div id="notifications-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between items-center">
                        <h3 class="font-semibold text-gray-800">Notifications</h3>
                        <span class="text-sm text-blue-600 cursor-pointer hover:text-blue-800">Mark all read</span>
                    </div>
                </div>
                
                <div class="max-h-96 overflow-y-auto">
                    <!-- Notification Item 1 -->
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                <i class="fas fa-calendar-check text-green-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium">New booking received</p>
                                <p class="text-xs text-gray-500 mt-1">Apartment #302 booked for 6 months</p>
                                <p class="text-xs text-gray-400 mt-1">2 hours ago</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Item 2 -->
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                <i class="fas fa-money-bill-wave text-blue-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium">Payment received</p>
                                <p class="text-xs text-gray-500 mt-1">$850 payment for Hostel Room #12</p>
                                <p class="text-xs text-gray-400 mt-1">5 hours ago</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Item 3 -->
                    <div class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium">Complaint submitted</p>
                                <p class="text-xs text-gray-500 mt-1">New complaint about water supply</p>
                                <p class="text-xs text-gray-400 mt-1">1 day ago</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Notification Item 4 -->
                    <div class="p-4 hover:bg-gray-50 cursor-pointer">
                        <div class="flex items-start">
                            <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                <i class="fas fa-star text-purple-600"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm text-gray-800 font-medium">New review received</p>
                                <p class="text-xs text-gray-500 mt-1">★★★★★ rating for Sunshine Apartments</p>
                                <p class="text-xs text-gray-400 mt-1">2 days ago</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-3 border-t border-gray-200 text-center">
                    <a href="{{ route('owner.notifications') }}" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All Notifications</a>
                </div>
            </div>
        </div>
        
        <!-- Profile Dropdown -->
        <div class="relative">
            <button id="profile-btn" class="flex items-center space-x-3 focus:outline-none">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold">
                    OP
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-gray-800">Owner Profile</p>
                    <p class="text-xs text-gray-500">Property Owner</p>
                </div>
                <i class="fas fa-chevron-down text-gray-500 hidden md:block"></i>
            </button>
            
            <!-- Profile Dropdown Menu -->
            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl border border-gray-200 z-50">
                <div class="p-4 border-b border-gray-200">
                    <p class="text-sm font-medium text-gray-800">Owner Profile</p>
                    <p class="text-xs text-gray-500 mt-1">owner@rentease.com</p>
                </div>
                
                <div class="p-2">
                    <a href="{{ route('owner.profile') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-user-circle w-5 mr-2 text-gray-500"></i>
                        My Profile
                    </a>
                    <a href="{{ route('owner.settings.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-cog w-5 mr-2 text-gray-500"></i>
                        Settings
                    </a>
                    <a href="{{ route('owner.earnings.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-lg">
                        <i class="fas fa-wallet w-5 mr-2 text-gray-500"></i>
                        Earnings
                    </a>
                </div>
                
                <div class="p-2 border-t border-gray-200">
                    <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center px-3 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg">
                        <i class="fas fa-sign-out-alt w-5 mr-2"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    #notifications-dropdown, #profile-dropdown {
        animation: fadeIn 0.2s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<script>
    // Notifications dropdown toggle
    document.addEventListener('DOMContentLoaded', function() {
        const notificationsBtn = document.getElementById('notifications-btn');
        const notificationsDropdown = document.getElementById('notifications-dropdown');
        const profileBtn = document.getElementById('profile-btn');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        if (notificationsBtn) {
            notificationsBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                notificationsDropdown.classList.toggle('hidden');
                profileDropdown.classList.add('hidden');
            });
        }
        
        if (profileBtn) {
            profileBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
                notificationsDropdown.classList.add('hidden');
            });
        }
        
        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            if (notificationsDropdown) notificationsDropdown.classList.add('hidden');
            if (profileDropdown) profileDropdown.classList.add('hidden');
        });
        
        // Prevent dropdowns from closing when clicking inside them
        if (notificationsDropdown) {
            notificationsDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
        
        if (profileDropdown) {
            profileDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
</script>