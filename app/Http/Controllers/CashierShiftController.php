<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashierShift;
use App\Models\Sale;
use App\Models\AuditLog;
use Carbon\Carbon;

class CashierShiftController extends Controller
{
    public function index()
    {
        $shifts = CashierShift::with('cashier')->latest('opened_at')->paginate(20);
        $user = auth()->user();
        $currentShift = CashierShift::where('cashier_id', $user->id)->where('status', 'open')->first();

        return view('shifts.index', compact('shifts', 'currentShift'));
    }

    public function open(Request $request)
    {
        $user = auth()->user();

        $existing = CashierShift::where('cashier_id', $user->id)->where('status', 'open')->first();
        if ($existing) {
            return back()->with('error', 'Tayari una zamu (Shift) iliyo wazi. Funga hiyo kwanza kabla ya kufungua mpya.');
        }

        $validated = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
        ]);

        $shift = CashierShift::create([
            'cashier_id' => $user->id,
            'opened_at' => now(),
            'opening_cash' => $validated['opening_cash'],
            'status' => 'open',
        ]);

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'shift_opened',
            'description' => "Opened cashier shift with opening float Tsh " . number_format($shift->opening_cash),
            'entity_type' => 'CashierShift',
            'entity_id' => $shift->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Zamu yako (Shift) imefunguliwa rasmi. Unaweza kuanza kuuza.');
    }

    public function close(Request $request, CashierShift $shift)
    {
        if ($shift->status === 'closed') {
            return back()->with('error', 'Zamu hii tayari imefungwa.');
        }

        $validated = $request->validate([
            'counted_cash' => 'required|numeric|min:0',
            'discrepancy_reason' => 'nullable|string',
        ]);

        // Calculate actual cash sales during this shift
        $cashSales = (float) Sale::where('shift_id', $shift->id)
            ->where('payment_method', 'cash')
            ->sum('total_amount');

        $expectedCash = (float) $shift->opening_cash + $cashSales;
        $countedCash = (float) $validated['counted_cash'];
        $difference = $countedCash - $expectedCash;

        $shift->update([
            'closed_at' => now(),
            'counted_cash' => $countedCash,
            'expected_cash' => $expectedCash,
            'cash_difference' => $difference,
            'discrepancy_reason' => $validated['discrepancy_reason'] ?? null,
            'status' => 'closed',
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'shift_closed',
            'description' => "Closed cashier shift #{$shift->id}. Expected cash: Tsh " . number_format($expectedCash) . ", Counted: Tsh " . number_format($countedCash) . ", Diff: Tsh " . number_format($difference),
            'entity_type' => 'CashierShift',
            'entity_id' => $shift->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Zamu yako imefungwa na kupatanishwa kikamilifu.');
    }
}
