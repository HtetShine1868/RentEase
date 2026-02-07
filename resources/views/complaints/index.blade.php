<!-- Complaint Modal -->
<div id="complaintModal" class="hidden fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Submit Complaint</h3>
        <form method="POST" action="{{ route('complaints.store') }}">
            @csrf
            
            <div class="space-y-4">
                <!-- Complaint Type -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Complaint Type</label>
                    <select name="complaint_type" id="complaint_type" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                            required>
                        <option value="">Select Type</option>
                        <option value="PROPERTY">Property Issue</option>
                        <option value="FOOD_SERVICE">Food Service</option>
                        <option value="LAUNDRY_SERVICE">Laundry Service</option>
                        <option value="USER">User Issue</option>
                        <option value="SYSTEM">System Issue</option>
                    </select>
                </div>

                <!-- Related Property -->
                <div id="propertyField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Related Property</label>
                    <select name="related_id" id="related_id" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Property</option>
                        @foreach($userProperties as $property)
                            <option value="{{ $property->id }}">{{ $property->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" id="title" 
                           class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="Brief description of your complaint"
                           required>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Detailed Description</label>
                    <textarea name="description" id="description" rows="6" 
                              class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                              placeholder="Please provide detailed information about your complaint..."
                              required></textarea>
                </div>

                <!-- Priority -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Priority</label>
                    <select name="priority" id="priority" 
                            class="block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="MEDIUM">Medium</option>
                        <option value="LOW">Low</option>
                        <option value="HIGH">High</option>
                        <option value="URGENT">Urgent</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <button type="button" onclick="closeComplaintModal()" 
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                    Submit Complaint
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Complaint Modal Functions
function openComplaintModal() {
    document.getElementById('complaintModal').classList.remove('hidden');
}

function closeComplaintModal() {
    document.getElementById('complaintModal').classList.add('hidden');
    document.getElementById('complaintForm').reset();
}

// Show/hide property field based on complaint type
document.getElementById('complaint_type').addEventListener('change', function() {
    const propertyField = document.getElementById('propertyField');
    if (this.value === 'PROPERTY') {
        propertyField.classList.remove('hidden');
        document.getElementById('related_id').required = true;
    } else {
        propertyField.classList.add('hidden');
        document.getElementById('related_id').required = false;
    }
});

// Close modal when clicking outside
document.getElementById('complaintModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeComplaintModal();
    }
});
</script>