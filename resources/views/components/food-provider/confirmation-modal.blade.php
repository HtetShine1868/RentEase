@props([
    'id' => 'confirmation-modal-' . uniqid(),
    'title' => 'Are you sure?',
    'message' => 'This action cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'confirmColor' => 'danger', // danger, primary, success, warning
    'icon' => 'fas fa-exclamation-triangle',
    'iconColor' => 'text-yellow-400',
    'onConfirm' => '',
    'onCancel' => '',
    'size' => 'small'
])

@php
    $confirmColors = [
        'danger' => 'bg-red-600 hover:bg-red-700 focus:ring-red-500',
        'primary' => 'bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500',
        'success' => 'bg-green-600 hover:bg-green-700 focus:ring-green-500',
        'warning' => 'bg-yellow-600 hover:bg-yellow-700 focus:ring-yellow-500'
    ];
    
    $confirmColorClass = $confirmColors[$confirmColor] ?? $confirmColors['danger'];
@endphp

<x-food-provider.modal :id="$id" :title="$title" :size="$size" :footer="false" :closeButton="false">
    <div class="sm:flex sm:items-start">
        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-{{ $confirmColor }}-100 sm:mx-0 sm:h-10 sm:w-10">
            <i class="{{ $icon }} {{ $iconColor }} h-6 w-6"></i>
        </div>
        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                {{ $title }}
            </h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500">
                    {{ $message }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
        <button type="button"
                @click="{{ $onConfirm ? $onConfirm : 'closeModal(\'' . $id . '\')' }}"
                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $confirmColorClass }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
            {{ $confirmText }}
        </button>
        <button type="button"
                @click="{{ $onCancel ? $onCancel : 'closeModal(\'' . $id . '\')' }}"
                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
            {{ $cancelText }}
        </button>
    </div>
</x-food-provider.modal>

@push('scripts')
<script>
    // Confirmation utility
    function showConfirmation(options = {}) {
        const modalId = options.id || '{{ $id }}';
        const modal = document.getElementById(modalId);
        
        if (modal) {
            // Update modal content if provided
            if (options.title) {
                const titleEl = modal.querySelector('#modal-title');
                if (titleEl) titleEl.textContent = options.title;
            }
            
            if (options.message) {
                const messageEl = modal.querySelector('.text-sm.text-gray-500');
                if (messageEl) messageEl.textContent = options.message;
            }
            
            if (options.confirmText) {
                const confirmBtn = modal.querySelector('button.' + '{{ $confirmColorClass.split(" ")[0] }}');
                if (confirmBtn) confirmBtn.textContent = options.confirmText;
            }
            
            // Open modal
            openModal(modalId);
            
            // Return promise for async operations
            return new Promise((resolve) => {
                const confirmBtn = modal.querySelector('button.' + '{{ $confirmColorClass.split(" ")[0] }}');
                const cancelBtn = modal.querySelector('button.bg-white');
                
                const handleConfirm = () => {
                    cleanup();
                    resolve(true);
                };
                
                const handleCancel = () => {
                    cleanup();
                    resolve(false);
                };
                
                const cleanup = () => {
                    confirmBtn.removeEventListener('click', handleConfirm);
                    cancelBtn.removeEventListener('click', handleCancel);
                    closeModal(modalId);
                };
                
                confirmBtn.addEventListener('click', handleConfirm);
                cancelBtn.addEventListener('click', handleCancel);
            });
        }
        
        return Promise.resolve(false);
    }
    
    // Example usage:
    // <button onclick="showConfirmation({
    //     title: 'Delete Item',
    //     message: 'Are you sure you want to delete this menu item?',
    //     confirmText: 'Delete',
    //     onConfirm: 'deleteItem(1)'
    // })">Delete</button>
</script>
@endpush