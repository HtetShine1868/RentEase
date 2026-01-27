@extends('dashboard')

@section('title', 'Food Services')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold text-gray-900">Food Services</h2>
        <p class="mt-2 text-gray-600">Order meals or subscribe for regular delivery.</p>
    </div>

    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <a href="#" 
                   class="border-indigo-500 text-indigo-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Browse Restaurants
                </a>
                <a href="#" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Orders
                </a>
                <a href="#" 
                   class="border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    My Subscriptions
                </a>
            </nav>
        </div>
        <div class="p-6">
            <!-- Coming Soon -->
            <div class="text-center py-12">
                <i class="fas fa-utensils text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Food Services Coming Soon</h3>
                <p class="mt-2 text-gray-500 max-w-md mx-auto">
                    Our food service platform is under development. You'll soon be able to order meals and manage subscriptions here.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection