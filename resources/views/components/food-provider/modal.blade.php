@props([
    'id' => 'modal-' . uniqid(),
    'title' => '',
    'size' => 'medium', // small, medium, large, xlarge, full
    'footer' => true,
    'closeButton' => true,
    'backdrop' => true,
    'centered' => true
])

@php
    $sizes = [
        'small' => 'max-w-md',
        'medium' => 'max-w-lg',
        'large' => 'max-w-2xl',
        'xlarge' => 'max-w-4xl',
        'full' => 'max-w-full mx-4'
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['medium'];
@endphp

<div id="{{ $id }}"
     x-data="{ isOpen: false }"
     x-show="isOpen"
     @keydown.escape.window="isOpen = false"
     x-cloak
     class="fixed inset-0 z-50 overflow-y-auto {{ $centered ? 'flex items-center justify-center' : '' }}"
     aria-labelledby="modal-title"
     role="dialog"
     aria-modal="true">
    
    @if($backdrop)
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-ref="backdrop"></div>
    @endif
    
    <div class="relative min-h-screen {{ $centered ? 'flex items-center justify-center' : '' }} px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- This element is to trick the browser into centering the modal contents. -->
        @if($centered)
            <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        @endif
        
        <div class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle {{ $sizeClass }} sm:w-full sm:p-6"
             x-show="isOpen"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            
            <!-- Header -->
            <div class="border-b border-gray-200 pb-3 mb-4">
                <div class="flex items-start justify-between">
                    <div>
                        @if($title)
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                {{ $title }}
                            </h3>
                        @endif
                    </div>
                    @if($closeButton)
                        <button type="button"
                                @click="isOpen = false"
                                class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
            
            <!-- Content -->
            <div class="mt-2">
                {{ $slot }}
            </div>
            
            <!-- Footer -->
            @if($footer && isset($actions))
                <div class="mt-5 border-t border-gray-200 pt-4">
                    <div class="flex justify-end space-x-3">
                        {{ $actions }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Modal utility functions
    function openModal(id) {
        const modal = document.getElementById(id);
        if (modal && modal.__x) {
            modal.__x.$data.isOpen = true;
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeModal(id) {
        const modal = document.getElementById(id);
        if (modal && modal.__x) {
            modal.__x.$data.isOpen = false;
            document.body.style.overflow = 'auto';
        }
    }
    
    // Close modal when clicking outside (if backdrop enabled)
    // Using Alpine.js approach instead of vanilla JS for better compatibility
    document.addEventListener('alpine:init', function() {
        // No need for DOMContentLoaded event listener since Alpine handles it
        // The backdrop click is already handled by Alpine in the template
    });
</script>
@endpush