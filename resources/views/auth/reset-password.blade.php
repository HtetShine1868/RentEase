@extends('layouts.auth')

@section('title', 'Reset Password')

@section('content')
<h2 class="text-lg font-semibold text-gray-900 mb-2">
    Reset password
</h2>

<form method="POST" action="{{ route('password.update') }}" class="space-y-4">
    @csrf
    <input type="hidden" name="token" value="{{ $request->route('token') }}">

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required
            value="{{ old('email', $request->email) }}"
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
        <div class="relative">
            <input id="new-password" type="password" name="password" required
                class="w-full rounded-lg border-gray-300 pr-10 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            <button type="button"
                onclick="togglePassword('new-password', this)"
                class="absolute inset-y-0 right-0 px-3 text-gray-400">
                👁️
            </button>
        </div>
    </div>

    <div>
        <label
