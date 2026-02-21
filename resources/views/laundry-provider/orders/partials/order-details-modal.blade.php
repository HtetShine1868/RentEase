<div id="orderDetailsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-lg bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-bold text-[#174455] flex items-center">
                <i class="fas fa-file-alt text-[#ffdb9f] mr-2"></i> Order Details
            </h3>
            <button onclick="closeOrderModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="modalContent" class="space-y-4">
            {{-- Content will be loaded via AJAX --}}
            <div class="text-center py-8">
                <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-[#174455] mx-auto mb-3"></div>
                <p class="text-gray-600">Loading order details...</p>
            </div>
        </div>
        
        <div class="mt-6 flex justify-end">
            <button onclick="closeOrderModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                Close
            </button>
        </div>
    </div>
</div>

<script>
function openOrderModal(orderId) {
    document.getElementById('orderDetailsModal').classList.remove('hidden');
    
    // Show loading state
    document.getElementById('modalContent').innerHTML = `
        <div class="text-center py-8">
            <div class="loading-spinner rounded-full h-12 w-12 border-t-2 border-b-2 border-[#174455] mx-auto mb-3"></div>
            <p class="text-gray-600">Loading order details...</p>
        </div>
    `;
    
    // Fetch order details
    fetch(`/laundry-provider/orders/${orderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.text();
    })
    .then(html => {
        document.getElementById('modalContent').innerHTML = html;
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('modalContent').innerHTML = `
            <div class="text-center py-8 text-red-600">
                <i class="fas fa-exclamation-circle text-4xl mb-2"></i>
                <p>Failed to load order details</p>
                <button onclick="closeOrderModal()" class="mt-3 px-4 py-2 bg-gray-100 rounded-lg">Close</button>
            </div>
        `;
    });
}

function closeOrderModal() {
    document.getElementById('orderDetailsModal').classList.add('hidden');
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('orderDetailsModal');
    if (event.target == modal) {
        closeOrderModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeOrderModal();
    }
});
</script>