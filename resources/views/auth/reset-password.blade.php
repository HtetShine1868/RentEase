<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RMS') }} - Reset Password</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Custom styles to ensure green/teal color scheme */
        .bg-primary {
            background-color: #174455 !important;
        }
        .bg-primary:hover {
            background-color: #1f556b !important;
        }
        .text-primary {
            color: #174455 !important;
        }
        .border-primary {
            border-color: #174455 !important;
        }
        .focus\:ring-primary:focus {
            --tw-ring-color: #174455 !important;
        }
        .focus\:border-primary:focus {
            border-color: #174455 !important;
        }
    </style>
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <!-- Header with green color -->
        <div class="w-full text-center mb-8">
            <a href="{{ route('home') }}" class="inline-block">
                <h1 class="text-3xl font-bold" style="color: #174455;">RMS</h1>
                <p class="text-gray-600 mt-1">Rent & Service Management System</p>
            </a>
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-lg">
            <h2 class="text-2xl font-bold text-center mb-2" style="color: #174455;">Reset Password</h2>
            <p class="text-center text-gray-600 text-sm mb-8">Enter your new password below</p>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $errors->first() }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Session Status -->
            @if (session('status'))
                <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded">
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

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

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
                            value="{{ old('email', $email) }}" 
                            required 
                            readonly
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-gray-50 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out"
                            style="focus:border-color: #174455; focus:ring-color: #174455;"
                            placeholder="you@example.com"
                        >
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password with Eye Toggle -->
                <div x-data="{ showPassword: false }">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                        New Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password" 
                            :type="showPassword ? 'text' : 'password'" 
                            name="password" 
                            required 
                            autocomplete="new-password"
                            class="pl-10 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out"
                            style="focus:border-color: #174455; focus:ring-color: #174455;"
                            placeholder="Enter new password"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showPassword = !showPassword"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password with Eye Toggle -->
                <div x-data="{ showConfirmPassword: false }">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                        Confirm Password
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input 
                            id="password_confirmation" 
                            :type="showConfirmPassword ? 'text' : 'password'" 
                            name="password_confirmation" 
                            required 
                            autocomplete="new-password"
                            class="pl-10 pr-10 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition duration-150 ease-in-out"
                            style="focus:border-color: #174455; focus:ring-color: #174455;"
                            placeholder="Confirm new password"
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            <button 
                                type="button"
                                @click="showConfirmPassword = !showConfirmPassword"
                                class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            >
                                <i :class="showConfirmPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Password Requirements -->
                <div class="rounded-lg p-3 text-xs" style="background-color: #e6f3f5; color: #174455;">
                    <p class="font-medium mb-1">Password requirements:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <li>Minimum 8 characters</li>
                        <li>At least one uppercase letter</li>
                        <li>At least one number</li>
                        <li>At least one special character</li>
                    </ul>
                </div>

                <!-- Submit Button - Green color -->
                <div>
                    <button 
                        type="submit" 
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out"
                        style="background-color: #174455; hover:background-color: #1f556b; focus:ring-color: #174455;"
                        onmouseover="this.style.backgroundColor='#1f556b'"
                        onmouseout="this.style.backgroundColor='#174455'"
                    >
                        <i class="fas fa-sync-alt mr-2"></i>
                        Reset Password
                    </button>
                </div>

                <!-- Divider -->
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Remember your password?</span>
                    </div>
                </div>

                <!-- Back to Login Link -->
                <div class="text-center">
                    <a 
                        href="{{ route('login') }}" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 transition duration-150 ease-in-out"
                        style="focus:ring-color: #174455;"
                    >
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Sign In
                    </a>
                </div>
            </form>
        </div>

        <!-- Demo Accounts (optional - you can remove if not needed) -->
        <div class="w-full sm:max-w-md mt-8 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-3" style="color: #174455;">Demo Accounts</h3>
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
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-sm text-gray-500">
            <p>&copy; {{ date('Y') }} RMS System. All rights reserved.</p>
        </div>
    </div>
</body>
</html>