@extends('owner.layout.owner-layout')

@section('title', 'Earnings Dashboard - RentEase')
@section('page-title', 'Earnings Dashboard')
@section('page-subtitle', 'Track your income, commissions, and transactions')

@section('content')
<div class="space-y-6">
    @include('owner.components.validation-messages')
    @include('owner.components.empty-states')

    <!-- Header with Date Range -->
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Earnings Dashboard</h1>
                <p class="text-gray-600 mt-1">Monitor your rental income and commission earnings</p>
            </div>
            
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <!-- Date Range Selector -->
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 {{ request('period') === 'today' || !request('period') ? 'bg-purple-50 border-purple-200 text-purple-700' : 'text-gray-700' }}">
                        Today
                    </button>
                    <button class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 {{ request('period') === 'week' ? 'bg-purple-50 border-purple-200 text-purple-700' : 'text-gray-700' }}">
                        This Week
                    </button>
                    <button class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 {{ request('period') === 'month' ? 'bg-purple-50 border-purple-200 text-purple-700' : 'text-gray-700' }}">
                        This Month
                    </button>
                    <button class="px-3 py-1.5 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 {{ request('period') === 'year' ? 'bg-purple-50 border-purple-200 text-purple-700' : 'text-gray-700' }}">
                        This Year
                    </button>
                </div>
                
                <!-- Custom Date Range -->
                <div class="flex items-center gap-2">
                    <input type="date" 
                           class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           value="{{ date('Y-m-d', strtotime('-30 days')) }}">
                    <span class="text-gray-500">to</span>
                    <input type="date" 
                           class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500"
                           value="{{ date('Y-m-d') }}">
                    <button class="px-3 py-1.5 text-sm font-medium text-white bg-purple-600 rounded-lg hover:bg-purple-700">
                        Apply
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Earnings -->
        <div class="bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-purple-700">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$12,850.50</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i> 15.2%
                        </span>
                        <span class="text-xs text-gray-500 ml-2">vs last month</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-600 flex items-center justify-center">
                    <i class="fas fa-wallet text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Commission Paid -->
        <div class="bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-blue-700">Commission Paid</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$642.50</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-gray-600 font-medium">5% average rate</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-600 flex items-center justify-center">
                    <i class="fas fa-percentage text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Net Income -->
        <div class="bg-gradient-to-br from-green-50 to-green-100 border border-green-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-green-700">Net Income</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$12,208.00</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i> $1,580.25
                        </span>
                        <span class="text-xs text-gray-500 ml-2">after commission</span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-600 flex items-center justify-center">
                    <i class="fas fa-money-check-alt text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Payout -->
        <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 border border-yellow-200 rounded-xl p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-700">Pending Payout</p>
                    <p class="text-2xl font-bold text-gray-900 mt-2">$1,850.75</p>
                    <div class="flex items-center mt-2">
                        <span class="text-xs text-yellow-600 font-medium">
                            <i class="fas fa-clock mr-1"></i> Next payout: Jan 30
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-600 flex items-center justify-center">
                    <i class="fas fa-hourglass-half text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Monthly Earnings Chart -->
        <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Monthly Earnings Overview</h2>
                    <p class="text-sm text-gray-600 mt-1">Last 6 months performance</p>
                </div>
                <select class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                    <option>All Properties</option>
                    <option>Sunshine Apartments</option>
                    <option>City Hostel</option>
                    <option>Luxury Villa</option>
                </select>
            </div>
            
            <!-- Chart Placeholder -->
            <div class="h-72 flex items-center justify-center bg-gray-50 rounded-lg border border-gray-200">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500 font-medium">Earnings Chart</p>
                    <p class="text-sm text-gray-400 mt-1">Visual representation of monthly earnings</p>
                    <div class="mt-4 flex justify-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-purple-500 mr-2"></div>
                            <span class="text-xs text-gray-600">Total Earnings</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded-full bg-green-500 mr-2"></div>
                            <span class="text-xs text-gray-600">Net Income</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Mini Stats -->
            <div class="grid grid-cols-3 gap-4 mt-6">
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Highest Month</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">$3,250.00</p>
                    <p class="text-xs text-gray-500">December 2023</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Average Monthly</p>
                    <p class="text-lg font-bold text-gray-900 mt-1">$2,141.75</p>
                    <p class="text-xs text-gray-500">Last 6 months</p>
                </div>
                <div class="text-center p-3 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500">Growth Rate</p>
                    <p class="text-lg font-bold text-green-600 mt-1">+15.2%</p>
                    <p class="text-xs text-gray-500">Month over month</p>
                </div>
            </div>
        </div>

        <!-- Earnings by Property -->
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Earnings by Property</h2>
            
            <div class="space-y-4">
                <!-- Property 1 -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                            <i class="fas fa-building text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Sunshine Apartments</p>
                            <p class="text-xs text-gray-500">3 bookings</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">$3,750.00</p>
                        <p class="text-xs text-green-600">45% of total</p>
                    </div>
                </div>

                <!-- Property 2 -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                            <i class="fas fa-bed text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">City Hostel</p>
                            <p class="text-xs text-gray-500">8 bookings</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">$5,400.00</p>
                        <p class="text-xs text-green-600">35% of total</p>
                    </div>
                </div>

                <!-- Property 3 -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center mr-3">
                            <i class="fas fa-home text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Luxury Villa</p>
                            <p class="text-xs text-gray-500">1 booking</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">$2,800.00</p>
                        <p class="text-xs text-green-600">18% of total</p>
                    </div>
                </div>

                <!-- Property 4 -->
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center mr-3">
                            <i class="fas fa-building text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">Beach House</p>
                            <p class="text-xs text-gray-500">0 bookings</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-bold text-gray-900">$0.00</p>
                        <p class="text-xs text-gray-500">No earnings</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-gray-900">Total Properties</p>
                    <p class="text-lg font-bold text-gray-900">4</p>
                </div>
                <div class="flex items-center justify-between mt-2">
                    <p class="text-sm font-medium text-gray-900">Active Properties</p>
                    <p class="text-lg font-bold text-green-600">3</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                <button class="px-4 py-2 text-sm font-medium text-purple-700 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors">
                    <i class="fas fa-download mr-2"></i> Export Statement
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Date & Time
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Transaction ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Description
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Property
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Amount
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <!-- Transaction 1 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Jan 15, 2024</div>
                            <div class="text-xs text-gray-500">10:30 AM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">TXN-001234</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Monthly Rental Payment</div>
                            <div class="text-xs text-gray-500">Booking #BK-001</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-building text-purple-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Sunshine Apartments</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-home mr-1"></i> Rental
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">+$1,250.00</div>
                            <div class="text-xs text-gray-500">Commission: $37.50</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewReceipt('TXN-001234')" 
                                    class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-receipt mr-1"></i> Receipt
                            </button>
                        </td>
                    </tr>

                    <!-- Transaction 2 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Jan 14, 2024</div>
                            <div class="text-xs text-gray-500">02:15 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">TXN-001233</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Hostel Room Booking</div>
                            <div class="text-xs text-gray-500">Booking #BK-002 • Room 101</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-bed text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">City Hostel</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">
                                <i class="fas fa-bed mr-1"></i> Hostel
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-green-600">+$450.00</div>
                            <div class="text-xs text-gray-500">Commission: $22.50</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1"></i> Pending
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewReceipt('TXN-001233')" 
                                    class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-receipt mr-1"></i> Invoice
                            </button>
                        </td>
                    </tr>

                    <!-- Transaction 3 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Jan 13, 2024</div>
                            <div class="text-xs text-gray-500">11:45 AM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">TXN-001232</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Security Deposit Refund</div>
                            <div class="text-xs text-gray-500">Booking #BK-005 • Checked out</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-green-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-home text-green-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Luxury Villa</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-arrow-left mr-1"></i> Refund
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-red-600">-$500.00</div>
                            <div class="text-xs text-gray-500">Security deposit</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewReceipt('TXN-001232')" 
                                    class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-file-invoice mr-1"></i> Statement
                            </button>
                        </td>
                    </tr>

                    <!-- Transaction 4 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Jan 12, 2024</div>
                            <div class="text-xs text-gray-500">09:20 AM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">TXN-001231</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Commission Deduction</div>
                            <div class="text-xs text-gray-500">Monthly service fee</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-percentage text-gray-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">System</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                <i class="fas fa-percentage mr-1"></i> Commission
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-red-600">-$64.25</div>
                            <div class="text-xs text-gray-500">Monthly commission</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewReceipt('TXN-001231')" 
                                    class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-receipt mr-1"></i> Details
                            </button>
                        </td>
                    </tr>

                    <!-- Transaction 5 -->
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">Jan 10, 2024</div>
                            <div class="text-xs text-gray-500">03:40 PM</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-mono text-gray-900">TXN-001230</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">Payout to Bank Account</div>
                            <div class="text-xs text-gray-500">Monthly earnings transfer</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center mr-2">
                                    <i class="fas fa-university text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-sm text-gray-900">Bank Transfer</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-indigo-100 text-indigo-800">
                                <i class="fas fa-money-check-alt mr-1"></i> Payout
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-bold text-red-600">-$2,500.00</div>
                            <div class="text-xs text-gray-500">To account ****1234</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <button onclick="viewReceipt('TXN-001230')" 
                                    class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg hover:bg-purple-100 transition-colors">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Transfer
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Transaction Summary -->
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Total Transactions</p>
                    <p class="text-xl font-bold text-gray-900 mt-1">24</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Total Income</p>
                    <p class="text-xl font-bold text-green-600 mt-1">$8,500.00</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Total Expenses</p>
                    <p class="text-xl font-bold text-red-600 mt-1">$3,064.25</p>
                </div>
                <div class="text-center p-3 bg-white rounded-lg border border-gray-200">
                    <p class="text-sm text-gray-500">Net Balance</p>
                    <p class="text-xl font-bold text-purple-600 mt-1">$5,435.75</p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold">1 to 5</span> of <span class="font-semibold">24</span> transactions
                </div>
                <div class="flex items-center gap-1">
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded bg-purple-600 text-white">1</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">2</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">3</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">4</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Earnings Dashboard Functions
function viewReceipt(transactionId) {
    Loading.show('Loading receipt...');
    setTimeout(() => {
        Toast.success('Receipt Generated', `Receipt for ${transactionId} is ready.`);
        // In real app, open receipt modal or PDF
        window.open(`/owner/earnings/receipt/${transactionId}`, '_blank');
        Loading.hide();
    }, 1000);
}

// Date Range Selection
document.addEventListener('DOMContentLoaded', function() {
    // Date range buttons
    const periodButtons = document.querySelectorAll('.px-3.py-1\\.5.text-sm');
    periodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            periodButtons.forEach(btn => {
                btn.classList.remove('bg-purple-50', 'border-purple-200', 'text-purple-700');
                btn.classList.add('text-gray-700');
            });
            
            // Add active class to clicked button
            this.classList.remove('text-gray-700');
            this.classList.add('bg-purple-50', 'border-purple-200', 'text-purple-700');
            
            // Apply filter based on period
            const period = this.textContent.toLowerCase().replace(' ', '_');
            applyDateFilter(period);
        });
    });
    
    // Apply custom date range
    const applyBtn = document.querySelector('.bg-purple-600');
    if (applyBtn) {
        applyBtn.addEventListener('click', function() {
            const startDate = document.querySelector('input[type="date"]:first-of-type').value;
            const endDate = document.querySelector('input[type="date"]:last-of-type').value;
            
            if (startDate && endDate) {
                Loading.show('Applying date filter...');
                setTimeout(() => {
                    Toast.success('Filter Applied', `Showing data from ${startDate} to ${endDate}`);
                    Loading.hide();
                }, 1000);
            }
        });
    }
});

function applyDateFilter(period) {
    Loading.show(`Loading ${period} data...`);
    
    // Simulate API call
    setTimeout(() => {
        let message = '';
        switch(period) {
            case 'today':
                message = 'Showing today\'s earnings data';
                break;
            case 'this_week':
                message = 'Showing this week\'s earnings data';
                break;
            case 'this_month':
                message = 'Showing this month\'s earnings data';
                break;
            case 'this_year':
                message = 'Showing this year\'s earnings data';
                break;
        }
        
        Toast.success('Filter Applied', message);
        Loading.hide();
    }, 800);
}

// Export statement
function exportStatement() {
    Loading.show('Generating statement...');
    setTimeout(() => {
        Toast.success('Statement Ready', 'Your earnings statement has been generated.');
        // In real app, trigger file download
        Loading.hide();
    }, 1500);
}
</script>

<style>
/* Earnings Dashboard Styles */
.gradient-card {
    transition: all 0.3s ease;
}

.gradient-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
}

/* Chart placeholder animation */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

.chart-placeholder {
    animation: pulse 2s ease-in-out infinite;
}

/* Transaction row hover effect */
tbody tr {
    transition: background-color 0.15s ease;
}

/* Status badge animation */
.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Amount colors */
.amount-positive {
    color: #10b981;
}

.amount-negative {
    color: #ef4444;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .date-range-selector {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .custom-date-range {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .grid-cols-4 {
        grid-template-columns: repeat(2, 1fr);
    }
}

/* Loading animation for charts */
.loading-chart {
    position: relative;
    overflow: hidden;
}

.loading-chart::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}
</style>
@endsection