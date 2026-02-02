<?php

namespace App\Http\Controllers\FoodProvider;

use App\Http\Controllers\Controller;
use App\Models\FoodItem;
use App\Models\ServiceProvider;
use App\Models\MealType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MenuItemController extends Controller
{
    /**
     * Display a listing of menu items.
     */
    public function index(Request $request)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->first();

            if (!$foodProvider) {
                return view('food-provider.menu.items.index', [
                    'menuItems' => collect([]),
                    'totalItems' => 0,
                    'activeItems' => 0,
                    'mostPopularItem' => 'N/A',
                    'averagePrice' => 0,
                    'mealTypes' => collect([])
                ]);
            }

            // Start query
            $query = FoodItem::with('mealType')
                ->where('service_provider_id', $foodProvider->id);

            // Apply search filter
            if ($request->has('search') && $request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            }

            // Apply category filter (vegetarian/non-vegetarian)
            if ($request->has('category') && $request->filled('category')) {
                if ($request->category == 'vegetarian') {
                    $query->where('dietary_tags', 'like', '%vegetarian%');
                } elseif ($request->category == 'non-vegetarian') {
                    $query->where(function($q) {
                        $q->where('dietary_tags', 'not like', '%vegetarian%')
                          ->orWhereNull('dietary_tags');
                    });
                }
            }

            // Apply meal type filter
            if ($request->has('meal_type') && $request->filled('meal_type')) {
                $query->where('meal_type_id', $request->meal_type);
            }

            // Apply status filter
            if ($request->has('status') && $request->filled('status')) {
                if ($request->status == 'active') {
                    $query->where('is_available', true);
                } elseif ($request->status == 'inactive') {
                    $query->where('is_available', false);
                }
            }

            // Order by most recent first
            $menuItems = $query->orderBy('created_at', 'desc')
                ->paginate(15);

            // Get stats
            $totalItems = FoodItem::where('service_provider_id', $foodProvider->id)->count();
            $activeItems = FoodItem::where('service_provider_id', $foodProvider->id)
                ->where('is_available', true)
                ->count();
            
            // Get most popular item (by order count - you might need to add this logic)
            $mostPopularItem = FoodItem::where('service_provider_id', $foodProvider->id)
                ->where('is_available', true)
                ->orderBy('sold_today', 'desc')
                ->value('name') ?? 'N/A';
            
            // Get average price
            $averagePrice = FoodItem::where('service_provider_id', $foodProvider->id)
                ->where('is_available', true)
                ->avg('base_price') ?? 0;

            // Get meal types for filter
            $mealTypes = MealType::all();

            return view('food-provider.menu.items.index', compact(
                'menuItems',
                'totalItems',
                'activeItems',
                'mostPopularItem',
                'averagePrice',
                'mealTypes'
            ));

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@index: ' . $e->getMessage());
            return view('food-provider.menu.items.index', [
                'menuItems' => collect([]),
                'totalItems' => 0,
                'activeItems' => 0,
                'mostPopularItem' => 'N/A',
                'averagePrice' => 0,
                'mealTypes' => collect([])
            ]);
        }
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            // Get meal types for the dropdown
            $mealTypes = MealType::all();
            
            return view('food-provider.menu.items.create', compact('mealTypes'));

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@create: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error loading menu creation form. Please make sure you are registered as a food provider.');
        }
    }

    /**
     * Store a newly created menu item.
     */
    public function store(Request $request)
    {
        try {
            // Get the food service provider for the current user
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'meal_type_id' => 'required|exists:meal_types,id',
                'base_price' => 'required|numeric|min:0',
                'commission_rate' => 'nullable|numeric|min:0|max:100',
                'dietary_tags' => 'nullable|array',
                'calories' => 'nullable|integer|min:0',
                'daily_quantity' => 'nullable|integer|min:0',
                'preparation_time' => 'nullable|string|max:50',
                'is_available' => 'required|in:0,1',
                'is_featured' => 'nullable|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('menu-items', 'public');
            }

            // Prepare dietary tags
            $dietaryTags = $request->input('dietary_tags', []);
            if (!empty($dietaryTags)) {
                $dietaryTags = array_map('trim', $dietaryTags);
                $dietaryTags = array_filter($dietaryTags);
            }

            // Create the menu item
            $menuItem = FoodItem::create([
                'service_provider_id' => $foodProvider->id,
                'name' => $request->name,
                'description' => $request->description,
                'meal_type_id' => $request->meal_type_id,
                'base_price' => $request->base_price,
                'commission_rate' => $request->commission_rate ?? 8.00,
                'is_available' => $request->is_available,
                'daily_quantity' => $request->daily_quantity,
                'sold_today' => 0,
                'dietary_tags' => !empty($dietaryTags) ? json_encode($dietaryTags) : null,
                'calories' => $request->calories,
                'image_url' => $imagePath ? Storage::url($imagePath) : null,
            ]);

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', 'Menu item created successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@store: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error creating menu item. Please try again.');
        }
    }

    /**
     * Display the specified menu item.
     */
    public function show($id)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $menuItem = FoodItem::with('mealType')
                ->where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            return view('food-provider.menu.items.show', compact('menuItem'));

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@show: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Menu item not found or you don\'t have permission to view it.');
        }
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit($id)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $menuItem = FoodItem::with('mealType')
                ->where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            $mealTypes = MealType::all();

            // Parse dietary tags for the view
            $dietaryTags = [];
            if ($menuItem->dietary_tags) {
                $dietaryTags = json_decode($menuItem->dietary_tags, true) ?? [];
            }

            return view('food-provider.menu.items.edit', compact('menuItem', 'mealTypes', 'dietaryTags'));

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@edit: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Menu item not found or you don\'t have permission to edit it.');
        }
    }

    /**
     * Update the specified menu item.
     */
    public function update(Request $request, $id)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $menuItem = FoodItem::where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            // Validate the request
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:100',
                'description' => 'nullable|string',
                'meal_type_id' => 'required|exists:meal_types,id',
                'base_price' => 'required|numeric|min:0',
                'commission_rate' => 'nullable|numeric|min:0|max:100',
                'dietary_tags' => 'nullable|array',
                'calories' => 'nullable|integer|min:0',
                'daily_quantity' => 'nullable|integer|min:0',
                'preparation_time' => 'nullable|string|max:50',
                'is_available' => 'required|in:0,1',
                'is_featured' => 'nullable|in:0,1',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($menuItem->image_url) {
                    $oldImagePath = str_replace('/storage/', '', $menuItem->image_url);
                    Storage::disk('public')->delete($oldImagePath);
                }
                
                $imagePath = $request->file('image')->store('menu-items', 'public');
                $menuItem->image_url = Storage::url($imagePath);
            }

            // Prepare dietary tags
            $dietaryTags = $request->input('dietary_tags', []);
            if (!empty($dietaryTags)) {
                $dietaryTags = array_map('trim', $dietaryTags);
                $dietaryTags = array_filter($dietaryTags);
            }

            // Update the menu item
            $menuItem->update([
                'name' => $request->name,
                'description' => $request->description,
                'meal_type_id' => $request->meal_type_id,
                'base_price' => $request->base_price,
                'commission_rate' => $request->commission_rate ?? 8.00,
                'is_available' => $request->is_available,
                'daily_quantity' => $request->daily_quantity,
                'dietary_tags' => !empty($dietaryTags) ? json_encode($dietaryTags) : null,
                'calories' => $request->calories,
            ]);

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', 'Menu item updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@update: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error updating menu item. Please try again.');
        }
    }

    /**
     * Remove the specified menu item.
     */
    public function destroy($id)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $menuItem = FoodItem::where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            // Delete image if exists
            if ($menuItem->image_url) {
                $imagePath = str_replace('/storage/', '', $menuItem->image_url);
                Storage::disk('public')->delete($imagePath);
            }

            $menuItem->delete();

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', 'Menu item deleted successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@destroy: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error deleting menu item. Please try again.');
        }
    }

    /**
     * Toggle menu item status.
     */
    public function toggleStatus($id)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $menuItem = FoodItem::where('service_provider_id', $foodProvider->id)
                ->findOrFail($id);

            $menuItem->is_available = !$menuItem->is_available;
            $menuItem->save();

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', 'Menu item status updated successfully!');

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@toggleStatus: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error updating menu item status.');
        }
    }

    /**
     * Bulk update menu items status.
     */
    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'item_ids' => 'required|array',
                'item_ids.*' => 'exists:food_items,id',
                'status' => 'required|in:active,inactive'
            ]);

            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $status = $request->status == 'active' ? true : false;
            
            $updatedCount = FoodItem::where('service_provider_id', $foodProvider->id)
                ->whereIn('id', $request->item_ids)
                ->update(['is_available' => $status]);

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', "Successfully updated {$updatedCount} menu item(s) status.");

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@bulkUpdateStatus: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error updating menu items status.');
        }
    }

    /**
     * Export menu items to CSV.
     */
    public function export(Request $request)
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $query = FoodItem::with('mealType')
                ->where('service_provider_id', $foodProvider->id);

            // Apply filters if present
            if ($request->has('status') && $request->filled('status')) {
                $query->where('is_available', $request->status == 'active');
            }

            $menuItems = $query->orderBy('name')->get();

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="menu_items_' . date('Y-m-d') . '.csv"',
            ];

            $callback = function() use ($menuItems) {
                $file = fopen('php://output', 'w');
                
                // Add CSV headers
                fputcsv($file, [
                    'ID',
                    'Name',
                    'Description',
                    'Meal Type',
                    'Base Price',
                    'Commission Rate',
                    'Total Price',
                    'Status',
                    'Daily Quantity',
                    'Calories',
                    'Created At'
                ]);

                // Add data rows
                foreach ($menuItems as $item) {
                    fputcsv($file, [
                        $item->id,
                        $item->name,
                        $item->description ?? '',
                        $item->mealType->name ?? 'N/A',
                        $item->base_price,
                        $item->commission_rate . '%',
                        $item->total_price,
                        $item->is_available ? 'Active' : 'Inactive',
                        $item->daily_quantity ?? 'Unlimited',
                        $item->calories ?? 'N/A',
                        $item->created_at->format('Y-m-d H:i:s')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@export: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error exporting menu items.');
        }
    }

    /**
     * Reset sold today count for all menu items.
     */
    public function resetSoldToday()
    {
        try {
            $foodProvider = ServiceProvider::where('user_id', Auth::id())
                ->where('service_type', 'FOOD')
                ->firstOrFail();

            $updatedCount = FoodItem::where('service_provider_id', $foodProvider->id)
                ->update(['sold_today' => 0]);

            return redirect()->route('food-provider.menu.items.index')
                ->with('success', "Reset sold today count for {$updatedCount} menu item(s).");

        } catch (\Exception $e) {
            \Log::error('Error in MenuItemController@resetSoldToday: ' . $e->getMessage());
            return redirect()->route('food-provider.menu.items.index')
                ->with('error', 'Error resetting sold today count.');
        }
    }
}