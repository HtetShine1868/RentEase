@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('header', 'Dashboard')

@section('subtitle', 'Welcome back, ' . Auth::user()->name)

@section('content')
<div class="space-y-6">
    <!-- Welcome Card -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold mb-2">Welcome to Admin Dashboard</h2>
                <p class="text-indigo-100">Here's what's happening with your platform today.</p>
            </div>
            <div class="hidden md:block">
                <i class="fas fa-chart-line text-6xl opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Users -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-indigo-500 rounded-lg p-3">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Users</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_users']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['new_users_today'] ?? 0 }} today
                        </span>
                        <span class="text-gray-500">active users</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Properties -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-lg p-3">
                        <i class="fas fa-building text-white text-xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Properties</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_properties']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-check-circle mr-1"></i>{{ $stats['active_properties'] ?? 0 }} active
                        </span>
                        <span class="text-gray-500">properties</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Bookings -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-yellow-500 rounded-lg p-3">
                        <i class="fas fa-calendar-check text-white text-xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Bookings</dt>
                            <dd class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_bookings']) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-blue-600 font-medium">
                            <i class="fas fa-clock mr-1"></i>{{ $stats['active_bookings'] }} active
                        </span>
                        <span class="text-gray-500">bookings</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="bg-white overflow-hidden shadow-lg rounded-lg hover:shadow-xl transition-shadow duration-300">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-500 rounded-lg p-3">
                        <i class="fas fa-rupee-sign text-white text-xl"></i>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Total Revenue</dt>
                            <dd class="text-2xl font-bold text-gray-900">₹{{ number_format($stats['total_revenue'], 2) }}</dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-green-600 font-medium">
                            <i class="fas fa-arrow-up mr-1"></i>+{{ $stats['today_revenue'] ?? 0 }} today
                        </span>
                        <span class="text-gray-500">revenue</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats Row -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <!-- Pending Applications -->
        <a href="{{ route('admin.role-applications.index', ['status' => 'PENDING']) }}" 
           class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pending Applications</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending_applications'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-lg p-3">
                    <i class="fas fa-clock text-yellow-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Need your review</span>
            </div>
        </a>

        <!-- Active Providers -->
        <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Active Providers</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['active_providers'] }}</p>
                </div>
                <div class="bg-green-100 rounded-lg p-3">
                    <i class="fas fa-store text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Food & Laundry</span>
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white shadow-lg rounded-lg p-6 hover:shadow-xl transition-shadow duration-300">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500">Total Orders</p>
                    <p class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div class="bg-purple-100 rounded-lg p-3">
                    <i class="fas fa-shopping-bag text-purple-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Food orders</span>
            </div>
        </div>
    </div>

    <!-- Charts Section -->


    <!-- Recent Activity Grid -->
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- Recent Applications -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Applications</h3>
                    <a href="{{ route('admin.role-applications.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($recentApplications as $app)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                    <span class="text-indigo-800 font-medium">{{ substr($app->user->name ?? 'NA', 0, 2) }}</span>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $app->user->name ?? 'Unknown' }}</p>
                                <p class="text-xs text-gray-500">{{ $app->business_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($app->role_type == 'OWNER') bg-blue-100 text-blue-800
                                @elseif($app->role_type == 'FOOD') bg-green-100 text-green-800
                                @else bg-purple-100 text-purple-800
                                @endif">
                                {{ $app->role_type }}
                            </span>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($app->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($app->status == 'APPROVED') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $app->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p>No recent applications</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Bookings -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Bookings</h3>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($recentBookings as $booking)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $booking->booking_reference }}</p>
                            <p class="text-xs text-gray-500">{{ $booking->user->name ?? 'Unknown' }} • {{ $booking->property->name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₹{{ number_format($booking->total_amount, 0) }}</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($booking->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($booking->status == 'CONFIRMED') bg-green-100 text-green-800
                                @elseif($booking->status == 'CHECKED_IN') bg-blue-100 text-blue-800
                                @elseif($booking->status == 'CHECKED_OUT') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $booking->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-calendar-times text-3xl mb-2"></i>
                    <p>No recent bookings</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-800">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>
            <div class="divide-y divide-gray-200 max-h-96 overflow-y-auto">
                @forelse($recentOrders as $order)
                <div class="px-6 py-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $order->order_reference }}</p>
                            <p class="text-xs text-gray-500">{{ $order->user->name ?? 'Unknown' }} • {{ $order->serviceProvider->business_name ?? 'N/A' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-semibold text-gray-900">₹{{ number_format($order->total_amount, 0) }}</p>
                            <span class="px-2 py-1 text-xs rounded-full 
                                @if($order->status == 'PENDING') bg-yellow-100 text-yellow-800
                                @elseif($order->status == 'DELIVERED') bg-green-100 text-green-800
                                @elseif($order->status == 'CANCELLED') bg-red-100 text-red-800
                                @else bg-blue-100 text-blue-800
                                @endif">
                                {{ $order->status }}
                            </span>
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-6 py-8 text-center text-gray-500">
                    <i class="fas fa-shopping-bag text-3xl mb-2"></i>
                    <p>No recent orders</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Role Distribution -->
    <div class="bg-white shadow-lg rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">User Role Distribution</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-blue-600">{{ number_format($roleDistribution['owners']) }}</p>
                <p class="text-sm text-gray-600">Property Owners</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-green-600">{{ number_format($roleDistribution['food']) }}</p>
                <p class="text-sm text-gray-600">Food Providers</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-purple-600">{{ number_format($roleDistribution['laundry']) }}</p>
                <p class="text-sm text-gray-600">Laundry Providers</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4 text-center">
                <p class="text-2xl font-bold text-gray-600">{{ number_format($roleDistribution['users']) }}</p>
                <p class="text-sm text-gray-600">Regular Users</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize charts with sample data
    initializeCharts();
    
    // Load real data
    loadChartData();
});

function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart')?.getContext('2d');
    if (revenueCtx) {
        window.revenueChart = new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Revenue',
                    data: [],
                    borderColor: 'rgb(99, 102, 241)',
                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // User Chart
    const userCtx = document.getElementById('userChart')?.getContext('2d');
    if (userCtx) {
        window.userChart = new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: [],
                datasets: [{
                    label: 'New Users',
                    data: [],
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
}


// Handle range changes
document.getElementById('revenueRange')?.addEventListener('change', function() {
    loadChartData();
});

document.getElementById('userRange')?.addEventListener('change', function() {
    loadChartData();
});
</script>
@endpush
@endsection