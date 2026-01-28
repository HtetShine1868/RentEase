<!DOCTYPE html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Food Provider Dashboard - RMS')</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js for interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="h-full" x-data="{ sidebarOpen: false }">
    <!-- Sidebar for mobile -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-40 flex md:hidden" x-transition>
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75" @click="sidebarOpen = false"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
            @include('components.food-provider.sidebar')
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
        @include('components.food-provider.sidebar')
    </div>

    <!-- Main content -->
    <div class="md:pl-64 flex flex-col flex-1">
        @include('components.food-provider.header')
        
        <main class="flex-1">
            <div class="py-6">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
                    @if(isset($header))
                        <h1 class="text-2xl font-semibold text-gray-900 mb-6">{{ $header }}</h1>
                    @endif
                    
                    @yield('content')
                </div>
            </div>
        </main>
        
        @include('components.food-provider.footer')
    </div>

    @stack('scripts')
</body>
</html>