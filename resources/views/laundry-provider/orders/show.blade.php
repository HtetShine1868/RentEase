@extends('layouts.laundry-provider')

@section('title', 'Order Details - ' . $order->order_reference)

@section('header', 'Order Details')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <a href="{{ route('laundry-provider.orders.index') }}" class="text-gray-400 hover:text-gray-500">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <h2 class="text-2xl font-bold text-gray-900">
                Order #{{ $order->order_reference }}
            </h2>
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
            <span class="px-3 py-1 text-sm font-semibold rounded-full {{ $statusColors[$order->status] ?? 'bg-gray-100' }}">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
            @if($order->service_mode === 'RUSH')
                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">
                    <i class="fas fa-bolt mr-1"></i> RUSH
                </span>
            @endif
        </div>
        <div class="flex space-x-3">
            @if(!in_array($order->status, ['DELIVERED', 'CANCELLED']))
                <button type="button"
                        onclick="showStatusModal()"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-edit mr-2"></i>
                    Update Status
                </button>
            @endif
            <a href="{{ route('laundry-provider.orders.print', $order->id) }}" 
               target="_blank"
               class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                <i class="fas fa-print mr-2"></i>
                Print Invoice
            </a>
        </div>
    </div>

    <!-- Progress Bar -->
    @if(!in_array($order->status, ['DELIVERED', 'CANCELLED']))
        <div class="bg-white shadow-sm sm:rounded-lg p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Order Progress</h3>
            <div class="relative">
                <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-200">
                    <div style="width: {{ $progress }}%" 
                         class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-indigo-600 transition-all duration-500">
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-600">
                    <span>Pending</span>
                    <span>Pickup Scheduled</span>
                    <span>Picked Up</span>
                    <span>In Progress</span>
                    <span>Ready</span>
                    <span>Out for Delivery</span>
                    <span>Delivered</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Rush Order Alert -->
    @if($order->service_mode === 'RUSH' && !in_array($order->status, ['DELIVERED', 'CANCELLED']))
        <div class="bg-purple-50 border-l-4 border-purple-500 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-bolt text-purple-500 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-purple-800">Rush Order</h3>
                    <p class="text-sm text-purple-700">
                        This is a rush order. Expected return: {{ \Carbon\Carbon::parse($order->expected_return_date)->format('d M Y') }}
                        @if(isset($isRushOnTrack) && $isRushOnTrack)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i> On Track
                            </span>
                        @else
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Need Attention
                            </span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Order Details Grid -->
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Customer Information -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Customer Information</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center">
                            <span class="text-indigo-800 font-medium text-lg">
                                {{ substr($order->user->name ?? 'NA', 0, 2) }}
                            </span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $order->user->name ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->email ?? 'N/A' }}</div>
                            <div class="text-sm text-gray-500">{{ $order->user->phone ?? 'N/A' }}</div>
                        </div>
                    </div>
                    
                    <div class="border-t border-gray-200 pt-3">
                        <h4 class="text-sm font-medium text-gray-500 mb-2">Pickup Address</h4>
                        <p class="text-sm text-gray-900">{{ $order->pickup_address }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            Distance: {{ number_format($order->distance_km, 2) }} km
                        </p>
                        @if($order->pickup_instructions)
                            <p class="text-sm text-gray-600 mt-2 italic">
                                "{{ $order->pickup_instructions }}"
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Service Mode:</dt>
                        <dd class="font-medium {{ $order->service_mode === 'RUSH' ? 'text-purple-600' : 'text-gray-900' }}">
                            {{ $order->service_mode }}
                            @if($order->service_mode === 'RUSH')
                                <i class="fas fa-bolt ml-1"></i>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Pickup Time:</dt>
                        <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($order->pickup_time)->format('d M Y, h:i A') }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Expected Return:</dt>
                        <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($order->expected_return_date)->format('d M Y') }}</dd>
                    </div>
                    @if($order->actual_return_date)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Actual Return:</dt>
                            <dd class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($order->actual_return_date)->format('d M Y') }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Total Items:</dt>
                        <dd class="font-medium text-gray-900">{{ $order->items->sum('quantity') }} pieces</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Payment Summary -->
        <div class="bg-white shadow-sm sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Payment Summary</h3>
                <dl class="space-y-3">
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Base Amount:</dt>
                        <dd class="font-medium text-gray-900">₹{{ number_format($order->base_amount, 2) }}</dd>
                    </div>
                    @if($order->rush_surcharge > 0)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Rush Surcharge:</dt>
                            <dd class="font-medium text-purple-600">+ ₹{{ number_format($order->rush_surcharge, 2) }}</dd>
                        </div>
                    @endif
                    @if($order->pickup_fee > 0)
                        <div class="flex justify-between text-sm">
                            <dt class="text-gray-500">Pickup Fee:</dt>
                            <dd class="font-medium text-gray-900">₹{{ number_format($order->pickup_fee, 2) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between text-sm border-t border-gray-200 pt-2">
                        <dt class="text-gray-700 font-medium">Total Amount:</dt>
                        <dd class="font-bold text-gray-900">₹{{ number_format($order->total_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm">
                        <dt class="text-gray-500">Commission:</dt>
                        <dd class="font-medium text-gray-900">₹{{ number_format($order->commission_amount, 2) }}</dd>
                    </div>
                    <div class="flex justify-between text-sm bg-green-50 p-2 rounded">
                        <dt class="text-green-700 font-medium">Your Earnings:</dt>
                        <dd class="font-bold text-green-700">₹{{ number_format($order->total_amount - $order->commission_amount, 2) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:px-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Order Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Instructions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $item->laundryItem->item_name ?? 'Unknown' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    {{ $item->laundryItem->item_type ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₹{{ number_format($item->total_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs">
                                {{ $item->special_instructions ?? '-' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Status Update Modal -->
    <div id="statusModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 hidden z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Update Order Status</h3>
                    <form id="statusForm">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Select New Status</label>
                                <select name="status" id="statusSelect" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                                    <option value="PICKUP_SCHEDULED" {{ $order->status == 'PICKUP_SCHEDULED' ? 'selected' : '' }}>Pickup Scheduled</option>
                                    <option value="PICKED_UP" {{ $order->status == 'PICKED_UP' ? 'selected' : '' }}>Picked Up</option>
                                    <option value="IN_PROGRESS" {{ $order->status == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                    <option value="READY" {{ $order->status == 'READY' ? 'selected' : '' }}>Ready</option>
                                    <option value="OUT_FOR_DELIVERY" {{ $order->status == 'OUT_FOR_DELIVERY' ? 'selected' : '' }}>Out for Delivery</option>
                                    <option value="DELIVERED" {{ $order->status == 'DELIVERED' ? 'selected' : '' }}>Delivered</option>
                                    <option value="CANCELLED" {{ $order->status == 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                            <div id="cancellationReason" class="hidden">
                                <label class="block text-sm font-medium text-gray-700">Cancellation Reason</label>
                                <textarea name="cancellation_reason" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="submitStatusUpdate()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Update
                    </button>
                    <button type="button" onclick="hideStatusModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function showStatusModal() {
    document.getElementById('statusModal').classList.remove('hidden');
}

function hideStatusModal() {
    document.getElementById('statusModal').classList.add('hidden');
}

document.getElementById('statusSelect').addEventListener('change', function() {
    const reasonDiv = document.getElementById('cancellationReason');
    if (this.value === 'CANCELLED') {
        reasonDiv.classList.remove('hidden');
    } else {
        reasonDiv.classList.add('hidden');
    }
});

function submitStatusUpdate() {
    const form = document.getElementById('statusForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    fetch('/laundry-provider/orders/{{ $order->id }}/status', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Status updated successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error updating status');
    });
}

// Hide modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('statusModal');
    if (event.target === modal) {
        hideStatusModal();
    }
}
</script>
@endpush
@endsection