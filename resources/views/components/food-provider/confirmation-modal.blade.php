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
            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title-{{ $id }}">
                {{ $title }}
            </h3>
            <div class="mt-2">
                <p class="text-sm text-gray-500" id="message-{{ $id }}">
                    {{ $message }}
                </p>
            </div>
        </div>
    </div>
    
    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
        <button type="button"
            id="confirm-btn-{{ $id }}"
            @click="{{ $onConfirm ? $onConfirm : 'closeModal(\'' . $id . '\')' }}"
            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 {{ $confirmColorClass }} text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 sm:ml-3 sm:w-auto sm:text-sm">
            {{ $confirmText }}
        </button>
        <button type="button"
                id="cancel-btn-{{ $id }}"
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
                const titleEl = modal.querySelector(`#modal-title-${modalId}`);
                if (titleEl) titleEl.textContent = options.title;
            }
            
            if (options.message) {
                const messageEl = modal.querySelector(`#message-${modalId}`);
                if (messageEl) messageEl.textContent = options.message;
            }
            
            if (options.confirmText) {
                const confirmBtn = modal.querySelector(`#confirm-btn-${modalId}`);
                if (confirmBtn) confirmBtn.textContent = options.confirmText;
            }
            
            if (options.cancelText) {
                const cancelBtn = modal.querySelector(`#cancel-btn-${modalId}`);
                if (cancelBtn) cancelBtn.textContent = options.cancelText;
            }
            
            if (options.confirmColor) {
                const confirmBtn = modal.querySelector(`#confirm-btn-${modalId}`);
                if (confirmBtn) {
                    // Remove existing color classes
                    confirmBtn.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
                    confirmBtn.classList.remove('bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500');
                    confirmBtn.classList.remove('bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500');
                    confirmBtn.classList.remove('bg-yellow-600', 'hover:bg-yellow-700', 'focus:ring-yellow-500');
                    
                    // Add new color classes
                    const colorMap = {
                        'danger': ['bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500'],
                        'primary': ['bg-indigo-600', 'hover:bg-indigo-700', 'focus:ring-indigo-500'],
                        'success': ['bg-green-600', 'hover:bg-green-700', 'focus:ring-green-500'],
                        'warning': ['bg-yellow-600', 'hover:bg-yellow-700', 'focus:ring-yellow-500']
                    };
                    
                    const colors = colorMap[options.confirmColor] || colorMap['danger'];
                    colors.forEach(color => confirmBtn.classList.add(color));
                }
            }
            
            // Update icon if provided
            if (options.icon) {
                const iconContainer = modal.querySelector(`.bg-{{ $confirmColor }}-100`);
                const iconElement = modal.querySelector('.fas, .far, .fal, .fab');
                if (iconContainer && options.iconColor) {
                    // Update icon color
                    iconContainer.classList.remove('bg-red-100', 'bg-indigo-100', 'bg-green-100', 'bg-yellow-100');
                    iconContainer.classList.add(`bg-${options.iconColor}-100`);
                }
                if (iconElement && options.icon) {
                    iconElement.className = options.icon;
                    if (options.iconColor) {
                        iconElement.classList.remove('text-red-400', 'text-indigo-400', 'text-green-400', 'text-yellow-400');
                        iconElement.classList.add(`text-${options.iconColor}-400`);
                    }
                }
            }
            
            // Open modal
            openModal(modalId);
            
            // Return promise for async operations
            return new Promise((resolve) => {
                const confirmBtn = modal.querySelector(`#confirm-btn-${modalId}`);
                const cancelBtn = modal.querySelector(`#cancel-btn-${modalId}`);
                
                const handleConfirm = () => {
                    cleanup();
                    if (options.onConfirm && typeof options.onConfirm === 'function') {
                        options.onConfirm();
                    }
                    resolve(true);
                };
                
                const handleCancel = () => {
                    cleanup();
                    if (options.onCancel && typeof options.onCancel === 'function') {
                        options.onCancel();
                    }
                    resolve(false);
                };
                
                const cleanup = () => {
                    if (confirmBtn) confirmBtn.removeEventListener('click', handleConfirm);
                    if (cancelBtn) cancelBtn.removeEventListener('click', handleCancel);
                    closeModal(modalId);
                };
                
                if (confirmBtn) confirmBtn.addEventListener('click', handleConfirm);
                if (cancelBtn) cancelBtn.addEventListener('click', handleCancel);
            });
        }
        
        return Promise.resolve(false);
    }
    
    // Convenience functions for common confirmation types
    function confirmDelete(options = {}) {
        return showConfirmation({
            title: 'Delete Item',
            message: 'Are you sure you want to delete this item? This action cannot be undone.',
            confirmText: 'Delete',
            confirmColor: 'danger',
            icon: 'fas fa-trash',
            iconColor: 'red',
            ...options
        });
    }
    
    function confirmPublish(options = {}) {
        return showConfirmation({
            title: 'Publish Item',
            message: 'Are you sure you want to publish this item? It will be visible to customers.',
            confirmText: 'Publish',
            confirmColor: 'success',
            icon: 'fas fa-check-circle',
            iconColor: 'green',
            ...options
        });
    }
    
    function confirmCancel(options = {}) {
        return showConfirmation({
            title: 'Cancel Changes',
            message: 'Are you sure you want to cancel? Any unsaved changes will be lost.',
            confirmText: 'Yes, Cancel',
            confirmColor: 'warning',
            icon: 'fas fa-times-circle',
            iconColor: 'yellow',
            ...options
        });
    }
    
    function confirmSave(options = {}) {
        return showConfirmation({
            title: 'Save Changes',
            message: 'Are you sure you want to save these changes?',
            confirmText: 'Save',
            confirmColor: 'primary',
            icon: 'fas fa-save',
            iconColor: 'indigo',
            ...options
        });
    }
    
    // Example usage:
    // 1. Basic confirmation:
    // <button onclick="showConfirmation({
    //     title: 'Custom Title',
    //     message: 'Custom message here...',
    //     confirmText: 'Proceed',
    //     onConfirm: () => console.log('Confirmed!')
    // })">Show Confirm</button>
    
    // 2. Delete confirmation:
    // <button onclick="confirmDelete({
    //     message: 'Delete menu item "Butter Chicken"?',
    //     onConfirm: () => deleteItem(1)
    // })">Delete</button>
    
    // 3. With async/await:
    // async function handleDelete() {
    //     const confirmed = await confirmDelete();
    //     if (confirmed) {
    //         // Perform delete action
    //         await deleteItem(1);
    //     }
    // }
</script>
@endpush