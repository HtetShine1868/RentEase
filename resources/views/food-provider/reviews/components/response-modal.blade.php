<div id="responseModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

        <!-- Modal panel -->
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <!-- Header -->
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Respond to Review
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">
                            <span id="response-review-id">Review #1</span> by 
                            <span id="response-customer-name" class="font-medium">John Doe</span>
                        </p>
                    </div>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeResponseModal()">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Response Form -->
                <form id="responseForm" onsubmit="submitResponse(event)">
                    <div class="space-y-4">
                        <!-- Response Type -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Response Type</label>
                            <div class="flex space-x-4">
                                <label class="inline-flex items-center">
                                    <input type="radio" name="response_type" value="thank" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" checked>
                                    <span class="ml-2 text-sm text-gray-700">Thank You</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="response_type" value="apology" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Apology</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" name="response_type" value="clarification" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-700">Clarification</span>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Response Text -->
                        <div>
                            <label for="response_text" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Response
                            </label>
                            <textarea id="response_text" name="response_text" rows="6" 
                                      class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                      placeholder="Type your response here..."></textarea>
                            <p class="mt-1 text-sm text-gray-500">
                                Keep your response professional and helpful. This will be visible to all customers.
                            </p>
                        </div>
                        
                        <!-- Templates -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quick Templates</label>
                            <div class="grid grid-cols-1 gap-2">
                                <button type="button" onclick="useTemplate('thank')" 
                                        class="text-left px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Thank you for your positive feedback! We're thrilled you enjoyed your meal.
                                </button>
                                <button type="button" onclick="useTemplate('apology')"
                                        class="text-left px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    We apologize for any inconvenience. Please contact us directly so we can make it right.
                                </button>
                                <button type="button" onclick="useTemplate('improvement')"
                                        class="text-left px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                    Thank you for your feedback. We're always working to improve our service.
                                </button>
                            </div>
                        </div>
                        
                        <!-- Preview -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                            <div class="bg-white p-3 rounded border border-gray-200">
                                <div class="flex items-center mb-2">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-semibold text-sm mr-2">
                                        YR
                                    </div>
                                    <div>
                                        <h5 class="text-sm font-medium text-gray-900">Your Response</h5>
                                        <p class="text-xs text-gray-500">Just now</p>
                                    </div>
                                </div>
                                <p id="response-preview" class="text-sm text-gray-700">
                                    Your response will appear here...
                                </p>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <!-- Footer -->
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="submit" form="responseForm" 
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                    Submit Response
                </button>
                <button type="button" 
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeResponseModal()">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const templates = {
        'thank': "Thank you for your positive feedback! We're thrilled you enjoyed your meal. We strive to provide the best quality food and service to all our customers. Looking forward to serving you again!",
        'apology': "We sincerely apologize for the inconvenience you experienced. This is not the standard we aim to deliver. Please contact us directly at support@restaurant.com so we can make this right and ensure your next experience is perfect.",
        'improvement': "Thank you for taking the time to share your feedback. We appreciate your input and are always working to improve our service. Your comments have been noted and will help us serve you better in the future."
    };
    
    function useTemplate(templateKey) {
        const textarea = document.getElementById('response_text');
        const preview = document.getElementById('response-preview');
        
        if (templates[templateKey]) {
            textarea.value = templates[templateKey];
            preview.textContent = templates[templateKey];
        }
    }
    
    function submitResponse(event) {
        event.preventDefault();
        
        const formData = new FormData(event.target);
        const responseText = formData.get('response_text');
        const responseType = formData.get('response_type');
        
        // Validate response
        if (!responseText.trim()) {
            alert('Please enter a response before submitting.');
            return;
        }
        
        // In production: Submit via API
        console.log('Submitting response:', {
            type: responseType,
            text: responseText
        });
        
        // Show success message
        alert('Response submitted successfully!');
        closeResponseModal();
        
        // In production: Reload reviews or update UI dynamically
    }
    
    // Update preview as user types
    document.getElementById('response_text').addEventListener('input', function() {
        document.getElementById('response-preview').textContent = this.value || 'Your response will appear here...';
    });
    
    // Close modal on background click
    document.getElementById('responseModal').addEventListener('click', function(event) {
        if (event.target.id === 'responseModal') {
            closeResponseModal();
        }
    });
</script>