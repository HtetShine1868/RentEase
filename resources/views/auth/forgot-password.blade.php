@extends('layouts.auth')

@section('title', 'Forgot Password')

@section('content')
<h2 class="text-lg font-semibold text-gray-900 mb-2">
    Forgot your password?
</h2>

<p class="text-sm text-gray-600 mb-6">
    Enter your email and we’ll send a reset link.
</p>

@if (session('status'))
    <div class="text-sm text-green-600 mb-4">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="space-y-4">
    @csrf

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" required
            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 text-sm">
    </div>

    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-medium">
        Send reset link
    </button>
</form>

<div class="text-center mt-6 text-sm text-gray-600">
    <a href="{{ route('login') }}" class="hover:underline">Back to login</a>
</div>
@endsection
