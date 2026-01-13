@extends('layouts.auth')

@section('title', 'Register')

@section('content')
<h2 class="text-lg font-semibold text-gray-900 mb-1">Create your account</h2>
<p class="text-sm text-gray-500 mb-6">
    Register once. Apply roles later if needed.
</p>

<form method="POST" action="{{ route('register') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
        <input type="text" name="name" required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
        <div class="relative">
            <input id="reg-password" type="password" name="password" required
                class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <button type="button"
                onclick="togglePassword('reg-password', this)"
                class="absolute inset-y-0 right-0 px-3 text-gray-400">
                👁️
            </button>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
        <div class="relative">
            <input id="reg-password-confirm" type="password" name="password_confirmation" required
                class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <button type="button"
                onclick="togglePassword('reg-password-confirm', this)"
                class="absolute inset-y-0 right-0 px-3 text-gray-400">
                👁️
            </button>
        </div>
    </div>

    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-medium">
        Create account
    </button>
</form>

<div class="text-center mt-6 text-sm text-gray-600">
    Already registered?
    <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">
        Login
    </a>
</div>
@endsection
