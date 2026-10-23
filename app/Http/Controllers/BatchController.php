<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryBatch;
use App\Models\InventoryMovement;
use App\Models\StockWriteOff;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Product;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BatchController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $expiryFilter = $request->get('expiry');
        $search = $request->get('search');

        $query = InventoryBatch::with(['product.unit', 'supplier']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhereHas('product', function ($pq) use ($search) {
                      $pq->where('generic_name', 'like', "%{$search}%")
                         ->orWhere('brand_name', 'like', "%{$search}%");
                  });
            });
        }

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $today = Carbon::today()->toDateString();

        if ($expiryFilter === 'expired') {
            $query->where('expiry_date', '<', $today)->where('quantity_remaining', '>', 0);
        } elseif ($expiryFilter === '30') {
            $in30 = Carbon::today()->addDays(30)->toDateString();
            $query->whereBetween('expiry_date', [$today, $in30])->where('quantity_remaining', '>', 0);
        } elseif ($expiryFilter === '60') {
            $in60 = Carbon::today()->addDays(60)->toDateString();
            $query->whereBetween('expiry_date', [$today, $in60])->where('quantity_remaining', '>', 0);
        } elseif ($expiryFilter === '90') {
            $in90 = Carbon::today()->addDays(90)->toDateString();
            $query->whereBetween('expiry_date', [$today, $in90])->where('quantity_remaining', '>', 0);
        }

        // FEFO order: earliest expiry date first!
        $batches = $query->orderByRaw('CASE WHEN expiry_date IS NULL THEN 1 ELSE 0 END, expiry_date ASC')
                         ->paginate(25)
                         ->withQueryString();

        return view('batches.index', compact('batches', 'status', 'expiryFilter', 'search'));
    }

    public function quarantine(Request $request, InventoryBatch $batch)
    {
        $validated = $request->validate([
            'action' => 'required|in:quarantine,release',
            'reason' => 'required|string|max:255',
        ]);

        $oldStatus = $batch->status;
        $newStatus = ($validated['action'] === 'quarantine') ? 'quarantined' : 'active';
        $batch->status = $newStatus;
        $batch->save();

        // Record movement ledger
        InventoryMovement::create([
            'product_id' => $batch->product_id,
            'batch_id' => $batch->id,
            'movement_type' => ($newStatus === 'quarantined') ? 'quarantine' : 'quarantine_release',
            'quantity' => $batch->quantity_remaining,
            'balance_after' => $batch->quantity_remaining,
            'unit_cost' => $batch->purchase_cost,
            'reference_type' => 'Batch',
            'reference_id' => $batch->batch_number,
            'notes' => "Quarantine status changed to {$newStatus}. Sababu: {$validated['reason']}",
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'batch_quarantine_toggle',
            'description' => "Batch {$batch->batch_number} of {$batch->product->generic_name} changed from {$oldStatus} to {$newStatus}. Reason: {$validated['reason']}",
            'entity_type' => 'InventoryBatch',
            'entity_id' => $batch->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        $msg = ($newStatus === 'quarantined')
            ? "Batch #{$batch->batch_number} imetengwa kwenye Karantini (Haiwezi kuuzwa)."
            : "Batch #{$batch->batch_number} imerejeshwa stoo na ipo tayari kwa mauzo.";

        return back()->with('success', $msg);
    }

    public function writeOff(Request $request, InventoryBatch $batch)
    {
        $validated = $request->validate([
            'quantity' => 'required|numeric|min:0.01|max:' . $batch->quantity_remaining,
            'reason' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $qty = (float) $validated['quantity'];
        $unitCost = (float) $batch->purchase_cost;
        $totalLoss = $qty * $unitCost;

        DB::transaction(function () use ($batch, $qty, $unitCost, $totalLoss, $validated, $request) {
            $batch->quantity_remaining -= $qty;
            if ($batch->quantity_remaining <= 0) {
                $batch->status = 'depleted';
            }
            $batch->save();

            $writeOffNumber = 'WO-' . date('Ymd') . '-' . str_pad(StockWriteOff::count() + 1, 4, '0', STR_PAD_LEFT);

            StockWriteOff::create([
                'write_off_number' => $writeOffNumber,
                'product_id' => $batch->product_id,
                'batch_id' => $batch->id,
                'quantity' => $qty,
                'unit_cost' => $unitCost,
                'total_loss' => $totalLoss,
                'reason' => $validated['reason'],
                'approved_by' => auth()->id(),
                'notes' => $validated['notes'],
            ]);

            InventoryMovement::create([
                'product_id' => $batch->product_id,
                'batch_id' => $batch->id,
                'movement_type' => 'write_off',
                'quantity' => -$qty,
                'balance_after' => $batch->quantity_remaining,
                'unit_cost' => $unitCost,
                'reference_type' => 'StockWriteOff',
                'reference_id' => $writeOffNumber,
                'notes' => "Write off: {$validated['reason']}. Hasara: Tsh " . number_format($totalLoss),
                'user_id' => auth()->id(),
                'created_at' => now(),
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'stock_write_off',
                'description' => "Wrote off {$qty} units of batch {$batch->batch_number} ({$batch->product->generic_name}). Total loss: Tsh " . number_format($totalLoss),
                'entity_type' => 'InventoryBatch',
                'entity_id' => $batch->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return back()->with('success', "Dawa imeondolewa kwenye stoo (Write-off). Hasara ya Tsh " . number_format($totalLoss) . " imerekodiwa.");
    }

    public function movements(Request $request)
    {
        $query = InventoryMovement::with(['product.unit', 'batch', 'user'])->latest('id');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        $movements = $query->paginate(30)->withQueryString();
        $products = Product::orderBy('generic_name')->get();

        return view('batches.movements', compact('movements', 'products'));
    }
}
