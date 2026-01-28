@props([
    'id' => 'toast-' . uniqid(),
    'type' => 'success', // success, error, warning, info
    'title' => '',
    'message' => '',
    'duration' => 5000,
    'dismissible' => true
])

@php
    $typeConfig = [
        'success' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'icon' => 'fas fa-check-circle text-green-400',
            'titleColor' => 'text-green-800',
            'buttonColor' => 'text-green-500 hover:bg-green-100',
            'progressColor' => 'bg-green-500'
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'icon' => 'fas fa-exclamation-circle text-red-400',
            'titleColor' => 'text-red-800',
            'buttonColor' => 'text-red-500 hover:bg-red-100',
            'progressColor' => 'bg-red-500'
        ],
        'warning' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'icon' => 'fas fa-exclamation-triangle text-yellow-400',
            'titleColor' => 'text-yellow-800',
            'buttonColor' => 'text-yellow-500 hover:bg-yellow-100',
            'progressColor' => 'bg-yellow-500'
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'icon' => 'fas fa-info-circle text-blue-400',
            'titleColor' => 'text-blue-800',
            'buttonColor' => 'text-blue-500 hover:bg-blue-100',
            'progressColor' => 'bg-blue-500'
        ]
    ];
    
    $config = $typeConfig[$type] ?? $typeConfig['info'];
@endphp

<div id="{{ $id }}"
     x-data="{ show: true }"
     x-show="show"
     x-init="
        // Auto-hide after duration
        @if($duration > 0)
            setTimeout(() => { show = false; }, {{ $duration }});
        @endif
     "
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="transform opacity-0 translate-y-2"
     x-transition:enter-end="transform opacity-100 translate-y-0"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="transform opacity-100 translate-y-0"
     x-transition:leave-end="transform opacity-0 translate-y-2"
     @if($duration > 0)
         x-on:toast-hide.window="if ($event.detail.id === '{{ $id }}') show = false;"
     @endif
     class="fixed top-4 right-4 z-50 max-w-sm w-full {{ $config['bg'] }} rounded-lg shadow-lg border {{ $config['border'] }} p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <i class="{{ $config['icon'] }} h-5 w-5"></i>
        </div>
        <div class="ml-3 flex-1">
            @if($title)
                <p class="text-sm font-medium {{ $config['titleColor'] }}">
                    {{ $title }}
                </p>
            @endif
            @if($message)
                <p class="mt-1 text-sm text-gray-700">
                    {{ $message }}
                </p>
            @endif
        </div>
        @if($dismissible)
            <div class="ml-4 flex-shrink-0 flex">
                <button type="button"
                        @click="show = false"
                        class="inline-flex rounded-md p-1.5 {{ $config['buttonColor'] }} focus:outline-none focus:ring-2 focus:ring-offset-2">
                    <span class="sr-only">Dismiss</span>
                    <i class="fas fa-times h-4 w-4"></i>
                </button>
            </div>
        @endif
    </div>
    
    @if($duration > 0)
        <div class="mt-2">
            <div class="h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                <div class="h-full {{ $config['progressColor'] }}"
                     x-ref="progressBar"
                     x-init="
                        $nextTick(() => {
                            const progressBar = $refs.progressBar;
                            progressBar.style.width = '100%';
                            setTimeout(() => {
                                progressBar.style.transition = 'width {{ $duration }}ms linear';
                                progressBar.style.width = '0%';
                            }, 100);
                        });
                     "></div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Toast utility functions
    function showToast(id) {
        const toast = document.getElementById(id);
        if (toast && toast.__x) {
            toast.__x.$data.show = true;
        }
    }
    
    function hideToast(id) {
        const toast = document.getElementById(id);
        if (toast && toast.__x) {
            toast.__x.$data.show = false;
        }
    }
    
    // Alternative method to hide toast using custom event
    function hideToastById(id) {
        window.dispatchEvent(new CustomEvent('toast-hide', {
            detail: { id: id }
        }));
    }
</script>
@endpush