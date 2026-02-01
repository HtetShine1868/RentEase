
@extends('owner.layout.owner-layout')

@section('title', 'My Properties - RentEase')
@section('page-title', 'My Properties')
@section('page-subtitle', 'Manage all your hostel and apartment listings')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">My Properties</h1>
            <p class="text-gray-600 mt-1">Manage all your property listings in one place</p>
        </div>
        <div class="flex gap-3">
            <select class="bg-white border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <option>All Properties</option>
                <option>Hostels</option>
                <option>Apartments</option>
                <option>Active</option>
                <option>Inactive</option>
            </select>
            <a href="{{ route('owner.properties.create') }}" 
               class="bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-5 py-2 rounded-lg font-medium flex items-center gap-2 transition-all hover:shadow-lg">
                <i class="fas fa-plus"></i>
                Add New Property
            </a>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Properties</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">12</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-building text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Active</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">8</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Inactive</p>
                    <p class="text-2xl font-bold text-yellow-600 mt-1">2</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <i class="fas fa-pause-circle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl p-5 border border-gray-200 shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Draft</p>
                    <p class="text-2xl font-bold text-gray-600 mt-1">2</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-edit text-gray-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>


<!-- Properties Grid/Table -->
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <!-- Table Header -->
        <div class="border-b border-gray-200">
            <div class="px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="flex items-center">
                            <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                            <span class="ml-2 text-sm text-gray-600">Select all</span>
                        </div>
                        <div class="text-sm text-gray-500">
                            12 properties found
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button class="text-gray-600 hover:text-gray-900 p-2">
                            <i class="fas fa-filter"></i>
                        </button>
                        <button class="text-gray-600 hover:text-gray-900 p-2">
                            <i class="fas fa-sort"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Properties List -->
        <div class="divide-y divide-gray-200">
            <!-- Property 1 -->
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    
                    <!-- Property Image -->
                    <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=400&h=300&fit=crop" 
                             alt="Property" class="w-full h-full object-cover">
                    </div>
                    
                    <!-- Property Details -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Sunshine Apartments</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-building"></i>
                                        Apartment
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Downtown, City
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-bed"></i>
                                        3 Beds • 2 Baths
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-calendar-check mr-1"></i> 8 Bookings
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i> 4.7 (24 reviews)
                                    </span>


</div>
                            </div>
                            
                            <!-- Price & Actions -->

                            
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-700">$1,250<span class="text-sm font-normal text-gray-500">/month</span></p>
                                <p class="text-sm text-gray-500 mt-1">After 3% commission: <span class="font-medium">$1,212</span></p>
                                
                                <div class="flex items-center gap-2 mt-4">
                                    <a href="{{ route('owner.properties.edit', 1) }}" 
                                       class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="{{ route('owner.properties.rooms.index', 1) }}" 
                                       class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-door-closed mr-1"></i> Rooms
                                    </a>
                                    <button class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </button>
                                    <button class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property 2 -->
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    
                    <div class="w-20 h-20 rounded-lg overflow-hidden flex-shrink-0">
                        <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=400&h=300&fit=crop" 
                             alt="Property" class="w-full h-full object-cover">
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">City Hostel</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-bed"></i>
                                        Hostel
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt"></i>
                                        University Area
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-users"></i>
                                        24 Rooms • Mixed
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">


<i class="fas fa-check-circle mr-1"></i> Active
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        <i class="fas fa-calendar-check mr-1"></i> 12 Bookings
                                    </span>
                                    <span class="text-sm text-gray-600">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i> 4.5 (36 reviews)
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-2xl font-bold text-purple-700">$450<span class="text-sm font-normal text-gray-500">/month</span></p>
                                <p class="text-sm text-gray-500 mt-1">After 5% commission: <span class="font-medium">$428</span></p>
                                
                                <div class="flex items-center gap-2 mt-4">
                                    <a href="{{ route('owner.properties.edit', 2) }}" 
                                       class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Edit
                                    </a>
                                    <a href="{{ route('owner.properties.rooms.index', 2) }}" 
                                       class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-sm font-medium hover:bg-blue-100 transition-colors">
                                        <i class="fas fa-door-closed mr-1"></i> Rooms
                                    </a>
                                    <button class="px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                                        <i class="fas fa-eye mr-1"></i> View
                                    </button>
                                    <button class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<!-- Property 3 (Draft) -->
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center gap-4">
                    <input type="checkbox" class="rounded border-gray-300 text-purple-600 focus:ring-purple-500">
                    
                    <div class="w-20 h-20 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-home text-gray-400 text-2xl"></i>
                    </div>
                    
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Luxury Villa</h3>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-building"></i>
                                        Apartment
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Beachfront
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                        <i class="fas fa-bed"></i>
                                        4 Beds • 3 Baths
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-edit mr-1"></i> Draft
                                    </span>
                                    <span class="text-sm text-gray-500 italic">
                                        Last edited: 2 days ago
                                    </span>
                                </div>
                            </div>
                            
                            <div class="text-right">
                                <p class="text-2xl font-bold text-gray-500">$2,800<span class="text-sm font-normal text-gray-400">/month</span></p>
                                <p class="text-sm text-gray-400 mt-1">Not published yet</p>
                                
                                <div class="flex items-center gap-2 mt-4">
                                    <a href="{{ route('owner.properties.edit', 3) }}" 
                                       class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium hover:bg-purple-100 transition-colors">
                                        <i class="fas fa-edit mr-1"></i> Continue
                                    </a>
                                    <button class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-sm font-medium hover:bg-red-100 transition-colors">
                                        <i class="fas fa-trash mr-1"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<!-- Empty State (Uncomment if needed) -->
        <!--
        <div class="text-center py-16">
            <i class="fas fa-home text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700">No properties yet</h3>
            <p class="text-gray-500 mt-2">Start by adding your first property listing</p>
            <a href="{{ route('owner.properties.create') }}" 
               class="mt-4 inline-flex items-center gap-2 bg-purple-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-purple-700 transition-colors">
                <i class="fas fa-plus"></i>
                Add Your First Property
            </a>
        </div>
        -->

        <!-- Pagination -->
        <div class="border-t border-gray-200 px-6 py-4">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-sm text-gray-600">
                    Showing <span class="font-semibold">1 to 3</span> of <span class="font-semibold">12</span> properties
                </div>
                <div class="flex items-center gap-1">
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50 disabled:opacity-50">
                        <i class="fas fa-chevron-left text-sm"></i>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded bg-purple-600 text-white">1</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">2</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">3</button>
                    <span class="px-2 text-gray-400">...</span>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">5</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded border border-gray-300 hover:bg-gray-50">
                        <i class="fas fa-chevron-right text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Custom styles for property management */
.property-card:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

/* Image hover effect */
.property-image {
    transition: transform 0.3s ease;
}

.property-image:hover {
    transform: scale(1.05);
}
</style>

<script>
// Add interactivity to checkboxes
document.addEventListener('DOMContentLoaded', function() {
    const selectAll = document.querySelector('input[type="checkbox"]:first-of-type');
    const propertyCheckboxes = document.querySelectorAll('input[type="checkbox"]:not(:first-of-type)');
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            propertyCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }
    
    // Add property deletion confirmation
    document.querySelectorAll('.bg-red-50').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const propertyName = this.closest('.hover\\:bg-gray-50').querySelector('h3').textContent;
            
            if (confirm(Are you sure you want to delete "${propertyName}"? This action cannot be undone.)) {
                // In real app, this would trigger API call
                console.log(Deleting property: ${propertyName});
                // Show success message
                alert("${propertyName}" has been scheduled for deletion.);
            }
        });
    });
});
</script>
@endsection    index.blade.php