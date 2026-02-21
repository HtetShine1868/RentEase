@extends('layouts.food-provider')

@section('title', 'Business Information')
@section('subtitle', 'Update your restaurant business details')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-[#174455]">Business Information</h2>
            <a href="{{ route('food-provider.profile.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        @php
            $serviceProvider = auth()->user()->serviceProvider;
            $foodConfig = $serviceProvider->foodServiceConfig ?? null;
            $allMealTypes = App\Models\MealType::all();
            $serviceProviderMealTypes = $foodConfig ? $foodConfig->mealTypes->pluck('id')->toArray() : [];
        @endphp

        <form action="{{ route('food-provider.profile.business.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-6">
                {{-- Restaurant Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Restaurant Name</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $serviceProvider->business_name ?? '') }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('business_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="4" 
                              class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">{{ old('description', $serviceProvider->description ?? '') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Email</label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $serviceProvider->contact_email ?? '') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        @error('contact_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Phone</label>
                        <input type="text" name="contact_phone" value="{{ old('contact_phone', $serviceProvider->contact_phone ?? '') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        @error('contact_phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- City & Service Radius --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ old('city', $serviceProvider->city ?? '') }}" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        @error('city')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Service Radius (km)</label>
                        <input type="number" name="service_radius_km" value="{{ old('service_radius_km', $serviceProvider->service_radius_km ?? 5) }}" 
                               step="0.5" min="1" max="20" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        @error('service_radius_km')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Operating Hours --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Opening Time</label>
                        <input type="time" name="opening_time" value="{{ old('opening_time', $foodConfig->opening_time ?? '08:00') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Closing Time</label>
                        <input type="time" name="closing_time" value="{{ old('closing_time', $foodConfig->closing_time ?? '22:00') }}"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                </div>

                {{-- Meal Types --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Available Meal Types</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($allMealTypes as $mealType)
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="meal_types[]" value="{{ $mealType->id }}"
                                   class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]"
                                   {{ in_array($mealType->id, $serviceProviderMealTypes) ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">{{ $mealType->name }}</span>
                        </label>
                        @endforeach
                    </div>
                    @error('meal_types')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Service Types --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Types</label>
                    <div class="space-y-2">
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="supports_subscription" value="1"
                                   class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]"
                                   {{ $foodConfig && $foodConfig->supports_subscription ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Monthly Subscription</span>
                        </label>
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" name="supports_pay_per_eat" value="1"
                                   class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]"
                                   {{ !$foodConfig || $foodConfig->supports_pay_per_eat ? 'checked' : '' }}>
                            <span class="text-sm text-gray-700">Pay-Per-Eat</span>
                        </label>
                    </div>
                </div>

                {{-- Additional Settings --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Preparation Time (minutes)</label>
                        <input type="number" name="avg_preparation_minutes" value="{{ old('avg_preparation_minutes', $foodConfig->avg_preparation_minutes ?? 30) }}"
                               min="5" max="120" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Buffer (min/km)</label>
                        <input type="number" name="delivery_buffer_minutes" value="{{ old('delivery_buffer_minutes', $foodConfig->delivery_buffer_minutes ?? 15) }}"
                               min="1" max="30" required
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Discount (%)</label>
                        <input type="number" name="subscription_discount_percent" value="{{ old('subscription_discount_percent', $foodConfig->subscription_discount_percent ?? 10) }}"
                               min="0" max="50" step="0.5"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Cuisine Type</label>
                        <input type="text" name="cuisine_type" value="{{ old('cuisine_type', $foodConfig->cuisine_type ?? '') }}"
                               placeholder="e.g., Bangladeshi, Indian"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    </div>
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