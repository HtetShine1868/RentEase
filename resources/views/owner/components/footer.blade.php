<footer class="bg-white border-t border-gray-200 py-4 px-6">
    <div class="flex flex-col md:flex-row justify-between items-center">
        <!-- Copyright & Links -->
        <div class="mb-4 md:mb-0 text-center md:text-left">
            <p class="text-sm text-gray-600">
                &copy; {{ date('Y') }} <span class="font-semibold text-blue-600">RentEase System</span>. 
                All rights reserved.
            </p>
            <div class="flex flex-wrap justify-center md:justify-start space-x-4 mt-2">
                <a href="#" class="text-xs text-gray-500 hover:text-blue-600 transition">Privacy Policy</a>
                <a href="#" class="text-xs text-gray-500 hover:text-blue-600 transition">Terms of Service</a>
                <a href="#" class="text-xs text-gray-500 hover:text-blue-600 transition">Help Center</a>
                <a href="#" class="text-xs text-gray-500 hover:text-blue-600 transition">Contact Support</a>
            </div>
        </div>
        
        <!-- Stats & Version -->
        <div class="flex flex-col md:flex-row items-center space-y-3 md:space-y-0 md:space-x-6">
            <!-- System Status -->
            <div class="flex items-center">
                <div class="w-3 h-3 rounded-full bg-green-500 mr-2 animate-pulse"></div>
                <span class="text-sm text-gray-600">System Active</span>
            </div>
            
            <!-- Quick Stats -->
            <div class="hidden lg:flex items-center space-x-4">
                <div class="text-center">
                    <p class="text-xs text-gray-500">Response Time</p>
                    <p class="text-sm font-semibold text-gray-800">< 24h</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Uptime</p>
                    <p class="text-sm font-semibold text-green-600">99.8%</p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500">Properties</p>
                    <p class="text-sm font-semibold text-blue-600">12</p>
                </div>
            </div>
            
            <!-- Version -->
            <div class="text-center md:text-right">
                <p class="text-xs text-gray-500">Version</p>
                <p class="text-sm font-semibold text-gray-800">v2.1.0</p>
            </div>
        </div>
    </div>
    
    <!-- Mobile-only Quick Actions -->
    <div class="md:hidden mt-4 pt-4 border-t border-gray-200">
        <div class="flex justify-around">
            <a href="{{ route('owner.dashboard') }}" class="flex flex-col items-center text-blue-600 hover:text-blue-800">
                <i class="fas fa-home text-lg"></i>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="{{ route('owner.properties.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600">
                <i class="fas fa-building text-lg"></i>
                <span class="text-xs mt-1">Properties</span>
            </a>
            <a href="{{ route('owner.bookings.index') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600">
                <i class="fas fa-calendar-check text-lg"></i>
                <span class="text-xs mt-1">Bookings</span>
            </a>
            <a href="{{ route('owner.notifications') }}" class="flex flex-col items-center text-gray-600 hover:text-blue-600 relative">
                <i class="fas fa-bell text-lg"></i>
                <span class="text-xs mt-1">Alerts</span>
                <span class="absolute -top-1 -right-2 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center text-[10px]">5</span>
            </a>
        </div>
    </div>
</footer>

<style>
    footer a {
        transition: color 0.2s ease;
    }
    
    @media (max-width: 768px) {
        footer {
            padding-bottom: 80px; /* Space for mobile nav */
        }
        
        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    }
</style>