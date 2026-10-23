<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\SupplierPayment;
use App\Models\Product;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $query = Purchase::with(['supplier', 'receiver'])->latest('purchase_date');

        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $purchases = $query->paginate(20)->withQueryString();
        $suppliers = Supplier::orderBy('name')->get();

        return view('purchases.index', compact('purchases', 'suppliers'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('generic_name')->get();
        return view('purchases.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'invoice_number' => 'required|string|unique:purchases,invoice_number',
            'purchase_date' => 'required|date',
            'payment_method' => 'required|string',
            'paid_amount' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.batch_number' => 'required|string',
            'items.*.expiry_date' => 'nullable|date',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $totalAmount = 0;
            foreach ($validated['items'] as $item) {
                $totalAmount += ($item['quantity'] * $item['unit_cost']);
            }

            $paidAmount = (float) $validated['paid_amount'];
            $balanceDue = max(0, $totalAmount - $paidAmount);
            $paymentStatus = ($balanceDue == 0) ? 'paid' : (($paidAmount > 0) ? 'partial' : 'unpaid');

            $purchase = Purchase::create([
                'invoice_number' => $validated['invoice_number'],
                'supplier_id' => $validated['supplier_id'],
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $totalAmount,
                'paid_amount' => $paidAmount,
                'balance_due' => $balanceDue,
                'payment_status' => $paymentStatus,
                'payment_method' => $validated['payment_method'],
                'received_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            // If there's an outstanding balance, update supplier debt
            if ($balanceDue > 0) {
                $supplier = Supplier::find($validated['supplier_id']);
                $supplier->outstanding_balance += $balanceDue;
                $supplier->save();
            }

            // Record items, batches, and stock movements
            foreach ($validated['items'] as $itemData) {
                $subtotal = $itemData['quantity'] * $itemData['unit_cost'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $itemData['product_id'],
                    'batch_number' => $itemData['batch_number'],
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'subtotal' => $subtotal,
                ]);

                $product = Product::find($itemData['product_id']);

                // Create inventory batch
                $batch = InventoryBatch::create([
                    'product_id' => $itemData['product_id'],
                    'batch_number' => $itemData['batch_number'],
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_id' => $purchase->id,
                    'quantity_received' => $itemData['quantity'],
                    'quantity_remaining' => $itemData['quantity'],
                    'purchase_cost' => $itemData['unit_cost'],
                    'selling_price' => $product->selling_price,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'date_received' => $validated['purchase_date'],
                    'storage_location' => 'Main Pharmacy Shelf',
                    'status' => 'active',
                    'created_by' => auth()->id(),
                ]);

                // Record inventory movement ledger
                InventoryMovement::create([
                    'product_id' => $itemData['product_id'],
                    'batch_id' => $batch->id,
                    'movement_type' => 'purchase_receipt',
                    'quantity' => $itemData['quantity'],
                    'balance_after' => $itemData['quantity'],
                    'unit_cost' => $itemData['unit_cost'],
                    'reference_type' => 'Purchase',
                    'reference_id' => $purchase->invoice_number,
                    'notes' => "Purchase delivery invoice {$purchase->invoice_number}",
                    'user_id' => auth()->id(),
                    'created_at' => now(),
                ]);
            }

            // Record initial supplier payment if paid
            if ($paidAmount > 0) {
                $voucherNumber = 'PV-' . date('Ymd') . '-' . str_pad(SupplierPayment::count() + 1, 4, '0', STR_PAD_LEFT);
                SupplierPayment::create([
                    'payment_voucher' => $voucherNumber,
                    'supplier_id' => $validated['supplier_id'],
                    'purchase_id' => $purchase->id,
                    'payment_date' => $validated['purchase_date'],
                    'amount' => $paidAmount,
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['invoice_number'],
                    'notes' => 'Payment recorded at goods receipt',
                    'recorded_by' => auth()->id(),
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'purchase_received',
                'description' => "Received purchase invoice {$purchase->invoice_number} from supplier. Total: Tsh " . number_format($totalAmount),
                'entity_type' => 'Purchase',
                'entity_id' => $purchase->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('purchases.index')->with('success', 'Mzigo wa dawa umepokelewa na kuingizwa kwenye stoo kikamilifu.');
    }

    public function show(Purchase $purchase)
    {
        $purchase->load(['supplier', 'receiver', 'items.product.unit', 'payments.recorder']);
        return view('purchases.show', compact('purchase'));
    }

    public function recordPayment(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $purchase->balance_due,
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $amount = (float) $validated['amount'];

        DB::transaction(function () use ($purchase, $amount, $validated, $request) {
            $purchase->paid_amount += $amount;
            $purchase->balance_due = max(0, $purchase->total_amount - $purchase->paid_amount);
            $purchase->payment_status = ($purchase->balance_due == 0) ? 'paid' : 'partial';
            $purchase->save();

            $supplier = $purchase->supplier;
            $supplier->outstanding_balance = max(0, $supplier->outstanding_balance - $amount);
            $supplier->save();

            $voucher = 'PV-' . date('Ymd') . '-' . str_pad(SupplierPayment::count() + 1, 4, '0', STR_PAD_LEFT);

            SupplierPayment::create([
                'payment_voucher' => $voucher,
                'supplier_id' => $supplier->id,
                'purchase_id' => $purchase->id,
                'payment_date' => $validated['payment_date'],
                'amount' => $amount,
                'payment_method' => $validated['payment_method'],
                'reference_number' => $validated['reference_number'],
                'notes' => $validated['notes'],
                'recorded_by' => auth()->id(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'supplier_payment',
                'description' => "Paid Tsh " . number_format($amount) . " to supplier {$supplier->name} for invoice {$purchase->invoice_number}",
                'entity_type' => 'Purchase',
                'entity_id' => $purchase->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return back()->with('success', 'Malipo ya msambazaji ya Tsh ' . number_format($amount) . ' yamerekodiwa.');
    }
}
