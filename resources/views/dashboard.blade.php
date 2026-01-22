<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>RMS - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

<style>
    [x-cloak] {
        display: none;
    }
    
    /* Fix sidebar layout */
    .sidebar-container {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow-y: auto;
    }
    
    .sidebar-links {
        display: flex;
        flex-direction: column;
        gap: 0.25rem; /* Space between links */
        flex: 1;
    }
    
    .sidebar-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        color: #374151; /* text-gray-700 */
        border-radius: 0.5rem;
        transition: all 0.15s ease-in-out;
        text-decoration: none;
        white-space: nowrap;
    }
    
    .sidebar-link:hover {
        background-color: #f3f4f6; /* hover:bg-gray-100 */
        color: #111827; /* hover:text-gray-900 */
    }
    
    .sidebar-link.active {
        background-color: #e0e7ff; /* bg-indigo-50 */
        color: #4338ca; /* text-indigo-700 */
        font-weight: 500;
    }
    
    .sidebar-link i {
        width: 1.25rem;
        margin-right: 0.75rem;
        text-align: center;
        font-size: 1rem;
    }
    
    .sidebar-section {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb; /* border-gray-200 */
    }
    
    .sidebar-section-title {
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280; /* text-gray-500 */
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
</style>
</head>
<body class="font-sans antialiased bg-gray-50" x-data="{ sidebarOpen: false }">
    <!-- Mobile sidebar backdrop -->
    <div x-show="sidebarOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-40 flex md:hidden" 
         @click="sidebarOpen = false">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
    </div>

    <!-- Sidebar for Mobile -->
    <div class="md:hidden">
        <div x-show="sidebarOpen" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg">
            @include('partials.sidebar')
        </div>
    </div>

    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 fixed w-full z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Left: Logo and Mobile Menu Button -->
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" 
                            class="md:hidden inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        <span class="sr-only">Open sidebar</span>
                        <i class="fas fa-bars h-6 w-6"></i>
                    </button>
                    
                    <div class="flex-shrink-0 ml-4 md:ml-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <i class="fas fa-home text-white"></i>
                            </div>
                            <span class="ml-2 text-xl font-bold text-gray-900">RMS</span>
                        </a>
                    </div>
                    
                    <!-- Global Search (Desktop) -->
                    <div class="hidden md:block ml-8">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="search" 
                                   class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" 
                                   placeholder="Search properties & services...">
                        </div>
                    </div>
                </div>

                <!-- Right: Notification and Profile -->
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <button class="relative p-2 text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i class="fas fa-bell h-6 w-6"></i>
                        <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400"></span>
                    </button>
                    
                    <!-- Profile Dropdown -->
                    <div class="relative ml-3" x-data="{ open: false }" @click.outside="open = false">
                        <button @click="open = !open" 
                                class="flex items-center max-w-xs text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                <i class="fas fa-user text-gray-600"></i>
                            </div>
                            <span class="hidden md:block ml-2 text-sm font-medium text-gray-700">
                                {{ Auth::user()->name }}
                            </span>
                            <i class="hidden md:block ml-1 fas fa-chevron-down text-gray-400"></i>
                        </button>
                        
                        <!-- Dropdown Menu -->
                        <div x-show="open" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
                            <a href="{{ route('profile.edit') }}" 
                               class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i>My Profile
                            </a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content with Sidebar -->
    <div class="flex pt-16">
        <!-- Desktop Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 md:pt-16">
            <div class="flex-1 flex flex-col min-h-0 border-r border-gray-200 bg-white">
                @include('partials.sidebar')
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="md:pl-64 flex flex-col flex-1">
            <main class="flex-1">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                        <!-- Page Title -->
                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">
                            @yield('title', 'Dashboard')
                        </h1>
                        
                        <!-- Page Content -->
                        <div class="bg-white rounded-lg shadow">
                            <div class="px-4 py-5 sm:p-6">
                                @yield('content')
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            
            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200">
                <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 md:px-8">
                    <p class="text-sm text-gray-500 text-center">
                        &copy; {{ date('Y') }} Rent & Service Management System. All rights reserved.
                    </p>
                </div>
            </footer>
        </div>
    </div>

    <!-- Alpine.js -->
    <script src="//unpkg.com/alpinejs" defer></script>
    
    <!-- JavaScript for active link highlighting -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const currentPath = window.location.pathname;
        const sidebarLinks = document.querySelectorAll('.sidebar-link');
        
        sidebarLinks.forEach(link => {
            const linkPath = link.getAttribute('href');
            if (currentPath === linkPath || 
                (currentPath.startsWith(linkPath) && linkPath !== '/dashboard')) {
                link.classList.add('active');
            }
        });
        
        // Mobile search toggle
        const searchToggle = document.getElementById('mobile-search-toggle');
        const mobileSearch = document.getElementById('mobile-search');
        
        if (searchToggle) {
            searchToggle.addEventListener('click', function() {
                mobileSearch.classList.toggle('hidden');
            });
        }
    });
    </script>
</body>
</html>