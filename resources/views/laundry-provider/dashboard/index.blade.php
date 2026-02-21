{{-- resources/views/laundry-provider/dashboard/index.blade.php --}}
@extends('laundry-provider.layouts.provider')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, ' . ($provider->business_name ?? auth()->user()->name))

@section('content')
<div class="space-y-6" x-data="dashboardData()" x-init="initChart()">
    {{-- Rush Alert --}}
    @if($rushOrders > 0)
    <div class="bg-[#ffdb9f] bg-opacity-20 border-l-4 border-[#ffdb9f] p-4 rounded-lg flex items-center justify-between">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle text-[#ffdb9f] text-xl mr-3"></i>
            <div>
                <h4 class="font-medium text-[#174455]">⚠️ {{ $rushOrders }} Rush Order(s) Need Immediate Attention</h4>
                <p class="text-sm text-[#286b7f]">These orders must be picked up within 2 hours</p>
            </div>
        </div>
        <a href="{{ route('laundry-provider.orders.rush') }}" 
           @click.prevent="navigate('{{ route('laundry-provider.orders.rush') }}')"
           class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
            View Rush Orders
        </a>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-[#174455]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Today's Orders</p>
                    <p class="text-3xl font-bold text-[#174455]">{{ $stats['total_orders_today'] }}</p>
                </div>
                <div class="bg-[#174455] bg-opacity-10 p-3 rounded-lg">
                    <i class="fas fa-shopping-bag text-[#174455] text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                <span class="text-[#ffdb9f] font-medium">{{ $stats['pending_pickups'] }}</span> pending pickup
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-[#286b7f]">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">In Progress</p>
                    <p class="text-3xl font-bold text-[#286b7f]">{{ $stats['in_progress'] }}</p>
                </div>
                <div class="bg-[#286b7f] bg-opacity-10 p-3 rounded-lg">
                    <i class="fas fa-spinner text-[#286b7f] text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                <span class="text-green-600 font-medium">{{ $stats['ready_for_delivery'] }}</span> ready for delivery
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-green-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Today's Earnings</p>
                    <p class="text-3xl font-bold text-green-600">${{ number_format($stats['total_earnings_today'], 2) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-lg">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                <span class="text-blue-600 font-medium">{{ $stats['completed_today'] }}</span> completed today
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Rating</p>
                    <div class="flex items-center">
                        <p class="text-3xl font-bold text-yellow-500">{{ number_format($stats['average_rating'], 1) }}</p>
                        <i class="fas fa-star text-yellow-500 ml-2"></i>
                    </div>
                </div>
                <div class="bg-yellow-100 p-3 rounded-lg">
                    <i class="fas fa-star text-yellow-500 text-xl"></i>
                </div>
            </div>
            <div class="mt-2 text-sm text-gray-600">
                Based on {{ $stats['total_reviews'] }} reviews
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-[#174455] mb-4 flex items-center">
            <i class="fas fa-bolt text-[#ffdb9f] mr-2"></i> Quick Actions
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="{{ route('laundry-provider.orders.index') }}" 
               @click.prevent="navigate('{{ route('laundry-provider.orders.index') }}')"
               class="bg-[#174455] bg-opacity-5 hover:bg-opacity-10 p-4 rounded-lg text-center transition-colors group">
                <i class="fas fa-clipboard-list text-[#174455] text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <p class="text-sm font-medium text-[#174455]">View All Orders</p>
            </a>
            <a href="{{ route('laundry-provider.orders.rush') }}" 
               @click.prevent="navigate('{{ route('laundry-provider.orders.rush') }}')"
               class="bg-[#ffdb9f] bg-opacity-20 hover:bg-opacity-30 p-4 rounded-lg text-center transition-colors group">
                <i class="fas fa-bolt text-[#ffdb9f] text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <p class="text-sm font-medium text-[#174455]">Rush Orders</p>
                @if($rushOrders > 0)
                    <span class="inline-block mt-1 px-2 py-0.5 bg-[#174455] text-white text-xs rounded-full">{{ $rushOrders }}</span>
                @endif
            </a>
            <a href="{{ route('laundry-provider.items.index') }}" 
               @click.prevent="navigate('{{ route('laundry-provider.items.index') }}')"
               class="bg-[#286b7f] bg-opacity-5 hover:bg-opacity-10 p-4 rounded-lg text-center transition-colors group">
                <i class="fas fa-tags text-[#286b7f] text-2xl mb-2 group-hover:scale-110 transition-transform"></i>
                <p class="text-sm font-medium text-[#286b7f]">Manage Pricing</p>
            </a>
        </div>
    </div>

    {{-- Today's Timeline --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Today's Pickups --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                    <i class="fas fa-clock text-[#ffdb9f] mr-2"></i> Today's Pickups ({{ $todayPickups->count() }})
                </h3>
                <a href="{{ route('laundry-provider.orders.index') }}?tab=normal&section=pickup" 
                   @click.prevent="navigate('{{ route('laundry-provider.orders.index') }}?tab=normal&section=pickup')"
                   class="text-sm text-[#286b7f] hover:text-[#174455]">
                    View all →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($todayPickups as $pickup)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div>
                        <div class="flex items-center">
                            @if($pickup->service_mode == 'RUSH')
                                <span class="bg-[#ffdb9f] text-[#174455] text-xs px-2 py-0.5 rounded-full mr-2">RUSH</span>
                            @endif
                            <p class="font-medium text-[#174455]">#{{ $pickup->order_reference }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $pickup->user->name }} • {{ $pickup->items->count() }} items</p>
                        @if($pickup->pickup_instructions)
                            <p class="text-xs text-gray-500 mt-1"><i class="fas fa-info-circle mr-1"></i> {{ Str::limit($pickup->pickup_instructions, 30) }}</p>
                        @endif
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-[#286b7f]">{{ \Carbon\Carbon::parse($pickup->pickup_time)->format('g:i A') }}</p>
                        <a href="{{ route('laundry-provider.orders.show', $pickup->id) }}" 
                           @click.prevent="navigate('{{ route('laundry-provider.orders.show', $pickup->id) }}')"
                           class="text-xs text-[#174455] hover:underline">
                            View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>No pickups scheduled for today</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- Today's Deliveries --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-[#174455] flex items-center">
                    <i class="fas fa-truck text-[#ffdb9f] mr-2"></i> Today's Deliveries ({{ $todayDeliveries->count() }})
                </h3>
                <a href="{{ route('laundry-provider.orders.index') }}?tab=normal&section=deliver" 
                   @click.prevent="navigate('{{ route('laundry-provider.orders.index') }}?tab=normal&section=deliver')"
                   class="text-sm text-[#286b7f] hover:text-[#174455]">
                    View all →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($todayDeliveries as $delivery)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div>
                        <div class="flex items-center">
                            @if($delivery->service_mode == 'RUSH')
                                <span class="bg-[#ffdb9f] text-[#174455] text-xs px-2 py-0.5 rounded-full mr-2">RUSH</span>
                            @endif
                            <p class="font-medium text-[#174455]">#{{ $delivery->order_reference }}</p>
                        </div>
                        <p class="text-sm text-gray-600">{{ $delivery->user->name }} • {{ $delivery->items->count() }} items</p>
                    </div>
                    <div class="text-right">
                        @if($delivery->status == 'READY')
                            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full mb-1">Ready</span>
                        @elseif($delivery->status == 'OUT_FOR_DELIVERY')
                            <span class="inline-block px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full mb-1">Out for Delivery</span>
                        @else
                            <span class="inline-block px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded-full mb-1">{{ $delivery->status }}</span>
                        @endif
                        <a href="{{ route('laundry-provider.orders.show', $delivery->id) }}" 
                           @click.prevent="navigate('{{ route('laundry-provider.orders.show', $delivery->id) }}')"
                           class="text-xs text-[#174455] hover:underline block">
                            View Details
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-gray-500">
                    <i class="fas fa-check-circle text-4xl text-green-300 mb-2"></i>
                    <p>No deliveries scheduled for today</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>


        {{-- Recent Orders --}}
        <div class="lg:col-span-1 bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-[#174455] mb-4 flex items-center">
                <i class="fas fa-history text-[#ffdb9f] mr-2"></i> Recent Orders
            </h3>
            <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                @foreach($recentOrders as $order)
                <div class="flex items-center justify-between p-3 hover:bg-gray-50 rounded-lg transition-colors border-b border-gray-100 last:border-0">
                    <div>
                        <div class="flex items-center">
                            @if($order->service_mode == 'RUSH')
                                <span class="bg-[#ffdb9f] text-[#174455] text-xs px-2 py-0.5 rounded-full mr-2">RUSH</span>
                            @endif
                            <p class="font-medium text-sm text-[#174455]">#{{ $order->order_reference }}</p>
                        </div>
                        <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-2 py-1 bg-{{ $order->status == 'DELIVERED' ? 'green' : ($order->status == 'CANCELLED' ? 'red' : 'blue') }}-100 rounded-full">
                            {{ $order->status }}
                        </span>
                        <p class="text-xs font-medium text-gray-700 mt-1">৳{{ number_format($order->total_amount) }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <a href="{{ route('laundry-provider.orders.index') }}" 
               @click.prevent="navigate('{{ route('laundry-provider.orders.index') }}')"
               class="mt-4 block text-center text-sm text-[#286b7f] hover:text-[#174455] font-medium">
                View All Orders →
            </a>
        </div>


    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardData() {
    return {
        chart: null,
        
        initChart() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'New Orders',
                            data: {!! json_encode($chartData['orders']) !!},
                            borderColor: '#174455',
                            backgroundColor: 'rgba(23, 68, 85, 0.1)',
                            tension: 0.4,
                            fill: true
                        },
                        {
                            label: 'Completed',
                            data: {!! json_encode($chartData['completed']) !!},
                            borderColor: '#ffdb9f',
                            backgroundColor: 'rgba(255, 219, 159, 0.1)',
                            tension: 0.4,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            labels: {
                                color: '#174455'
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                color: '#286b7f'
                            },
                            grid: {
                                color: 'rgba(40, 107, 127, 0.1)'
                            }
                        },
                        x: {
                            ticks: {
                                color: '#286b7f'
                            },
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>
@endpush