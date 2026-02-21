<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') - {{ config('app.name', 'RMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Super smooth transitions */
        .sidebar-transition {
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Page transition */
        .page-enter {
            animation: pageFadeIn 0.3s ease-out;
        }
        
        @keyframes pageFadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Sidebar styling - ALWAYS ICON-ONLY */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 5rem; /* Icon-only width */
            background-color: #111827; /* gray-900 */
            color: white;
            border-right: 1px solid #374151;
            z-index: 40;
            overflow-y: auto;
            overflow-x: hidden;
            transform: translateX(-5rem);
        }
        
        /* Sidebar when open */
        .sidebar-open {
            transform: translateX(0);
        }
        
        /* Sidebar hover effect - expands on hover */
        .sidebar:hover {
            width: 16rem !important;
        }
        
        .sidebar:hover .sidebar-text,
        .sidebar:hover .user-name,
        .sidebar:hover .user-email,
        .sidebar:hover .user-role,
        .sidebar:hover .logo-text {
            opacity: 1;
            max-width: 200px;
        }
        
        .sidebar:hover .logo-full {
            display: flex;
        }
        
        .sidebar:hover .logo-icon {
            display: none;
        }
        
        /* Hide text by default in icon-only mode */
        .sidebar-text,
        .user-name,
        .user-email,
        .user-role,
        .logo-text {
            opacity: 0;
            max-width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.25s ease;
        }
        
        /* Show only icons by default */
        .logo-icon {
            display: flex;
        }
        
        .logo-full {
            display: none;
        }
        
        /* Main content adjustment */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .main-content-sidebar-open {
            margin-left: 5rem;
        }
        
        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 30;
            opacity: 0;
            visibility: hidden;
            transition: all 0.25s ease;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* For mobile, sidebar is full width */
        @media (max-width: 1023px) {
            .sidebar {
                width: 16rem;
                transform: translateX(-16rem);
            }
            
            .sidebar-open {
                transform: translateX(0);
            }
            
            .sidebar:hover {
                width: 16rem !important;
            }
            
            .main-content-sidebar-open {
                margin-left: 0;
            }
            
            .sidebar-text,
            .user-name,
            .user-email,
            .user-role,
            .logo-text {
                opacity: 1;
                max-width: 200px;
            }
            
            .logo-icon {
                display: none;
            }
            
            .logo-full {
                display: flex;
            }
        }
        
        /* Smooth link hover */
        a {
            transition: all 0.2s ease;
        }
        
        /* Loading animation */
        .loading-spinner {
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        /* Nested menu styles */
        .nested-menu {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        
        .nested-menu.open {
            max-height: 500px;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #1f2937;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #6b7280;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{
        sidebarOpen: false,
        isPageLoading: false,
        currentPage: 'dashboard',
        roleMenuOpen: false,
        
        init() {
            // On desktop, sidebar is always visible (icon-only)
            if (window.innerWidth >= 1024) {
                this.sidebarOpen = true;
            }
            
            // Set current page based on route
            this.setCurrentPage();
            
            // Listen for page changes
            window.addEventListener('popstate', () => {
                this.setCurrentPage();
            });
        },
        
        setCurrentPage() {
            const path = window.location.pathname;
            if (path.includes('admin/dashboard')) this.currentPage = 'dashboard';
            else if (path.includes('admin/role-applications')) this.currentPage = 'role-applications';
            else if (path.includes('admin/commissions')) this.currentPage = 'commissions';
            else this.currentPage = 'dashboard';
        },
        
        navigate(url) {
            this.isPageLoading = true;
            
            // Add page transition class to content
            const content = document.querySelector('.page-content');
            if (content) {
                content.classList.remove('page-enter');
                void content.offsetWidth; // Trigger reflow
                content.classList.add('page-enter');
            }
            
            // Navigate after a short delay for smooth transition
            setTimeout(() => {
                window.location.href = url;
            }, 150);
        },
        
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
        }
    }">
        
        <!-- Mobile Overlay -->
        <div x-show="sidebarOpen && window.innerWidth < 1024" 
             @click="sidebarOpen = false"
             class="sidebar-overlay"
             :class="{ 'active': sidebarOpen && window.innerWidth < 1024 }"
             x-cloak>
        </div>

        <!-- Loading Overlay -->
        <div x-show="isPageLoading" 
             class="fixed inset-0 bg-white bg-opacity-80 z-50 flex items-center justify-center"
             x-cloak>
            <div class="flex flex-col items-center">
                <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500 mb-3"></div>
                <p class="text-gray-600">Loading...</p>
            </div>
        </div>

        <!-- Sidebar - ALWAYS ICON-ONLY ON DESKTOP -->
        <aside :class="{ 'sidebar-open': sidebarOpen }"
               class="sidebar sidebar-transition"
               x-cloak>
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                <!-- Icon-only logo -->
                <div class="logo-icon">
                    <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                </div>
                
                <!-- Full logo (shown on hover/expand) -->
                <div class="logo-full items-center">
                    <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                        <i class="fas fa-crown text-white"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-bold logo-text">Admin Panel</h1>
                        <p class="text-xs text-gray-400">Management</p>
                    </div>
                </div>
                
                <!-- Close button for mobile -->
                <button @click="sidebarOpen = false" 
                        class="lg:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Admin Profile -->
            <div class="px-4 py-6 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Storage::url(Auth::user()->avatar_url) }}" 
                                 alt="{{ Auth::user()->name }}"
                                 class="h-10 w-10 rounded-full border-2 border-indigo-500 object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-gray-700 border-2 border-indigo-500 flex items-center justify-center">
                                <span class="text-white font-medium">{{ substr(Auth::user()->name, 0, 2) }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium truncate user-name">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate user-email">{{ Auth::user()->email }}</p>
                        <div class="mt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-indigo-500 text-white truncate user-role">
                                SUPERADMIN
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}" 
                   @click.prevent="navigate('{{ route('admin.dashboard') }}')"
                   :class="currentPage === 'dashboard' ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md sidebar-transition">
                    <i class="fas fa-tachometer-alt text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Dashboard</span>
                </a>

                <!-- Role Applications with Tabs -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" 
                            :class="currentPage === 'role-applications' ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                            class="w-full text-left group flex items-center justify-between px-3 py-3 rounded-md sidebar-transition">
                        <div class="flex items-center">
                            <i class="fas fa-file-alt text-lg w-6 text-center"></i>
                            <span class="ml-3 truncate sidebar-text">Role Applications</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform" :class="{ 'rotate-180': open }"></i>
                    </button>
                    
                    <div x-show="open" 
                         x-collapse
                         class="ml-9 mt-1 space-y-1">
                        <a href="{{ route('admin.role-applications.index', ['tab' => 'owner']) }}" 
                           @click.prevent="navigate('{{ route('admin.role-applications.index', ['tab' => 'owner']) }}')"
                           class="block px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-700 rounded-md">
                            <i class="fas fa-building mr-2 w-4"></i>
                            <span class="sidebar-text">Property Owners</span>
                            @php $ownerCount = \App\Models\RoleApplication::where('role_type', 'OWNER')->where('status', 'PENDING')->count(); @endphp
                            @if($ownerCount > 0)
                                <span class="ml-2 px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $ownerCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.role-applications.index', ['tab' => 'food']) }}" 
                           @click.prevent="navigate('{{ route('admin.role-applications.index', ['tab' => 'food']) }}')"
                           class="block px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-700 rounded-md">
                            <i class="fas fa-utensils mr-2 w-4"></i>
                            <span class="sidebar-text">Food Providers</span>
                            @php $foodCount = \App\Models\RoleApplication::where('role_type', 'FOOD')->where('status', 'PENDING')->count(); @endphp
                            @if($foodCount > 0)
                                <span class="ml-2 px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $foodCount }}</span>
                            @endif
                        </a>
                        <a href="{{ route('admin.role-applications.index', ['tab' => 'laundry']) }}" 
                           @click.prevent="navigate('{{ route('admin.role-applications.index', ['tab' => 'laundry']) }}')"
                           class="block px-3 py-2 text-sm text-gray-400 hover:text-white hover:bg-gray-700 rounded-md">
                            <i class="fas fa-tshirt mr-2 w-4"></i>
                            <span class="sidebar-text">Laundry Providers</span>
                            @php $laundryCount = \App\Models\RoleApplication::where('role_type', 'LAUNDRY')->where('status', 'PENDING')->count(); @endphp
                            @if($laundryCount > 0)
                                <span class="ml-2 px-1.5 py-0.5 text-xs bg-red-500 text-white rounded-full">{{ $laundryCount }}</span>
                            @endif
                        </a>
                    </div>
                </div>

                <!-- Commissions (Admin Only) -->
                <a href="{{ route('admin.commissions.index') }}" 
                   @click.prevent="navigate('{{ route('admin.commissions.index') }}')"
                   :class="currentPage === 'commissions' ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md sidebar-transition">
                    <i class="fas fa-percentage text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Commissions</span>
                </a>

                <!-- Users -->
                <a href="{{ route('admin.users.index') }}" 
                @click.prevent="navigate('{{ route('admin.users.index') }}')"
                :class="currentPage === 'users' ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
                class="group flex items-center px-3 py-3 rounded-md sidebar-transition">
                    <i class="fas fa-users text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Users</span>
                </a>


            </nav>

            <!-- Sidebar Footer -->
            <div class="border-t border-gray-800 mt-auto">
                <form method="POST" action="{{ route('logout') }}" id="logout-form">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-4 py-3 sidebar-transition">
                        <i class="fas fa-sign-out-alt text-lg w-6 text-center"></i>
                        <span class="ml-3 truncate sidebar-text">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="{ 'main-content-sidebar-open': sidebarOpen && window.innerWidth >= 1024 }" 
             class="main-content min-h-screen">
            
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="toggleSidebar()" 
                                class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop menu button -->
                        <button @click="toggleSidebar()" 
                                class="hidden lg:flex items-center text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="ml-4 flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <div>
                                        <a href="{{ route('admin.dashboard') }}" 
                                           @click.prevent="navigate('{{ route('admin.dashboard') }}')"
                                           class="text-gray-400 hover:text-gray-500">
                                            <i class="fas fa-home"></i>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="flex items-center">
                                        <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                        <span class="text-sm font-medium text-gray-500">@yield('title', 'Dashboard')</span>
                                    </div>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="hidden md:block relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   placeholder="Search..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-64">
                        </div>

                        <!-- Notifications -->
                        <button class="relative text-gray-400 hover:text-gray-500 focus:outline-none">
                            <i class="fas fa-bell text-xl"></i>
                            @php $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count(); @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </button>

                        <!-- User Menu -->
                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" 
                                    class="flex items-center space-x-2 focus:outline-none">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Storage::url(Auth::user()->avatar_url) }}" 
                                         alt="{{ Auth::user()->name }}"
                                         class="h-8 w-8 rounded-full">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <span class="text-indigo-800 font-medium text-sm">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="hidden md:block text-sm font-medium text-gray-700 truncate max-w-[120px]">
                                    {{ Auth::user()->name }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div x-show="userMenuOpen" 
                                 @click.away="userMenuOpen = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                                 style="display: none;">
                                <div class="py-1">
                             
                                    <a href="{{ route('admin.dashboard') }}" 
                                       @click.prevent="navigate('{{ route('admin.dashboard') }}')"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content Area -->
            <main class="bg-gray-50 min-h-[calc(100vh-64px)] page-content page-enter">
                <div class="p-6">
                    <!-- Page Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('header', 'Dashboard')</h1>
                        <p class="mt-2 text-gray-600">@yield('subtitle', 'Welcome to the admin dashboard')</p>
                    </div>

                    <!-- Flash Messages -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-check-circle text-green-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Page Content -->
                    @yield('content')
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-600">
                        &copy; {{ date('Y') }} Admin Panel. All rights reserved.
                    </div>
                    <div class="mt-2 md:mt-0">
                        <span class="text-sm text-gray-600">Version 1.0.0</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        // Handle page transitions
        document.addEventListener('DOMContentLoaded', function() {
            // Add page enter animation
            const content = document.querySelector('.page-content');
            if (content) {
                content.classList.add('page-enter');
            }
            
            // Handle all navigation links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && link.href && !link.href.includes('javascript:') && 
                    link.href !== '#' && !link.hasAttribute('target')) {
                    
                    // Check if it's an internal link
                    const isInternal = link.href.includes(window.location.hostname) || 
                                      link.href.startsWith('/');
                    
                    if (isInternal && !link.href.includes('logout')) {
                        e.preventDefault();
                        
                        // Trigger loading state
                        const app = document.querySelector('[x-data]').__x;
                        if (app && app.$data) {
                            app.$data.isPageLoading = true;
                        }
                        
                        // Navigate after short delay for smooth transition
                        setTimeout(() => {
                            window.location.href = link.href;
                        }, 200);
                    }
                }
            });
            
            // Handle form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (this.id.includes('logout')) {
                        const app = document.querySelector('[x-data]').__x;
                        if (app && app.$data) {
                            app.$data.isPageLoading = true;
                        }
                    }
                });
            });
        });
        
        // Remove loading state when page is fully loaded
        window.addEventListener('load', function() {
            const app = document.querySelector('[x-data]').__x;
            if (app && app.$data) {
                setTimeout(() => {
                    app.$data.isPageLoading = false;
                }, 300);
            }
        });
    </script>

    @stack('scripts')
</body>
</html>