@extends('layouts.auth')

@section('title', 'Login')

@section('content')
<h2 class="text-lg font-semibold text-gray-900 mb-1">Welcome back</h2>
<p class="text-sm text-gray-500 mb-6">
    Login to continue managing your rentals and services.
</p>

<form method="POST" action="{{ route('login') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required autofocus
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
            <input id="login-password" type="password" name="password" required
                class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <button type="button"
                onclick="togglePassword('login-password', this)"
                class="absolute inset-y-0 right-0 px-3 text-gray-400">
                👁️
            </button>
        </div>
    </div>

    <div class="flex items-center justify-between text-sm">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="remember" class="rounded border-gray-300">
            Remember me
        </label>

        <a href="{{ route('password.request') }}"
           class="text-indigo-600 hover:underline">
            Forgot password?
        </a>
    </div>

    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-medium">
        Login
    </button>
</form>

<div class="text-center mt-6 text-sm text-gray-600">
    Don’t have an account?
    <a href="{{ route('register') }}" class="text-indigo-600 hover:underline">
        Create one
    </a>
</div>
@endsection
