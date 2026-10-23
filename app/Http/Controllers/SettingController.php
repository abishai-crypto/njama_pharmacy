<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PharmacySetting;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index()
    {
        $settings = PharmacySetting::getSettings();
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $settings = PharmacySetting::getSettings();

        $validated = $request->validate([
            'pharmacy_name' => 'required|string|max:255',
            'registration_number' => 'nullable|string|max:100',
            'tin_number' => 'nullable|string|max:100',
            'vrn_number' => 'nullable|string|max:100',
            'owner_doctor_name' => 'required|string|max:255',
            'owner_phone' => 'required|string|max:50',
            'technician_name' => 'required|string|max:255',
            'technician_phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'currency_symbol' => 'required|string|max:10',
            'default_tax_percentage' => 'nullable|numeric|min:0',
            'expiry_warning_days_near' => 'required|integer|min:1',
            'expiry_warning_days_medium' => 'required|integer|min:1',
            'expiry_warning_days_far' => 'required|integer|min:1',
            'receipt_footer' => 'nullable|string',
            'receipt_printer_type' => 'required|string|in:browser,thermal_58,thermal_80',
            'enforce_fefo' => 'boolean',
            'block_expired_sales' => 'boolean',
            'require_prescription_for_rx_drugs' => 'boolean',
        ]);

        $validated['enforce_fefo'] = $request->has('enforce_fefo');
        $validated['block_expired_sales'] = $request->has('block_expired_sales');
        $validated['require_prescription_for_rx_drugs'] = $request->has('require_prescription_for_rx_drugs');

        $oldValues = $settings->toArray();
        $settings->update($validated);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'settings_updated',
            'description' => 'Updated pharmacy business settings and contacts.',
            'entity_type' => 'PharmacySetting',
            'entity_id' => $settings->id,
            'old_values' => $oldValues,
            'new_values' => $settings->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return back()->with('success', 'Mipangilio ya NJAMA PHARMACY imehifadhiwa kikamilifu.');
    }
}
