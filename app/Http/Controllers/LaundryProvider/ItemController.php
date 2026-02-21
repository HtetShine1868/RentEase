<?php

namespace App\Http\Controllers\LaundryProvider;

use App\Http\Controllers\Controller;
use App\Models\LaundryItem;
use App\Models\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ItemController extends Controller
{
    protected $provider;
    
    /**
     * Get the authenticated laundry provider
     */
    private function getProvider()
    {
        if (!$this->provider) {
            $this->provider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'LAUNDRY')
                ->firstOrFail();
        }
        return $this->provider;
    }
    
    /**
     * Display a listing of laundry items
     */
    public function index(Request $request)
    {
        $provider = $this->getProvider();
        
        $query = LaundryItem::where('service_provider_id', $provider->id);
        
        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('item_type', $request->type);
        }
        
        // Search
        if ($request->has('search')) {
            $query->where('item_name', 'like', '%' . $request->search . '%');
        }
        
        $items = $query->orderBy('item_name')->paginate(15);
        
        $itemTypes = [
            'CLOTHING' => 'Clothing',
            'BEDDING' => 'Bedding',
            'CURTAIN' => 'Curtain',
            'OTHER' => 'Other'
        ];
        
        return view('laundry-provider.items.index', compact('items', 'itemTypes'));
    }
    
    /**
     * Show form for creating new item
     */
    public function create()
    {
        $itemTypes = [
            'CLOTHING' => 'Clothing',
            'BEDDING' => 'Bedding',
            'CURTAIN' => 'Curtain',
            'OTHER' => 'Other'
        ];
        
        return view('laundry-provider.items.create', compact('itemTypes'));
    }
    
    /**
     * Store a newly created item
     */
/**
 * Store a newly created item
 */
public function store(Request $request)
{
    $provider = $this->getProvider();
    
    $validator = Validator::make($request->all(), [
        'item_name' => 'required|string|max:100',
        'item_type' => 'required|in:CLOTHING,BEDDING,CURTAIN,OTHER',
        'base_price' => 'required|numeric|min:0',
        'rush_surcharge_percent' => 'nullable|numeric|min:0|max:100',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean'
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    
    // Check for duplicate item name for this provider
    $exists = LaundryItem::where('service_provider_id', $provider->id)
        ->where('item_name', $request->item_name)
        ->exists();
        
    if ($exists) {
        return redirect()->back()
            ->with('error', 'An item with this name already exists.')
            ->withInput();
    }
    
    // Get commission rate from config table
    $commissionConfig = \App\Models\CommissionConfig::where('service_type', 'LAUNDRY')->first();
    $commissionRate = $commissionConfig ? $commissionConfig->rate : 10.00;
    
    $item = new LaundryItem();
    $item->service_provider_id = $provider->id;
    $item->item_name = $request->item_name;
    $item->item_type = $request->item_type;
    $item->base_price = $request->base_price;
    $item->rush_surcharge_percent = $request->rush_surcharge_percent ?? 30.00;
    $item->commission_rate = $commissionRate;
    $item->description = $request->description;
    $item->is_active = $request->has('is_active') ? true : false;
    
    // Total price will be auto-calculated in the model's boot method
    $item->save();
    
    return redirect()->route('laundry-provider.items.index')
        ->with('success', 'Item created successfully.');
}
    
    /**
     * Display the specified item
     */
    public function show($id)
    {
        $provider = $this->getProvider();
        
        $item = LaundryItem::where('service_provider_id', $provider->id)
            ->findOrFail($id);
            
        return view('laundry-provider.items.show', compact('item'));
    }
    
    /**
     * Show form for editing item
     */
    public function edit($id)
    {
        $provider = $this->getProvider();
        
        $item = LaundryItem::where('service_provider_id', $provider->id)
            ->findOrFail($id);
            
        $itemTypes = [
            'CLOTHING' => 'Clothing',
            'BEDDING' => 'Bedding',
            'CURTAIN' => 'Curtain',
            'OTHER' => 'Other'
        ];
        
        return view('laundry-provider.items.edit', compact('item', 'itemTypes'));
    }
    
    /**
     * Update the specified item
     */
/**
 * Update the specified item
 */
public function update(Request $request, $id)
{
    $provider = $this->getProvider();
    
    $validator = Validator::make($request->all(), [
        'item_name' => 'required|string|max:100',
        'item_type' => 'required|in:CLOTHING,BEDDING,CURTAIN,OTHER',
        'base_price' => 'required|numeric|min:0',
        'rush_surcharge_percent' => 'nullable|numeric|min:0|max:100',
        'description' => 'nullable|string|max:500',
        'is_active' => 'boolean'
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }
    
    $item = LaundryItem::where('service_provider_id', $provider->id)
        ->findOrFail($id);
    
    // Check for duplicate name (excluding current item)
    $exists = LaundryItem::where('service_provider_id', $provider->id)
        ->where('item_name', $request->item_name)
        ->where('id', '!=', $id)
        ->exists();
        
    if ($exists) {
        return redirect()->back()
            ->with('error', 'An item with this name already exists.')
            ->withInput();
    }
    
    // Get commission rate from config table
    $commissionConfig = \App\Models\CommissionConfig::where('service_type', 'LAUNDRY')->first();
    $commissionRate = $commissionConfig ? $commissionConfig->rate : 10.00;
    
    $item->item_name = $request->item_name;
    $item->item_type = $request->item_type;
    $item->base_price = $request->base_price;
    $item->rush_surcharge_percent = $request->rush_surcharge_percent ?? 30.00;
    $item->commission_rate = $commissionRate;
    $item->description = $request->description;
    $item->is_active = $request->has('is_active') ? true : false;
    
    // Total price will be auto-calculated in the model's boot method
    $item->save();
    
    return redirect()->route('laundry-provider.items.index')
        ->with('success', 'Item updated successfully.');
}
    /**
     * Remove the specified item
     */
    public function destroy($id)
    {
        $provider = $this->getProvider();
        
        $item = LaundryItem::where('service_provider_id', $provider->id)
            ->findOrFail($id);
            
        // Check if item is used in any orders
        if ($item->orderItems()->count() > 0) {
            return redirect()->route('laundry-provider.items.index')
                ->with('error', 'Cannot delete item because it has been used in orders. You can deactivate it instead.');
        }
        
        $item->delete();
        
        return redirect()->route('laundry-provider.items.index')
            ->with('success', 'Item deleted successfully.');
    }
    
    /**
     * Toggle item active status
     */
    public function toggleStatus($id)
    {
        $provider = $this->getProvider();
        
        $item = LaundryItem::where('service_provider_id', $provider->id)
            ->findOrFail($id);
            
        $item->is_active = !$item->is_active;
        $item->save();
        
        $status = $item->is_active ? 'activated' : 'deactivated';
        
        return redirect()->route('laundry-provider.items.index')
            ->with('success', "Item {$status} successfully.");
    }
    
    /**
     * Duplicate an item
     */
    public function duplicate($id)
    {
        $provider = $this->getProvider();
        
        $originalItem = LaundryItem::where('service_provider_id', $provider->id)
            ->findOrFail($id);
            
        // Create duplicate with unique name
        $newItem = $originalItem->replicate();
        $newItem->item_name = $originalItem->item_name . ' (Copy)';
        $newItem->save();
        
        return redirect()->route('laundry-provider.items.index')
            ->with('success', 'Item duplicated successfully.');
    }
    
    /**
     * Bulk delete items
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer'
        ]);
        
        $provider = $this->getProvider();
        
        $deleted = LaundryItem::whereIn('id', $request->item_ids)
            ->where('service_provider_id', $provider->id)
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => "{$deleted} items deleted successfully."
        ]);
    }
    
    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer',
            'status' => 'required|boolean'
        ]);
        
        $provider = $this->getProvider();
        
        $updated = LaundryItem::whereIn('id', $request->item_ids)
            ->where('service_provider_id', $provider->id)
            ->update(['is_active' => $request->status]);
        
        return response()->json([
            'success' => true,
            'message' => "{$updated} items updated successfully."
        ]);
    }
    
    /**
     * Bulk price update
     */
    public function bulkPriceUpdate(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array',
            'item_ids.*' => 'integer',
            'price_adjustment' => 'required|numeric',
            'adjustment_type' => 'required|in:fixed,percentage'
        ]);
        
        $provider = $this->getProvider();
        
        $items = LaundryItem::whereIn('id', $request->item_ids)
            ->where('service_provider_id', $provider->id)
            ->get();
            
        foreach ($items as $item) {
            if ($request->adjustment_type == 'fixed') {
                $item->base_price = $item->base_price + $request->price_adjustment;
            } else {
                $item->base_price = $item->base_price * (1 + ($request->price_adjustment / 100));
            }
            $item->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => count($items) . " items price updated successfully."
        ]);
    }
    
    /**
     * Get item categories/types
     */
    public function categories()
    {
        $itemTypes = [
            'CLOTHING' => 'Clothing',
            'BEDDING' => 'Bedding',
            'CURTAIN' => 'Curtain',
            'OTHER' => 'Other'
        ];
        
        return response()->json($itemTypes);
    }
    
    /**
     * Export items to CSV
     */
    public function export(Request $request)
    {
        $provider = $this->getProvider();
        
        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->orderBy('item_name')
            ->get();
        
        $filename = "laundry-items-export.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($items) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID',
                'Item Name',
                'Item Type',
                'Base Price',
                'Rush Surcharge %',
                'Commission Rate %',
                'Total Price',
                'Is Active',
                'Created At'
            ]);
            
            // Data rows
            foreach ($items as $item) {
                fputcsv($file, [
                    $item->id,
                    $item->item_name,
                    $item->item_type,
                    $item->base_price,
                    $item->rush_surcharge_percent,
                    $item->commission_rate,
                    $item->total_price,
                    $item->is_active ? 'Yes' : 'No',
                    $item->created_at->format('Y-m-d H:i')
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $filename = "laundry-items-import-template.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'item_name',
                'item_type',
                'base_price',
                'rush_surcharge_percent',
                'description'
            ]);
            
            // Example row
            fputcsv($file, [
                'T-Shirt',
                'CLOTHING',
                '50',
                '30',
                'Cotton T-Shirt'
            ]);
            
            fputcsv($file, [
                'Jeans',
                'CLOTHING',
                '80',
                '30',
                'Denim Jeans'
            ]);
            
            fputcsv($file, [
                'Bedsheet',
                'BEDDING',
                '100',
                '30',
                'Single bedsheet'
            ]);
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Import items from CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt'
        ]);
        
        $provider = $this->getProvider();
        
        $file = $request->file('import_file');
        $path = $file->getRealPath();
        
        $csvData = array_map('str_getcsv', file($path));
        $headers = array_shift($csvData);
        
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($csvData as $rowIndex => $row) {
            try {
                // Map CSV columns
                $data = array_combine($headers, $row);
                
                // Validate data
                $validator = Validator::make($data, [
                    'item_name' => 'required|string|max:100',
                    'item_type' => 'required|in:CLOTHING,BEDDING,CURTAIN,OTHER',
                    'base_price' => 'required|numeric|min:0',
                    'rush_surcharge_percent' => 'nullable|numeric|min:0|max:100',
                    'description' => 'nullable|string|max:255'
                ]);
                
                if ($validator->fails()) {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": " . implode(', ', $validator->errors()->all());
                    continue;
                }
                
                // Check for duplicate
                $exists = LaundryItem::where('service_provider_id', $provider->id)
                    ->where('item_name', $data['item_name'])
                    ->exists();
                    
                if ($exists) {
                    $errorCount++;
                    $errors[] = "Row " . ($rowIndex + 2) . ": Item name '{$data['item_name']}' already exists";
                    continue;
                }
                
                // Create item
                $item = new LaundryItem();
                $item->service_provider_id = $provider->id;
                $item->item_name = $data['item_name'];
                $item->item_type = $data['item_type'];
                $item->base_price = $data['base_price'];
                $item->rush_surcharge_percent = $data['rush_surcharge_percent'] ?? 30;
                $item->description = $data['description'] ?? null;
                $item->is_active = true;
                $item->save();
                
                $successCount++;
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }
        
        $message = "Import completed: {$successCount} items imported, {$errorCount} errors.";
        
        if ($errorCount > 0) {
            return redirect()->route('laundry-provider.items.index')
                ->with('warning', $message)
                ->with('import_errors', $errors);
        }
        
        return redirect()->route('laundry-provider.items.index')
            ->with('success', $message);
    }
    
    /**
     * Get popular items for dashboard
     */
    public function popularItems(Request $request)
    {
        $provider = $this->getProvider();
        
        $limit = $request->get('limit', 5);
        
        $items = LaundryItem::where('service_provider_id', $provider->id)
            ->withCount('orderItems')
            ->orderBy('order_items_count', 'desc')
            ->limit($limit)
            ->get();
        
        return response()->json($items);
    }
}