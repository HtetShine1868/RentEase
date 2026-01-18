<!-- Add role indicator -->
@auth
    <div class="hidden sm:ml-6 sm:flex sm:items-center space-x-4">
        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
            {{ Auth::user()->primaryRole }}
        </span>
        <!-- ... rest of the navigation ... -->
    </div>
@endauth