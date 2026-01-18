@extends('layouts.app')

@section('title', 'User Dashboard')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-bold mb-4">User Dashboard</h1>
                <p class="mb-4">Welcome back, {{ Auth::user()->name }}!</p>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-blue-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Properties</h3>
                        <p class="text-gray-600">Browse and book hostels & apartments</p>
                    </div>
                    <div class="bg-green-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Food Services</h3>
                        <p class="text-gray-600">Order meals or subscribe</p>
                    </div>
                    <div class="bg-purple-50 p-6 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Laundry Services</h3>
                        <p class="text-gray-600">Normal or rush service</p>
                    </div>
                </div>

                @if(!Auth::user()->hasOperationalRole() && !Auth::user()->hasPendingRoleApplication())
                    <div class="mt-8 p-6 bg-yellow-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Want to become a service provider?</h3>
                        <p class="mb-4">Apply for a service provider role to list your properties or services.</p>
                        <a href="{{ route('role-application.create') }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-600 active:bg-yellow-700 focus:outline-none focus:border-yellow-700 focus:ring focus:ring-yellow-200 disabled:opacity-25 transition">
                            Apply Now
                        </a>
                    </div>
                @endif

                @if(Auth::user()->hasPendingRoleApplication())
                    <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                        <h3 class="text-lg font-semibold mb-2">Role Application Status</h3>
                        <p class="mb-4">Your role application is currently under review.</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                            Pending Review
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection