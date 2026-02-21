@extends('dashboard')

@section('title', 'Edit Profile')
@section('subtitle', 'Update your personal information')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-[#174455]">Edit Profile</h2>
            <a href="{{ route('profile.index') }}" class="text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back
            </a>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                {{-- Avatar Upload --}}
                <div class="flex items-center space-x-6 mb-4">
                    <div class="shrink-0">
                        @if(auth()->user()->avatar_url)
                            <img class="h-16 w-16 rounded-full object-cover border-2 border-[#174455]"
                                 src="{{ Storage::url(auth()->user()->avatar_url) }}"
                                 alt="Current avatar">
                        @else
                            <div class="h-16 w-16 rounded-full bg-[#174455] flex items-center justify-center text-white text-xl font-bold">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Profile Avatar</label>
                        <input type="file" name="avatar" accept="image/*"
                               class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-[#174455] file:text-white hover:file:bg-[#1f556b]">
                        @error('avatar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                    <select name="gender" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                        <option value="">Select Gender</option>
                        <option value="MALE" {{ old('gender', auth()->user()->gender) == 'MALE' ? 'selected' : '' }}>Male</option>
                        <option value="FEMALE" {{ old('gender', auth()->user()->gender) == 'FEMALE' ? 'selected' : '' }}>Female</option>
                        <option value="OTHER" {{ old('gender', auth()->user()->gender) == 'OTHER' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="pt-4">
                    <button type="submit" class="bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                        Update Profile
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection