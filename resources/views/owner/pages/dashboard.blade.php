@extends('layouts.owner-layout')

@section('title', 'Owner Dashboard - RentEase')
@section('page-title', 'Dashboard Overview')
@section('page-subtitle', 'Welcome back to your property management dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Banner with Stats -->
    <div class="bg-gradient-to-r from-blue-600 to-blue-800 rounded-2xl p-6 text-white">
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center">
            <div class="mb-6 lg:mb-0 lg:pr-8">
                <div class="flex items-center mb-4">
                    <div class="w-14 h-14 rounded-full bg-white bg-opacity-20 flex items-center justify-center mr-4">
                        <i class="fas fa-home text-2xl"></i>
                    </div>
                    <div>
                        <h2 class="text-2xl md:text-3xl font-bold">Welcome back, Property Owner!</h2>
                        <p class="text-blue-100 mt-1">Your comprehensive property management dashboard</p>
                    </div>
                </div>
                
                <!-- Mini Stats Row -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                    <div class="text-center p-3 bg-white bg-opacity-10 rounded-lg">
                        <p class="text-2xl font-bold">12</p>
                        <p class="text-blue-100 text-sm">Properties</p>
                    </div>
                    <div class="text-center p-3 bg-white bg-opacity-10 rounded-lg">
                        <p class="text-2xl font-bold">48</p>
                        <p class="text-blue-100 text-sm">Total Bookings</p>
                    </div>
                    <div class="text-center p-3 bg-white bg-opacity-10 rounded-lg">
                        <p class="text-2xl font-bold">98%</p>
                        <p class="text-blue-100 text-sm">Satisfaction</p>
                    </div>
                    <div class="text-center p-3 bg-white bg-opacity-10 rounded-lg">
                        <p class="text-2xl font-bold">24</p>
                        <p class="text-blue-100 text-sm">Days Active</p>
                    </div>
                </div>
            </div>
            
            <div class="flex flex-col space-y-3">
                <button class="bg-white text-blue-700 hover:bg-blue-50 px-5 py-3 rounded-lg font-semibold transition-all hover:scale-[1.02] shadow-lg flex items-center justify-center">
                    <i class="fas fa-plus mr-2"></i> Add New Property
                </button>
                <button class="bg-blue-700 bg-opacity-30 hover:bg-opacity-40 text-white px-5 py-3 rounded-lg font-semibold transition-all flex items-center justify-center">
                    <i class="fas fa-chart-line mr-2"></i> View Analytics
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        <!-- Total Properties Card with Progress -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-5">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Properties</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">12</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-building text-blue-600 text-2xl"></i>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-3">
                <div class="flex justify-between text-sm text-gray-600 mb-1">
                    <span>Capacity</span>
                    <span>83%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-2 rounded-full" style="width: 83%"></div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <div class="flex justify-between">
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">8</p>
                        <p class="text-xs text-gray-500">Hostels</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-gray-800">4</p>
                        <p class="text-xs text-gray-500">Apartments</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg font-bold text-green-600">+2</p>
                        <p class="text-xs text-gray-500">New</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Listings Card with Chart -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-5">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Active Listings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">10</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-clipboard-check text-green-600 text-2xl"></i>
                </div>
            </div>
            
            <!-- Mini Bar Chart -->
            <div class="mb-3">
                <div class="flex justify-between text-sm text-gray-600 mb-2">
                    <span>Performance</span>
                    <span class="font-semibold text-green-600">↑ 15%</span>
                </div>
                <div class="flex items-end h-8 space-x-1">
                    <div class="flex-1 bg-green-200 rounded-t" style="height: 60%"></div>
                    <div class="flex-1 bg-green-300 rounded-t" style="height: 80%"></div>
                    <div class="flex-1 bg-green-400 rounded-t" style="height: 100%"></div>
                    <div class="flex-1 bg-green-500 rounded-t" style="height: 70%"></div>
                    <div class="flex-1 bg-green-600 rounded-t" style="height: 90%"></div>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                    <span class="text-sm text-gray-600">10 active listings</span>
                    <span class="ml-auto text-yellow-600 text-sm font-medium">
                        <i class="fas fa-clock mr-1"></i> 2 pending
                    </span>
                </div>
            </div>
        </div>

        <!-- Total Bookings Card with Trend -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-5">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Bookings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">48</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-calendar-alt text-purple-600 text-2xl"></i>
                </div>
            </div>
            
            <!-- Trend Indicator -->
            <div class="mb-4">
                <div class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-medium">
                    <i class="fas fa-chart-line mr-2"></i>
                    <span>15% growth this month</span>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <div class="grid grid-cols-3 gap-2">
                    <div class="text-center p-2 bg-green-50 rounded-lg">
                        <p class="text-lg font-bold text-green-700">8</p>
                        <p class="text-xs text-green-600">Active</p>
                    </div>
                    <div class="text-center p-2 bg-yellow-50 rounded-lg">
                        <p class="text-lg font-bold text-yellow-700">3</p>
                        <p class="text-xs text-yellow-600">Pending</p>
                    </div>
                    <div class="text-center p-2 bg-blue-50 rounded-lg">
                        <p class="text-lg font-bold text-blue-700">37</p>
                        <p class="text-xs text-blue-600">Completed</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Earnings Card with Comparison -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 p-5">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Earnings</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">$24,580</p>
                </div>
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-yellow-100 to-yellow-200 flex items-center justify-center shadow-inner">
                    <i class="fas fa-dollar-sign text-yellow-600 text-2xl"></i>
                </div>
            </div>
            
            <!-- Earnings Comparison -->
            <div class="mb-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm text-gray-600">This month</span>
                    <span class="text-sm font-bold text-green-600">$2,450</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Last month</span>
                    <span class="text-sm font-bold text-gray-600">$2,150</span>
                </div>
                <div class="mt-2 text-right">
                    <span class="text-xs text-green-600 font-medium">
                        <i class="fas fa-arrow-up mr-1"></i> 14% increase
                    </span>
                </div>
            </div>
            
            <div class="pt-4 border-t border-gray-100">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">After 5% commission:</span>
                    <span class="text-lg font-bold text-green-600">$23,351</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Property Type Distribution -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl shadow border p-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Property Distribution</h3>
                    <p class="text-gray-600 text-sm">Overview of your property types and occupancy</p>
                </div>
                <select class="border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option>Last 30 days</option>
                    <option>Last 90 days</option>
                    <option>This Year</option>
                </select>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Hostels Stats -->
                <div class="border rounded-xl p-5 hover:border-blue-300 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-bed text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Hostels</h4>
                            <p class="text-gray-500 text-sm">8 properties</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Occupancy Rate</span>
                                <span class="font-semibold">92%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-500 h-2 rounded-full" style="width: 92%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Avg. Rating</span>
                                <span class="font-semibold">4.5/5</span>
                            </div>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $i <= 4 ? '' : ($i == 5 ? 'text-gray-300' : '') }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Monthly Revenue:</span>
                            <span class="font-bold text-green-600">$1,850</span>
                        </div>
                    </div>
                </div>
                
                <!-- Apartments Stats -->
                <div class="border rounded-xl p-5 hover:border-green-300 transition-colors">
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-building text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">Apartments</h4>
                            <p class="text-gray-500 text-sm">4 properties</p>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Occupancy Rate</span>
                                <span class="font-semibold">75%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 75%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Avg. Rating</span>
                                <span class="font-semibold">4.8/5</span>
                            </div>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-yellow-400 {{ $i <= 4 ? '' : ($i == 5 ? 'text-gray-300' : '') }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-gray-100">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Monthly Revenue:</span>
                            <span class="font-bold text-green-600">$3,200</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats Widget -->
        <div class="bg-gradient-to-b from-blue-50 to-white rounded-xl shadow border p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-6">Performance Summary</h3>
            
            <div class="space-y-4">
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Booking Success Rate</p>
                            <p class="text-sm text-gray-500">Completed bookings</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-green-600">94%</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-users text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Customer Satisfaction</p>
                            <p class="text-sm text-gray-500">Based on reviews</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-green-600">4.7</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <i class="fas fa-bolt text-purple-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Response Time</p>
                            <p class="text-sm text-gray-500">Avg. to inquiries</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-blue-600">2.4h</span>
                </div>
                
                <div class="flex items-center justify-between p-3 bg-white rounded-lg border">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                            <i class="fas fa-chart-pie text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">Revenue Growth</p>
                            <p class="text-sm text-gray-500">This quarter</p>
                        </div>
                    </div>
                    <span class="text-2xl font-bold text-green-600">+18%</span>
                </div>
            </div>
            
            <div class="mt-6 pt-6 border-t border-gray-200">
                <a href="#" class="block text-center bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-download mr-2"></i> Download Full Report
                </a>
            </div>
        </div>
    </div>
</div>
@endsection