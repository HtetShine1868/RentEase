{{-- resources/views/rental/invoice.blade.php --}}
@extends('dashboard')

@section('title', 'Booking Invoice')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Booking Invoice</h1>
                    <p class="mt-2 text-gray-600">Invoice for booking #{{ $booking->booking_reference }}</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="window.print()" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Invoice
                    </button>
                    <a href="{{ route('bookings.show', $booking) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <svg class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Invoice Card -->
        <div class="bg-white shadow-lg rounded-lg overflow-hidden border border-gray-200">
            <!-- Invoice Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-indigo-600 to-blue-600 text-white">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold">INVOICE</h2>
                        <p class="text-indigo-100 mt-1">Booking Reference: {{ $booking->booking_reference }}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-xl font-bold">PAID</div>
                        <div class="text-indigo-100">Status: 
                            @php
                                $latestPayment = $booking->payments->sortByDesc('created_at')->first();
                            @endphp
                            {{ $latestPayment ? $latestPayment->status : 'PENDING' }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Details -->
            <div class="px-8 py-6">
                <!-- Company & Customer Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">From:</h3>
                        <div class="text-gray-700">
                            <p class="font-semibold">{{ config('app.name', 'Rental System') }}</p>
                            <p>Rental Management System</p>
                            <p>Dhaka, Bangladesh</p>
                            <p>Email: support@rentalsystem.com</p>
                            <p>Phone: +880 1234 567890</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">To:</h3>
                        <div class="text-gray-700">
                            <p class="font-semibold">{{ auth()->user()->name }}</p>
                            <p>Email: {{ auth()->user()->email }}</p>
                            <p>Phone: {{ auth()->user()->phone ?? 'N/A' }}</p>
                            <p>Customer ID: USR-{{ str_pad(auth()->id(), 5, '0', STR_PAD_LEFT) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Invoice Meta -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Invoice Date</div>
                        <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->created_at)->format('M d, Y') }}</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Booking Date</div>
                        <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</div>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="text-sm text-gray-500">Due Date</div>
                        <div class="font-semibold text-gray-900">{{ \Carbon\Carbon::parse($booking->check_in)->format('M d, Y') }}</div>
                    </div>
                </div>

                <!-- Booking Summary -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Booking Summary</h3>
                    <div class="border border-gray-200 rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate/Day</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900">{{ $booking->property->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            @if($booking->room)
                                                {{ $booking->room->room_type }} ({{ $booking->room->room_number }})
                                            @else
                                                {{ $booking->property->type }}
                                            @endif
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ $booking->property->address }}, {{ $booking->property->city }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $booking->duration_days }} days
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ৳{{ number_format($booking->room_price_per_day, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        ৳{{ number_format($booking->room_price_per_day * $booking->duration_days, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Payment Breakdown -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Breakdown</h3>
                    <div class="bg-gray-50 rounded-lg p-6">
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Room Charges:</span>
                                <span class="font-medium">৳{{ number_format($booking->room_price_per_day * $booking->duration_days, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Commission ({{ $booking->property->commission_rate }}%):</span>
                                <span class="font-medium">৳{{ number_format($booking->commission_amount, 2) }}</span>
                            </div>
                            @if($booking->payments->where('status', 'COMPLETED')->sum('amount') > $booking->total_amount)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Previous Payments:</span>
                                    <span class="font-medium">-৳{{ number_format($booking->payments->where('status', 'COMPLETED')->sum('amount'), 2) }}</span>
                                </div>
                            @endif
                            <div class="border-t pt-3 mt-3">
                                <div class="flex justify-between text-lg font-bold">
                                    <span>Total Amount:</span>
                                    <span class="text-indigo-600">৳{{ number_format($booking->total_amount, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment History -->
                @if($booking->payments->count() > 0)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment History</h3>
                        <div class="border border-gray-200 rounded-lg overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Ref</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($booking->payments as $payment)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $payment->payment_reference }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ \Carbon\Carbon::parse($payment->created_at)->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $payment->payment_method }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ৳{{ number_format($payment->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                    @if($payment->status === 'COMPLETED') bg-green-100 text-green-800
                                                    @elseif($payment->status === 'PENDING') bg-yellow-100 text-yellow-800
                                                    @elseif($payment->status === 'FAILED') bg-red-100 text-red-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $payment->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

                <!-- Terms & Conditions -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Terms & Conditions</h3>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p>1. This invoice is generated automatically by the Rental Management System.</p>
                        <p>2. All payments are non-refundable once the booking is confirmed.</p>
                        <p>3. Any disputes regarding payments must be raised within 7 days of payment.</p>
                        <p>4. Commission fees are calculated based on the service type and are non-negotiable.</p>
                        <p>5. For any queries, please contact our support team.</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Footer -->
            <div class="px-8 py-6 bg-gray-50 border-t border-gray-200">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-center md:text-left mb-4 md:mb-0">
                        <p class="text-sm text-gray-600">Thank you for your business!</p>
                        <p class="text-xs text-gray-500 mt-1">{{ config('app.name', 'Rental System') }} Team</p>
                    </div>
                    <div class="text-center">
                        <div class="text-sm text-gray-600">Questions?</div>
                        <div class="text-sm font-medium text-indigo-600">support@rentalsystem.com</div>
                        <div class="text-xs text-gray-500">+880 1234 567890</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .bg-gray-50 { background-color: #f9fafb !important; }
    .shadow-lg { box-shadow: none !important; }
    .border { border: 1px solid #e5e7eb !important; }
    .rounded-lg { border-radius: 0 !important; }
    .overflow-hidden { overflow: visible !important; }
    .hidden { display: none !important; }
    
    /* Ensure good print layout */
    body { font-size: 12pt; }
    .text-2xl { font-size: 18pt !important; }
    .text-xl { font-size: 16pt !important; }
    .text-lg { font-size: 14pt !important; }
    .text-sm { font-size: 10pt !important; }
    .text-xs { font-size: 8pt !important; }
    
    /* Remove backgrounds for better print */
    .bg-gradient-to-r { background: #4f46e5 !important; }
    .bg-gray-50 { background: #f9fafb !important; }
    .bg-white { background: white !important; }
    
    /* Ensure table borders are visible */
    table { border-collapse: collapse; }
    th, td { border: 1px solid #d1d5db !important; padding: 4px !important; }
}
</style>

<script>
// Auto-trigger print dialog for printing
function printInvoice() {
    window.print();
}

// Add event listener for Ctrl+P
document.addEventListener('keydown', function(e) {
    if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
        e.preventDefault();
        printInvoice();
    }
});
</script>
@endsection