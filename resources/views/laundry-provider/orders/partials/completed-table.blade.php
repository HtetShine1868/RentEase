<div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium text-gray-900">
                <i class="fas fa-check-circle text-gray-600 mr-2"></i>
                Completed Orders
            </h3>
            <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                {{ $orders->total() }} orders
            </span>
        </div>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-12">
            <div class="mx-auto h-24 w-24 text-gray-400">
                <i class="fas fa-check-circle text-4xl"></i>
            </div>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No completed orders</h3>
            <p class="mt-1 text-sm text-gray-500">Completed orders will appear here.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pickup Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Return Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($orders as $order)
                        @php
                            $isRush = $order->service_mode === 'RUSH';
                            $returnDate = \Carbon\Carbon::parse($order->expected_return_date);
                            $actualReturn = $order->actual_return_date ? \Carbon\Carbon::parse($order->actual_return_date) : null;
                            $onTime = $actualReturn && $actualReturn->lte($returnDate);
                            
                            $statusColors = [
                                'DELIVERED' => 'bg-green-100 text-green-800',
                                'CANCELLED' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $order->order_reference }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->created_at->format('d M Y') }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $order->user->name ?? 'N/A' }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $order->user->phone ?? 'N/A' }}
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
                                        Normal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ \Carbon\Carbon::parse($order->pickup_time)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    {{ $returnDate->format('d M Y') }}
                                </div>
                                @if($actualReturn)
                                    <div class="text-xs {{ $onTime ? 'text-green-600' : 'text-orange-600' }}">
                                        {{ $onTime ? 'On time' : 'Delayed' }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('laundry-provider.orders.show', $order->id) }}" 
                                       class="text-indigo-600 hover:text-indigo-900 p-1" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('laundry-provider.orders.print', $order->id) }}" 
                                       target="_blank"
                                       class="text-gray-600 hover:text-gray-900 p-1" title="Print Invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>