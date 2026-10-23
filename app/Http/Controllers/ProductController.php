<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use App\Models\ProductPriceHistory;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'unit', 'batches']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('item_code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('prescription_only')) {
            $query->where('requires_prescription', true);
        }

        if ($request->filled('restricted')) {
            $query->where('is_restricted', true);
        }

        $categories = Category::orderBy('name')->get();
        $products = $query->orderBy('generic_name')->paginate(25)->withQueryString();

        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('code')->get();
        $nextCode = 'MED-' . str_pad(Product::count() + 1, 3, '0', STR_PAD_LEFT);

        return view('products.create', compact('categories', 'units', 'nextCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'generic_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'item_code' => 'required|string|unique:products,item_code',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units_of_measure,id',
            'dosage_form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'pack_size' => 'nullable|string|max:100',
            'purchase_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock_alert' => 'required|integer|min:0',
            'requires_prescription' => 'boolean',
            'is_restricted' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['is_restricted'] = $request->has('is_restricted');

        $product = Product::create($validated);

        // Record initial price history
        ProductPriceHistory::create([
            'product_id' => $product->id,
            'old_purchase_cost' => 0,
            'new_purchase_cost' => $product->purchase_cost,
            'old_selling_price' => 0,
            'new_selling_price' => $product->selling_price,
            'changed_by' => auth()->id(),
            'reason' => 'Initial product creation',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_created',
            'description' => "Created product {$product->generic_name} ({$product->item_code})",
            'entity_type' => 'Product',
            'entity_id' => $product->id,
            'new_values' => $product->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Dawa '{$product->generic_name}' imesajiliwa kikamilifu.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'batches.supplier', 'priceHistories.user', 'movements.user']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $units = UnitOfMeasure::orderBy('code')->get();

        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'generic_name' => 'required|string|max:255',
            'brand_name' => 'nullable|string|max:255',
            'item_code' => 'required|string|unique:products,item_code,' . $product->id,
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'required|exists:units_of_measure,id',
            'dosage_form' => 'nullable|string|max:100',
            'strength' => 'nullable|string|max:100',
            'pack_size' => 'nullable|string|max:100',
            'purchase_cost' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'minimum_stock_alert' => 'required|integer|min:0',
            'requires_prescription' => 'boolean',
            'is_restricted' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'price_change_reason' => 'nullable|string|max:255',
        ]);

        $validated['requires_prescription'] = $request->has('requires_prescription');
        $validated['is_restricted'] = $request->has('is_restricted');

        $priceChanged = ($product->purchase_cost != $validated['purchase_cost']) || ($product->selling_price != $validated['selling_price']);
        $oldValues = $product->toArray();

        if ($priceChanged) {
            ProductPriceHistory::create([
                'product_id' => $product->id,
                'old_purchase_cost' => $product->purchase_cost,
                'new_purchase_cost' => $validated['purchase_cost'],
                'old_selling_price' => $product->selling_price,
                'new_selling_price' => $validated['selling_price'],
                'changed_by' => auth()->id(),
                'reason' => $request->get('price_change_reason', 'Price updated from product edit screen'),
            ]);
        }

        $product->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'product_updated',
            'description' => "Updated product {$product->generic_name}" . ($priceChanged ? " (Prices updated: Buy {$validated['purchase_cost']}, Sell {$validated['selling_price']})" : ""),
            'entity_type' => 'Product',
            'entity_id' => $product->id,
            'old_values' => $oldValues,
            'new_values' => $product->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', "Taarifa za '{$product->generic_name}' zimesasishwa.");
    }

    /**
     * Dedicated Bulk Selling Price Updater Screen
     * Allows Dr. Njama or Abishai to update selling prices across all medicines quickly!
     */
    public function bulkPriceUpdater(Request $request)
    {
        $categoryId = $request->get('category_id');
        $query = Product::with(['category', 'unit'])->orderBy('generic_name');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $products = $query->get();
        $categories = Category::orderBy('name')->get();

        return view('products.bulk_prices', compact('products', 'categories', 'categoryId'));
    }

    public function saveBulkPrices(Request $request)
    {
        $prices = $request->input('prices', []);
        $updatedCount = 0;

        DB::transaction(function () use ($prices, &$updatedCount, $request) {
            foreach ($prices as $productId => $sellingPrice) {
                $product = Product::find($productId);
                if ($product && (float)$product->selling_price !== (float)$sellingPrice) {
                    $oldPrice = $product->selling_price;
                    $product->selling_price = (float)$sellingPrice;
                    $product->save();

                    ProductPriceHistory::create([
                        'product_id' => $product->id,
                        'old_purchase_cost' => $product->purchase_cost,
                        'new_purchase_cost' => $product->purchase_cost,
                        'old_selling_price' => $oldPrice,
                        'new_selling_price' => $sellingPrice,
                        'changed_by' => auth()->id(),
                        'reason' => 'Bulk selling price update by user',
                    ]);

                    $updatedCount++;
                }
            }
        });

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'bulk_price_updated',
            'description' => "Updated selling prices for {$updatedCount} products via Bulk Price Updater.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->route('products.bulk-prices')->with('success', "Bei za bidhaa {$updatedCount} zimesasishwa kikamilifu.");
    }
}
