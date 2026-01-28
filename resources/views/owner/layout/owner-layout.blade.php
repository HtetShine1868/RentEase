<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Owner Dashboard - Rent Ease')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700" rel="stylesheet" />
    
    <!-- Styles -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
     <link rel="stylesheet" href="{{ asset('css/owner.css') }}">

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-dark: #2563eb;
            --secondary-color: #10b981;
            --dark-bg: #1f2937;
            --light-bg: #f9fafb;
            --sidebar-width: 260px;
            --header-height: 70px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Figtree', sans-serif;
            background-color: var(--light-bg);
        }
    </style>
    
    @stack('styles')
</head>
<body class="min-h-screen bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        @include('owner.components.sidebar')
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            @include('owner.components.header')
            
            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto p-4 md:p-6 bg-gray-50">
                <!-- Page Title -->
                <div class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800">
                        @yield('page-title', 'Dashboard')
                    </h1>
                    <p class="text-gray-600 mt-2">
                        @yield('page-subtitle', 'Welcome back to your owner dashboard')
                    </p>
                </div>
                
                <!-- Content -->
                <div class="bg-white rounded-xl shadow-sm p-4 md:p-6">
                    @yield('content')
                </div>
            </main>
            
            <!-- Footer -->
            @include('owner.components.footer')
        </div>
    </div>
    
    <!-- Scripts -->
    <script>
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('owner-sidebar');
            sidebar.classList.toggle('-translate-x-full');
            sidebar.classList.toggle('translate-x-0');
        }
    </script>
    
    @stack('scripts')
</body>
</html>