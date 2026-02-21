@extends('laundry-provider.layouts.provider')

@section('title', 'Items & Pricing')
@section('subtitle', 'Manage your laundry items and pricing')

@section('content')
<div class="space-y-6">
    {{-- Header with actions --}}
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="text-xl font-semibold text-[#174455]">Laundry Items</h2>
                <p class="text-gray-600">Manage your pricing and item catalog</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('laundry-provider.items.create') }}" 
                   class="bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b] transition-colors inline-flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add New Item
                </a>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow-sm p-4">
        <form method="GET" action="{{ route('laundry-provider.items.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search by item name..." 
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
            </div>
            <div class="w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Item Type</label>
                <select name="type" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#174455] focus:ring-[#174455]">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                    @foreach($itemTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-[#174455] text-white px-6 py-2 rounded-lg hover:bg-[#1f556b] transition-colors">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
                @if(request('search') || request('type'))
                    <a href="{{ route('laundry-provider.items.index') }}" class="ml-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Items Table --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-[#174455] focus:ring-[#174455]">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Base Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rush %</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" 
                                       class="item-checkbox rounded border-gray-300 text-[#174455] focus:ring-[#174455]">
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-900">{{ $item->item_name }}</div>
                                @if($item->description)
                                    <div class="text-xs text-gray-500">{{ Str::limit($item->description, 30) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">
                                    {{ $itemTypes[$item->item_type] ?? $item->item_type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-medium">৳{{ number_format($item->base_price, 2) }}</td>
                            <td class="px-6 py-4">{{ $item->rush_surcharge_percent }}%</td>
                            <td class="px-6 py-4 font-bold text-[#174455]">৳{{ number_format($item->total_price, 2) }}</td>
                            <td class="px-6 py-4">
                                @if($item->is_active)
                                    <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Active</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Inactive</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('laundry-provider.items.edit', $item->id) }}" 
                                       class="text-blue-600 hover:text-blue-900" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('laundry-provider.items.show', $item->id) }}" 
                                       class="text-green-600 hover:text-green-900" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('laundry-provider.items.toggle-status', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-{{ $item->is_active ? 'orange' : 'green' }}-600 hover:text-{{ $item->is_active ? 'orange' : 'green' }}-900"
                                                title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $item->is_active ? 'ban' : 'check-circle' }}"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('laundry-provider.items.duplicate', $item->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-gray-600 hover:text-gray-900" title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('laundry-provider.items.destroy', $item->id) }}" method="POST" class="inline" 
                                          onsubmit="return confirm('Are you sure you want to delete this item?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-box-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-lg">No items found</p>
                                <p class="text-sm mt-1">Get started by adding your first laundry item</p>
                                <a href="{{ route('laundry-provider.items.create') }}" class="mt-4 inline-block bg-[#174455] text-white px-4 py-2 rounded-lg hover:bg-[#1f556b]">
                                    <i class="fas fa-plus mr-2"></i> Add New Item
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        <div class="px-6 py-4 border-t">
            {{ $items->links() }}
        </div>
    </div>
    
    {{-- Bulk Actions --}}
    <div class="bg-white rounded-lg shadow-sm p-4 hidden fixed bottom-4 left-1/2 transform -translate-x-1/2 z-50" id="bulk-actions" style="width: 90%; max-width: 600px;">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600"><span id="selected-count">0</span> items selected</span>
                <button onclick="bulkActivate()" class="px-3 py-1 bg-green-100 text-green-800 rounded-lg hover:bg-green-200 text-sm">
                    <i class="fas fa-check-circle mr-1"></i> Activate
                </button>
                <button onclick="bulkDeactivate()" class="px-3 py-1 bg-orange-100 text-orange-800 rounded-lg hover:bg-orange-200 text-sm">
                    <i class="fas fa-ban mr-1"></i> Deactivate
                </button>
                <button onclick="bulkDelete()" class="px-3 py-1 bg-red-100 text-red-800 rounded-lg hover:bg-red-200 text-sm">
                    <i class="fas fa-trash mr-1"></i> Delete
                </button>
            </div>
            <button onclick="clearSelection()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Select all functionality
    document.getElementById('select-all')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.item-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateBulkActions();
    });
    
    document.querySelectorAll('.item-checkbox').forEach(cb => {
        cb.addEventListener('change', updateBulkActions);
    });
    
    function updateBulkActions() {
        const selected = document.querySelectorAll('.item-checkbox:checked').length;
        const bulkActions = document.getElementById('bulk-actions');
        
        if (selected > 0) {
            bulkActions.classList.remove('hidden');
            document.getElementById('selected-count').textContent = selected;
        } else {
            bulkActions.classList.add('hidden');
            if (document.getElementById('select-all')) {
                document.getElementById('select-all').checked = false;
            }
        }
    }
    
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.item-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }
    
    function bulkActivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        
        fetch('{{ route("laundry-provider.items.bulk-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_ids: ids,
                status: true
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function bulkDeactivate() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        
        fetch('{{ route("laundry-provider.items.bulk-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_ids: ids,
                status: false
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function bulkDelete() {
        const ids = getSelectedIds();
        if (ids.length === 0) return;
        
        if (!confirm('Are you sure you want to delete selected items?')) return;
        
        fetch('{{ route("laundry-provider.items.bulk-delete") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                item_ids: ids
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function clearSelection() {
        document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
        if (document.getElementById('select-all')) {
            document.getElementById('select-all').checked = false;
        }
        document.getElementById('bulk-actions').classList.add('hidden');
    }
    
    function exportItems() {
        window.location.href = '{{ route("laundry-provider.items.export") }}';
    }
</script>
@endpush