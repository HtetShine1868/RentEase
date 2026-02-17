<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'RMS'))</title>

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
        
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 16rem; /* 256px */
            transform: translateX(-100%);
            transition: transform 0.3s ease-in-out;
            z-index: 40;
            background-color: #111827; /* gray-900 */
            color: white;
            border-right: 1px solid #374151; /* gray-700 */
            overflow-y: auto;
        }
        
        .sidebar-open {
            transform: translateX(0);
        }
        
        /* Large screen behavior */
        @media (min-width: 1024px) {
            .sidebar {
                transform: translateX(0);
                position: fixed;
            }
            
            .main-content {
                margin-left: 16rem;
                width: calc(100% - 16rem);
            }
            
            .toggle-sidebar-btn {
                display: none !important;
            }
        }
        
        /* Mobile overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 30;
            display: none;
        }
        
        .sidebar-overlay.active {
            display: block;
        }
        
        /* Prevent body scroll when sidebar is open on mobile */
        body.sidebar-active {
            overflow: hidden;
        }
    </style>
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{
        sidebarOpen: false,
        mobileMenuOpen: false,
        init() {
            // Check localStorage for sidebar state on large screens
            if (window.innerWidth >= 1024) {
                const savedState = localStorage.getItem('sidebarOpen');
                this.sidebarOpen = savedState ? savedState === 'true' : true;
            } else {
                this.sidebarOpen = false;
            }
            
            // Update on resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                } else {
                    this.sidebarOpen = false;
                }
            });
        },
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            if (window.innerWidth >= 1024) {
                localStorage.setItem('sidebarOpen', this.sidebarOpen);
            }
        },
        closeMobileMenu() {
            this.mobileMenuOpen = false;
        }
    }" 
    :class="{ 'sidebar-active': mobileMenuOpen }">
        
        <!-- Mobile Overlay -->
        <div x-show="mobileMenuOpen" 
             @click="mobileMenuOpen = false"
             class="sidebar-overlay lg:hidden"
             x-cloak>
        </div>

        <!-- Sidebar -->
        <aside :class="{ 'sidebar-open': sidebarOpen || mobileMenuOpen }"
               class="sidebar"
               x-cloak>
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="h-8 w-8 rounded-lg bg-indigo-600 flex items-center justify-center">
                            <i class="fas fa-home text-white"></i>
                        </div>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-bold truncate">RMS System</h1>
                        <p class="text-xs text-gray-400">Dashboard</p>
                    </div>
                </div>
                <button @click="mobileMenuOpen = false; sidebarOpen = false" 
                        class="lg:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- User Profile -->
            <div class="px-4 py-6 border-b border-gray-800">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if(Auth::user()->avatar_url)
                            <img src="{{ Storage::url(Auth::user()->avatar_url) }}" 
                                 alt="{{ Auth::user()->name }}"
                                 class="h-12 w-12 rounded-full border-2 border-indigo-500 object-cover">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gray-700 border-2 border-indigo-500 flex items-center justify-center">
                                <i class="fas fa-user text-gray-300"></i>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                        <div class="mt-1">
                            @foreach(Auth::user()->roles as $role)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-500 text-white truncate">
                                    {{ ucfirst(strtolower($role->name)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-2 py-4 space-y-1" style="max-height: calc(100vh - 260px); overflow-y: auto;">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                class="{{ request()->routeIs('dashboard') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-tachometer-alt mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">Dashboard</span>
                </a>

                <!-- Profile -->
                <a href="{{ route('profile.show') }}" 
                class="{{ request()->routeIs('profile.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-user mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">My Profile</span>
                </a>

                <!-- Properties (for Owners) -->
                @if(auth()->user()->isOwner())
                    <a href="{{ route('owner.properties.index') }}" 
                    class="{{ request()->routeIs('owner.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                    @click="mobileMenuOpen = false">
                        <i class="fas fa-home mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                        <span class="truncate">My Properties</span>
                    </a>
                @endif

                <!-- Food Orders (for Food Providers) -->
                @if(auth()->user()->isFoodProvider())
                    <a href="{{ route('food.orders') }}" 
                    class="{{ request()->routeIs('food.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                    @click="mobileMenuOpen = false">
                        <i class="fas fa-utensils mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                        <span class="truncate">Food Orders</span>
                    </a>
                @endif

                <!-- Laundry Orders (for Laundry Providers) -->
                @if(auth()->user()->isLaundryProvider())
                    <a href="{{ route('laundry.orders') }}" 
                    class="{{ request()->routeIs('laundry.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                    @click="mobileMenuOpen = false">
                        <i class="fas fa-tshirt mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                        <span class="truncate">Laundry Orders</span>
                    </a>
                @endif

                <!-- Find Properties -->
                <a href="{{ route('rental.index') }}" 
                class="{{ request()->routeIs('rental.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-search mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">Find Properties</span>
                </a>

                <!-- Food Services -->
                <a href="{{ route('food.index') }}" 
                class="{{ request()->routeIs('food.index') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-utensils mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">Food Services</span>
                </a>

                <!-- Laundry Services -->
                <a href="{{ route('laundry.index') }}" 
                class="{{ request()->routeIs('laundry.index') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-tshirt mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">Laundry Services</span>
                </a>

                <!-- My Bookings -->
                <a href="{{ route('rental.index') }}" 
                class="text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                @click="mobileMenuOpen = false">
                    <i class="fas fa-calendar-alt mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                    <span class="truncate">My Bookings</span>
                </a>

           

                <!-- Role Application (for regular users) -->
                @if(auth()->user()->hasRole('USER') && !auth()->user()->isOwner() && !auth()->user()->isFoodProvider() && !auth()->user()->isLaundryProvider())
                    <a href="{{ route('role.apply.index') }}" 
                    class="{{ request()->routeIs('role.apply.*') ? 'bg-gray-800 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md"
                    @click="mobileMenuOpen = false">
                        <i class="fas fa-user-plus mr-3 text-gray-400 group-hover:text-gray-300 flex-shrink-0"></i>
                        <span class="truncate">Apply for Role</span>
                    </a>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="absolute bottom-0 left-0 right-0 border-t border-gray-800 bg-gray-900">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left text-gray-300 hover:bg-gray-700 hover:text-white group flex items-center px-4 py-3 text-sm font-medium"
                            @click="mobileMenuOpen = false">
                        <i class="fas fa-sign-out-alt mr-3 text-gray-400 group-hover:text-gray-300"></i>
                        <span class="truncate">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div :class="{ 'main-content': sidebarOpen && window.innerWidth >= 1024 }" 
             class="min-h-screen transition-all duration-300">
            
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-20">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center">
                        <!-- Mobile menu button -->
                        <button @click="mobileMenuOpen = true" 
                                class="lg:hidden text-gray-500 hover:text-gray-700 focus:outline-none">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Desktop sidebar toggle -->
                        <button @click="toggleSidebar()" 
                                class="hidden lg:block text-gray-500 hover:text-gray-700 focus:outline-none ml-2 toggle-sidebar-btn">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <!-- Breadcrumb -->
                        <nav class="ml-4 flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <div>
                                        <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                            <i class="fas fa-home"></i>
                                        </a>
                                    </div>
                                </li>
                                @if(isset($breadcrumbs))
                                    @foreach($breadcrumbs as $crumb)
                                        <li>
                                            <div class="flex items-center">
                                                <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                                @if($loop->last)
                                                    <span class="text-sm font-medium text-gray-500">{{ $crumb['title'] }}</span>
                                                @else
                                                    <a href="{{ $crumb['url'] }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">
                                                        {{ $crumb['title'] }}
                                                    </a>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                @endif
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

                        <!-- User Menu -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" 
                                    class="flex items-center space-x-2 focus:outline-none">
                                @if(Auth::user()->avatar_url)
                                    <img src="{{ Storage::url(Auth::user()->avatar_url) }}" 
                                         alt="{{ Auth::user()->name }}"
                                         class="h-8 w-8 rounded-full">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-gray-200 flex items-center justify-center">
                                        <i class="fas fa-user text-gray-600"></i>
                                    </div>
                                @endif
                                <span class="hidden md:block text-sm font-medium text-gray-700 truncate max-w-[120px]">
                                    {{ Auth::user()->name }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400"></i>
                            </button>
                            
                            <!-- User Dropdown -->
                            <div x-show="open" 
                                 @click.away="open = false"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="transform opacity-0 scale-95"
                                 x-transition:enter-end="transform opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="transform opacity-100 scale-100"
                                 x-transition:leave-end="transform opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 z-50"
                                 style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('profile.show') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
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
            <main class="bg-gray-50 min-h-[calc(100vh-64px)]">
                <div class="p-6">
                    <!-- Page Header -->
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">@yield('title', 'Dashboard')</h1>
                        <p class="mt-2 text-gray-600">@yield('subtitle', 'Welcome to your dashboard')</p>
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
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        @yield('content')
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 py-4 px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-sm text-gray-600">
                        &copy; {{ date('Y') }} RMS System. All rights reserved.
                    </div>
                    <div class="mt-2 md:mt-0">
                        <span class="text-sm text-gray-600">Version 1.0.0</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-close mobile menu on page navigation
            const navLinks = document.querySelectorAll('aside a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        const app = document.querySelector('[x-data]').__x;
                        if (app && app.$data) {
                            app.$data.mobileMenuOpen = false;
                        }
                    }
                });
            });

            // Initialize sidebar state
            const app = document.querySelector('[x-data]').__x;
            if (app && app.$data) {
                // On large screens, sidebar is open by default
                if (window.innerWidth >= 1024) {
                    app.$data.sidebarOpen = true;
                    localStorage.setItem('sidebarOpen', 'true');
                }
                
                // Listen for page changes
                window.addEventListener('popstate', function() {
                    if (window.innerWidth < 1024) {
                        app.$data.mobileMenuOpen = false;
                    }
                });
            }
        });
    </script>
</body>
</html>