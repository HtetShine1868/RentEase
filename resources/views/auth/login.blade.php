<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RMS') }} - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Header -->
        <div class="w-full text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block">
                <h1 class="text-3xl font-bold text-indigo-600">RMS</h1>
                <p class="text-gray-600 mt-1">Rent & Service Management System</p>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-gray-800 text-center mb-8">Sign In</h2>

            <!-- Session Status -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-envelope text-gray-400"></i>
                        </div>
                        <input 
                            id="email" 
                            type="email" 
                            name="email" 
                            value="{{ old('email') }}" 
                            required 
                            autofocus 
                            autocomplete="email"
                            class="pl-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out"
                            placeholder="you@example.com"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @if (session('status'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="password" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                                Forgot password?
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="current-password"
                            class="pl-10 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out"
                            placeholder="Enter your password"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center">
                    <input 
                        id="remember_me" 
                        type="checkbox" 
                        name="remember" 
                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                    >
                    <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                        Remember me
                    </label>
                </div>

                <!-- Submit Button -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-sign-in-alt mr-2"></i>
                        Sign In
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">New to RMS?</span>
                    </div>
                </div>

                <!-- Register Link -->
                <div class="text-center">
                    <a 
                        href="{{ route('register') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out"
                    >
                        <i class="fas fa-user-plus mr-2"></i>
                        Create New Account
                    </a>
                </div>
            </form>
        </div>

        <!-- Demo Accounts -->
        <div class="w-full sm:max-w-md mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Demo Accounts</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Regular User</p>
                        <p class="text-xs text-gray-500">user@rms.com</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">password</code>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Property Owner</p>
                        <p class="text-xs text-gray-500">owner@rms.com</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">password</code>
                </div>
                <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Food Provider</p>
                        <p class="text-xs text-gray-500">food@rms.com</p>
                    </div>
                    <code class="text-xs bg-gray-100 px-2 py-1 rounded">password</code>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} RMS System. All rights reserved.</p>
        </div>
    </div>

    <!-- Alpine.js for password visibility toggle -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('loginForm', () => ({
                showPassword: false
            }));
        });
    </script>
    <script src="//unpkg.com/alpinejs" defer></script>
</body>
</html>