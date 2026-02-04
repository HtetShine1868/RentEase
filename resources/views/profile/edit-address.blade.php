@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Manage Addresses</h1>
                <p class="mt-2 text-gray-600">Add or update your delivery addresses</p>
            </div>
            <div>
                <a href="{{ route('profile.show') }}" 
                   class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Address List -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Your Addresses</h3>
        </div>
        
        @if($addresses->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($addresses as $address)
                    <div class="px-6 py-4 hover:bg-gray-50">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center">
                                    <h4 class="text-md font-medium text-gray-900">
                                        {{ $address->address_type }} Address
                                        @if($address->is_default)
                                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                <i class="fas fa-star mr-1"></i> Default
                                            </span>
                                        @endif
                                    </h4>
                                </div>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>{{ $address->address_line1 }}</p>
                                    @if($address->address_line2)
                                        <p>{{ $address->address_line2 }}</p>
                                    @endif
                                    <p>
                                        {{ $address->city }}, {{ $address->state }} 
                                        {{ $address->postal_code }}, {{ $address->country }}
                                    </p>
                                    @if($address->latitude && $address->longitude)
                                        <p class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-map-marker-alt mr-1"></i>
                                            Coordinates: {{ $address->latitude }}, {{ $address->longitude }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center space-x-2 ml-4">
                                @if(!$address->is_default)
                                    <form action="{{ route('profile.address.set-default', $address->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-gray-300 text-xs font-medium rounded text-gray-700 bg-white hover:bg-gray-50">
                                            <i class="fas fa-star mr-1"></i> Set Default
                                        </button>
                                    </form>
                                @endif
                                
                                @if($addresses->count() > 1)
                                    <form action="{{ route('profile.address.delete', $address->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this address?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex items-center px-2 py-1 border border-red-300 text-xs font-medium rounded text-red-700 bg-white hover:bg-red-50">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <i class="fas fa-map-marker-alt text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500">No addresses added yet</p>
            </div>
        @endif
    </div>

    <!-- Add/Edit Address Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">
                {{ $defaultAddress ? 'Edit Default Address' : 'Add New Address' }}
            </h3>
        </div>
        
        <form method="POST" action="{{ route('profile.address.update') }}">
            @csrf
            @if($defaultAddress)
                <input type="hidden" name="address_id" value="{{ $defaultAddress->id }}">
            @endif
            
            <div class="p-6 space-y-6">
                <!-- Address Type -->
                <div>
                    <label for="address_type" class="block text-sm font-medium text-gray-700">
                        Address Type <span class="text-red-500">*</span>
                    </label>
                    <select id="address_type" name="address_type" required
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Select Type</option>
                        <option value="HOME" {{ old('address_type', $defaultAddress->address_type ?? '') == 'HOME' ? 'selected' : '' }}>Home</option>
                        <option value="WORK" {{ old('address_type', $defaultAddress->address_type ?? '') == 'WORK' ? 'selected' : '' }}>Work</option>
                        <option value="OTHER" {{ old('address_type', $defaultAddress->address_type ?? '') == 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('address_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Line 1 -->
                <div>
                    <label for="address_line1" class="block text-sm font-medium text-gray-700">
                        Address Line 1 <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="address_line1" name="address_line1" 
                           value="{{ old('address_line1', $defaultAddress->address_line1 ?? '') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('address_line1')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Address Line 2 -->
                <div>
                    <label for="address_line2" class="block text-sm font-medium text-gray-700">
                        Address Line 2 (Optional)
                    </label>
                    <input type="text" id="address_line2" name="address_line2" 
                           value="{{ old('address_line2', $defaultAddress->address_line2 ?? '') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- City, State, Postal Code -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700">
                            City <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="city" name="city" 
                               value="{{ old('city', $defaultAddress->city ?? '') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700">
                            State/Division <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="state" name="state" 
                               value="{{ old('state', $defaultAddress->state ?? '') }}" required
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        @error('state')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="postal_code" class="block text-sm font-medium text-gray-700">
                            Postal Code
                        </label>
                        <input type="text" id="postal_code" name="postal_code" 
                               value="{{ old('postal_code', $defaultAddress->postal_code ?? '') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Country -->
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700">
                        Country <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="country" name="country" 
                           value="{{ old('country', $defaultAddress->country ?? 'Bangladesh') }}" required
                           class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    @error('country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Coordinates (Optional) -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="latitude" class="block text-sm font-medium text-gray-700">
                            Latitude (Optional)
                        </label>
                        <input type="number" step="any" id="latitude" name="latitude" 
                               value="{{ old('latitude', $defaultAddress->latitude ?? '') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., 23.8103">
                    </div>

                    <div>
                        <label for="longitude" class="block text-sm font-medium text-gray-700">
                            Longitude (Optional)
                        </label>
                        <input type="number" step="any" id="longitude" name="longitude" 
                               value="{{ old('longitude', $defaultAddress->longitude ?? '') }}"
                               class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="e.g., 90.4125">
                    </div>
                </div>

                <!-- Default Address Checkbox -->
                @if($addresses->count() > 0 && $defaultAddress)
                    <div class="flex items-center">
                        <input type="checkbox" id="is_default" name="is_default" value="1"
                               {{ old('is_default', $defaultAddress->is_default) ? 'checked' : '' }}
                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_default" class="ml-2 block text-sm text-gray-700">
                            Set as default address
                        </label>
                    </div>
                @endif

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('profile.show') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-times mr-2"></i>
                        Cancel
                    </a>
                    <button type="submit" 
                            class="inline-flex items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-save mr-2"></i>
                        {{ $defaultAddress ? 'Update Address' : 'Add Address' }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection