<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemBatchAllocation;
use App\Models\Payment;
use App\Models\CashierShift;
use App\Models\Customer;
use App\Models\PharmacySetting;
use App\Models\AuditLog;
use App\Models\DispensingRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PosController extends Controller
{
    public function index()
    {
        $settings = PharmacySetting::getSettings();
        $user = auth()->user();

        // Check active shift for cashier
        $activeShift = CashierShift::where('cashier_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        $customers = Customer::orderBy('name')->take(50)->get();

        return view('pos.index', compact('settings', 'activeShift', 'customers'));
    }

    /**
     * Live search for products in POS
     */
    public function search(Request $request)
    {
        $q = $request->get('q', '');
        $today = Carbon::today()->toDateString();

        if (strlen($q) < 1) {
            return response()->json([]);
        }

        $products = Product::with(['unit', 'category', 'batches' => function ($bq) use ($today) {
            $bq->where('status', 'active')
               ->where('quantity_remaining', '>', 0)
               ->where(function ($sub) use ($today) {
                   $sub->whereNull('expiry_date')->orWhere('expiry_date', '>=', $today);
               })
               ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC');
        }])
        ->where('is_active', true)
        ->where(function ($query) use ($q) {
            $query->where('generic_name', 'like', "%{$q}%")
                  ->orWhere('brand_name', 'like', "%{$q}%")
                  ->orWhere('item_code', 'like', "%{$q}%")
                  ->orWhere('barcode', $q);
        })
        ->take(15)
        ->get();

        $results = $products->map(function ($product) {
            $sellableStock = $product->batches->sum('quantity_remaining');
            $earliestBatch = $product->batches->first();

            return [
                'id' => $product->id,
                'item_code' => $product->item_code,
                'name' => $product->displayName,
                'generic_name' => $product->generic_name,
                'brand_name' => $product->brand_name,
                'strength' => $product->strength,
                'unit' => $product->unit ? $product->unit->code : 'PCS',
                'selling_price' => (float) $product->selling_price,
                'purchase_cost' => (float) $product->purchase_cost,
                'stock' => (float) $sellableStock,
                'requires_prescription' => (bool) $product->requires_prescription,
                'is_restricted' => (bool) $product->is_restricted,
                'earliest_expiry' => $earliestBatch && $earliestBatch->expiry_date ? $earliestBatch->expiry_date->format('d/m/Y') : 'N/A',
                'earliest_batch_no' => $earliestBatch ? $earliestBatch->batch_number : 'N/A',
            ];
        });

        return response()->json($results);
    }

    /**
     * Checkout Sale (Atomic Transaction)
     */
    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'payment_method' => 'required|string', // cash, mobile_money, bank_transfer, card, split
            'amount_paid' => 'required|numeric|min:0',
            'split_payments' => 'nullable|array',
            'offline_uuid' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Idempotency check: if offline_uuid was already processed, return existing sale!
        if (!empty($validated['offline_uuid'])) {
            $existing = Sale::where('offline_uuid', $validated['offline_uuid'])->first();
            if ($existing) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sale already processed (Idempotency verification).',
                    'sale_id' => $existing->id,
                    'invoice_number' => $existing->invoice_number,
                    'receipt_url' => route('pos.receipt', $existing->id),
                ]);
            }
        }

        $user = auth()->user();
        $today = Carbon::today()->toDateString();

        // Check active shift
        $shift = CashierShift::where('cashier_id', $user->id)
            ->where('status', 'open')
            ->latest()
            ->first();

        // Process Customer if provided
        $customerId = $validated['customer_id'] ?? null;
        if (!$customerId && !empty($validated['customer_name'])) {
            $customerPhone = $validated['customer_phone'] ?? null;
            if ($customerPhone) {
                $customer = Customer::firstOrCreate(
                    ['phone' => $customerPhone],
                    [
                        'name' => $validated['customer_name'],
                        'notes' => 'Created via POS',
                    ]
                );
            } else {
                $customer = Customer::create([
                    'name' => $validated['customer_name'],
                    'notes' => 'Created via POS',
                ]);
            }
            $customerId = $customer->id;
        }

        try {
            $sale = DB::transaction(function () use ($validated, $user, $shift, $customerId, $today, $request) {
                $subtotal = 0;
                $totalDiscount = 0;
                $totalCogs = 0;
                $preparedItems = [];

                // 1. Validate stock and allocate batches using FEFO
                foreach ($validated['items'] as $itemData) {
                    $product = Product::findOrFail($itemData['product_id']);
                    $requestedQty = (float) $itemData['quantity'];
                    $unitPrice = (float) $itemData['unit_price'];
                    $discount = (float) ($itemData['discount'] ?? 0);

                    // Row lock eligible batches to prevent race conditions & overselling!
                    $batches = InventoryBatch::where('product_id', $product->id)
                        ->where('status', 'active')
                        ->where('quantity_remaining', '>', 0)
                        ->where(function ($sub) use ($today) {
                            $sub->whereNull('expiry_date')->orWhere('expiry_date', '>=', $today);
                        })
                        ->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC')
                        ->lockForUpdate()
                        ->get();

                    $availableStock = $batches->sum('quantity_remaining');

                    if ($availableStock < $requestedQty) {
                        throw new \Exception("Stoo haitoshi kwa dawa: {$product->generic_name}. Inahitajika: {$requestedQty}, Iliyopo: {$availableStock}.");
                    }

                    // FEFO allocation
                    $remainingToAllocate = $requestedQty;
                    $itemAllocations = [];
                    $itemCost = 0;

                    foreach ($batches as $batch) {
                        if ($remainingToAllocate <= 0) break;

                        $take = min($batch->quantity_remaining, $remainingToAllocate);
                        $itemAllocations[] = [
                            'batch' => $batch,
                            'quantity' => $take,
                            'unit_cost' => (float) $batch->purchase_cost,
                        ];

                        $itemCost += ($take * (float) $batch->purchase_cost);
                        $remainingToAllocate -= $take;
                    }

                    $itemSubtotal = $requestedQty * $unitPrice;
                    $itemTotal = max(0, $itemSubtotal - $discount);

                    $subtotal += $itemSubtotal;
                    $totalDiscount += $discount;
                    $totalCogs += $itemCost;

                    $preparedItems[] = [
                        'product' => $product,
                        'quantity' => $requestedQty,
                        'unit_price' => $unitPrice,
                        'unit_cost' => ($requestedQty > 0) ? ($itemCost / $requestedQty) : 0,
                        'subtotal' => $itemSubtotal,
                        'discount' => $discount,
                        'total' => $itemTotal,
                        'allocations' => $itemAllocations,
                    ];
                }

                $totalAmount = max(0, $subtotal - $totalDiscount);
                $amountPaid = (float) $validated['amount_paid'];
                $changeDue = max(0, $amountPaid - $totalAmount);

                // Generate unique sequential invoice number
                $todayDate = date('Ymd');
                $countToday = Sale::whereDate('created_at', Carbon::today())->count() + 1;
                $invoiceNumber = 'NJM-' . $todayDate . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

                // Create Sale
                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'shift_id' => $shift ? $shift->id : null,
                    'cashier_id' => $user->id,
                    'customer_id' => $customerId,
                    'subtotal' => $subtotal,
                    'discount_amount' => $totalDiscount,
                    'tax_amount' => 0.00,
                    'total_amount' => $totalAmount,
                    'cost_of_goods_sold' => $totalCogs,
                    'payment_method' => $validated['payment_method'],
                    'amount_paid' => $amountPaid,
                    'change_due' => $changeDue,
                    'payment_status' => 'paid',
                    'offline_uuid' => $validated['offline_uuid'] ?? Str::uuid()->toString(),
                    'is_synced' => true,
                    'notes' => $validated['notes'] ?? null,
                ]);

                // Create Sale Items, Deduct Batches, and Record Movements
                foreach ($preparedItems as $prep) {
                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'product_id' => $prep['product']->id,
                        'quantity' => $prep['quantity'],
                        'unit_price' => $prep['unit_price'],
                        'unit_cost' => $prep['unit_cost'],
                        'subtotal' => $prep['subtotal'],
                        'discount' => $prep['discount'],
                        'total' => $prep['total'],
                    ]);

                    foreach ($prep['allocations'] as $alloc) {
                        $batch = $alloc['batch'];
                        $deductQty = $alloc['quantity'];

                        // Deduct from batch
                        $batch->quantity_remaining -= $deductQty;
                        if ($batch->quantity_remaining <= 0) {
                            $batch->status = 'depleted';
                        }
                        $batch->save();

                        // Record allocation
                        SaleItemBatchAllocation::create([
                            'sale_item_id' => $saleItem->id,
                            'batch_id' => $batch->id,
                            'quantity' => $deductQty,
                            'unit_cost' => $alloc['unit_cost'],
                        ]);

                        // Record immutable movement ledger
                        InventoryMovement::create([
                            'product_id' => $prep['product']->id,
                            'batch_id' => $batch->id,
                            'movement_type' => 'sale',
                            'quantity' => -$deductQty,
                            'balance_after' => $batch->quantity_remaining,
                            'unit_cost' => $alloc['unit_cost'],
                            'reference_type' => 'Sale',
                            'reference_id' => $sale->invoice_number,
                            'notes' => "Sold via POS Inv #{$sale->invoice_number}",
                            'user_id' => $user->id,
                            'created_at' => now(),
                        ]);
                    }

                    // If prescription drug, log dispensing record
                    if ($prep['product']->requires_prescription || $prep['product']->is_restricted) {
                        DispensingRecord::create([
                            'dispensing_number' => 'DSP-' . date('Ymd') . '-' . str_pad(DispensingRecord::count() + 1, 4, '0', STR_PAD_LEFT),
                            'sale_id' => $sale->id,
                            'product_id' => $prep['product']->id,
                            'quantity' => $prep['quantity'],
                            'patient_name' => $validated['customer_name'] ?? 'Walk-in Patient',
                            'instructions' => 'Dispensed via POS direct sale',
                            'dispensed_by' => $user->id,
                            'notes' => 'Prescription verification confirmed at POS',
                        ]);
                    }
                }

                // Record Payments
                if ($validated['payment_method'] === 'split' && !empty($validated['split_payments'])) {
                    foreach ($validated['split_payments'] as $split) {
                        Payment::create([
                            'sale_id' => $sale->id,
                            'payment_method' => $split['method'],
                            'amount' => (float) $split['amount'],
                            'reference_number' => $split['reference'] ?? null,
                            'is_verified' => true,
                            'recorded_by' => $user->id,
                        ]);
                    }
                } else {
                    Payment::create([
                        'sale_id' => $sale->id,
                        'payment_method' => $validated['payment_method'],
                        'amount' => $totalAmount,
                        'reference_number' => $request->get('payment_reference'),
                        'is_verified' => true,
                        'recorded_by' => $user->id,
                    ]);
                }

                // Update Cashier Shift if open
                if ($shift) {
                    $shift->total_sales_amount += $totalAmount;
                    $shift->total_transactions_count += 1;
                    $shift->save();
                }

                // Audit log
                AuditLog::create([
                    'user_id' => $user->id,
                    'action' => 'sale_completed',
                    'description' => "Completed sale {$sale->invoice_number}. Total: Tsh " . number_format($totalAmount) . " ({$validated['payment_method']})",
                    'entity_type' => 'Sale',
                    'entity_id' => $sale->id,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'created_at' => now(),
                ]);

                return $sale;
            });

            return response()->json([
                'success' => true,
                'message' => 'Mauzo yamekamilika kikamilifu.',
                'sale_id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total_amount' => (float) $sale->total_amount,
                'change_due' => (float) $sale->change_due,
                'receipt_url' => route('pos.receipt', $sale->id),
                'digital_receipt_url' => route('pos.digital-receipt', $sale->id),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Thermal Receipt Format (58mm / 80mm preview and print)
     */
    public function receipt(Sale $sale)
    {
        $sale->load(['cashier', 'customer', 'items.product.unit', 'items.batchAllocations.batch']);
        $settings = PharmacySetting::getSettings();
        return view('pos.receipt', compact('sale', 'settings'));
    }

    /**
     * Digital Printable / Downloadable PDF-friendly Receipt
     */
    public function digitalReceipt(Sale $sale)
    {
        $sale->load(['cashier', 'customer', 'items.product.unit', 'payments', 'dispensingRecords']);
        $settings = PharmacySetting::getSettings();
        return view('pos.digital_receipt', compact('sale', 'settings'));
    }

    /**
     * Sync Offline Sales Endpoint
     */
    public function syncOfflineSales(Request $request)
    {
        $offlineSales = $request->input('sales', []);
        $results = [];

        foreach ($offlineSales as $saleData) {
            $offlineUuid = $saleData['offline_uuid'] ?? null;
            if ($offlineUuid) {
                $existing = Sale::where('offline_uuid', $offlineUuid)->first();
                if ($existing) {
                    $results[] = [
                        'offline_uuid' => $offlineUuid,
                        'status' => 'already_synced',
                        'invoice_number' => $existing->invoice_number,
                    ];
                    continue;
                }
            }

            // Create fake request to reuse checkout
            $req = new Request($saleData);
            $req->setUserResolver(function () {
                return auth()->user();
            });

            $response = $this->checkout($req);
            $data = json_decode($response->getContent(), true);

            $results[] = [
                'offline_uuid' => $offlineUuid,
                'status' => ($data['success'] ?? false) ? 'synced' : 'failed',
                'message' => $data['message'] ?? '',
                'invoice_number' => $data['invoice_number'] ?? null,
            ];
        }

        return response()->json([
            'success' => true,
            'synced_count' => count(array_filter($results, fn($r) => in_array($r['status'], ['synced', 'already_synced']))),
            'results' => $results,
        ]);
    }
}
