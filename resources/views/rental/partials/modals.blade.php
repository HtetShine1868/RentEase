<!-- Extend Stay Modal -->
<div id="extendModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Extend Your Stay</h3>
            <p id="extendPropertyName" class="text-sm text-gray-600 mt-1"></p>
        </div>
        <form id="extendForm" method="POST" action="{{ route('bookings.extend') }}">
            @csrf
            <input type="hidden" name="booking_id" id="extendBookingId">
            <input type="hidden" name="daily_price" id="extendDailyPrice">
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Check-out</label>
                        <input type="text" id="currentCheckout" readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Check-out Date</label>
                        <input type="date" name="new_check_out" required
                               id="newCheckoutDate"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               min="{{ now()->addDay()->format('Y-m-d') }}">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Extension Days</label>
                        <input type="number" id="extensionDays" readonly
                               class="block w-full rounded-md border-gray-300 bg-gray-50 shadow-sm sm:text-sm">
                    </div>
                    
                    <div class="p-3 bg-blue-50 rounded">
                        <div class="flex justify-between text-sm">
                            <span class="text-blue-700">Daily Rate:</span>
                            <span class="font-medium text-blue-700" id="dailyRateDisplay"></span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-blue-700">Total Extension Cost:</span>
                            <span class="font-bold text-blue-700" id="totalExtensionCost"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeExtendModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Extend Stay
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Submit Complaint</h3>
        </div>
        <form id="complaintForm" method="POST" action="{{ route('complaints.store') }}">
            @csrf
            <input type="hidden" name="booking_id" id="complaintBookingId">
            <input type="hidden" name="complaint_type" id="complaintType">
            <input type="hidden" name="related_id" id="relatedId">
            <input type="hidden" name="related_type" id="relatedType">
            
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                               placeholder="Brief description of the issue">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="4" required
                                  class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                  placeholder="Describe the issue in detail..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Complaint Type</label>
                        <select name="complaint_type_display" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="updateComplaintType(this.value)">
                            <option value="">Select type</option>
                            <option value="PROPERTY">Property Issue</option>
                            <option value="FOOD_SERVICE">Food Service</option>
                            <option value="LAUNDRY_SERVICE">Laundry Service</option>
                            <option value="USER">User Issue</option>
                            <option value="SYSTEM">System Issue</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" required
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="MEDIUM">Medium</option>
                            <option value="LOW">Low</option>
                            <option value="HIGH">High</option>
                            <option value="URGENT">Urgent</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="closeComplaintModal()"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700">
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Extend Stay Modal Functions
function openExtendModal(bookingId, propertyName, dailyPrice) {
    document.getElementById('extendBookingId').value = bookingId;
    document.getElementById('extendDailyPrice').value = dailyPrice;
    document.getElementById('extendPropertyName').textContent = propertyName;
    document.getElementById('dailyRateDisplay').textContent = '৳' + dailyPrice.toFixed(2);
    
    const checkoutInput = document.getElementById('newCheckoutDate');
    const today = new Date().toISOString().split('T')[0];
    checkoutInput.min = today;
    
    document.getElementById('extendModal').classList.remove('hidden');
    
    // Initialize date calculation
    updateExtensionCost();
}

function closeExtendModal() {
    document.getElementById('extendModal').classList.add('hidden');
    document.getElementById('extendForm').reset();
}

function updateExtensionCost() {
    const dailyPrice = parseFloat(document.getElementById('extendDailyPrice').value);
    const newCheckout = document.getElementById('newCheckoutDate').value;
    
    if (newCheckout) {
        const currentDate = new Date();
        const newDate = new Date(newCheckout);
        const diffTime = Math.abs(newDate - currentDate);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        document.getElementById('extensionDays').value = diffDays;
        document.getElementById('totalExtensionCost').textContent = '৳' + (diffDays * dailyPrice).toFixed(2);
    }
}

// Complaint Modal Functions
function openComplaintModal(bookingId, relatedType = 'property', relatedId = null) {
    document.getElementById('complaintBookingId').value = bookingId;
    document.getElementById('relatedId').value = relatedId;
    document.getElementById('relatedType').value = relatedType.toUpperCase();
    
    document.getElementById('complaintModal').classList.remove('hidden');
}

function updateComplaintType(type) {
    document.getElementById('complaintType').value = type;
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.getElementById('complaintForm').reset();
}

// Event Listeners
document.getElementById('newCheckoutDate').addEventListener('change', updateExtensionCost);

document.getElementById('extendModal').addEventListener('click', function(e) {
    if (e.target === this) closeExtendModal();
});

document.getElementById('complaintModal').addEventListener('click', function(e) {
    if (e.target === this) closeComplaintModal();
});
</script>