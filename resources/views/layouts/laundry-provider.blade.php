<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'Laundry Provider') - {{ config('app.name') }}</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav class="bg-indigo-700 border-b border-indigo-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('laundry-provider.dashboard') }}" class="text-white font-bold text-xl">
                                <i class="fas fa-tshirt mr-2"></i>
                                Laundry Provider
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('laundry-provider.dashboard') }}" 
                               class="{{ request()->routeIs('laundry-provider.dashboard') ? 'text-white border-b-2 border-white' : 'text-indigo-200 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                <i class="fas fa-tachometer-alt mr-2"></i>
                                Dashboard
                            </a>
                            
                            <a href="{{ route('laundry-provider.orders.index') }}" 
                               class="{{ request()->routeIs('laundry-provider.orders.*') ? 'text-white border-b-2 border-white' : 'text-indigo-200 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                <i class="fas fa-shopping-bag mr-2"></i>
                                Orders
                            </a>
                            
                            <a href="{{ route('laundry-provider.reviews.index') }}" 
                               class="{{ request()->routeIs('laundry-provider.reviews.*') ? 'text-white border-b-2 border-white' : 'text-indigo-200 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium">
                                <i class="fas fa-star mr-2"></i>
                                Reviews
                            </a>
                            
                            <a href="{{ route('laundry-provider.notifications') }}" 
                               class="{{ request()->routeIs('laundry-provider.notifications') ? 'text-white border-b-2 border-white' : 'text-indigo-200 hover:text-white' }} inline-flex items-center px-1 pt-1 text-sm font-medium relative">
                                <i class="fas fa-bell mr-2"></i>
                                Notifications
                                @php
                                    // Use DB facade to query your custom notifications table
                                    use Illuminate\Support\Facades\DB;
                                    $unreadCount = DB::table('notifications')
                                        ->where('user_id', Auth::id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" 
                                    class="flex items-center text-sm font-medium text-white hover:text-indigo-200 focus:outline-none">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-indigo-500 flex items-center justify-center border-2 border-white">
                                        <span class="text-sm font-medium text-white">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </span>
                                    </div>
                                    <span class="ml-2">{{ Auth::user()->name }}</span>
                                    <i class="fas fa-chevron-down ml-1 text-xs"></i>
                                </div>
                            </button>

                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                
                                <div class="py-1">
                                    <a href="{{ route('laundry-provider.profile') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-user mr-2"></i> Profile
                                    </a>
                                    
                                    <a href="{{ route('laundry-provider.settings') }}" 
                                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="fas fa-cog mr-2"></i> Settings
                                    </a>
                                    
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Heading -->
        @if(isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $header }}
                    </h2>
                </div>
            </header>
        @endif
        
        <!-- Page Content -->
        <main>
            <div class="py-6">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @stack('scripts')
    
    <script>
    // Auto-refresh notification badge
    function updateNotificationBadge() {
        fetch('/notifications/unread-count')
            .then(response => response.json())
            .then(data => {
                // Find the badge element
                const notificationLink = document.querySelector('a[href*="notifications"]');
                if (notificationLink) {
                    const badge = notificationLink.querySelector('span.bg-red-500');
                    
                    if (data.count > 0) {
                        if (badge) {
                            badge.classList.remove('hidden');
                            badge.textContent = data.count > 9 ? '9+' : data.count;
                        } else {
                            // Create badge if it doesn't exist
                            const newBadge = document.createElement('span');
                            newBadge.className = 'absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center';
                            newBadge.textContent = data.count > 9 ? '9+' : data.count;
                            notificationLink.appendChild(newBadge);
                        }
                    } else {
                        if (badge) {
                            badge.classList.add('hidden');
                        }
                    }
                }
            })
            .catch(error => console.error('Error updating notification badge:', error));
    }

    // Update every 30 seconds
    setInterval(updateNotificationBadge, 30000);
    
    // Initial update when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateNotificationBadge();
    });
    </script>
</body>
</html>