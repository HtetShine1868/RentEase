@extends('layouts.auth')

@section('title', 'Verify Email')

@section('content')
<h2 class="text-lg font-semibold text-gray-900 mb-2">Verify your email</h2>

<p class="text-sm text-gray-600 mb-6">
    We’ve sent a verification link to your email.
    Please verify before continuing.
</p>

@if (session('status') === 'verification-link-sent')
    <div class="text-sm text-green-600 mb-4">
        A new verification link has been sent.
    </div>
@endif

<form method="POST" action="{{ route('verification.send') }}">
    @csrf
    <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg py-2.5 text-sm font-medium">
        Resend verification email
    </button>
</form>

<form method="POST" action="{{ route('logout') }}" class="mt-4">
    @csrf
    <button class="w-full text-sm text-gray-600 hover:underline">
        Logout
    </button>
</form>
@endsection
