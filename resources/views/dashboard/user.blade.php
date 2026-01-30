@extends('dashboard')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, ' . Auth::user()->name . '!')

@section('breadcrumbs')
    @php
        $breadcrumbs = [
            ['title' => 'Dashboard', 'url' => route('dashboard')]
        ];
    @endphp
@endsection

@section('content')
<div class="p-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Active Bookings -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Active Bookings</p>
                    <p class="text-3xl font-bold mt-2">2</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-400 bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-home text-xl"></i>
                </div>
            </div>
            <!-- USING EXISTING ROUTE: rental.index -->
            <a href="{{ route('rental.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                View all <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Pending Orders -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Pending Orders</p>
                    <p class="text-3xl font-bold mt-2">3</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-400 bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-xl"></i>
                </div>
            </div>
            <!-- USING EXISTING ROUTE: food.orders (since orders doesn't exist) -->
            <a href="{{ route('food.orders') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                View all <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Total Spent -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Total Spent</p>
                    <p class="text-3xl font-bold mt-2">৳ 12,500</p>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-400 bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
            </div>
            <!-- USING EXISTING ROUTE: payments.index -->
            <a href="{{ route('payments.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                View details <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Rating -->
        <div class="bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-xl shadow-lg text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-90">Your Rating</p>
                    <div class="flex items-center mt-2">
                        <span class="text-3xl font-bold">4.5</span>
                        <div class="ml-2">
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-sm {{ $i <= 4.5 ? 'text-yellow-300' : 'text-gray-300' }}"></i>
                                @endfor
                            </div>
                            <p class="text-xs opacity-90">Based on 12 reviews</p>
                        </div>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-full bg-yellow-400 bg-opacity-20 flex items-center justify-center">
                    <i class="fas fa-star text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl shadow-lg text-white p-8 mb-8">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="md:w-2/3">
                <h2 class="text-2xl font-bold mb-2">Welcome to RMS!</h2>
                <p class="text-indigo-100 mb-4">
                    You're logged in as a regular user. Complete your profile to get better recommendations 
                    and start booking properties or ordering services.
                </p>
                <div class="flex flex-wrap gap-3">
                    @if(!Auth::user()->phone || !Auth::user()->gender)
                        <a href="{{ route('profile.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-user-edit mr-2"></i> Complete Profile
                        </a>
                    @endif
                    
                    @if(!Auth::user()->defaultAddress)
                        <a href="{{ route('profile.address.edit') }}" 
                           class="inline-flex items-center px-4 py-2 bg-white text-indigo-600 rounded-lg hover:bg-gray-100">
                            <i class="fas fa-map-marker-alt mr-2"></i> Add Address
                        </a>
                    @endif

                    @if(auth()->user()->hasRole('USER') && !auth()->user()->isOwner() && !auth()->user()->isFoodProvider() && !auth()->user()->isLaundryProvider())
                        <a href="{{ route('role.apply.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                            <i class="fas fa-user-plus mr-2"></i> Apply for Provider Role
                        </a>
                    @endif
                </div>
            </div>
            <div class="mt-6 md:mt-0">
                <div class="h-40 w-40 rounded-full bg-white bg-opacity-10 flex items-center justify-center border-4 border-white border-opacity-20">
                    <i class="fas fa-home text-6xl text-white opacity-80"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Find Properties -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-blue-100 flex items-center justify-center">
                        <i class="fas fa-search text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Find Properties</h4>
                        <p class="text-sm text-gray-500">Browse hostels & apartments</p>
                    </div>
                </div>
                <!-- USING EXISTING ROUTE: rental.index (properties doesn't exist) -->
                <a href="{{ route('rental.search') }}" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium">
                    Start Searching <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Order Food -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-green-100 flex items-center justify-center">
                        <i class="fas fa-utensils text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Order Food</h4>
                        <p class="text-sm text-gray-500">Subscribe or order meals</p>
                    </div>
                </div>
                <!-- USING EXISTING ROUTE: food.index (food.services doesn't exist) -->
                <a href="#" 
                   class="inline-flex items-center text-green-600 hover:text-green-700 font-medium">
                    View Restaurants <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>

            <!-- Laundry Service -->
            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center mb-4">
                    <div class="h-12 w-12 rounded-lg bg-purple-100 flex items-center justify-center">
                        <i class="fas fa-tshirt text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h4 class="text-lg font-medium text-gray-900">Laundry Service</h4>
                        <p class="text-sm text-gray-500">Schedule pickup & delivery</p>
                    </div>
                </div>
                <!-- USING EXISTING ROUTE: laundry.index (laundry.services doesn't exist) -->
                <a href="#" 
                   class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium">
                    Find Services <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Activity & Quick Stats -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Activity -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Recent Activity</h3>
                <!-- REMOVED: activity link since route doesn't exist -->
                <span class="text-sm text-gray-400">Recent activity</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="divide-y divide-gray-100">
                    @php
                        $activities = [
                            ['icon' => 'fa-home', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 
                             'title' => 'Booking Confirmed', 'desc' => 'Sunrise Hostel - Room 203', 
                             'time' => '2 hours ago', 'status' => 'success'],
                            ['icon' => 'fa-utensils', 'color' => 'text-green-500', 'bg' => 'bg-green-50', 
                             'title' => 'Food Order Delivered', 'desc' => 'Spicy Bites Restaurant', 
                             'time' => 'Yesterday', 'status' => 'success'],
                            ['icon' => 'fa-tshirt', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 
                             'title' => 'Laundry Pickup Scheduled', 'desc' => 'Fresh Clean Laundry', 
                             'time' => '2 days ago', 'status' => 'pending'],
                            ['icon' => 'fa-user-check', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 
                             'title' => 'Profile Updated', 'desc' => 'Added phone number', 
                             'time' => '3 days ago', 'status' => 'info'],
                        ];
                    @endphp
                    
                    @foreach($activities as $activity)
                        <div class="p-4 hover:bg-gray-50">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg {{ $activity['bg'] }} flex items-center justify-center">
                                        <i class="fas {{ $activity['icon'] }} {{ $activity['color'] }}"></i>
                                    </div>
                                </div>
                                <div class="ml-4 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-medium text-gray-900">{{ $activity['title'] }}</p>
                                        <span class="text-xs text-gray-500">{{ $activity['time'] }}</span>
                                    </div>
                                    <p class="text-sm text-gray-500 mt-1">{{ $activity['desc'] }}</p>
                                    <div class="mt-2">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                            {{ $activity['status'] == 'success' ? 'bg-green-100 text-green-800' : 
                                              ($activity['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucfirst($activity['status']) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Upcoming Bookings -->
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-gray-900">Upcoming Bookings</h3>
                <!-- REMOVED: bookings link since route doesn't exist -->
                <span class="text-sm text-gray-400">Your bookings</span>
            </div>
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                @php
                    $bookings = [
                        ['property' => 'Sunrise Hostel', 'room' => 'Room 203', 
                         'dates' => 'Jan 25 - Feb 25, 2024', 'status' => 'confirmed', 'amount' => '৳ 8,500'],
                        ['property' => 'Green Valley Apartment', 'room' => 'Unit 4B', 
                         'dates' => 'Feb 1 - Aug 1, 2024', 'status' => 'upcoming', 'amount' => '৳ 45,000'],
                    ];
                @endphp
                
                @foreach($bookings as $booking)
                    <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ $booking['property'] }}</h4>
                                <p class="text-sm text-gray-500">{{ $booking['room'] }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ $booking['dates'] }}</p>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                    {{ $booking['status'] == 'confirmed' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                    {{ ucfirst($booking['status']) }}
                                </span>
                                <p class="text-sm font-medium text-gray-900 mt-1">{{ $booking['amount'] }}</p>
                            </div>
                        </div>
                        <div class="flex space-x-2 mt-3">
                            <a href="#" class="flex-1 text-center px-3 py-2 text-xs font-medium bg-indigo-50 text-indigo-600 rounded hover:bg-indigo-100">
                                <i class="fas fa-eye mr-1"></i> View
                            </a>
                            <a href="#" class="flex-1 text-center px-3 py-2 text-xs font-medium bg-gray-50 text-gray-600 rounded hover:bg-gray-100">
                                <i class="fas fa-calendar mr-1"></i> Reschedule
                            </a>
                            <a href="#" class="flex-1 text-center px-3 py-2 text-xs font-medium bg-red-50 text-red-600 rounded hover:bg-red-100">
                                <i class="fas fa-times mr-1"></i> Cancel
                            </a>
                        </div>
                    </div>
                @endforeach
                
                @if(count($bookings) === 0)
                    <div class="p-8 text-center">
                        <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">No upcoming bookings</p>
                        <!-- USING EXISTING ROUTE: rental.index -->
                        <a href="{{ route('rental.index') }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-500">
                            <i class="fas fa-search mr-1"></i> Find Properties
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Profile Completion -->
    @if(!Auth::user()->phone || !Auth::user()->gender || !Auth::user()->defaultAddress)
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-100 rounded-xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Complete Your Profile</h3>
                    <p class="text-gray-600">Complete your profile to get personalized recommendations</p>
                    
                    <div class="mt-4 space-y-3">
                        @if(!Auth::user()->phone)
                            <div class="flex items-center">
                                <i class="fas fa-phone text-gray-400 mr-3"></i>
                                <span class="text-gray-700">Add phone number</span>
                            </div>
                        @endif
                        
                        @if(!Auth::user()->gender)
                            <div class="flex items-center">
                                <i class="fas fa-venus-mars text-gray-400 mr-3"></i>
                                <span class="text-gray-700">Specify gender</span>
                            </div>
                        @endif
                        
                        @if(!Auth::user()->defaultAddress)
                            <div class="flex items-center">
                                <i class="fas fa-map-marker-alt text-gray-400 mr-3"></i>
                                <span class="text-gray-700">Add delivery address</span>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="text-center">
                    @php
                        $totalFields = 3;
                        $completedFields = 0;
                        if(Auth::user()->phone) $completedFields++;
                        if(Auth::user()->gender) $completedFields++;
                        if(Auth::user()->defaultAddress) $completedFields++;
                        $percentage = ($completedFields / $totalFields) * 100;
                    @endphp
                    
                    <div class="relative w-32 h-32">
                        <svg class="w-full h-full" viewBox="0 0 36 36">
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#E5E7EB" stroke-width="3"/>
                            <path d="M18 2.0845
                                    a 15.9155 15.9155 0 0 1 0 31.831
                                    a 15.9155 15.9155 0 0 1 0 -31.831"
                                  fill="none" stroke="#4F46E5" stroke-width="3" stroke-dasharray="{{ $percentage }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-2xl font-bold text-gray-900">{{ round($percentage) }}%</span>
                        </div>
                    </div>
                    <a href="{{ route('profile.edit') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-user-edit mr-2"></i> Complete Now
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection