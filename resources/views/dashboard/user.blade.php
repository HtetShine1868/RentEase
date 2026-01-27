@extends('dashboard')

@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="mt-1 opacity-90">Here's what's happening with your account today.</p>
            </div>
            <div class="hidden md:block">
                <div class="h-16 w-16 rounded-full bg-white/20 flex items-center justify-center">
                    <i class="fas fa-user text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Rental Status -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-home text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Rental Status</h3>
                    <div class="mt-1">
                        <p class="text-2xl font-semibold text-gray-900">Not Active</p>
                        <p class="text-sm text-gray-500">No current rental</p>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('rental.search') }}" 
                   class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-500">
                    Search for rental
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Food Orders -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-utensils text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Food Orders</h3>
                    <div class="mt-1">
                        <p class="text-2xl font-semibold text-gray-900">0</p>
                        <p class="text-sm text-gray-500">Active orders</p>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('food.index') }}" 
                   class="inline-flex items-center text-sm font-medium text-green-600 hover:text-green-500">
                    View food orders
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Laundry Orders -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-tshirt text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Laundry Orders</h3>
                    <div class="mt-1">
                        <p class="text-2xl font-semibold text-gray-900">0</p>
                        <p class="text-sm text-gray-500">In process</p>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('laundry.index') }}" 
                   class="inline-flex items-center text-sm font-medium text-purple-600 hover:text-purple-500">
                    View laundry orders
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>

        <!-- Monthly Spending -->
        <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-lg bg-yellow-100 flex items-center justify-center">
                        <i class="fas fa-credit-card text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="ml-4">
                    <h3 class="text-sm font-medium text-gray-900">Monthly Spending</h3>
                    <div class="mt-1">
                        <p class="text-2xl font-semibold text-gray-900">৳0</p>
                        <p class="text-sm text-gray-500">This month</p>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('payments.index') }}" 
                   class="inline-flex items-center text-sm font-medium text-yellow-600 hover:text-yellow-500">
                    View payments
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Recent Activity</h3>
        </div>
        <div class="divide-y divide-gray-200">
            <!-- Activity Item -->
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Account Verified</p>
                        <p class="text-sm text-gray-500">Your email has been verified successfully</p>
                    </div>
                    <div class="ml-auto text-sm text-gray-500">
                        {{ now()->format('M d, H:i') }}
                    </div>
                </div>
            </div>
            
            <!-- Activity Item -->
            <div class="px-6 py-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-900">Account Created</p>
                        <p class="text-sm text-gray-500">Welcome to RMS system</p>
                    </div>
                    <div class="ml-auto text-sm text-gray-500">
                        {{ now()->format('M d, H:i') }}
                    </div>
                </div>
            </div>
            
            <!-- No Recent Activity -->
            <div class="px-6 py-8 text-center">
                <i class="fas fa-stream text-gray-300 text-3xl mb-3"></i>
                <p class="text-gray-500">No recent activity</p>
                <p class="text-sm text-gray-400 mt-1">Your recent activities will appear here</p>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Search Rental -->
        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-100">
            <div class="flex items-center mb-4">
                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-search text-blue-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Find Your Perfect Place</h3>
            </div>
            <p class="text-gray-600 mb-4">
                Browse hostels and apartments with transparent pricing and commission breakdown.
            </p>
            <a href="{{ route('rental.search') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Search Rentals
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- Order Food -->
        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-100">
            <div class="flex items-center mb-4">
                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                    <i class="fas fa-utensils text-green-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Order Delicious Meals</h3>
            </div>
            <p class="text-gray-600 mb-4">
                Subscribe for regular meals or order pay-per-eat from nearby providers.
            </p>
            <a href="{{ route('food.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                Browse Food
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>

        <!-- Laundry Service -->
        <div class="bg-gradient-to-br from-purple-50 to-violet-50 rounded-lg p-6 border border-purple-100">
            <div class="flex items-center mb-4">
                <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-tshirt text-purple-600"></i>
                </div>
                <h3 class="ml-3 text-lg font-medium text-gray-900">Laundry Service</h3>
            </div>
            <p class="text-gray-600 mb-4">
                Schedule laundry pickup with normal or rush turnaround options.
            </p>
            <a href="{{ route('laundry.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                Find Laundry
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</div>
@endsection