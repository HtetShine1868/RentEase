<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-green-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-truck text-green-600 mr-2"></i>
                Delivery Orders
            </h3>
            <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ $orders->total() }} orders
            </span>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <i class="fas fa-truck text-4xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No delivery orders</h3>
            <p class="mt-1 text-sm text-gray-500">No orders ready for delivery at the moment.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        @php
                            $isRush = $order->service_mode === 'RUSH';
                            $returnDate = \Carbon\Carbon::parse($order->expected_return_date);
                            $isOverdue = $returnDate->isPast() && !in_array($order->status, ['DELIVERED', 'CANCELLED']);
                            $daysLeft = \Carbon\Carbon::today()->diffInDays($returnDate, false);
                            
                            $statusColors = [
                                'IN_PROGRESS' => 'bg-purple-100 text-purple-800',
                                'READY' => 'bg-green-100 text-green-800',
                                'OUT_FOR_DELIVERY' => 'bg-blue-100 text-blue-800',
                            ];
                            
                            $statusIcons = [
                                'IN_PROGRESS' => 'fa-spinner',
                                'READY' => 'fa-check-circle',
                                'OUT_FOR_DELIVERY' => 'fa-truck',
                            ];
                        @endphp
                        <tr class="{{ $isRush ? 'bg-purple-50' : '' }} {{ $isOverdue ? 'bg-red-50' : '' }} hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $order->order_reference }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->created_at->format('d M, h:i A') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $order->user->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->user->phone ?? 'No phone' }}
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $order->items->sum('quantity') }} items
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($isRush)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                        <i class="fas fa-bolt mr-1"></i>
                                        RUSH
                                    </span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm {{ $isOverdue ? 'text-red-600 font-medium' : ($daysLeft <= 2 ? 'text-yellow-600' : 'text-gray-900') }}">
                                    {{ $returnDate->format('d M, Y') }}
                                </div>
                                @if($isOverdue)
                                    <div class="text-xs text-red-500">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Overdue by {{ abs($daysLeft) }} days
                                    </div>
                                @elseif($daysLeft <= 2 && $daysLeft > 0)
                                    <div class="text-xs text-yellow-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        Due in {{ $daysLeft }} days
                                    </div>
                                @elseif($daysLeft == 0)
                                    <div class="text-xs text-orange-500">
                                        <i class="fas fa-clock mr-1"></i>
                                        Due today
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                    <i class="fas {{ $statusIcons[$order->status] ?? 'fa-circle' }} mr-1"></i>
                                    {{ str_replace('_', ' ', $order->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 p-1" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($order->status === 'IN_PROGRESS')
                                        <button onclick="markAsReady({{ $order->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Mark as Ready">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    
                                    @if($order->status === 'READY')
                                        <button onclick="markAsOutForDelivery({{ $order->id }})"
                                                class="text-blue-600 hover:text-blue-900 p-1" title="Out for Delivery">
                                            <i class="fas fa-truck"></i>
                                        </button>
                                    @endif
                                    
                                    @if($order->status === 'OUT_FOR_DELIVERY')
                                        <button onclick="markAsDelivered({{ $order->id }})"
                                                class="text-green-600 hover:text-green-900 p-1" title="Mark as Delivered">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>