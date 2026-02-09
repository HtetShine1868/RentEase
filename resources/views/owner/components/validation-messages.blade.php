{{-- Success Messages --}}
@if(session('success'))
<div class="mb-6">
    <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-green-800">Success</h3>
                <div class="mt-1 text-sm text-green-700">
                    <p>{{ session('success') }}</p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="inline-flex text-green-700 hover:text-green-800">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Error Messages --}}
@if(session('error'))
<div class="mb-6">
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Error</h3>
                <div class="mt-1 text-sm text-red-700">
                    <p>{{ session('error') }}</p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="inline-flex text-red-700 hover:text-red-800">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Validation Errors --}}
@if($errors->any())
<div class="mb-6">
    <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                    @if($errors->count() > 1)
                        There were {{ $errors->count() }} errors with your submission
                    @else
                        There was an error with your submission
                    @endif
                </h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="inline-flex text-red-700 hover:text-red-800">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Warning Messages --}}
@if(session('warning'))
<div class="mb-6">
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-yellow-800">Warning</h3>
                <div class="mt-1 text-sm text-yellow-700">
                    <p>{{ session('warning') }}</p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="inline-flex text-yellow-700 hover:text-yellow-800">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Info Messages --}}
@if(session('info'))
<div class="mb-6">
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-info-circle text-blue-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Information</h3>
                <div class="mt-1 text-sm text-blue-700">
                    <p>{{ session('info') }}</p>
                </div>
            </div>
            <div class="ml-auto pl-3">
                <button type="button" onclick="this.parentElement.parentElement.style.display='none'" 
                        class="inline-flex text-blue-700 hover:text-blue-800">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</div>
@endif