@extends('layouts.food-provider')

@section('title', 'Menu Categories')
@section('header', 'Menu Categories')

@section('content')

@php
$colorMap = [
    'green' => ['bg'=>'bg-green-100','text'=>'text-green-600'],
    'red' => ['bg'=>'bg-red-100','text'=>'text-red-600'],
    'pink' => ['bg'=>'bg-pink-100','text'=>'text-pink-600'],
    'blue' => ['bg'=>'bg-blue-100','text'=>'text-blue-600'],
    'yellow' => ['bg'=>'bg-yellow-100','text'=>'text-yellow-600'],
    'purple' => ['bg'=>'bg-purple-100','text'=>'text-purple-600'],
    'indigo' => ['bg'=>'bg-indigo-100','text'=>'text-indigo-600'],
    'teal' => ['bg'=>'bg-teal-100','text'=>'text-teal-600'],
];

$categories = [
    ['id'=>1,'name'=>'Vegetarian','description'=>'Pure vegetarian dishes','item_count'=>8,'icon'=>'fa-leaf','color'=>'green','sort_order'=>1],
    ['id'=>2,'name'=>'Non-Vegetarian','description'=>'Chicken & seafood','item_count'=>12,'icon'=>'fa-drumstick-bite','color'=>'red','sort_order'=>2],
    ['id'=>3,'name'=>'Desserts','description'=>'Sweet treats','item_count'=>6,'icon'=>'fa-ice-cream','color'=>'pink','sort_order'=>3],
    ['id'=>4,'name'=>'Beverages','description'=>'Drinks','item_count'=>4,'icon'=>'fa-glass-whiskey','color'=>'blue','sort_order'=>4],
];
@endphp

<div class="space-y-6">

{{-- HEADER --}}
<div class="flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold">Menu Categories</h2>
        <p class="text-sm text-gray-500">Organize your menu</p>
    </div>
    <button onclick="openModal('create-category-modal')" class="px-4 py-2 bg-indigo-600 text-white rounded">
        + Add Category
    </button>
</div>

{{-- GRID --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

@foreach($categories as $category)
@php $c = $colorMap[$category['color']]; @endphp

<div class="bg-white border rounded shadow-sm p-5">
    <div class="flex justify-between">
        <div class="flex space-x-3">
            <div class="h-12 w-12 rounded {{ $c['bg'] }} flex items-center justify-center">
                <i class="fas {{ $category['icon'] }} {{ $c['text'] }} text-xl"></i>
            </div>
            <div>
                <h3 class="font-semibold">{{ $category['name'] }}</h3>
                <p class="text-sm text-gray-500">{{ $category['description'] }}</p>
            </div>
        </div>

        <div class="space-x-2">
            <div class="space-x-2">
                <button
                    type="button"
                    class="text-indigo-600 edit-btn"
                    data-id="{{ $category['id'] }}"
                    data-name="{{ $category['name'] }}"
                    data-description="{{ $category['description'] }}"
                    data-order="{{ $category['sort_order'] }}">
                    <i class="fas fa-edit"></i>
                </button>

                <button
                    type="button"
                    class="text-red-600 delete-btn"
                    data-id="{{ $category['id'] }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

    <div class="mt-4 text-sm flex justify-between">
        <span>{{ $category['item_count'] }} items</span>
        <span>Order {{ $category['sort_order'] }}</span>
    </div>
</div>
@endforeach

{{-- ADD CARD --}}
<div class="border-2 border-dashed rounded flex items-center justify-center p-8">
    <button onclick="openModal('create-category-modal')" class="text-indigo-600">
        + Add New Category
    </button>
</div>

</div>
</div>

{{-- CREATE MODAL --}}
<x-food-provider.modal id="create-category-modal" title="Create Category" size="small">
<form>
@csrf
<div class="space-y-4">

<input class="w-full border rounded p-2" placeholder="Category Name" required>

<textarea class="w-full border rounded p-2" placeholder="Description"></textarea>

<select class="w-full border rounded p-2">
    @for($i=1;$i<=10;$i++)
        <option>{{ $i }}</option>
    @endfor
</select>

</div>

<x-slot name="actions">
<button type="button" onclick="closeModal('create-category-modal')" class="px-4 py-2 border rounded">Cancel</button>
<button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
</x-slot>
</form>
</x-food-provider.modal>

{{-- EDIT MODAL --}}
<x-food-provider.modal id="edit-category-modal" title="Edit Category" size="small">
<form>
@csrf
<input type="hidden" id="edit_id">

<div class="space-y-4">
<input id="edit_name" class="w-full border rounded p-2">
<textarea id="edit_description" class="w-full border rounded p-2"></textarea>
<input id="edit_order" type="number" class="w-full border rounded p-2">
</div>

<x-slot name="actions">
<button type="button" onclick="closeModal('edit-category-modal')" class="px-4 py-2 border rounded">Cancel</button>
<button class="px-4 py-2 bg-indigo-600 text-white rounded">Update</button>
</x-slot>
</form>
</x-food-provider.modal>

{{-- DELETE CONFIRM --}}
<x-food-provider.confirmation-modal
    id="delete-category-modal"
    title="Delete Category"
    message="Are you sure you want to delete this category?"
    confirmText="Delete"
    cancelText="Cancel"
    confirmColor="danger"
    icon="fas fa-exclamation-triangle"
    iconColor="text-red-500" />

@push('scripts')
<script>
function openEditCategoryModal(id, name, description, order){
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_name').value = name;
    document.getElementById('edit_description').value = description;
    document.getElementById('edit_order').value = order;
    openModal('edit-category-modal');
}

function showDeleteCategoryConfirmation(id){
    showConfirmation({
        id: 'delete-category-modal',
        onConfirm: `deleteCategory(${id})`
    });
}

function deleteCategory(id){
    console.log("Delete category:", id);
}
</script>
@endpush

@endsection
