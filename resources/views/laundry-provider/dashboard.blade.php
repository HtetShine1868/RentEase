@extends('layouts.laundry-provider')

@section('title', 'Laundry Provider Dashboard')

@section('header', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Welcome back, {{ Auth::user()->name }}!</h2>
                <p class="mt-2 opacity-90">{{ Carbon\Carbon::now()->format('l, F j, Y') }}</p>
                <div class="mt-4 flex items-center space-x-4">
                    <div class="flex items-center">
                        <i class="fas fa-tachometer-alt mr-2"></i>
                        <span>Normal Turnaround: {{ $config->normal_turnaround_hours ?? 120 }}h ({{ floor(($config->normal_turnaround_hours ?? 120)/24) }} days)</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-bolt mr-2 text-yellow-300"></i>
                        <span>Rush Turnaround: {{ $config->rush_turnaround_hours ?? 48 }}h ({{ floor(($config->rush_turnaround_hours ?? 48)/24) }} days)</span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div class="text-4xl font-bold">{{ $stats['total_orders'] }}</div>
                <div class="text-sm opacity-90">Total Orders</div>
                <div class="mt-2 flex items-center justify-end">
                    <i class="fas fa-star text-yellow-300 mr-1"></i>
                    <span>{{ $stats['average_rating'] }} ({{ $stats['total_reviews'] }} reviews)</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pending Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Pending Orders</p>
                    <p class="text-3xl font-bold">{{ $stats['pending_orders'] }}</p>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('laundry-provider.orders.index', ['status' => 'PENDING']) }}" class="text-sm text-yellow-600 hover:text-yellow-800 mt-2 inline-block">
                View Pending →
            </a>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">In Progress</p>
                    <p class="text-3xl font-bold">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('laundry-provider.orders.index', ['status' => 'IN_PROGRESS']) }}" class="text-sm text-blue-600 hover:text-blue-800 mt-2 inline-block">
                View Progress →
            </a>
        </div>

        <!-- Ready for Delivery -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Ready for Delivery</p>
                    <p class="text-3xl font-bold">{{ $stats['ready_for_delivery'] }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('laundry-provider.orders.index', ['status' => 'READY']) }}" class="text-sm text-green-600 hover:text-green-800 mt-2 inline-block">
                View Ready →
            </a>
        </div>

        <!-- Rush Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Rush Orders</p>
                    <p class="text-3xl font-bold">{{ $stats['rush_orders'] }}</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-bolt text-purple-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('laundry-provider.orders.rush') }}" class="text-sm text-purple-600 hover:text-purple-800 mt-2 inline-block">
                View Rush →
            </a>
        </div>

        <!-- Delayed Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Delayed Orders</p>
                    <p class="text-3xl font-bold {{ $stats['delayed_orders'] > 0 ? 'text-red-600' : '' }}">{{ $stats['delayed_orders'] }}</p>
                </div>
                <div class="bg-red-100 p-3 rounded-full">
                    <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                </div>
            </div>
            @if($stats['delayed_orders'] > 0)
                <a href="{{ route('laundry-provider.orders.index', ['status' => 'delayed']) }}" class="text-sm text-red-600 hover:text-red-800 mt-2 inline-block">
                    View Delayed →
                </a>
            @endif
        </div>

        <!-- Today's Deliveries -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-indigo-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Delivered Today</p>
                    <p class="text-3xl font-bold">{{ $stats['delivered_today'] }}</p>
                </div>
                <div class="bg-indigo-100 p-3 rounded-full">
                    <i class="fas fa-truck text-indigo-600 text-xl"></i>
                </div>
            </div>
            <div class="text-sm text-gray-600 mt-2">
                Revenue: ₹{{ number_format($stats['revenue_today'], 2) }}
            </div>
        </div>

        <!-- Total Orders -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-gray-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Orders</p>
                    <p class="text-3xl font-bold">{{ $stats['total_orders'] }}</p>
                </div>
                <div class="bg-gray-100 p-3 rounded-full">
                    <i class="fas fa-shopping-bag text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Average Rating -->
        <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Average Rating</p>
                    <div class="flex items-center">
                        <p class="text-3xl font-bold mr-2">{{ $stats['average_rating'] }}</p>
                        <div class="flex">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $stats['average_rating'] ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-full">
                    <i class="fas fa-star text-yellow-600 text-xl"></i>
                </div>
            </div>
            <a href="{{ route('laundry-provider.reviews.index') }}" class="text-sm text-yellow-600 hover:text-yellow-800 mt-2 inline-block">
                View Reviews ({{ $stats['total_reviews'] }}) →
            </a>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Orders Last 7 Days</h3>
            <canvas id="ordersChart" height="200"></canvas>
        </div>
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-lg font-semibold mb-4">Revenue Last 7 Days</h3>
            <canvas id="revenueChart" height="200"></canvas>
        </div>
    </div>

    <!-- Rush Orders Alert -->
    @if($rushOrders->count() > 0)
        <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-4">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="fas fa-bolt text-purple-500 text-2xl"></i>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-lg font-medium text-purple-800">Rush Orders Need Attention</h3>
                    <p class="text-sm text-purple-600">You have {{ $rushOrders->count() }} rush orders that require priority processing.</p>
                </div>
                <div class="ml-4">
                    <a href="{{ route('laundry-provider.orders.rush') }}" class="text-purple-600 hover:text-purple-800 font-medium">
                        View All →
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Today's Schedule -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Today's Pickup/Delivery Schedule</h3>
        @if($todaysSchedule->isEmpty())
            <p class="text-gray-500 text-center py-4">No pickups or deliveries scheduled for today.</p>
        @else
            <div class="space-y-3">
                @foreach($todaysSchedule as $schedule)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-2 h-2 rounded-full {{ $schedule->status === 'PICKUP_SCHEDULED' ? 'bg-blue-500' : 'bg-green-500' }} mr-3"></div>
                            <div>
                                <p class="font-medium">{{ $schedule->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $schedule->order_reference }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium">{{ Carbon\Carbon::parse($schedule->pickup_time)->format('h:i A') }}</p>
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if($schedule->service_mode === 'RUSH') bg-purple-100 text-purple-800 @else bg-gray-100 text-gray-800 @endif">
                                {{ $schedule->service_mode }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Upcoming Returns -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-lg font-semibold mb-4">Upcoming Returns (Next 3 Days)</h3>
        @if($upcomingReturns->isEmpty())
            <p class="text-gray-500 text-center py-4">No returns scheduled in the next 3 days.</p>
        @else
            <div class="space-y-3">
                @foreach($upcomingReturns as $return)
                    @php
                        $daysLeft = Carbon\Carbon::today()->diffInDays(Carbon\Carbon::parse($return->expected_return_date), false);
                        $urgencyClass = $daysLeft <= 1 ? 'text-red-600' : ($daysLeft <= 2 ? 'text-yellow-600' : 'text-green-600');
                    @endphp
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-medium">{{ $return->user->name }}</p>
                            <p class="text-sm text-gray-500">{{ $return->order_reference }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium {{ $urgencyClass }}">
                                {{ Carbon\Carbon::parse($return->expected_return_date)->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-gray-500">{{ $daysLeft == 0 ? 'Today' : ($daysLeft == 1 ? 'Tomorrow' : $daysLeft . ' days') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Recent Orders -->
    <div class="bg-white rounded-lg shadow-lg p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold">Recent Orders</h3>
            <a href="{{ route('laundry-provider.orders.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">View All →</a>
        </div>
        
        @if($recentOrders->isEmpty())
            <p class="text-gray-500 text-center py-4">No orders yet.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Mode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Return Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($recentOrders as $order)
                            <tr>
                                <td class="px-4 py-3">
                                    <p class="font-medium">{{ $order->order_reference }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->format('h:i A, d M') }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p>{{ $order->user->name ?? 'N/A' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 text-xs rounded-full {{ $order->service_mode === 'RUSH' ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas {{ $order->service_mode === 'RUSH' ? 'fa-bolt' : 'fa-clock' }} mr-1"></i>
                                        {{ $order->service_mode }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $statusColors = [
                                            'PENDING' => 'bg-yellow-100 text-yellow-800',
                                            'PICKUP_SCHEDULED' => 'bg-blue-100 text-blue-800',
                                            'PICKED_UP' => 'bg-indigo-100 text-indigo-800',
                                            'IN_PROGRESS' => 'bg-purple-100 text-purple-800',
                                            'READY' => 'bg-green-100 text-green-800',
                                            'OUT_FOR_DELIVERY' => 'bg-orange-100 text-orange-800',
                                            'DELIVERED' => 'bg-green-100 text-green-800',
                                            'CANCELLED' => 'bg-red-100 text-red-800'
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                        {{ str_replace('_', ' ', $order->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @php
                                        $returnDate = Carbon\Carbon::parse($order->expected_return_date);
                                        $isOverdue = $returnDate->isPast() && !in_array($order->status, ['DELIVERED', 'CANCELLED']);
                                    @endphp
                                    <p class="{{ $isOverdue ? 'text-red-600 font-medium' : '' }}">
                                        {{ $returnDate->format('d M Y') }}
                                        @if($order->service_mode === 'RUSH')
                                            <i class="fas fa-bolt text-purple-500 ml-1" title="Rush Order"></i>
                                        @endif
                                    </p>
                                    @if($isOverdue)
                                        <p class="text-xs text-red-500">Overdue</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Orders Chart
    const ordersCtx = document.getElementById('ordersChart').getContext('2d');
    new Chart(ordersCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['dates']) !!},
            datasets: [{
                label: 'Orders',
                data: {!! json_encode($chartData['orders']) !!},
                borderColor: 'rgb(79, 70, 229)',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
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
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['dates']) !!},
            datasets: [{
                label: 'Revenue (₹)',
                data: {!! json_encode($chartData['revenue']) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
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
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush
@endsection