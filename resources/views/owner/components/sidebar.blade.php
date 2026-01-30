<aside id="owner-sidebar" class="bg-gray-900 text-white w-64 md:w-72 flex-shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 z-40 fixed md:relative h-screen overflow-y-auto">
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center">
                <i class="fas fa-home text-white text-lg"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold">RentEase</h2>
                <p class="text-gray-400 text-sm">Owner Dashboard</p>
            </div>
        </div>
    </div>
    
    <div class="p-4 sidebar-menu">
        <ul class="space-y-2">
            <li>
                <a href="{{ route('owner.dashboard') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('owner.dashboard') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-tachometer-alt w-6 mr-3 text-gray-400"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('owner.properties.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('owner.properties.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-building w-6 mr-3 text-gray-400"></i>
                    <span>My Properties</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('owner.bookings.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('owner.bookings.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-calendar-check w-6 mr-3 text-gray-400"></i>
                    <span>Bookings</span>
                    <span class="ml-auto bg-blue-500 text-xs px-2 py-1 rounded-full">12</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('owner.earnings.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('owner.earnings.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-chart-line w-6 mr-3 text-gray-400"></i>
                    <span>Earnings</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('owner.complaints.index') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors {{ request()->routeIs('owner.complaints.*') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                    <i class="fas fa-exclamation-circle w-6 mr-3 text-gray-400"></i>
                    <span>Complaints</span>
                    <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full">3</span>
                </a>
            </li>
            
            <li>
                <a href="{{ route('owner.notifications') }}" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors">
                    <i class="fas fa-bell w-6 mr-3 text-gray-400"></i>
                    <span>Notifications</span>
                    <span class="ml-auto bg-yellow-500 text-xs px-2 py-1 rounded-full">5</span>
                </a>
            </li>
            
            
            
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center p-3 rounded-lg hover:bg-gray-800 transition-colors text-red-400 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-6 mr-3"></i>
                    <span>Logout</span>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </li>
        </ul>
        
        <!-- Statistics Card -->
        <div class="mt-8 p-4 bg-gray-800 rounded-lg">
            <h3 class="text-sm font-semibold text-gray-400 mb-3">QUICK STATS</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Active Properties</span>
                    <span class="font-bold text-blue-400">8</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">This Month Earnings</span>
                    <span class="font-bold text-green-400">$2,450</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-300">Pending Bookings</span>
                    <span class="font-bold text-yellow-400">4</span>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-30 md:hidden" onclick="toggleSidebar()" style="display: none;"></div>

<style>
    .sidebar-menu a.active-menu {
        background-color: #1f2937;
        border-left: 4px solid #3b82f6;
    }
    
    .sidebar-menu a.active-menu i {
        color: #3b82f6;
    }
    
    .sidebar-menu a.active-menu span {
        color: white;
        font-weight: 500;
    }
    
    #owner-sidebar {
        scrollbar-width: thin;
        scrollbar-color: #4b5563 #1f2937;
    }
    
    #owner-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #owner-sidebar::-webkit-scrollbar-track {
        background: #1f2937;
    }
    
    #owner-sidebar::-webkit-scrollbar-thumb {
        background-color: #4b5563;
        border-radius: 3px;
    }
</style>

<script>
    // Show/hide mobile overlay
    function toggleSidebar() {
        const sidebar = document.getElementById('owner-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.toggle('-translate-x-full');
        sidebar.classList.toggle('translate-x-0');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            overlay.style.display = 'none';
        } else {
            overlay.style.display = 'block';
        }
    }
</script>