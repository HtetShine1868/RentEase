@props([
    'action' => 'create', // 'create' or 'edit'
    'category' => null
])

<div class="space-y-4">
    <!-- Category Name -->
    <div>
        <label for="category_name_{{ $action }}" class="block text-sm font-medium text-gray-700">
            Category Name *
        </label>
        <div class="mt-1">
            <input type="text" 
                   name="name" 
                   id="category_name_{{ $action }}" 
                   value="{{ $category['name'] ?? '' }}"
                   class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                   placeholder="e.g., Appetizers"
                   required>
        </div>
    </div>

    <!-- Slug (Auto-generated) -->
    <div>
        <label for="category_slug_{{ $action }}" class="block text-sm font-medium text-gray-700">
            URL Slug
        </label>
        <div class="mt-1 flex rounded-md shadow-sm">
            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500 sm:text-sm">
                /menu/
            </span>
            <input type="text" 
                   name="slug" 
                   id="category_slug_{{ $action }}" 
                   value="{{ $category['slug'] ?? '' }}"
                   class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-r-md border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                   placeholder="auto-generated">
        </div>
        <p class="mt-1 text-xs text-gray-500">
            Auto-generated from name, used in URLs
        </p>
    </div>

    <!-- Description -->
    <div>
        <label for="category_description_{{ $action }}" class="block text-sm font-medium text-gray-700">
            Description
        </label>
        <div class="mt-1">
            <textarea id="category_description_{{ $action }}" 
                      name="description" 
                      rows="3" 
                      class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                      placeholder="Brief description of this category">{{ $category['description'] ?? '' }}</textarea>
        </div>
    </div>

    <!-- Icon & Color Selection -->
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Icon
            </label>
            <div class="relative">
                <button type="button" 
                        class="relative w-full bg-white border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left cursor-default focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        @click="iconDropdownOpen = !iconDropdownOpen">
                    <span class="flex items-center">
                        <i class="fas {{ $category['icon'] ?? 'fa-utensils' }} text-gray-400 mr-2"></i>
                        <span class="block truncate">{{ $category['icon'] ?? 'Select Icon' }}</span>
                    </span>
                    <span class="ml-3 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </span>
                </button>
                
                <!-- Icon Dropdown -->
                <div x-show="iconDropdownOpen" 
                     @click.away="iconDropdownOpen = false"
                     class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                    <div class="grid grid-cols-6 gap-2 p-2">
                        @foreach(['fa-utensils', 'fa-utensil-spoon', 'fa-hamburger', 'fa-pizza-slice', 'fa-ice-cream', 'fa-glass-whiskey', 'fa-coffee', 'fa-leaf', 'fa-drumstick-bite', 'fa-fish', 'fa-egg', 'fa-cheese'] as $icon)
                        <button type="button"
                                class="p-2 rounded-md hover:bg-gray-100 flex items-center justify-center"
                                @click="selectedIcon = '{{ $icon }}'; iconDropdownOpen = false">
                            <i class="fas {{ $icon }} text-gray-600"></i>
                        </button>
                        @endforeach
                    </div>
                </div>
                <input type="hidden" name="icon" x-model="selectedIcon" value="{{ $category['icon'] ?? 'fa-utensils' }}">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                Color
            </label>
            <div class="flex flex-wrap gap-2">
                @foreach([
                    'green' => 'bg-green-500', 
                    'blue' => 'bg-blue-500', 
                    'red' => 'bg-red-500', 
                    'yellow' => 'bg-yellow-500',
                    'purple' => 'bg-purple-500', 
                    'pink' => 'bg-pink-500',
                    'indigo' => 'bg-indigo-500',
                    'teal' => 'bg-teal-500'
                ] as $colorName => $colorClass)
                <button type="button"
                        class="h-8 w-8 rounded-full {{ $colorClass }} border-2 {{ ($category['color'] ?? 'green') === $colorName ? 'border-gray-800 ring-2 ring-offset-1 ring-gray-400' : 'border-white' }} focus:outline-none"
                        @click="selectedColor = '{{ $colorName }}'"
                        title="{{ ucfirst($colorName) }}">
                </button>
                @endforeach
            </div>
            <input type="hidden" name="color" x-model="selectedColor" value="{{ $category['color'] ?? 'green' }}">
        </div>
    </div>

    <!-- Sort Order -->
    <div>
        <label for="sort_order_{{ $action }}" class="block text-sm font-medium text-gray-700">
            Display Order
        </label>
        <div class="mt-1">
            <select id="sort_order_{{ $action }}" 
                    name="sort_order" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                @for($i = 1; $i <= 20; $i++)
                    <option value="{{ $i }}" {{ ($category['sort_order'] ?? 1) == $i ? 'selected' : '' }}>
                        {{ $i }}
                    </option>
                @endfor
            </select>
        </div>
        <p class="mt-1 text-xs text-gray-500">
            Lower numbers appear first in the menu
        </p>
    </div>

    <!-- Status -->
    <div>
        <label for="status_{{ $action }}" class="block text-sm font-medium text-gray-700">
            Status
        </label>
        <div class="mt-1">
            <select id="status_{{ $action }}" 
                    name="status" 
                    class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
                <option value="active" {{ ($category['status'] ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ ($category['status'] ?? 'active') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <!-- Commission Override (Optional) -->
    <div x-data="{ showCommissionOverride: false }">
        <div class="flex items-center justify-between">
            <label class="block text-sm font-medium text-gray-700">
                Commission Settings
            </label>
            <button type="button" 
                    @click="showCommissionOverride = !showCommissionOverride"
                    class="text-xs text-indigo-600 hover:text-indigo-500">
                <span x-show="!showCommissionOverride">Set Custom Commission</span>
                <span x-show="showCommissionOverride" x-cloak>Use Default</span>
            </button>
        </div>
        
        <div x-show="showCommissionOverride" x-transition class="mt-2">
            <div class="rounded-md bg-yellow-50 p-3">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-yellow-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">
                            Custom commission rates override the default 12%. Use cautiously.
                        </p>
                    </div>
                </div>
                <div class="mt-3">
                    <label for="custom_commission" class="block text-xs font-medium text-gray-700">
                        Commission Percentage
                    </label>
                    <div class="mt-1">
                        <input type="range" 
                               id="custom_commission" 
                               name="custom_commission" 
                               min="5" 
                               max="20" 
                               step="0.5"
                               value="{{ $category['custom_commission'] ?? 12 }}"
                               class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <div class="flex justify-between text-xs text-gray-500 mt-1">
                            <span>5%</span>
                            <span id="commission_value">{{ $category['custom_commission'] ?? 12 }}%</span>
                            <span>20%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-generate slug from category name
    document.getElementById('category_name_{{ $action }}').addEventListener('input', function() {
        const slugInput = document.getElementById('category_slug_{{ $action }}');
        if (slugInput && !slugInput.dataset.manual) {
            const slug = this.value
                .toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/--+/g, '-')
                .trim();
            slugInput.value = slug;
        }
    });
    
    // Track manual slug edits
    document.getElementById('category_slug_{{ $action }}').addEventListener('input', function() {
        this.dataset.manual = 'true';
    });
    
    // Update commission value display
    document.getElementById('custom_commission')?.addEventListener('input', function() {
        document.getElementById('commission_value').textContent = this.value + '%';
    });
</script>
@endpush