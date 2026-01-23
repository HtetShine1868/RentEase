@extends('dashboard')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Profile</h1>
                <p class="mt-2 text-gray-600">Update your personal information</p>
            </div>
            <div>
                <a href="{{ route('profile.show') }}" 
                   class="inline-flex items-center px-3 py-1 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-6">
                <!-- Avatar Section -->
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <div class="relative">
                            @if($user->avatar_url)
                                <img src="{{ Storage::url($user->avatar_url) }}" 
                                     alt="{{ $user->name }}" 
                                     class="h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg">
                            @else
                                <div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center border-4 border-white shadow-lg">
                                    <i class="fas fa-user text-gray-400 text-5xl"></i>
                                </div>
                            @endif
                            <label for="avatar" class="absolute bottom-0 right-0 h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center cursor-pointer hover:bg-indigo-700 transition-colors">
                                <i class="fas fa-camera text-white"></i>
                                <input type="file" id="avatar" name="avatar" class="sr-only" accept="image/*">
                            </label>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Profile Picture</h3>
                        <p class="mt-1 text-sm text-gray-500">JPG, PNG or GIF (Max 2MB)</p>
                        @error('avatar')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Personal Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">
                            Full Name <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                   class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">
                            Email Address <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                   class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">
                            Phone Number
                        </label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="text" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                                   class="pl-10 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        </div>
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Gender -->
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700">
                            Gender <span class="text-red-500">*</span>
                        </label>
                        <select id="gender" name="gender" required
                                class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Select Gender</option>
                            <option value="MALE" {{ old('gender', $user->gender) == 'MALE' ? 'selected' : '' }}>Male</option>
                            <option value="FEMALE" {{ old('gender', $user->gender) == 'FEMALE' ? 'selected' : '' }}>Female</option>
                            <option value="OTHER" {{ old('gender', $user->gender) == 'OTHER' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

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
                        Save Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript for avatar preview -->
<script>
document.getElementById('avatar').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Create a new image element
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'h-32 w-32 rounded-full object-cover border-4 border-white shadow-lg';
            
            // Replace the current avatar
            const avatarContainer = document.querySelector('.flex-shrink-0 .relative');
            const currentAvatar = avatarContainer.querySelector('img, div');
            avatarContainer.insertBefore(img, currentAvatar);
            currentAvatar.remove();
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>
@endsection