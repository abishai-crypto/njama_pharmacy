<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\DispensingRecord;
use App\Models\Product;
use App\Models\Customer;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with(['verifier', 'items.product'])->latest('prescription_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('prescription_number', 'like', "%{$s}%")
                  ->orWhere('patient_name', 'like', "%{$s}%")
                  ->orWhere('prescriber_name', 'like', "%{$s}%");
            });
        }

        $prescriptions = $query->paginate(20)->withQueryString();
        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('generic_name')->get();
        $customers = Customer::orderBy('name')->get();
        $nextRef = 'RX-' . date('Ymd') . '-' . str_pad(Prescription::count() + 1, 4, '0', STR_PAD_LEFT);

        return view('prescriptions.create', compact('products', 'customers', 'nextRef'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'prescription_number' => 'required|string|unique:prescriptions,prescription_number',
            'prescription_date' => 'required|date',
            'patient_name' => 'required|string|max:255',
            'patient_age' => 'nullable|integer|min:0',
            'patient_gender' => 'nullable|in:male,female,other',
            'patient_phone' => 'nullable|string|max:50',
            'prescriber_name' => 'required|string|max:255',
            'prescriber_hospital' => 'nullable|string|max:255',
            'prescriber_phone' => 'nullable|string|max:50',
            'prescriber_reg_no' => 'nullable|string|max:100',
            'diagnosis_summary' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.dosage_instructions' => 'required|string',
            'items.*.prescribed_quantity' => 'required|numeric|min:1',
            'items.*.duration_days' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $prescription = Prescription::create([
                'prescription_number' => $validated['prescription_number'],
                'prescription_date' => $validated['prescription_date'],
                'patient_name' => $validated['patient_name'],
                'patient_age' => $validated['patient_age'] ?? null,
                'patient_gender' => $validated['patient_gender'] ?? null,
                'patient_phone' => $validated['patient_phone'] ?? null,
                'prescriber_name' => $validated['prescriber_name'],
                'prescriber_hospital' => $validated['prescriber_hospital'] ?? null,
                'prescriber_phone' => $validated['prescriber_phone'] ?? null,
                'prescriber_reg_no' => $validated['prescriber_reg_no'] ?? null,
                'diagnosis_summary' => $validated['diagnosis_summary'] ?? null,
                'status' => 'pending',
                'verified_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                PrescriptionItem::create([
                    'prescription_id' => $prescription->id,
                    'product_id' => $itemData['product_id'],
                    'dosage_instructions' => $itemData['dosage_instructions'],
                    'prescribed_quantity' => $itemData['prescribed_quantity'],
                    'duration_days' => $itemData['duration_days'] ?? null,
                ]);
            }

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'prescription_created',
                'description' => "Recorded prescription {$prescription->prescription_number} for patient {$prescription->patient_name}",
                'entity_type' => 'Prescription',
                'entity_id' => $prescription->id,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
            ]);
        });

        return redirect()->route('prescriptions.index')->with('success', 'Hati ya daktari (Prescription) imerekodiwa kikamilifu.');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load(['items.product.unit', 'verifier', 'dispensingRecords.dispenser']);
        return view('prescriptions.show', compact('prescription'));
    }

    public function dispensingRecords()
    {
        $records = DispensingRecord::with(['prescription', 'product.unit', 'dispenser'])
            ->latest()
            ->paginate(30);

        return view('prescriptions.dispensing', compact('records'));
    }
}
