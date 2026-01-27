<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'RMS') }} - Verify Email</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
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
            <div class="text-center mb-8">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-blue-100">
                    <i class="fas fa-envelope text-blue-600 text-2xl"></i>
                </div>
                <h2 class="mt-4 text-2xl font-bold text-gray-900">Verify Your Email</h2>
                <p class="mt-2 text-gray-600">Thanks for signing up!</p>
            </div>

            <!-- Session Status -->
            @if (session('status') == 'verification-link-sent')
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                A new verification link has been sent to your email address.
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            @if (session('success'))
                <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
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

            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">Verification Required</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <p>Before proceeding, please check your email for a verification link.</p>
                            <p class="mt-1">If you didn't receive the email, click the button below to request another.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <div class="space-y-4">
                    <!-- Resend Verification Email -->
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-3 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Resend Verification Email
                        </button>
                    </form>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                            <i class="fas fa-sign-out-alt mr-2"></i>
                            Log Out
                        </button>
                    </form>
                </div>

                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Need help?</span>
                    </div>
                </div>

                <!-- Help Information -->
                <div class="text-left text-sm text-gray-600">
                    <p class="font-medium mb-1">Didn't receive the email?</p>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Check your spam or junk folder</li>
                        <li>Make sure you entered the correct email address</li>
                        <li>Wait a few minutes and try again</li>
                        <li>Contact support if the problem persists</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Demo Accounts Note -->
        <div class="w-full sm:max-w-md mt-8 bg-white rounded-lg shadow p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-lightbulb text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-gray-900">Demo Accounts</h3>
                    <div class="mt-2 text-sm text-gray-500">
                        <p>For testing purposes, you can use these pre-verified accounts:</p>
                        <div class="mt-2 space-y-1 text-xs">
                            <div class="flex justify-between">
                                <span>User:</span>
                                <code class="bg-gray-100 px-2 py-1 rounded">user@rms.com / password</code>
                            </div>
                            <div class="flex justify-between">
                                <span>Owner:</span>
                                <code class="bg-gray-100 px-2 py-1 rounded">owner@rms.com / password</code>
                            </div>
                        </div>
                    </div>
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