@extends('layouts.food-provider')

@section('title', 'Advanced Statistics')

@section('header', 'Advanced Statistics')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:text-3xl">
                Advanced Statistics
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Detailed analytics and insights for your restaurant
            </p>
        </div>
        <div class="mt-4 sm:mt-0">
            <div class="flex items-center space-x-3">
                <select class="block w-full sm:w-auto pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option>Last 7 days</option>
                    <option selected>Last 30 days</option>
                    <option>Last 90 days</option>
                    <option>Last 12 months</option>
                </select>
                <button type="button" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-download mr-2"></i>
                    Export Data
                </button>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <i class="fas fa-shopping-cart text-blue-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Orders
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                1,248
                            </dd>
                            <dd class="text-xs text-green-600 mt-1">
                                ↑ 12% from last month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <i class="fas fa-rupee-sign text-green-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Total Revenue
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                MMK 2,45,680
                            </dd>
                            <dd class="text-xs text-green-600 mt-1">
                                ↑ 18% from last month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <i class="fas fa-users text-purple-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Active Customers
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                186
                            </dd>
                            <dd class="text-xs text-green-600 mt-1">
                                ↑ 8% from last month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                        <i class="fas fa-percentage text-yellow-600 h-6 w-6"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">
                                Avg. Order Value
                            </dt>
                            <dd class="text-2xl font-semibold text-gray-900">
                                MMK 320
                            </dd>
                            <dd class="text-xs text-red-600 mt-1">
                                ↓ 2% from last month
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Revenue Chart -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Revenue Trend
                </h3>
            </div>
            <div class="p-6">
                <div class="h-80 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-line text-gray-400 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-500">Revenue trend chart will be displayed here</p>
                        <p class="text-xs text-gray-400 mt-1">(Integration with chart library required)</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-4 gap-4 text-center">
                    <div>
                        <div class="text-lg font-bold text-gray-900">₹42,560</div>
                        <div class="text-sm text-gray-500">This Week</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">₹1,45,240</div>
                        <div class="text-sm text-gray-500">This Month</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">₹4,25,680</div>
                        <div class="text-sm text-gray-500">This Quarter</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">₹18,24,560</div>
                        <div class="text-sm text-gray-500">This Year</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Volume -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Order Volume
                </h3>
            </div>
            <div class="p-6">
                <div class="h-80 flex items-center justify-center bg-gray-50 rounded-lg">
                    <div class="text-center">
                        <i class="fas fa-chart-bar text-gray-400 text-4xl mb-3"></i>
                        <p class="text-sm text-gray-500">Order volume chart will be displayed here</p>
                        <p class="text-xs text-gray-400 mt-1">(Integration with chart library required)</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg font-bold text-gray-900">142</div>
                        <div class="text-sm text-gray-500">This Week</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">648</div>
                        <div class="text-sm text-gray-500">This Month</div>
                    </div>
                    <div>
                        <div class="text-lg font-bold text-gray-900">1,248</div>
                        <div class="text-sm text-gray-500">This Quarter</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Analytics -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                Customer Analytics
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Customer Segmentation -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Customer Segmentation</h4>
                    <div class="space-y-3">
                        @foreach([
                            ['segment' => 'New Customers', 'count' => 42, 'percentage' => 22.6, 'color' => 'bg-blue-500'],
                            ['segment' => 'Regular Customers', 'count' => 96, 'percentage' => 51.6, 'color' => 'bg-green-500'],
                            ['segment' => 'VIP Customers', 'count' => 24, 'percentage' => 12.9, 'color' => 'bg-purple-500'],
                            ['segment' => 'Inactive Customers', 'count' => 24, 'percentage' => 12.9, 'color' => 'bg-gray-500']
                        ] as $segment)
                        @php
                            $width = $segment['percentage'];
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">{{ $segment['segment'] }}</span>
                                <span class="font-medium">{{ $segment['count'] }} ({{ $segment['percentage'] }}%)</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full {{ $segment['color'] }}" style="<?php echo 'width: '.$width.'%'; ?>"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Repeat Order Rate -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Repeat Order Rate</h4>
                    <div class="flex items-center justify-center h-48">
                        <div class="relative">
                            <div class="h-32 w-32 rounded-full border-8 border-gray-200"></div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="text-center">
                                    <div class="text-3xl font-bold text-gray-900">72%</div>
                                    <div class="text-sm text-gray-500">Repeat Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center text-sm text-gray-500 mt-2">
                        134 out of 186 customers ordered again
                    </div>
                </div>

                <!-- Customer Acquisition -->
                <div>
                    <h4 class="text-sm font-medium text-gray-900 mb-3">Customer Acquisition</h4>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Word of Mouth</span>
                                <span class="font-medium">42%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-green-500 rounded-full" style="width: 42%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Platform Discovery</span>
                                <span class="font-medium">35%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width: 35%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Social Media</span>
                                <span class="font-medium">18%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-purple-500 rounded-full" style="width: 18%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Other</span>
                                <span class="font-medium">5%</span>
                            </div>
                            <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gray-500 rounded-full" style="width: 5%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Popular Items -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Most Popular Items
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach([
                        ['name' => 'Butter Chicken', 'orders' => 148, 'revenue' => '₹47,360', 'percentage' => 85],
                        ['name' => 'Chicken Biryani', 'orders' => 96, 'revenue' => '₹26,880', 'percentage' => 65],
                        ['name' => 'Paneer Tikka', 'orders' => 84, 'revenue' => '₹23,520', 'percentage' => 55],
                        ['name' => 'Fish Curry', 'orders' => 72, 'revenue' => '₹25,200', 'percentage' => 45],
                        ['name' => 'Vegetable Biryani', 'orders' => 64, 'revenue' => '₹17,920', 'percentage' => 35]
                    ] as $item)
                    @php
                        $width = $item['percentage'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-900">{{ $item['name'] }}</span>
                            <span class="text-gray-600">{{ $item['orders'] }} orders ({{ $item['revenue'] }})</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="<?php echo 'width: '.$width.'%'; ?>"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Peak Hours -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">
                    Peak Order Hours
                </h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach([
                        ['time' => '12:00 PM - 1:00 PM', 'orders' => 42, 'percentage' => 95],
                        ['time' => '1:00 PM - 2:00 PM', 'orders' => 36, 'percentage' => 80],
                        ['time' => '7:00 PM - 8:00 PM', 'orders' => 32, 'percentage' => 70],
                        ['time' => '8:00 PM - 9:00 PM', 'orders' => 28, 'percentage' => 60],
                        ['time' => '11:00 AM - 12:00 PM', 'orders' => 24, 'percentage' => 50]
                    ] as $peak)
                    @php
                        $width = $peak['percentage'];
                    @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="font-medium text-gray-900">{{ $peak['time'] }}</span>
                            <span class="text-gray-600">{{ $peak['orders'] }} orders</span>
                        </div>
                        <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 rounded-full" style="<?php echo 'width: '.$width.'%'; ?>"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                    <div class="flex items-center">
                        <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                        <p class="text-sm text-blue-700">
                            <span class="font-medium">Recommendation:</span> Schedule extra staff during peak hours (12 PM - 2 PM & 7 PM - 9 PM)
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Commission Summary -->
    <div class="bg-white shadow-sm sm:rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">
                Commission & Earnings Summary
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="text-2xl font-bold text-gray-900">₹2,45,680</div>
                    <div class="text-sm text-gray-500 mt-1">Total Revenue</div>
                </div>
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="text-2xl font-bold text-red-600">₹29,481</div>
                    <div class="text-sm text-gray-500 mt-1">Total Commission (12%)</div>
                </div>
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="text-2xl font-bold text-green-600">₹2,16,199</div>
                    <div class="text-sm text-gray-500 mt-1">Your Earnings</div>
                </div>
                <div class="text-center p-4 border border-gray-200 rounded-lg">
                    <div class="text-2xl font-bold text-blue-600">88%</div>
                    <div class="text-sm text-gray-500 mt-1">Earnings Ratio</div>
                </div>
            </div>
            <div class="mt-6">
                <div class="flex justify-between text-sm text-gray-500 mb-2">
                    <span>Commission Breakdown</span>
                    <span>Total: 12% average</span>
                </div>
                <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full bg-green-500 rounded-full" style="width: 88%"></div>
                    <div class="h-full bg-red-500 rounded-full -ml-1" style="width: 12%"></div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Your Earnings (88%)</span>
                    <span>Platform Commission (12%)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection