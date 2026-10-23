<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\AuditLog;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('purchases')->orderBy('name')->get();
        return view('suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tin_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
        ]);

        $supplier = Supplier::create($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'supplier_created',
            'description' => "Created supplier {$supplier->name}",
            'entity_type' => 'Supplier',
            'entity_id' => $supplier->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', "Msambazaji '{$supplier->name}' amesajiliwa.");
    }

    public function show(Supplier $supplier)
    {
        $supplier->load(['purchases.items.product', 'payments']);
        return view('suppliers.show', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'tin_number' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $supplier->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'supplier_updated',
            'description' => "Updated supplier {$supplier->name}",
            'entity_type' => 'Supplier',
            'entity_id' => $supplier->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Taarifa za msambazaji zimesasishwa.');
    }
}
