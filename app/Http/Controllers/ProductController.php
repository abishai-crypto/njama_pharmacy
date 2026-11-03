<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\UnitOfMeasure;
use App\Models\ProductPriceHistory;
use App\Models\AuditLog;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            'initial_quantity' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'requires_prescription' => 'boolean',
            'is_restricted' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $product = DB::transaction(function () use ($validated, $request) {
            $validated['requires_prescription'] = $request->has('requires_prescription');
            $validated['is_restricted'] = $request->has('is_restricted');

            $productData = collect($validated)->except(['initial_quantity', 'batch_number', 'expiry_date'])->toArray();
            $product = Product::create($productData);

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

            $initialQty = (float) ($request->initial_quantity ?? 0);
            if ($initialQty > 0) {
                $batchNumber = $request->filled('batch_number')
                    ? trim($request->batch_number)
                    : ('INIT-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

                $batch = InventoryBatch::create([
                    'product_id' => $product->id,
                    'batch_number' => $batchNumber,
                    'supplier_id' => null,
                    'purchase_id' => null,
                    'quantity_received' => $initialQty,
                    'quantity_remaining' => $initialQty,
                    'purchase_cost' => $product->purchase_cost,
                    'selling_price' => $product->selling_price,
                    'date_received' => now()->toDateString(),
                    'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : null,
                    'storage_location' => $product->storage_conditions ?: 'Main Pharmacy Shelf',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]);

                InventoryMovement::create([
                    'product_id' => $product->id,
                    'batch_id' => $batch->id,
                    'movement_type' => 'opening_stock',
                    'quantity' => $initialQty,
                    'balance_after' => $initialQty,
                    'unit_cost' => $product->purchase_cost,
                    'reference_type' => 'InitialStock',
                    'reference_id' => $batchNumber,
                    'notes' => 'Idadi ya awali wakati wa kusajili dawa mpya',
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'product_created',
                'description' => "Created product {$product->generic_name} ({$product->item_code})" . ($initialQty > 0 ? " with initial stock {$initialQty}" : ""),
                'entity_type' => 'Product',
                'entity_id' => $product->id,
                'new_values' => $product->toArray(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);

            return $product;
        });

        $stockMsg = ($request->initial_quantity > 0) ? " ikiwa na stoo ya {$request->initial_quantity}" : "";
        return redirect()->route('products.index')->with('success', "Dawa '{$product->generic_name}'{$stockMsg} imesajiliwa kikamilifu.");
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'batches.supplier', 'priceHistories.user', 'movements.user']);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $product->load(['batches' => function ($q) {
            $q->orderBy('created_at', 'desc');
        }]);
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
            'stock_quantity' => 'nullable|numeric|min:0',
            'stock_adjustment_reason' => 'nullable|string|max:255',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'requires_prescription' => 'boolean',
            'is_restricted' => 'boolean',
            'storage_conditions' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'price_change_reason' => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($validated, $request, $product) {
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

            // Check if stock quantity was edited
            if ($request->has('stock_quantity') && $request->stock_quantity !== null) {
                $currentStock = (float) $product->total_stock;
                $newStock = (float) $request->stock_quantity;
                $diff = $newStock - $currentStock;

                if (abs($diff) > 0.0001) {
                    $reason = $request->get('stock_adjustment_reason') ?: "Marekebisho ya stoo kupitia ukurasa wa kuhariri dawa ({$currentStock} -> {$newStock})";

                    // Check if active batches exist
                    $activeBatch = $product->batches()
                        ->where('status', 'active')
                        ->where('quantity_remaining', '>', 0)
                        ->latest('id')
                        ->first();

                    if (!$activeBatch) {
                        $activeBatch = $product->batches()->where('status', 'active')->latest('id')->first();
                    }

                    if ($activeBatch) {
                        $activeBatch->quantity_remaining += $diff;
                        if ($diff > 0) {
                            $activeBatch->quantity_received += $diff;
                        }
                        if ($activeBatch->quantity_remaining <= 0) {
                            $activeBatch->quantity_remaining = 0;
                            $activeBatch->status = 'depleted';
                        } else {
                            $activeBatch->status = 'active';
                        }
                        if ($request->filled('expiry_date')) {
                            $activeBatch->expiry_date = $request->expiry_date;
                        }
                        $activeBatch->save();
                        $targetBatch = $activeBatch;
                    } else {
                        // Create a new active batch for this product
                        $batchNo = $request->filled('batch_number')
                            ? trim($request->batch_number)
                            : ('ADJ-' . date('Ymd') . '-' . strtoupper(Str::random(4)));

                        $targetBatch = InventoryBatch::create([
                            'product_id' => $product->id,
                            'batch_number' => $batchNo,
                            'quantity_received' => $newStock,
                            'quantity_remaining' => $newStock,
                            'purchase_cost' => $validated['purchase_cost'],
                            'selling_price' => $validated['selling_price'],
                            'date_received' => now()->toDateString(),
                            'expiry_date' => $request->filled('expiry_date') ? $request->expiry_date : null,
                            'storage_location' => $product->storage_conditions ?: 'Main Pharmacy Shelf',
                            'status' => 'active',
                            'created_by' => auth()->id(),
                        ]);
                    }

                    // Record movement ledger
                    InventoryMovement::create([
                        'product_id' => $product->id,
                        'batch_id' => $targetBatch->id,
                        'movement_type' => $diff > 0 ? 'adjustment_in' : 'adjustment_out',
                        'quantity' => $diff,
                        'balance_after' => $newStock,
                        'unit_cost' => $product->purchase_cost,
                        'reference_type' => 'StockAdjustment',
                        'reference_id' => $targetBatch->batch_number,
                        'notes' => $reason,
                        'user_id' => auth()->id(),
                        'created_at' => now(),
                    ]);
                }
            }

            $productData = collect($validated)->except(['stock_quantity', 'stock_adjustment_reason', 'batch_number', 'expiry_date', 'price_change_reason'])->toArray();
            $product->update($productData);

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
        });

        return redirect()->route('products.index')->with('success', "Taarifa na stoo ya dawa '{$product->generic_name}' zimesasishwa kikamilifu.");
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
