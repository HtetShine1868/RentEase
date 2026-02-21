<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Food Provider') - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        
        /* Sidebar styling */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 5rem;
            background: linear-gradient(180deg, #0f1f28 0%, #174455 100%);
            color: white;
            border-right: 1px solid #286b7f;
            z-index: 40;
            overflow-y: auto;
            overflow-x: hidden;
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .sidebar:hover {
            width: 16rem !important;
        }
        
        .sidebar:hover .sidebar-text {
            opacity: 1;
            max-width: 200px;
            margin-left: 0.75rem;
        }
        
        .sidebar:hover .logo-full {
            display: flex;
        }
        
        .sidebar:hover .logo-icon {
            display: none;
        }
        
        .sidebar-text {
            opacity: 0;
            max-width: 0;
            overflow: hidden;
            white-space: nowrap;
            transition: all 0.25s ease;
        }
        
        .logo-icon {
            display: flex;
        }
        
        .logo-full {
            display: none;
        }
        
        .main-content {
            margin-left: 5rem;
            transition: margin-left 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            width: calc(100% - 5rem);
        }
        
        @media (max-width: 1023px) {
            .sidebar {
                width: 16rem;
                transform: translateX(-16rem);
            }
            
            .sidebar-open {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                width: 100%;
            }
            
            .sidebar-text {
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
        
        .bg-primary {
            background-color: #174455;
        }
        .text-primary {
            color: #174455;
        }
        .border-primary {
            border-color: #286b7f;
        }
    </style>
    
    @stack('styles')
</head>
<body class="font-sans antialiased bg-gray-100">
    <div x-data="{
        sidebarOpen: window.innerWidth >= 1024,
        isPageLoading: false,
        currentPage: 'food-dashboard',
        
        init() {
            this.setCurrentPage();
            
            window.addEventListener('popstate', () => {
                this.setCurrentPage();
            });
            
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                }
            });
        },
        
        setCurrentPage() {
            const path = window.location.pathname;
            if (path.includes('food-provider/dashboard')) this.currentPage = 'food-dashboard';
            else if (path.includes('food-provider/menu')) this.currentPage = 'food-menu';
            else if (path.includes('food-provider/orders')) this.currentPage = 'food-orders';
            else if (path.includes('food-provider/subscriptions')) this.currentPage = 'food-subscriptions';
            else if (path.includes('food-provider/reviews')) this.currentPage = 'food-reviews';
            else if (path.includes('food-provider/profile')) this.currentPage = 'food-profile';
            else this.currentPage = 'food-dashboard';
        },
        
        navigate(url) {
            this.isPageLoading = true;
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
             class="fixed inset-0 bg-black bg-opacity-50 z-30"
             x-cloak>
        </div>

        <!-- Loading Overlay -->
        <div x-show="isPageLoading" 
             class="fixed inset-0 bg-white bg-opacity-80 z-50 flex items-center justify-center"
             x-cloak>
            <div class="flex flex-col items-center">
                <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-[#174455] mb-3"></div>
                <p class="text-gray-600">Loading...</p>
            </div>
        </div>

        <!-- Sidebar -->
        <aside :class="{ 'sidebar-open': sidebarOpen }"
               class="sidebar"
               x-cloak>
            
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-[#286b7f]">
                <div class="logo-icon">
                    <div class="h-8 w-8 rounded-lg bg-[#ffdb9f] flex items-center justify-center">
                        <i class="fas fa-utensils text-[#174455]"></i>
                    </div>
                </div>
                
                <div class="logo-full items-center">
                    <div class="h-8 w-8 rounded-lg bg-[#ffdb9f] flex items-center justify-center">
                        <i class="fas fa-utensils text-[#174455]"></i>
                    </div>
                    <div class="ml-3">
                        <h1 class="text-lg font-bold logo-text">Food Provider</h1>
                        <p class="text-xs text-[#ffdb9f]">Dashboard</p>
                    </div>
                </div>
                
                <button @click="sidebarOpen = false" 
                        class="lg:hidden text-[#ffdb9f] hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Provider Info -->
            <div class="px-4 py-6 border-b border-[#286b7f]">
                @php
                    $provider = App\Models\ServiceProvider::where('user_id', auth()->id())
                        ->where('service_type', 'FOOD')
                        ->first();
                @endphp
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if(auth()->user()->avatar_url)
                            <img src="{{ Storage::url(auth()->user()->avatar_url) }}" 
                                 alt="{{ auth()->user()->name }}"
                                 class="h-10 w-10 rounded-full border-2 border-[#ffdb9f] object-cover">
                        @else
                            <div class="h-10 w-10 rounded-full bg-[#286b7f] border-2 border-[#ffdb9f] flex items-center justify-center">
                                <i class="fas fa-user text-[#ffdb9f]"></i>
                            </div>
                        @endif
                    </div>
                    <div class="ml-3 overflow-hidden">
                        <p class="text-sm font-medium truncate user-name text-white">{{ $provider->business_name ?? auth()->user()->name }}</p>
                        <p class="text-xs text-[#ffdb9f] truncate user-email">{{ auth()->user()->email }}</p>
                        <div class="mt-1">
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium bg-[#ffdb9f] text-[#174455] truncate user-role">
                                FOOD PROVIDER
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-3 py-4 space-y-1">
                <a href="{{ route('food-provider.dashboard') }}" 
                   @click.prevent="navigate('{{ route('food-provider.dashboard') }}')"
                   :class="currentPage === 'food-dashboard' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-tachometer-alt text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Dashboard</span>
                </a>

                <a href="{{ route('food-provider.menu.index') }}" 
                   @click.prevent="navigate('{{ route('food-provider.menu.index') }}')"
                   :class="currentPage === 'food-menu' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-utensils text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Menu</span>
                </a>

                <a href="{{ route('food-provider.orders.index') }}" 
                   @click.prevent="navigate('{{ route('food-provider.orders.index') }}')"
                   :class="currentPage === 'food-orders' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-shopping-bag text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Orders</span>
                </a>

                <a href="{{ route('food-provider.subscriptions.index') }}" 
                   @click.prevent="navigate('{{ route('food-provider.subscriptions.index') }}')"
                   :class="currentPage === 'food-subscriptions' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-calendar-alt text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Subscriptions</span>
                </a>

                <a href="{{ route('food-provider.reviews.index') }}" 
                   @click.prevent="navigate('{{ route('food-provider.reviews.index') }}')"
                   :class="currentPage === 'food-reviews' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-star text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Reviews</span>
                </a>

                <a href="{{ route('food-provider.profile.index') }}" 
                   @click.prevent="navigate('{{ route('food-provider.profile.index') }}')"
                   :class="currentPage === 'food-profile' ? 'bg-[#286b7f] text-white' : 'text-gray-300 hover:bg-[#1f556b] hover:text-white'"
                   class="group flex items-center px-3 py-3 rounded-md transition-colors">
                    <i class="fas fa-user text-lg w-6 text-center"></i>
                    <span class="ml-3 truncate sidebar-text">Profile</span>
                </a>
            </nav>

            <!-- Sidebar Footer -->
            <div class="border-t border-[#286b7f] mt-auto">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full text-left text-gray-300 hover:bg-[#1f556b] hover:text-white group flex items-center px-4 py-3 transition-colors">
                        <i class="fas fa-sign-out-alt text-lg w-6 text-center"></i>
                        <span class="ml-3 truncate sidebar-text">Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            
            <!-- Top Navigation -->
            <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
                <div class="flex items-center justify-between px-4 py-3">
                    <div class="flex items-center">
                        <button @click="toggleSidebar()" 
                                class="lg:hidden text-gray-500 hover:text-[#174455] focus:outline-none mr-4">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        
                        <nav class="flex" aria-label="Breadcrumb">
                            <ol class="flex items-center space-x-2">
                                <li>
                                    <a href="{{ route('food-provider.dashboard') }}" 
                                       @click.prevent="navigate('{{ route('food-provider.dashboard') }}')"
                                       class="text-gray-400 hover:text-[#174455]">
                                        <i class="fas fa-home"></i>
                                    </a>
                                </li>
                                <li>
                                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                                    <span class="text-sm font-medium text-gray-500">@yield('header', 'Dashboard')</span>
                                </li>
                            </ol>
                        </nav>
                    </div>

                    <div class="flex items-center space-x-4">
                        <div class="hidden md:block text-gray-500">
                            <i class="far fa-calendar mr-2"></i>
                            {{ now()->format('l, F j, Y') }}
                        </div>

                        <div class="relative" x-data="{ userMenuOpen: false }">
                            <button @click="userMenuOpen = !userMenuOpen" 
                                    class="flex items-center space-x-2 focus:outline-none">
                                @if(auth()->user()->avatar_url)
                                    <img src="{{ Storage::url(auth()->user()->avatar_url) }}" 
                                         alt="{{ auth()->user()->name }}"
                                         class="h-8 w-8 rounded-full border-2 border-[#174455] object-cover">
                                @else
                                    <div class="h-8 w-8 rounded-full bg-[#174455] flex items-center justify-center">
                                        <span class="text-white text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <span class="hidden md:block text-sm font-medium text-gray-700">
                                    {{ $provider->business_name ?? auth()->user()->name }}
                                </span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </button>
                            
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
                                    <a href="{{ route('food-provider.profile.index') }}" 
                                       @click.prevent="navigate('{{ route('food-provider.profile.index') }}')"
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    <div class="border-t border-gray-100 my-1"></div>
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

            <!-- Page Content -->
            <div class="p-6">
                <!-- Page Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-[#174455]">@yield('title', 'Food Provider Dashboard')</h1>
                    <p class="mt-2 text-gray-600">@yield('subtitle', 'Manage your food business efficiently')</p>
                </div>

                <!-- Flash Messages -->
                @if(session('success'))
                    <div class="mb-6 bg-green-50 border-l-4 border-[#174455] p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-[#174455]"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-[#174455]">{{ session('success') }}</p>
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

                <!-- Main Content -->
                @yield('content')
            </div>
        </div>
    </div>

    <style>
        .loading-spinner {
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

    @stack('scripts')
</body>
</html>