<header class="bg-white shadow-sm border-b border-gray-200 h-16 md:h-20 flex items-center justify-between px-4 md:px-6 z-20">
    <!-- Left Section: Mobile Menu Button & Title -->
    <div class="flex items-center space-x-4">
        
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