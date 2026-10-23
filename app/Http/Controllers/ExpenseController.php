<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\AuditLog;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'recorder'])->latest('expense_date');

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        $expenses = $query->paginate(20)->withQueryString();
        $categories = ExpenseCategory::orderBy('name')->get();
        $totalExpenses = (float) (clone $query)->sum('amount');

        return view('expenses.index', compact('expenses', 'categories', 'totalExpenses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:expense_categories,id',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $expenseNumber = 'EXP-' . date('Ymd') . '-' . str_pad(Expense::count() + 1, 4, '0', STR_PAD_LEFT);

        $expense = Expense::create([
            'expense_number' => $expenseNumber,
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'amount' => $validated['amount'],
            'expense_date' => $validated['expense_date'],
            'payment_method' => $validated['payment_method'],
            'recorded_by' => auth()->id(),
            'notes' => $validated['notes'] ?? null,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'expense_recorded',
            'description' => "Recorded expense {$expense->title} (Tsh " . number_format($expense->amount) . ")",
            'entity_type' => 'Expense',
            'entity_id' => $expense->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Matumizi ya Tsh ' . number_format($expense->amount) . ' yamerekodiwa kikamilifu.');
    }
}
