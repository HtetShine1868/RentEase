@extends('layouts.owner-layout')

@section('title', 'Owner Dashboard')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Welcome to your property management dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-primary-500 to-primary-600 rounded-xl p-6 text-white">
        <div class="flex flex-col md:flex-row justify-between items-center">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, Property Owner!</h2>
                <p class="mt-2 opacity-90">Manage your properties, bookings, and earnings from one dashboard</p>
            </div>
            <button class="mt-4 md:mt-0 bg-white text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition">
                <i class="fas fa-plus mr-2"></i>Add New Property
            </button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-xl p-6 shadow border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-building text-blue-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Total Properties</p>
                    <p class="text-3xl font-bold">12</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-calendar-check text-green-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">Active Bookings</p>
                    <p class="text-3xl font-bold">8</p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-dollar-sign text-purple-600 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-500">This Month</p>
                    <p class="text-3xl font-bold">$2,450</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl p-6 shadow border">
        <h3 class="text-xl font-bold mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-plus-circle text-primary-600 text-2xl mb-2"></i>
                <p class="font-medium">Add Property</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-eye text-green-600 text-2xl mb-2"></i>
                <p class="font-medium">View Bookings</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-chart-bar text-purple-600 text-2xl mb-2"></i>
                <p class="font-medium">View Reports</p>
            </a>
            <a href="#" class="p-4 border rounded-lg hover:bg-gray-50 transition text-center">
                <i class="fas fa-cog text-gray-600 text-2xl mb-2"></i>
                <p class="font-medium">Settings</p>
            </a>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl p-6 shadow border">
        <h3 class="text-xl font-bold mb-4">Recent Activity</h3>
        <div class="space-y-4">
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">New booking confirmed</p>
                    <p class="text-sm text-gray-500">Apartment #302 booked for 6 months</p>
                </div>
                <span class="text-sm text-gray-500">2 hours ago</span>
            </div>
            
            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-money-bill-wave text-blue-600"></i>
                </div>
                <div class="flex-1">
                    <p class="font-medium">Payment received</p>
                    <p class="text-sm text-gray-500">$850 from Hostel Room #12</p>
                </div>
                <span class="text-sm text-gray-500">1 day ago</span>
            </div>
        </div>
    </div>
</div>

<!-- In your dashboard content, update cards like this: -->
<div class="bg-white rounded-xl shadow border hover-lift smooth-transition p-6">
    <!-- Card content -->
</div>

<!-- Add loading states example: -->
<button class="btn-primary focus-ring">
    <span class="loading-dots">Loading</span>
</button>

<!-- Add gradient text example: -->
<h2 class="text-2xl font-bold gradient-text">Welcome Back</h2>
@endsection