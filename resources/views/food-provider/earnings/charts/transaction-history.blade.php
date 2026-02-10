<table class="min-w-full divide-y divide-gray-200">
    <thead class="bg-gray-50">
        <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Transaction ID
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Type
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Customer
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Date
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Amount
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Commission
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Net Earnings
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Status
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                Actions
            </th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y divide-gray-200">
        @php
            $transactions = [
                [
                    'id' => 'TXN-001234',
                    'type' => 'subscription',
                    'customer' => 'John Doe',
                    'date' => '2024-03-15',
                    'amount' => 120.00,
                    'commission' => 9.60,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001233',
                    'type' => 'order',
                    'customer' => 'Jane Smith',
                    'date' => '2024-03-15',
                    'amount' => 45.50,
                    'commission' => 3.64,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001232',
                    'type' => 'subscription',
                    'customer' => 'Robert Johnson',
                    'date' => '2024-03-14',
                    'amount' => 90.00,
                    'commission' => 7.20,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001231',
                    'type' => 'order',
                    'customer' => 'Sarah Williams',
                    'date' => '2024-03-14',
                    'amount' => 28.75,
                    'commission' => 2.30,
                    'status' => 'pending'
                ],
                [
                    'id' => 'TXN-001230',
                    'type' => 'refund',
                    'customer' => 'Michael Brown',
                    'date' => '2024-03-13',
                    'amount' => -60.00,
                    'commission' => -4.80,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001229',
                    'type' => 'subscription',
                    'customer' => 'Emily Davis',
                    'date' => '2024-03-13',
                    'amount' => 150.00,
                    'commission' => 12.00,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001228',
                    'type' => 'order',
                    'customer' => 'David Wilson',
                    'date' => '2024-03-12',
                    'amount' => 65.25,
                    'commission' => 5.22,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001227',
                    'type' => 'order',
                    'customer' => 'Lisa Anderson',
                    'date' => '2024-03-12',
                    'amount' => 32.50,
                    'commission' => 2.60,
                    'status' => 'failed'
                ],
                [
                    'id' => 'TXN-001226',
                    'type' => 'subscription',
                    'customer' => 'James Miller',
                    'date' => '2024-03-11',
                    'amount' => 180.00,
                    'commission' => 14.40,
                    'status' => 'completed'
                ],
                [
                    'id' => 'TXN-001225',
                    'type' => 'order',
                    'customer' => 'Patricia Taylor',
                    'date' => '2024-03-11',
                    'amount' => 55.80,
                    'commission' => 4.46,
                    'status' => 'completed'
                ]
            ];
        @endphp
        
        @foreach($transactions as $transaction)
        <tr class="transaction-row hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                {{ $transaction['id'] }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    @if($transaction['type'] === 'subscription') bg-blue-100 text-blue-800
                    @elseif($transaction['type'] === 'order') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($transaction['type']) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                {{ $transaction['customer'] }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                {{ date('M d, Y', strtotime($transaction['date'])) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium 
                @if($transaction['amount'] < 0) text-red-600 @else text-gray-900 @endif">
                ${{ number_format(abs($transaction['amount']), 2) }}
                @if($transaction['amount'] < 0)
                <span class="text-xs text-red-500">(refund)</span>
                @endif
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${{ number_format($transaction['commission'], 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                ${{ number_format($transaction['amount'] - $transaction['commission'], 2) }}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full 
                    @if($transaction['status'] === 'completed') bg-green-100 text-green-800
                    @elseif($transaction['status'] === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800
                    @endif">
                    {{ ucfirst($transaction['status']) }}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button class="text-blue-600 hover:text-blue-900 mr-3 view-btn"
                        data-id="{{ $transaction['id'] }}">
                    View
                </button>            
                <button class="text-gray-600 hover:text-gray-900 download-btn"
                        data-id="{{ $transaction['id'] }}">
                    <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </button>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    function viewTransaction(transactionId) {
        // Placeholder for viewing transaction details
        alert('View transaction: ' + transactionId);
        // In production: window.location.href = '/food-provider/transactions/' + transactionId;
    }
    
    function downloadReceipt(transactionId) {
        // Placeholder for downloading receipt
        alert('Download receipt for: ' + transactionId);
        // In production: trigger file download
    }

    document.addEventListener("click", function(e) {
    if (e.target.closest(".view-btn")) {
        const id = e.target.closest(".view-btn").dataset.id;
        viewTransaction(id);
    }

    if (e.target.closest(".download-btn")) {
        const id = e.target.closest(".download-btn").dataset.id;
        downloadReceipt(id);
    }
    });
</script>