@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Apply as Property Owner</h1>
                <p class="mt-2 text-gray-600">Fill out the form below to become a property owner on RMS.</p>
            </div>
            <div class="flex items-center">
                <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-home text-blue-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Requirements -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Requirements</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Property ownership proof (Deed/Tax Receipt) for each property</li>
                        <li>Valid identification document</li>
                        <li>Property location details</li>
                        <li>Contact information</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('role.apply.store', 'OWNER') }}" enctype="multipart/form-data" id="ownerApplicationForm">
            @csrf
            
            <div class="p-6 space-y-6">
                <!-- Business Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Business Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Business Name -->
                        <div>
                            <label for="business_name" class="block text-sm font-medium text-gray-700">
                                Business/Company Name <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                <input type="text" id="business_name" name="business_name" value="{{ old('business_name') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="e.g., Green Valley Properties">
                            </div>
                            @error('business_name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Type -->
                        <div>
                            <label for="property_type" class="block text-sm font-medium text-gray-700">
                                Primary Property Type <span class="text-red-500">*</span>
                            </label>
                            <select id="property_type" name="property_type" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Property Type</option>
                                <option value="HOSTEL" {{ old('property_type') == 'HOSTEL' ? 'selected' : '' }}>Hostel</option>
                                <option value="APARTMENT" {{ old('property_type') == 'APARTMENT' ? 'selected' : '' }}>Apartment Building</option>
                                <option value="BOTH" {{ old('property_type') == 'BOTH' ? 'selected' : '' }}>Both</option>
                            </select>
                            @error('property_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Property Count -->
                        <div>
                            <label for="property_count" class="block text-sm font-medium text-gray-700">
                                Number of Properties <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="property_count" name="property_count" value="{{ old('property_count', 1) }}" required min="1" max="20"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Maximum 20 properties per application</p>
                            @error('property_count')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Years of Experience -->
                        <div>
                            <label for="years_experience" class="block text-sm font-medium text-gray-700">
                                Years of Experience <span class="text-red-500">*</span>
                            </label>
                            <input type="number" id="years_experience" name="years_experience" value="{{ old('years_experience', 0) }}" required min="0"
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('years_experience')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Contact Person -->
                        <div>
                            <label for="contact_person" class="block text-sm font-medium text-gray-700">
                                Contact Person <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-user text-gray-400"></i>
                                </div>
                                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="Full name of contact person">
                            </div>
                            @error('contact_person')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Email -->
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700">
                                Contact Email <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="contact@example.com">
                            </div>
                            @error('contact_email')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Phone -->
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700">
                                Contact Phone <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone text-gray-400"></i>
                                </div>
                                <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone') }}" required
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="+8801XXXXXXXXX">
                            </div>
                            @error('contact_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Alternative Phone -->
                        <div>
                            <label for="alt_phone" class="block text-sm font-medium text-gray-700">
                                Alternative Phone
                            </label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-phone-alt text-gray-400"></i>
                                </div>
                                <input type="text" id="alt_phone" name="alt_phone" value="{{ old('alt_phone') }}"
                                       class="pl-10 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                       placeholder="+8801XXXXXXXXX">
                            </div>
                            @error('alt_phone')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Primary Business Location</h3>
                    
                    <!-- Business Address -->
                    <div class="mb-4">
                        <label for="business_address" class="block text-sm font-medium text-gray-700">
                            Business Address <span class="text-red-500">*</span>
                        </label>
                        <textarea id="business_address" name="business_address" rows="2" required
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('business_address') }}</textarea>
                        @error('business_address')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- City -->
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700">
                                City <span class="text-red-500">*</span>
                            </label>
                            <select id="city" name="city" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select City</option>
                                <option value="Dhaka" {{ old('city') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ old('city') == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                <option value="Rajshahi" {{ old('city') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ old('city') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Sylhet" {{ old('city') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Barisal" {{ old('city') == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                                <option value="Rangpur" {{ old('city') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ old('city') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                                <option value="Comilla" {{ old('city') == 'Comilla' ? 'selected' : '' }}>Comilla</option>
                                <option value="Narayanganj" {{ old('city') == 'Narayanganj' ? 'selected' : '' }}>Narayanganj</option>
                                <option value="Gazipur" {{ old('city') == 'Gazipur' ? 'selected' : '' }}>Gazipur</option>
                            </select>
                            @error('city')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- State/Division -->
                        <div>
                            <label for="state" class="block text-sm font-medium text-gray-700">
                                Division <span class="text-red-500">*</span>
                            </label>
                            <select id="state" name="state" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Division</option>
                                <option value="Dhaka" {{ old('state') == 'Dhaka' ? 'selected' : '' }}>Dhaka</option>
                                <option value="Chittagong" {{ old('state') == 'Chittagong' ? 'selected' : '' }}>Chittagong</option>
                                <option value="Rajshahi" {{ old('state') == 'Rajshahi' ? 'selected' : '' }}>Rajshahi</option>
                                <option value="Khulna" {{ old('state') == 'Khulna' ? 'selected' : '' }}>Khulna</option>
                                <option value="Sylhet" {{ old('state') == 'Sylhet' ? 'selected' : '' }}>Sylhet</option>
                                <option value="Barisal" {{ old('state') == 'Barisal' ? 'selected' : '' }}>Barisal</option>
                                <option value="Rangpur" {{ old('state') == 'Rangpur' ? 'selected' : '' }}>Rangpur</option>
                                <option value="Mymensingh" {{ old('state') == 'Mymensingh' ? 'selected' : '' }}>Mymensingh</option>
                            </select>
                            @error('state')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <!-- Postal Code -->
                        <div>
                            <label for="postal_code" class="block text-sm font-medium text-gray-700">
                                Postal Code <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., 1205">
                            @error('postal_code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Country -->
                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700">
                                Country <span class="text-red-500">*</span>
                            </label>
                            <select id="country" name="country" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="Bangladesh" {{ old('country', 'Bangladesh') == 'Bangladesh' ? 'selected' : '' }}>Bangladesh</option>
                                <option value="India" {{ old('country') == 'India' ? 'selected' : '' }}>India</option>
                                <option value="Pakistan" {{ old('country') == 'Pakistan' ? 'selected' : '' }}>Pakistan</option>
                                <option value="Nepal" {{ old('country') == 'Nepal' ? 'selected' : '' }}>Nepal</option>
                                <option value="Sri Lanka" {{ old('country') == 'Sri Lanka' ? 'selected' : '' }}>Sri Lanka</option>
                                <option value="Maldives" {{ old('country') == 'Maldives' ? 'selected' : '' }}>Maldives</option>
                            </select>
                            @error('country')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Area/Locality -->
                    <div class="mb-4">
                        <label for="area" class="block text-sm font-medium text-gray-700">
                            Area/Locality <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="area" name="area" value="{{ old('area') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="e.g., Gulshan, Banani, Dhanmondi">
                        @error('area')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Map Link (Optional) -->
                    <div class="mb-4">
                        <label for="map_link" class="block text-sm font-medium text-gray-700">
                            Google Maps Link (Optional)
                        </label>
                        <input type="url" id="map_link" name="map_link" value="{{ old('map_link') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="https://maps.google.com/?q=...">
                        <p class="mt-1 text-xs text-gray-500">Share your exact location on Google Maps for better navigation</p>
                        @error('map_link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Dynamic Property Documents Section -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">
                        Property Ownership Documents
                        <span class="text-sm font-normal text-gray-500 ml-2">(Upload proof for each property)</span>
                    </h3>
                    
                    <div id="property_documents_container">
                        <!-- Documents will be dynamically added here -->
                    </div>

                    <div class="mt-4 text-sm text-gray-600 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                        <span class="font-medium">Important:</span> You need to upload ownership proof for each property you own. 
                        Accepted documents: Property Deed, Tax Receipt, Utility Bill (with ownership proof), or Municipal License.
                    </div>
                </div>

                <!-- Document Upload for Identification -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b">Identification Document</h3>
                    <div class="mb-6">
                        <label for="id_document" class="block text-sm font-medium text-gray-700 mb-2">
                            Upload Valid ID (NID/Passport/Driver's License) <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-id-card text-gray-400 text-3xl mx-auto"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="id_document" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500">
                                        <span>Upload ID document</span>
                                        <input id="id_document" name="id_document" type="file" accept=".pdf,.jpg,.jpeg,.png" required class="sr-only">
                                    </label>
                                </div>
                                <p class="text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                        @error('id_document')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Commission Information -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        Commission Information
                    </h4>
                    <p class="text-sm text-blue-700">
                        As a Property Owner, you'll pay <strong>3% commission</strong> on apartment bookings and 
                        <strong>5% commission</strong> on hostel bookings. This commission will be automatically deducted 
                        from your earnings.
                    </p>
                </div>

                <!-- Terms Agreement -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" id="terms" name="terms" required 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="terms" class="font-medium text-gray-700">
                                I confirm that I own all the properties listed and the documents provided are authentic
                            </label>
                            <p class="text-gray-500">By submitting this application, you confirm that all information provided is accurate and you agree to follow RMS guidelines for property owners.</p>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('role.apply.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Submit Application
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const propertyCountInput = document.getElementById('property_count');
    const documentsContainer = document.getElementById('property_documents_container');

    function generatePropertyDocuments(count) {
        let html = '';
        for (let i = 1; i <= count; i++) {
            html += `
                <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <h4 class="text-md font-medium text-gray-800 mb-3">
                        Property #${i} Documents
                        <span class="text-sm font-normal text-gray-500 ml-2">(Upload ownership proof)</span>
                    </h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                        <!-- Property Name/Address -->
                        <div class="md:col-span-2">
                            <label for="property_name_${i}" class="block text-sm font-medium text-gray-700">
                                Property Name/Address <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="property_name_${i}" name="property_names[]" required
                                   class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="e.g., Green Valley Apartment - House 12, Road 5, Gulshan">
                        </div>
                        
                        <!-- Property Type -->
                        <div>
                            <label for="property_doc_type_${i}" class="block text-sm font-medium text-gray-700">
                                Document Type <span class="text-red-500">*</span>
                            </label>
                            <select id="property_doc_type_${i}" name="property_doc_types[]" required
                                    class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Select Document Type</option>
                                <option value="DEED">Property Deed</option>
                                <option value="TAX_RECEIPT">Tax Receipt</option>
                                <option value="UTILITY_BILL">Utility Bill (with ownership proof)</option>
                                <option value="MUNICIPAL_LICENSE">Municipal License</option>
                                <option value="OTHER">Other Ownership Proof</option>
                            </select>
                        </div>
                        
                        <!-- Document Upload -->
                        <div>
                            <label for="property_doc_${i}" class="block text-sm font-medium text-gray-700">
                                Upload Document <span class="text-red-500">*</span>
                            </label>
                            <div class="mt-1">
                                <input type="file" id="property_doc_${i}" name="property_documents[]" 
                                       accept=".pdf,.jpg,.jpeg,.png" required
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>
                            <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG up to 2MB</p>
                        </div>
                    </div>
                    
                    <!-- Additional Notes (Optional) -->
                    <div>
                        <label for="property_notes_${i}" class="block text-sm font-medium text-gray-700">
                            Additional Notes (Optional)
                        </label>
                        <textarea id="property_notes_${i}" name="property_notes[]" rows="1"
                                  class="mt-1 block w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Any additional information about this property"></textarea>
                    </div>
                </div>
            `;
        }
        return html;
    }

    function updateDocuments() {
        const count = parseInt(propertyCountInput.value) || 1;
        documentsContainer.innerHTML = generatePropertyDocuments(count);
    }

    propertyCountInput.addEventListener('input', updateDocuments);
    
    // Initialize with default value
    updateDocuments();
});
</script>
@endpush
@endsection