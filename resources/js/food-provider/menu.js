/**
 * Food Provider Menu Management Utilities
 * This file contains reusable functions for menu management
 */

class MenuManager {
    constructor() {
        this.categories = [];
        this.items = [];
        this.init();
    }
    
    init() {
        this.bindEvents();
        this.loadCategories();
        this.setupDragAndDrop();
    }
    
    bindEvents() {
        // Search functionality
        document.getElementById('search')?.addEventListener('input', (e) => {
            this.filterItems(e.target.value);
        });
        
        // Filter changes
        document.getElementById('category')?.addEventListener('change', (e) => {
            this.applyFilters();
        });
        
        document.getElementById('meal-type')?.addEventListener('change', (e) => {
            this.applyFilters();
        });
        
        document.getElementById('status')?.addEventListener('change', (e) => {
            this.applyFilters();
        });
    }
    
    async loadCategories() {
        try {
            // In a real app, this would be an API call
            // const response = await fetch('/api/food-provider/categories');
            // this.categories = await response.json();
            
            // Mock data for now
            this.categories = [
                { id: 1, name: 'Vegetarian', item_count: 8 },
                { id: 2, name: 'Non-Vegetarian', item_count: 12 },
                { id: 3, name: 'Desserts', item_count: 6 },
                { id: 4, name: 'Beverages', item_count: 4 },
                { id: 5, name: 'Appetizers', item_count: 5 },
                { id: 6, name: 'Main Course', item_count: 15 }
            ];
            
            this.populateCategoryFilters();
        } catch (error) {
            console.error('Failed to load categories:', error);
            showToast('error', 'Failed to load categories');
        }
    }
    
    populateCategoryFilters() {
        const categorySelect = document.getElementById('category');
        if (!categorySelect) return;
        
        // Clear existing options except the first one
        while (categorySelect.options.length > 1) {
            categorySelect.remove(1);
        }
        
        // Add categories
        this.categories.forEach(category => {
            const option = document.createElement('option');
            option.value = category.id;
            option.textContent = category.name;
            categorySelect.appendChild(option);
        });
    }
    
    filterItems(searchTerm) {
        const rows = document.querySelectorAll('tbody tr, [data-item]');
        const searchLower = searchTerm.toLowerCase();
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchLower) ? '' : 'none';
        });
    }
    
    applyFilters() {
        const category = document.getElementById('category')?.value;
        const mealType = document.getElementById('meal-type')?.value;
        const status = document.getElementById('status')?.value;
        
        // In a real app, this would make an API call
        console.log('Applying filters:', { category, mealType, status });
        
        // For now, just show a loading state
        showToast('info', 'Applying filters...');
    }
    
    setupDragAndDrop() {
        const categoryGrid = document.querySelector('[data-sortable]');
        if (!categoryGrid) return;
        
        // Initialize SortableJS if available
        if (typeof Sortable !== 'undefined') {
            new Sortable(categoryGrid, {
                animation: 150,
                ghostClass: 'bg-blue-50',
                onEnd: (evt) => {
                    this.updateCategoryOrder();
                }
            });
        }
    }
    
    async updateCategoryOrder() {
        const order = Array.from(document.querySelectorAll('[data-category-id]'))
            .map(el => el.dataset.categoryId);
        
        try {
            // In a real app, make API call to update order
            // await fetch('/api/food-provider/categories/reorder', {
            //     method: 'POST',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ order })
            // });
            
            showToast('success', 'Category order updated successfully');
        } catch (error) {
            console.error('Failed to update category order:', error);
            showToast('error', 'Failed to update category order');
        }
    }
    
    // Menu Item Operations
    async deleteItem(itemId) {
        const confirmed = await showConfirmation({
            title: 'Delete Menu Item',
            message: 'Are you sure you want to delete this menu item? This action cannot be undone.',
            confirmText: 'Delete',
            cancelText: 'Cancel'
        });
        
        if (confirmed) {
            try {
                // API call to delete item
                // await fetch(`/api/food-provider/menu-items/${itemId}`, {
                //     method: 'DELETE'
                // });
                
                showToast('success', 'Menu item deleted successfully');
                // Reload or remove item from DOM
                this.reloadItems();
            } catch (error) {
                console.error('Failed to delete item:', error);
                showToast('error', 'Failed to delete menu item');
            }
        }
    }
    
    async toggleItemStatus(itemId, currentStatus) {
        const newStatus = currentStatus === 'active' ? 'inactive' : 'active';
        
        try {
            // API call to update status
            // await fetch(`/api/food-provider/menu-items/${itemId}/status`, {
            //     method: 'PUT',
            //     headers: { 'Content-Type': 'application/json' },
            //     body: JSON.stringify({ status: newStatus })
            // });
            
            showToast('success', `Item marked as ${newStatus}`);
            this.reloadItems();
        } catch (error) {
            console.error('Failed to update status:', error);
            showToast('error', 'Failed to update item status');
        }
    }
    
    reloadItems() {
        // Reload the page or refresh items via AJAX
        window.location.reload();
    }
    
    // Export menu data
    exportMenu(format = 'csv') {
        // Generate and download menu data
        const data = this.items.map(item => ({
            Name: item.name,
            Category: item.category,
            Price: item.price,
            Status: item.status,
            'Meal Types': item.meal_types.join(', ')
        }));
        
        let content = '';
        if (format === 'csv') {
            // Generate CSV
            const headers = Object.keys(data[0]).join(',');
            const rows = data.map(item => Object.values(item).join(',')).join('\n');
            content = headers + '\n' + rows;
        } else if (format === 'json') {
            // Generate JSON
            content = JSON.stringify(data, null, 2);
        }
        
        // Create download link
        const blob = new Blob([content], { type: 'text/plain' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `menu-export-${new Date().toISOString().split('T')[0]}.${format}`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
        
        showToast('success', `Menu exported as ${format.toUpperCase()}`);
    }
}

// Utility functions
function showToast(type, message) {
    // Implementation depends on your toast library
    console.log(`[${type.toUpperCase()}] ${message}`);
}

async function showConfirmation(options) {
    // Implementation depends on your modal library
    return confirm(options.message);
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    if (document.querySelector('[data-menu-manager]')) {
        window.menuManager = new MenuManager();
    }
});

// Export for use in other files
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { MenuManager };
}