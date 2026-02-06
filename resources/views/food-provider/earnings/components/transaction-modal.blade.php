<div id="transactionModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Transaction Details
                    </h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Transaction Info -->
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Transaction ID</label>
                            <p class="mt-1 text-sm text-gray-900" id="modal-transaction-id">TXN-001234</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date & Time</label>
                            <p class="mt-1 text-sm text-gray-900" id="modal-transaction-date">Mar 15, 2024 14:30</p>
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Customer</label>
                        <p class="mt-1 text-sm text-gray-900" id="modal-customer">John Doe</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-sm text-gray-900" id="modal-description">Monthly Lunch + Dinner Subscription</p>
                    </div>
                    
                    <!-- Amount Breakdown -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Amount Breakdown</h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-sm font-medium" id="modal-subtotal">$120.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600">Commission (8%)</span>
                                <span class="text-sm font-medium text-red-600" id="modal-commission">-$9.60</span>
                            </div>
                            <div class="flex justify-between border-t pt-2">
                                <span class="text-sm font-medium text-gray-900">Net Earnings</span>
                                <span class="text-sm font-bold text-green-600" id="modal-net-earnings">$110.40</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="text-sm font-medium text-gray-900 mb-2">Payment Method</h4>
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900" id="modal-payment-method">Credit Card</p>
                                <p class="text-xs text-gray-500" id="modal-payment-details">Ending in 4242</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div class="border-t border-gray-200 pt-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">Status</h4>
                                <p class="text-xs text-gray-500">Updated: <span id="modal-status-time">Mar 15, 2024 14:32</span></p>
                            </div>
                            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800" id="modal-status">
                                Completed
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="downloadReceipt()">
                    Download Receipt
                </button>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeModal()">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    function openTransactionModal(transactionData) {
        // Populate modal with transaction data
        document.getElementById('modal-transaction-id').textContent = transactionData.id || 'TXN-001234';
        document.getElementById('modal-transaction-date').textContent = transactionData.date || 'Mar 15, 2024 14:30';
        document.getElementById('modal-customer').textContent = transactionData.customer || 'John Doe';
        document.getElementById('modal-description').textContent = transactionData.description || 'Monthly Subscription';
        document.getElementById('modal-subtotal').textContent = '$' + (transactionData.amount || 120).toFixed(2);
        document.getElementById('modal-commission').textContent = '-$' + (transactionData.commission || 9.60).toFixed(2);
        document.getElementById('modal-net-earnings').textContent = '$' + ((transactionData.amount - transactionData.commission) || 110.40).toFixed(2);
        document.getElementById('modal-payment-method').textContent = transactionData.paymentMethod || 'Credit Card';
        document.getElementById('modal-payment-details').textContent = transactionData.paymentDetails || 'Ending in 4242';
        document.getElementById('modal-status-time').textContent = transactionData.statusTime || 'Mar 15, 2024 14:32';
        
        // Update status badge
        const statusElement = document.getElementById('modal-status');
        statusElement.textContent = transactionData.status || 'Completed';
        statusElement.className = 'px-3 py-1 text-xs font-semibold rounded-full ' + 
            (transactionData.status === 'completed' ? 'bg-green-100 text-green-800' :
             transactionData.status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
             'bg-red-100 text-red-800');
        
        // Show modal
        document.getElementById('transactionModal').classList.remove('hidden');
    }
    
    function closeModal() {
        document.getElementById('transactionModal').classList.add('hidden');
    }
    
    // Close modal on ESC key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeModal();
        }
    });
    
    // Close modal on background click
    document.getElementById('transactionModal').addEventListener('click', function(event) {
        if (event.target.id === 'transactionModal') {
            closeModal();
        }
    });
</script>