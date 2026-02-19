<aside id="owner-sidebar" class="bg-gradient-to-b from-gray-900 to-gray-800 text-white w-64 md:w-72 flex-shrink-0 transform -translate-x-full md:translate-x-0 transition-all duration-300 z-40 fixed md:relative h-screen overflow-y-auto shadow-xl">
    <!-- Sidebar Header with Logo and Close Button -->
    <div class="p-6 border-b border-gray-700 bg-gray-900 relative">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3 group flex-1">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-r from-blue-500 to-blue-600 flex items-center justify-center shadow-lg group-hover:from-blue-600 group-hover:to-blue-700 transition-all duration-300">
                    <i class="fas fa-home text-white text-xl"></i>
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold bg-gradient-to-r from-blue-400 to-blue-300 bg-clip-text text-transparent">RentEase</h2>
                    <p class="text-gray-400 text-sm flex items-center">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                        Owner Dashboard
                    </p>
                </div>
            </div>
            
            <!-- Close button for desktop -->
            <button id="sidebar-close-desktop" class="hidden md:flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all duration-200 ml-2" title="Collapse sidebar">
                <i class="fas fa-chevron-left text-sm"></i>
            </button>
            
            <!-- Close button for mobile -->
            <button id="sidebar-close-mobile" class="md:hidden flex items-center justify-center w-8 h-8 rounded-lg bg-gray-800 hover:bg-gray-700 text-gray-400 hover:text-white transition-all duration-200 ml-2" title="Close sidebar">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>
    
    <!-- User Profile Quick Info -->
    <div class="px-6 py-4 border-b border-gray-700 bg-gray-850">
        <div class="flex items-center space-x-3">
            <div class="relative">
                <div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-pink-500 flex items-center justify-center overflow-hidden shadow-md">
                    <i class="fas fa-user text-white"></i>
                </div>
                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-gray-900"></div>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-medium truncate">{{ Auth::user()->name ?? 'Owner' }}</p>
                <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email ?? 'owner@rentease.com' }}</p>
            </div>
        </div>
    </div>
    
    <!-- Navigation Menu -->
    <div class="p-4 sidebar-menu">
        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">MAIN NAVIGATION</h3>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('owner.dashboard') }}" 
                       class="flex items-center p-3 rounded-xl hover:bg-gray-800 transition-all duration-200 group {{ request()->routeIs('owner.dashboard') ? 'bg-gray-800 shadow-md border-l-4 border-blue-500' : '' }} hover:shadow-md hover:-translate-y-0.5">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-r from-blue-500/20 to-blue-600/20 flex items-center justify-center mr-3 group-hover:from-blue-500/30 group-hover:to-blue-600/30 transition-all">
                            <i class="fas fa-tachometer-alt text-blue-400 group-hover:text-blue-300 {{ request()->routeIs('owner.dashboard') ? 'text-blue-300' : '' }}"></i>
                        </div>
                        <span class="font-medium">Dashboard</span>
                        <div class="ml-auto">
                            <div class="w-2 h-2 rounded-full bg-blue-500 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('owner.properties.index') }}" 
                       class="flex items-center p-3 rounded-xl hover:bg-gray-800 transition-all duration-200 group {{ request()->routeIs('owner.properties.*') ? 'bg-gray-800 shadow-md border-l-4 border-green-500' : '' }} hover:shadow-md hover:-translate-y-0.5">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-r from-green-500/20 to-emerald-600/20 flex items-center justify-center mr-3 group-hover:from-green-500/30 group-hover:to-emerald-600/30 transition-all">
                            <i class="fas fa-building text-green-400 group-hover:text-green-300 {{ request()->routeIs('owner.properties.*') ? 'text-green-300' : '' }}"></i>
                        </div>
                        <span class="font-medium">My Properties</span>
                        <span class="ml-auto bg-green-500/20 text-green-400 text-xs px-2 py-1 rounded-lg font-medium">
                            {{ $propertiesCount ?? '8' }}
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('owner.bookings.index') }}" 
                       class="flex items-center p-3 rounded-xl hover:bg-gray-800 transition-all duration-200 group {{ request()->routeIs('owner.bookings.*') ? 'bg-gray-800 shadow-md border-l-4 border-yellow-500' : '' }} hover:shadow-md hover:-translate-y-0.5">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-r from-yellow-500/20 to-amber-600/20 flex items-center justify-center mr-3 group-hover:from-yellow-500/30 group-hover:to-amber-600/30 transition-all">
                            <i class="fas fa-calendar-check text-yellow-400 group-hover:text-yellow-300 {{ request()->routeIs('owner.bookings.*') ? 'text-yellow-300' : '' }}"></i>
                        </div>
                        <span class="font-medium">Bookings</span>
                        <span class="ml-auto flex items-center">
                            <span class="bg-yellow-500 text-white text-xs px-2 py-1 rounded-lg font-medium mr-2 shadow-sm">
                                {{ $pendingBookings ?? '12' }}
                            </span>
                            <i class="fas fa-chevron-right text-xs text-gray-500 opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('owner.complaints.index') }}" 
                       class="flex items-center p-3 rounded-xl hover:bg-gray-800 transition-all duration-200 group {{ request()->routeIs('owner.complaints.*') ? 'bg-gray-800 shadow-md border-l-4 border-red-500' : '' }} hover:shadow-md hover:-translate-y-0.5">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-r from-red-500/20 to-rose-600/20 flex items-center justify-center mr-3 group-hover:from-red-500/30 group-hover:to-rose-600/30 transition-all">
                            <i class="fas fa-exclamation-circle text-red-400 group-hover:text-red-300 {{ request()->routeIs('owner.complaints.*') ? 'text-red-300' : '' }}"></i>
                        </div>
                        <span class="font-medium">Complaints</span>
                        <span class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-lg font-medium shadow-sm animate-pulse">
                            {{ $pendingComplaints ?? '3' }}
                        </span>
                    </a>
                </li>
            

            </ul>
        </div>
        
        <!-- Quick Actions -->
        <div class="mb-6">
            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3 px-3">QUICK ACTIONS</h3>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="window.location.href='{{ route('owner.properties.create') }}'" 
                        class="p-3 bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 group">
                    <i class="fas fa-plus text-white text-sm mb-1 block"></i>
                    <span class="text-xs font-medium">Add Property</span>
                </button>
                <button onclick="window.location.href='{{ route('owner.bookings.index') }}'" 
                        class="p-3 bg-gradient-to-r from-green-600 to-emerald-700 rounded-lg hover:from-green-700 hover:to-emerald-800 transition-all duration-200 hover:shadow-lg hover:-translate-y-0.5 group">
                    <i class="fas fa-calendar-plus text-white text-sm mb-1 block"></i>
                    <span class="text-xs font-medium">New Booking</span>
                </button>
            </div>
        </div>
        
        <!-- Logout Button -->
        <div class="mt-auto">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
               class="flex items-center justify-center p-3 rounded-xl bg-gradient-to-r from-gray-800 to-gray-850 hover:from-red-900/30 hover:to-red-800/30 border border-gray-700 hover:border-red-700 transition-all duration-200 group">
                <div class="w-9 h-9 rounded-lg bg-red-500/10 flex items-center justify-center mr-3 group-hover:bg-red-500/20">
                    <i class="fas fa-sign-out-alt text-red-400 group-hover:text-red-300"></i>
                </div>
                <span class="font-medium text-gray-300 group-hover:text-red-300">Logout</span>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            </a>
        </div>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-30 md:hidden transition-opacity duration-300" onclick="toggleSidebar()" style="display: none;"></div>

<!-- Hamburger Button for Mobile - FIXED POSITION -->
<button id="hamburger-button" 
        class="md:hidden fixed top-4 left-4 z-50 w-10 h-10 rounded-lg bg-gray-900 text-white flex items-center justify-center shadow-lg hover:bg-gray-800 transition-all duration-200"
        aria-label="Toggle sidebar"
        style="display: none;">
    <i class="fas fa-bars text-lg"></i>
</button>

<!-- Expand/Collapse Button for Desktop (when sidebar is collapsed) -->
<button id="expand-sidebar-desktop" 
        class="hidden fixed top-4 left-4 z-50 w-10 h-10 rounded-lg bg-gray-900 text-white flex items-center justify-center shadow-lg hover:bg-gray-800 transition-all duration-200"
        aria-label="Expand sidebar"
        style="display: none;">
    <i class="fas fa-bars text-lg"></i>
</button>

<style>
    #owner-sidebar {
        scrollbar-width: thin;
        scrollbar-color: #4b5563 #1f2937;
    }
    
    #owner-sidebar::-webkit-scrollbar {
        width: 6px;
    }
    
    #owner-sidebar::-webkit-scrollbar-track {
        background: #1f2937;
        border-radius: 3px;
    }
    
    #owner-sidebar::-webkit-scrollbar-thumb {
        background: linear-gradient(to bottom, #3b82f6, #8b5cf6);
        border-radius: 3px;
    }
    
    #owner-sidebar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(to bottom, #2563eb, #7c3aed);
    }
    
    .sidebar-menu li a {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .sidebar-menu li a:hover {
        transform: translateY(-2px);
    }
    
    /* Glow effect for active items */
    .sidebar-menu li a.bg-gray-800 {
        box-shadow: 0 4px 20px -2px rgba(59, 130, 246, 0.2);
    }
    
    /* Collapsed sidebar state */
    #owner-sidebar.collapsed {
        width: 0;
        padding: 0;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    #owner-sidebar.collapsed .sidebar-menu,
    #owner-sidebar.collapsed .border-b {
        display: none;
    }
    
    #owner-sidebar.collapsed .p-6 {
        padding: 1rem;
    }
    
    #owner-sidebar.collapsed .w-12 {
        width: 2.5rem;
        height: 2.5rem;
    }
    
    /* FIX: Prevent hamburger button overlapping with sidebar */
    #hamburger-button, #expand-sidebar-desktop {
        z-index: 1000; /* Very high z-index */
    }
    
    /* When sidebar is open, move hamburger button to the right side */
    #owner-sidebar:not(.-translate-x-full) ~ #hamburger-button {
        left: calc(16rem + 1rem) !important; /* Move to right of sidebar */
        transition: left 0.3s ease;
    }
    
    /* FIX: Ensure main content doesn't overlap with hamburger */
    @media (max-width: 767px) {
        /* Add padding to main content to avoid hamburger overlap */
        main, .main-content {
            padding-top: 4rem !important;
        }
        
        /* Adjust header if you have one */
        header {
            padding-left: 4rem !important;
        }
    }
    
    /* Desktop collapsed state adjustments */
    @media (min-width: 768px) {
        #owner-sidebar.collapsed ~ main,
        #owner-sidebar.collapsed ~ .main-content {
            margin-left: 0;
        }
        
        #owner-sidebar:not(.collapsed) ~ main,
        #owner-sidebar:not(.collapsed) ~ .main-content {
            margin-left: 0;
        }
    }
</style>

<script>
    // Store sidebar state
    let sidebarState = {
        isMobile: window.innerWidth < 768,
        isOpen: false,
        isCollapsed: false
    };
    
    // Enhanced mobile sidebar toggle with animation
    function toggleSidebar() {
        const sidebar = document.getElementById('owner-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const hamburger = document.getElementById('hamburger-button');
        
        if (sidebar.classList.contains('-translate-x-full')) {
            // Opening sidebar
            sidebar.classList.remove('-translate-x-full');
            sidebar.classList.add('translate-x-0', 'shadow-2xl');
            overlay.style.display = 'block';
            setTimeout(() => overlay.style.opacity = '1', 10);
            
            // Update state
            sidebarState.isOpen = true;
            
            // Move hamburger to right side of sidebar
            hamburger.style.left = 'calc(16rem + 1rem)';
            
        } else {
            // Closing sidebar
            sidebar.classList.add('-translate-x-full');
            sidebar.classList.remove('translate-x-0', 'shadow-2xl');
            overlay.style.opacity = '0';
            
            // Update state
            sidebarState.isOpen = false;
            
            setTimeout(() => {
                overlay.style.display = 'none';
                // Move hamburger back to left
                hamburger.style.left = '1rem';
            }, 300);
        }
    }
    
    // Collapse/Expand sidebar on desktop
    function toggleDesktopSidebar() {
        const sidebar = document.getElementById('owner-sidebar');
        const closeBtn = document.getElementById('sidebar-close-desktop');
        const expandBtn = document.getElementById('expand-sidebar-desktop');
        
        if (sidebar.classList.contains('collapsed')) {
            // Expand sidebar
            sidebar.classList.remove('collapsed', 'w-0');
            sidebar.classList.add('w-64', 'md:w-72');
            closeBtn.innerHTML = '<i class="fas fa-chevron-left text-sm"></i>';
            expandBtn.style.display = 'none';
            sidebarState.isCollapsed = false;
        } else {
            // Collapse sidebar
            sidebar.classList.add('collapsed', 'w-0');
            sidebar.classList.remove('w-64', 'md:w-72');
            closeBtn.innerHTML = '<i class="fas fa-chevron-right text-sm"></i>';
            expandBtn.style.display = 'flex';
            sidebarState.isCollapsed = true;
        }
    }
    
    // Initialize everything when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('owner-sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const hamburger = document.getElementById('hamburger-button');
        const closeMobile = document.getElementById('sidebar-close-mobile');
        const closeDesktop = document.getElementById('sidebar-close-desktop');
        const expandDesktop = document.getElementById('expand-sidebar-desktop');
        
        // Show hamburger button on mobile
        if (window.innerWidth < 768) {
            hamburger.style.display = 'flex';
            sidebarState.isMobile = true;
        } else {
            hamburger.style.display = 'none';
            sidebarState.isMobile = false;
        }
        
        // Hamburger button click (mobile)
        hamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            toggleSidebar();
        });
        
        // Mobile close button
        closeMobile.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            toggleSidebar();
        });
        
        // Desktop close button
        closeDesktop.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            toggleDesktopSidebar();
        });
        
        // Desktop expand button
        expandDesktop.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            toggleDesktopSidebar();
        });
        
        // Close sidebar when clicking overlay
        overlay.addEventListener('click', function(e) {
            e.stopPropagation();
            if (window.innerWidth < 768 && sidebarState.isOpen) {
                toggleSidebar();
            }
        });
        
        // Add keyboard shortcut (Ctrl + B) to toggle sidebar
        document.addEventListener('keydown', function(event) {
            if ((event.ctrlKey || event.metaKey) && event.key === 'b') {
                event.preventDefault();
                if (window.innerWidth < 768) {
                    toggleSidebar();
                } else {
                    toggleDesktopSidebar();
                }
            }
            
            // Escape key to close sidebar
            if (event.key === 'Escape') {
                if (window.innerWidth < 768 && sidebarState.isOpen) {
                    toggleSidebar();
                }
            }
        });
        
        // Auto-hide sidebar on mobile when clicking a link (except logout)
        document.querySelectorAll('#owner-sidebar a:not([href="#"])').forEach(link => {
            link.addEventListener('click', function(e) {
                if (window.innerWidth < 768 && sidebarState.isOpen) {
                    // Small delay to allow navigation
                    setTimeout(() => {
                        toggleSidebar();
                    }, 100);
                }
            });
        });
        
        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                const isMobileNow = window.innerWidth < 768;
                
                if (isMobileNow && !sidebarState.isMobile) {
                    // Switched to mobile
                    hamburger.style.display = 'flex';
                    sidebarState.isMobile = true;
                    
                    // Ensure sidebar is closed on mobile
                    if (!sidebar.classList.contains('-translate-x-full')) {
                        sidebar.classList.add('-translate-x-full');
                        sidebar.classList.remove('translate-x-0');
                        overlay.style.display = 'none';
                        hamburger.style.left = '1rem';
                        sidebarState.isOpen = false;
                    }
                    
                } else if (!isMobileNow && sidebarState.isMobile) {
                    // Switched to desktop
                    hamburger.style.display = 'none';
                    sidebarState.isMobile = false;
                    
                    // Ensure sidebar is visible on desktop
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('translate-x-0');
                    overlay.style.display = 'none';
                    
                    // Handle collapsed state
                    if (sidebarState.isCollapsed) {
                        expandDesktop.style.display = 'flex';
                    }
                }
            }, 100);
        });
        
        // Add ripple effect to buttons
        function addRippleEffect(element, e) {
            const ripple = document.createElement('span');
            const rect = element.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                transform: scale(0);
                animation: ripple-animation 0.6s linear;
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
            `;
            
            element.style.position = 'relative';
            element.style.overflow = 'hidden';
            element.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        }
        
        // Add ripple to all interactive elements
        document.querySelectorAll('#owner-sidebar button, #owner-sidebar a, #hamburger-button, #expand-sidebar-desktop').forEach(element => {
            element.addEventListener('click', function(e) {
                if (this.id === 'logout-form' || this.getAttribute('href') === '#') {
                    return; // Skip for logout form
                }
                addRippleEffect(this, e);
            });
        });
        
        // Force initial position check
        setTimeout(() => {
            if (window.innerWidth < 768 && hamburger.style.display === 'flex') {
                hamburger.style.left = '1rem';
            }
        }, 100);
    });
</script>

<style>
    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
    
    /* Smooth transitions */
    #hamburger-button, #expand-sidebar-desktop {
        transition: left 0.3s ease, transform 0.2s ease, opacity 0.2s ease;
    }
    
    #hamburger-button:hover, #expand-sidebar-desktop:hover {
        transform: scale(1.05);
    }
    
    /* Fix for any content that might overlap */
    .main-content-container {
        position: relative;
        z-index: 1;
    }
</style>