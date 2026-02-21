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
    <!-- Dashboard Tabs -->
    <div class="mb-6 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="dashboardTabs" role="tablist">
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 rounded-t-lg" 
                        id="overview-tab" 
                        data-tabs-target="#overview" 
                        type="button" 
                        role="tab" 
                        aria-controls="overview" 
                        aria-selected="true">
                    <i class="fas fa-chart-pie mr-2"></i>Overview
                </button>
            </li>
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                        id="chat-tab" 
                        data-tabs-target="#chat" 
                        type="button" 
                        role="tab" 
                        aria-controls="chat" 
                        aria-selected="false">
                    <i class="fas fa-comments mr-2"></i>
                    Messages
                    <span id="chat-unread-badge-header" class="ml-2 bg-red-500 text-white text-xs font-medium px-2 py-0.5 rounded-full" style="display: none;">0</span>
                </button>
            </li>
            @if(Auth::user()->hasRole('OWNER'))
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                        id="properties-tab" 
                        data-tabs-target="#properties" 
                        type="button" 
                        role="tab" 
                        aria-controls="properties" 
                        aria-selected="false">
                    <i class="fas fa-building mr-2"></i>My Properties
                </button>
            </li>
            @endif
            @if(Auth::user()->hasRole('FOOD') || Auth::user()->hasRole('LAUNDRY'))
            <li class="mr-2" role="presentation">
                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" 
                        id="services-tab" 
                        data-tabs-target="#services" 
                        type="button" 
                        role="tab" 
                        aria-controls="services" 
                        aria-selected="false">
                    <i class="fas fa-concierge-bell mr-2"></i>My Services
                </button>
            </li>
            @endif
        </ul>
    </div>

    <!-- Tab Content -->
    <div id="tabContent">
        <!-- Overview Tab (Existing Dashboard Content) -->
        <div class="tab-pane active" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <!-- Stats Cards - Updated with green/teal colors -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Active Bookings -->
                <div class="bg-gradient-to-r from-[#174455] to-[#286b7f] rounded-xl shadow-lg text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Active Bookings</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['active_bookings'] ?? 0 }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-home text-xl" style="color: #ffdb9f;"></i>
                        </div>
                    </div>
                    <a href="{{ route('bookings.my-bookings') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                        View all <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Pending Orders -->
                <div class="bg-gradient-to-r from-[#1f556b] to-[#2d7a94] rounded-xl shadow-lg text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Pending Orders</p>
                            <p class="text-3xl font-bold mt-2">{{ $stats['pending_orders'] ?? 0 }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-xl" style="color: #ffdb9f;"></i>
                        </div>
                    </div>
                    @if(isset($stats['pending_orders']) && $stats['pending_orders'] > 0)
                        <a href="{{ route('food.orders') ?? route('food.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                            View all <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    @else
                        <span class="text-sm opacity-90 inline-flex items-center mt-4">
                            No pending orders
                        </span>
                    @endif
                </div>

                <!-- Total Spent -->
                <div class="bg-gradient-to-r from-[#286b7f] to-[#3a8da6] rounded-xl shadow-lg text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90">Total Spent</p>
                            <p class="text-3xl font-bold mt-2">৳ {{ number_format($stats['total_spent'] ?? 0) }}</p>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-[#ffdb9f] bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-wallet text-xl" style="color: #ffdb9f;"></i>
                        </div>
                    </div>
                    <a href="{{ route('payments.index') }}" class="text-sm opacity-90 hover:opacity-100 inline-flex items-center mt-4">
                        View details <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Rating -->
                <div class="bg-gradient-to-r from-[#ffdb9f] to-[#f8c570] rounded-xl shadow-lg text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm opacity-90 text-[#174455]">Your Rating</p>
                            <div class="flex items-center mt-2">
                                <span class="text-3xl font-bold text-[#174455]">{{ number_format($stats['avg_rating'] ?? 0, 1) }}</span>
                                <div class="ml-2">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-sm {{ $i <= ($stats['avg_rating'] ?? 0) ? 'text-[#174455]' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-xs opacity-90 text-[#174455]">Based on {{ $stats['total_reviews'] ?? 0 }} reviews</p>
                                </div>
                            </div>
                        </div>
                        <div class="h-12 w-12 rounded-full bg-[#174455] bg-opacity-20 flex items-center justify-center">
                            <i class="fas fa-star text-xl" style="color: #174455;"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Welcome Banner - Updated with green/teal gradient -->
            <div class="bg-gradient-to-r from-[#174455] to-[#286b7f] rounded-xl shadow-lg text-white p-8 mb-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="md:w-2/3">
                        <h2 class="text-2xl font-bold mb-2">Welcome to RMS!</h2>
                        <p class="text-[#ffdb9f] mb-4">
                            @if(Auth::user()->hasRole('OWNER'))
                                You're logged in as a property owner. Manage your properties and bookings from here.
                            @elseif(Auth::user()->hasRole('LAUNDRY'))
                                You're logged in as a laundry service provider. Manage your orders and services.
                            @elseif(Auth::user()->hasRole('FOOD'))
                                You're logged in as a food service provider. Manage your menu and orders.
                            @else
                                You're logged in as a regular user. Complete your profile to get better recommendations 
                                and start booking properties or ordering services.
                            @endif
                        </p>
                        <div class="flex flex-wrap gap-3">
                            @if(!Auth::user()->phone || !Auth::user()->gender)
                                <a href="{{ route('profile.edit') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                                    <i class="fas fa-user-edit mr-2"></i> Complete Profile
                                </a>
                            @endif
                            
                            @if(empty($userAddresses))
                                <a href="{{ route('profile.address') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                                    <i class="fas fa-map-marker-alt mr-2"></i> Add Address
                                </a>
                            @endif

                            @if(auth()->user()->hasRole('USER') && !auth()->user()->hasRole('OWNER') && !auth()->user()->hasRole('FOOD') && !auth()->user()->hasRole('LAUNDRY'))
                                <a href="{{ route('role.apply.index') }}" 
                                   class="inline-flex items-center px-4 py-2 bg-[#ffdb9f] text-[#174455] rounded-lg hover:bg-[#f8c570] transition">
                                    <i class="fas fa-user-plus mr-2"></i> Apply for Provider Role
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="mt-6 md:mt-0">
                        <div class="h-40 w-40 rounded-full bg-[#ffdb9f] bg-opacity-10 flex items-center justify-center border-4 border-[#ffdb9f] border-opacity-20">
                            @if(Auth::user()->avatar_url)
                                <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="h-36 w-36 rounded-full object-cover">
                            @else
                                <i class="fas fa-user text-6xl text-[#ffdb9f] opacity-80"></i>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Role-specific Content -->
            @if(Auth::user()->hasRole('OWNER'))
                @include('dashboard.parts.owner-dashboard')
            @elseif(Auth::user()->hasRole('LAUNDRY') || Auth::user()->hasRole('FOOD'))
                @include('dashboard.parts.provider-dashboard')
            @else
                <!-- Quick Actions for Regular Users - Updated colors -->
                <div class="mb-8">
                    <h3 class="text-xl font-semibold text-[#174455] mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <!-- Find Properties -->
                        <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-lg bg-[#174455] bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-search text-[#174455] text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-[#174455]">Find Properties</h4>
                                    <p class="text-sm text-gray-500">Browse hostels & apartments</p>
                                </div>
                            </div>
                            <a href="{{ route('properties.search') }}" 
                               class="inline-flex items-center text-[#174455] hover:text-[#286b7f] font-medium">
                                Start Searching <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>

                        <!-- Order Food -->
                        <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-lg bg-[#1f556b] bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-utensils text-[#1f556b] text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-[#174455]">Order Food</h4>
                                    <p class="text-sm text-gray-500">Subscribe or order meals</p>
                                </div>
                            </div>
                            <a href="{{ route('food.index') }}" 
                               class="inline-flex items-center text-[#1f556b] hover:text-[#286b7f] font-medium">
                                View Restaurants <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>

                        <!-- Laundry Service -->
                        <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl p-6 hover:shadow-md transition-shadow">
                            <div class="flex items-center mb-4">
                                <div class="h-12 w-12 rounded-lg bg-[#286b7f] bg-opacity-10 flex items-center justify-center">
                                    <i class="fas fa-tshirt text-[#286b7f] text-xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h4 class="text-lg font-medium text-[#174455]">Laundry Service</h4>
                                    <p class="text-sm text-gray-500">Schedule pickup & delivery</p>
                                </div>
                            </div>
                            <a href="{{ route('laundry.index') }}" 
                               class="inline-flex items-center text-[#286b7f] hover:text-[#174455] font-medium">
                                Find Services <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Recent Activity & Quick Stats -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Recent Activity -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-[#174455]">Recent Activity</h3>
                        <a href="#" class="text-sm text-[#286b7f] hover:text-[#174455]">
                            View all
                        </a>
                    </div>
                    <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl overflow-hidden">
                        <div class="divide-y divide-gray-100">
                            @forelse($recentActivities ?? [] as $activity)
                                <div class="p-4 hover:bg-gray-50">
                                    <div class="flex">
                                        <div class="flex-shrink-0">
                                            @php
                                                $iconConfig = [
                                                    'BOOKING' => ['icon' => 'fa-home', 'color' => 'text-[#174455]', 'bg' => 'bg-[#174455] bg-opacity-10'],
                                                    'ORDER' => ['icon' => 'fa-shopping-bag', 'color' => 'text-[#1f556b]', 'bg' => 'bg-[#1f556b] bg-opacity-10'],
                                                    'PAYMENT' => ['icon' => 'fa-wallet', 'color' => 'text-[#286b7f]', 'bg' => 'bg-[#286b7f] bg-opacity-10'],
                                                    'FOOD' => ['icon' => 'fa-utensils', 'color' => 'text-[#1f556b]', 'bg' => 'bg-[#1f556b] bg-opacity-10'],
                                                    'LAUNDRY' => ['icon' => 'fa-tshirt', 'color' => 'text-[#286b7f]', 'bg' => 'bg-[#286b7f] bg-opacity-10'],
                                                    'MESSAGE' => ['icon' => 'fa-comment', 'color' => 'text-[#174455]', 'bg' => 'bg-[#174455] bg-opacity-10'],
                                                    'default' => ['icon' => 'fa-bell', 'color' => 'text-gray-500', 'bg' => 'bg-gray-50']
                                                ];
                                                $config = $iconConfig[$activity->type] ?? $iconConfig['default'];
                                            @endphp
                                            <div class="h-10 w-10 rounded-lg {{ $config['bg'] }} flex items-center justify-center">
                                                <i class="fas {{ $config['icon'] }} {{ $config['color'] }}"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center justify-between">
                                                <p class="text-sm font-medium text-gray-900">{{ $activity->title }}</p>
                                                <span class="text-xs text-gray-500">{{ $activity->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-sm text-gray-500 mt-1">{{ $activity->message }}</p>
                                            @if($activity->related_entity_type && $activity->related_entity_id)
                                                <div class="mt-2">
                                                    <a href="#" class="text-xs text-[#286b7f] hover:text-[#174455]">
                                                        View Details
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-bell text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No recent activity</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Upcoming Bookings/Orders -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-semibold text-[#174455]">
                            @if(Auth::user()->hasRole('USER'))
                                Upcoming Bookings
                            @elseif(Auth::user()->hasRole('OWNER'))
                                Recent Bookings
                            @else
                                Recent Orders
                            @endif
                        </h3>
                        @if(Auth::user()->hasRole('USER'))
                            <a href="{{ route('bookings.my-bookings') }}" class="text-sm text-[#286b7f] hover:text-[#174455]">
                                View all
                            </a>
                        @endif
                    </div>
                    <div class="bg-white border border-[#286b7f] border-opacity-20 rounded-xl overflow-hidden">
                        @if(Auth::user()->hasRole('USER'))
                             @forelse($upcomingBookings ?? [] as $booking)
                                <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $booking->property->name }}</h4>
                                            <p class="text-sm text-gray-500">
                                                @if($booking->room)
                                                    {{ $booking->room->room_number }}
                                                @else
                                                    Apartment
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1">
                                                {{ \Carbon\Carbon::parse($booking->check_in)->format('M d') }} - 
                                                {{ \Carbon\Carbon::parse($booking->check_out)->format('M d, Y') }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            @php
                                                $statusColors = [
                                                    'PENDING' => 'bg-yellow-100 text-yellow-800',
                                                    'CONFIRMED' => 'bg-green-100 text-green-800',
                                                    'CHECKED_IN' => 'bg-blue-100 text-blue-800',
                                                    'CHECKED_OUT' => 'bg-gray-100 text-gray-800',
                                                    'CANCELLED' => 'bg-red-100 text-red-800'
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $statusColors[$booking->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                {{ str_replace('_', ' ', $booking->status) }}
                                            </span>
                                            <p class="text-sm font-medium text-gray-900 mt-1">৳ {{ number_format($booking->total_amount) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 mt-3">
                                        <a href="{{ route('bookings.show', $booking) }}" class="flex-1 text-center px-3 py-2 text-xs font-medium bg-[#174455] bg-opacity-10 text-[#174455] rounded hover:bg-opacity-20">
                                            <i class="fas fa-eye mr-1"></i> View
                                        </a>
                                        @if($booking->status == 'CONFIRMED')
                                            <a href="{{ route('bookings.reschedule', $booking) }}" class="flex-1 text-center px-3 py-2 text-xs font-medium bg-[#ffdb9f] text-[#174455] rounded hover:bg-[#f8c570]">
                                                <i class="fas fa-calendar mr-1"></i> Reschedule
                                            </a>
                                            <form action="{{ route('bookings.cancel', $booking) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="w-full px-3 py-2 text-xs font-medium bg-red-50 text-red-600 rounded hover:bg-red-100">
                                                    <i class="fas fa-times mr-1"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No upcoming bookings</p>
                                    <a href="{{ route('properties.search') }}" class="mt-3 inline-flex items-center text-sm text-[#174455] hover:text-[#286b7f]">
                                        <i class="fas fa-search mr-1"></i> Find Properties
                                    </a>
                                </div>
                            @endforelse
                        @elseif(Auth::user()->hasRole('OWNER'))
                            <!-- Owner specific content -->
                            @forelse($recentBookings as $booking)
                                <!-- Similar booking display for owner -->
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-calendar text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No recent bookings</p>
                                </div>
                            @endforelse
                        @else
                            <!-- Service provider orders -->
                            @forelse($recentOrders as $order)
                                <!-- Display recent orders for service providers -->
                            @empty
                                <div class="p-8 text-center">
                                    <i class="fas fa-shopping-bag text-gray-300 text-4xl mb-3"></i>
                                    <p class="text-gray-500">No recent orders</p>
                                </div>
                            @endforelse
                        @endif
                    </div>
                </div>
            </div>

            <!-- Profile Completion - Updated colors -->
            @php
                $totalFields = 3;
                $completedFields = 0;
                if(Auth::user()->phone) $completedFields++;
                if(Auth::user()->gender) $completedFields++;
                if(!empty($userAddresses)) $completedFields++;
                $percentage = ($completedFields / $totalFields) * 100;
            @endphp
            
            @if($percentage < 100)
                <div class="mt-8 bg-gradient-to-r from-[#174455] to-[#286b7f] bg-opacity-5 border border-[#286b7f] border-opacity-20 rounded-xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-[#174455] mb-2">Complete Your Profile</h3>
                            <p class="text-gray-600">Complete your profile to get personalized recommendations</p>
                            
                            <div class="mt-4 space-y-3">
                                @if(!Auth::user()->phone)
                                    <div class="flex items-center">
                                        <i class="fas fa-phone text-[#286b7f] mr-3"></i>
                                        <span class="text-gray-700">Add phone number</span>
                                    </div>
                                @endif
                                
                                @if(!Auth::user()->gender)
                                    <div class="flex items-center">
                                        <i class="fas fa-venus-mars text-[#286b7f] mr-3"></i>
                                        <span class="text-gray-700">Specify gender</span>
                                    </div>
                                @endif
                                
                                @if(empty($userAddresses))
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-[#286b7f] mr-3"></i>
                                        <span class="text-gray-700">Add delivery address</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <div class="relative w-32 h-32">
                                <svg class="w-full h-full" viewBox="0 0 36 36">
                                    <path d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#E5E7EB" stroke-width="3"/>
                                    <path d="M18 2.0845
                                            a 15.9155 15.9155 0 0 1 0 31.831
                                            a 15.9155 15.9155 0 0 1 0 -31.831"
                                          fill="none" stroke="#174455" stroke-width="3" stroke-dasharray="{{ $percentage }}, 100"/>
                                </svg>
                                <div class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-2xl font-bold text-[#174455]">{{ round($percentage) }}%</span>
                                </div>
                            </div>
                            <a href="{{ route('profile.edit') }}" 
                               class="mt-4 inline-flex items-center px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#286b7f] transition">
                                <i class="fas fa-user-edit mr-2"></i> Complete Now
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Chat Tab -->
        <div class="tab-pane hidden" id="chat" role="tabpanel" aria-labelledby="chat-tab">
            <div class="bg-white rounded-xl shadow-lg overflow-hidden" style="height: calc(100vh - 250px);">
                <div class="flex h-full">
                    <!-- Conversations List -->
                    <div class="w-1/3 border-r border-gray-200 bg-gray-50">
                        <div class="p-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-[#174455]">Messages</h3>
                            <div class="mt-2">
                                <input type="text" 
                                       placeholder="Search conversations..." 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#174455] focus:border-transparent"
                                       id="chat-search">
                            </div>
                        </div>
                        <div class="overflow-y-auto" style="height: calc(100% - 85px);" id="conversations-list">
                            @include('chat.partials.conversation-list')
                        </div>
                    </div>

                    <!-- Active Chat -->
                    <div class="w-2/3 flex flex-col" id="active-chat-container">
                        @if(isset($activeConversation))
                            @include('chat.partials.chat-window', ['conversation' => $activeConversation])
                        @else
                            <div class="flex-1 flex items-center justify-center bg-gray-50">
                                <div class="text-center">
                                    <div class="h-24 w-24 rounded-full bg-[#174455] bg-opacity-10 flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-comments text-4xl text-[#174455]"></i>
                                    </div>
                                    <h4 class="text-lg font-medium text-[#174455] mb-2">Your Messages</h4>
                                    <p class="text-gray-500 mb-4">Select a conversation to start chatting</p>
                                    <a href="{{ route('properties.search') }}" class="inline-flex items-center px-4 py-2 bg-[#174455] text-white rounded-lg hover:bg-[#286b7f] transition">
                                        <i class="fas fa-search mr-2"></i> Browse Properties
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties Tab (for Owners) -->
        @if(Auth::user()->hasRole('OWNER'))
        <div class="tab-pane hidden" id="properties" role="tabpanel" aria-labelledby="properties-tab">
            @include('dashboard.parts.owner-properties')
        </div>
        @endif

        <!-- Services Tab (for Providers) -->
        @if(Auth::user()->hasRole('FOOD') || Auth::user()->hasRole('LAUNDRY'))
        <div class="tab-pane hidden" id="services" role="tabpanel" aria-labelledby="services-tab">
            @include('dashboard.parts.provider-services')
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab functionality
    const tabs = document.querySelectorAll('[role="tab"]');
    const tabPanes = document.querySelectorAll('[role="tabpanel"]');

    function switchTab(oldTab, newTab) {
        newTab.focus();
        // Make the active tab
        tabs.forEach(tab => {
            tab.setAttribute('aria-selected', 'false');
            tab.classList.remove('border-b-2', 'border-[#174455]', 'text-[#174455]');
            tab.classList.add('border-transparent', 'text-gray-500');
        });
        newTab.classList.remove('border-transparent', 'text-gray-500');
        newTab.classList.add('border-b-2', 'border-[#174455]', 'text-[#174455]');
        
        // Show the active panel
        const activeId = newTab.getAttribute('data-tabs-target').substring(1);
        tabPanes.forEach(panel => {
            if (panel.id === activeId) {
                panel.classList.remove('hidden');
            } else {
                panel.classList.add('hidden');
            }
        });
    }

    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(document.querySelector('[aria-selected="true"]'), this);
        });
    });

    // Chat search functionality
    const chatSearch = document.getElementById('chat-search');
    if (chatSearch) {
        chatSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const conversations = document.querySelectorAll('.conversation-item');
            
            conversations.forEach(conv => {
                const name = conv.dataset.userName?.toLowerCase() || '';
                const property = conv.dataset.propertyName?.toLowerCase() || '';
                const lastMessage = conv.dataset.lastMessage?.toLowerCase() || '';
                
                if (name.includes(searchTerm) || property.includes(searchTerm) || lastMessage.includes(searchTerm)) {
                    conv.style.display = 'block';
                } else {
                    conv.style.display = 'none';
                }
            });
        });
    }

    // Update unread count periodically
    function updateUnreadCounts() {
        fetch('{{ route("chat.unread-count") }}')
            .then(response => response.json())
            .then(data => {
                const headerBadge = document.getElementById('chat-unread-badge-header');
                const tabBadge = document.getElementById('chat-unread-badge-tab');
                
                if (data.unread_count > 0) {
                    if (headerBadge) {
                        headerBadge.textContent = data.unread_count;
                        headerBadge.style.display = 'inline';
                    }
                    if (tabBadge) {
                        tabBadge.textContent = data.unread_count;
                        tabBadge.style.display = 'inline';
                    }
                } else {
                    if (headerBadge) headerBadge.style.display = 'none';
                    if (tabBadge) tabBadge.style.display = 'none';
                }
            });
    }

    // Update every 30 seconds
    setInterval(updateUnreadCounts, 30000);
    
    // Check URL hash for tab selection
    if (window.location.hash) {
        const tabId = window.location.hash.substring(1) + '-tab';
        const tab = document.getElementById(tabId);
        if (tab) {
            tab.click();
        }
    }
});

// Function to load conversation
function loadConversation(conversationId) {
    fetch(`/chat/${conversationId}/messages`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('active-chat-container').innerHTML = html;
            // Update URL hash without reloading
            window.location.hash = 'chat';
            
            // Mark messages as read
            fetch(`/chat/${conversationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
        });
}

// Function to send message
function sendMessage(conversationId) {
    const input = document.getElementById('message-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    fetch('{{ route("chat.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            conversation_id: conversationId,
            message: message
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            // Append message to chat
            appendMessage(data.message);
        }
    });
}

function appendMessage(message) {
    const container = document.getElementById('chat-messages');
    const isSender = message.sender_id == {{ Auth::id() }};
    
    const messageHtml = `
        <div class="mb-4 ${isSender ? 'text-right' : 'text-left'}">
            <div class="inline-block max-w-xs lg:max-w-md">
                <div class="rounded-lg px-4 py-2 ${isSender ? 'bg-[#174455] text-white' : 'bg-gray-100 text-gray-800'}">
                    <p class="whitespace-pre-wrap">${message.message}</p>
                </div>
                <span class="text-xs text-gray-500 mt-1 block">
                    ${new Date(message.created_at).toLocaleTimeString()}
                </span>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', messageHtml);
    container.scrollTop = container.scrollHeight;
}
</script>
@endpush

@push('styles')
<style>
/* Chat styles */
.conversation-item:hover {
    background-color: #f9fafb;
}

.conversation-item.active {
    background-color: #eef2f6;
    border-left: 3px solid #174455;
}

#chat-messages {
    scroll-behavior: smooth;
}

/* Hide scrollbar for chat but keep functionality */
#chat-messages::-webkit-scrollbar {
    width: 6px;
}

#chat-messages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#chat-messages::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#chat-messages::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Tab styles */
[role="tab"]:focus {
    outline: none;
}

[role="tab"][aria-selected="true"] {
    border-bottom-width: 2px;
    border-color: #174455;
    color: #174455;
}
</style>
@endpush
@endsection