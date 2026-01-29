@extends('layouts.owner-layout')

@section('title', 'Owner Dashboard - RentEase')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Welcome back to your property management dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 text-white">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div class="mb-4 md:mb-0">
                <div class="flex items-center mb-2">
                    <i class="fas fa-hand-wave text-2xl mr-3"></i>
                    <h2 class="text-2xl md:text-3xl font-bold">Welcome back, Owner!</h2>
                </div>
                <p class="text-blue-100 mt-2">
                    Manage your properties, track bookings, and monitor earnings all in one place.
                    You have <span class="font-semibold">12 active properties</span> and 
                    <span class="font-semibold">8 ongoing bookings</span>.
                </p>
                <div class="flex flex-wrap gap-3 mt-4">
                    <span class="px-3 py-1 bg-blue-500 bg-opacity-30 rounded-full text-sm">
                        <i class="fas fa-bell mr-1"></i> 5 new notifications
                    </span>
                    <span class="px-3 py-1 bg-green-500 bg-opacity-30 rounded-full text-sm">
                        <i class="fas fa-money-bill-wave mr-1"></i> $2,450 earned this month
                    </span>
                </div>
            </div>
            <button class="bg-white text-blue-700 hover:bg-blue-50 px-5 py-3 rounded-lg font-semibold transition-all hover:scale-[1.02] shadow-lg">
                <i class="fas fa-plus mr-2"></i> Add New Property
            </button>
        </div>
    </div>

    <!-- Quick Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Properties Card -->
        <div class="bg-white rounded-xl shadow border hover:shadow-lg transition-shadow duration-300 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Properties</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">12</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-arrow-up mr-1"></i> 2 new
                        </span>
                        <span class="text-gray-400 text-sm mx-2">•</span>
                        <span class="text-gray-500 text-sm">this month</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Hostels: <span class="font-semibold">8</span></span>
                    <span class="text-gray-600">Apartments: <span class="font-semibold">4</span></span>
                </div>
            </div>
        </div>

        <!-- Active Listings Card -->
        <div class="bg-white rounded-xl shadow border hover:shadow-lg transition-shadow duration-300 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Active Listings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">10</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-check-circle mr-1"></i> 83%
                        </span>
                        <span class="text-gray-400 text-sm mx-2">•</span>
                        <span class="text-gray-500 text-sm">active rate</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-clipboard-check text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-sm text-gray-600">
                    <span>2 properties <span class="text-yellow-600">pending</span></span>
                </div>
            </div>
        </div>

        <!-- Total Bookings Card -->
        <div class="bg-white rounded-xl shadow border hover:shadow-lg transition-shadow duration-300 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">48</p>
                    <div class="flex items-center mt-2">
                        <span class="text-blue-600 text-sm font-medium">
                            <i class="fas fa-chart-line mr-1"></i> 15%
                        </span>
                        <span class="text-gray-400 text-sm mx-2">•</span>
                        <span class="text-gray-500 text-sm">growth</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Active: <span class="font-semibold text-green-600">8</span></span>
                    <span class="text-gray-600">Pending: <span class="font-semibold text-yellow-600">3</span></span>
                </div>
            </div>
        </div>

        <!-- Earnings Card -->
        <div class="bg-white rounded-xl shadow border hover:shadow-lg transition-shadow duration-300 p-5">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Earnings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">$24,580</p>
                    <div class="flex items-center mt-2">
                        <span class="text-green-600 text-sm font-medium">
                            <i class="fas fa-money-bill-wave mr-1"></i> $2,450
                        </span>
                        <span class="text-gray-400 text-sm mx-2">•</span>
                        <span class="text-gray-500 text-sm">this month</span>
                    </div>
                </div>
                <div class="w-14 h-14 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-sm">
                    <span class="text-gray-600">After commission: </span>
                    <span class="font-semibold text-green-600">$23,351</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow border p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-gray-800">Quick Actions</h3>
            <a href="#" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                View all <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="#" class="group p-5 border rounded-xl hover:border-blue-300 hover:bg-blue-50 transition-all text-center">
                <div class="w-12 h-12 rounded-full bg-blue-100 group-hover:bg-blue-200 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <i class="fas fa-plus text-blue-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800 group-hover:text-blue-700">Add Property</p>
                <p class="text-xs text-gray-500 mt-1">Create new listing</p>
            </a>
            
            <a href="#" class="group p-5 border rounded-xl hover:border-green-300 hover:bg-green-50 transition-all text-center">
                <div class="w-12 h-12 rounded-full bg-green-100 group-hover:bg-green-200 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <i class="fas fa-eye text-green-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800 group-hover:text-green-700">View Bookings</p>
                <p class="text-xs text-gray-500 mt-1">Manage reservations</p>
            </a>
            
            <a href="#" class="group p-5 border rounded-xl hover:border-purple-300 hover:bg-purple-50 transition-all text-center">
                <div class="w-12 h-12 rounded-full bg-purple-100 group-hover:bg-purple-200 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <i class="fas fa-chart-bar text-purple-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800 group-hover:text-purple-700">View Reports</p>
                <p class="text-xs text-gray-500 mt-1">Analytics & insights</p>
            </a>
            
            <a href="#" class="group p-5 border rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all text-center">
                <div class="w-12 h-12 rounded-full bg-gray-100 group-hover:bg-gray-200 flex items-center justify-center mx-auto mb-3 transition-colors">
                    <i class="fas fa-cog text-gray-600 text-xl"></i>
                </div>
                <p class="font-semibold text-gray-800 group-hover:text-gray-700">Settings</p>
                <p class="text-xs text-gray-500 mt-1">Account & preferences</p>
            </a>
        </div>
    </div>

    <!-- Recent Activity Preview -->
    <div class="lg:grid lg:grid-cols-3 gap-6 space-y-6 lg:space-y-0">
        <!-- Recent Bookings Preview -->
        <div class="lg:col-span-2 bg-white rounded-xl shadow border p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Recent Bookings</h3>
                <a href="{{ route('owner.bookings.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @for($i = 1; $i <= 3; $i++)
                <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-4">
                        <i class="fas fa-user text-blue-600"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-800">John Doe</h4>
                        <p class="text-sm text-gray-600">Sunshine Apartments #302</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Active</span>
                        <p class="text-sm text-gray-500 mt-1">Feb 15, 2024</p>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Notifications Preview -->
        <div class="bg-white rounded-xl shadow border p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-800">Recent Notifications</h3>
                <a href="{{ route('owner.notifications') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    View all <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-bell text-blue-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">New booking received</p>
                            <p class="text-xs text-gray-600 mt-1">2 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-3 bg-green-50 border border-green-100 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-money-bill-wave text-green-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Payment confirmed</p>
                            <p class="text-xs text-gray-600 mt-1">5 hours ago</p>
                        </div>
                    </div>
                </div>
                
                <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                    <div class="flex items-start">
                        <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-sm"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Complaint received</p>
                            <p class="text-xs text-gray-600 mt-1">1 day ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection