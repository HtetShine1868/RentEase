@extends('dashboard')

@section('title', 'My Rental')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900">My Rental</h2>
        <p class="mt-2 text-gray-600">Manage your current rental and view history.</p>
    </div>

    <!-- Current Rental Status -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Current Rental Status</h3>
        </div>
        <div class="p-6">
            <div class="text-center py-12">
                <i class="fas fa-home text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">No Active Rental</h3>
                <p class="mt-2 text-gray-500 max-w-md mx-auto">
                    You don't have an active rental right now. Find your perfect place to stay.
                </p>
                <div class="mt-6">
                    <a href="{{ route('rental.search') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-search mr-2"></i>
                        Search for Rental
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs for Rental Management -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="#" 
                   class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Booking History
                </a>
                <a href="#" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Rental Complaints
                </a>
            </nav>
        </div>
        <div class="p-6">
            <div class="text-center py-8">
                <p class="text-gray-500">No booking history available.</p>
            </div>
        </div>
    </div>
</div>
@endsection