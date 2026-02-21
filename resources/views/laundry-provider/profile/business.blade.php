@extends('laundry-provider.layouts.provider')

@section('title', 'Business Information')
@section('subtitle', 'Update your business details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-[#174455]">Business Information</h2>
            <a href="{{ route('laundry-provider.profile.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <form action="{{ route('laundry-provider.profile.business.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Business Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $provider->business_name ?? '') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">{{ old('description', $provider->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $provider->contact_email ?? '') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $provider->contact_phone ?? '') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Service Radius --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Service Radius (km)</label>
                    <input type="number" name="service_radius_km" value="{{ old('service_radius_km', $provider->service_radius_km ?? 5) }}" required
                           step="0.1" min="1" max="100"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('service_radius_km')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                        Update Business Information
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection