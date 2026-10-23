@extends('layouts.app')

@section('title', __('app.settings') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-gear text-teal me-2"></i>{{ __('app.settings') }}</h3>
            <p class="text-muted small mb-0">Mipangilio mikuu ya duka la dawa, anwani, wamiliki, wataalamu na sera za mfumo</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Kuna makosa yafuatayo:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Left: Pharmacy & Legal Info -->
            <div class="col-lg-6">
                <!-- Pharmacy Identity -->
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-building text-teal me-2"></i>Taarifa za Duka la Dawa (Pharmacy Identity)</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jina Rasmi la Pharmacy <span class="text-danger">*</span></label>
                            <input type="text" name="pharmacy_name" class="form-control" value="{{ old('pharmacy_name', $settings->pharmacy_name) }}" required>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Namba ya Usajili wa TMDA</label>
                                <input type="text" name="registration_number" class="form-control" value="{{ old('registration_number', $settings->registration_number) }}" placeholder="TMDA/PH/XXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Namba ya TIN</label>
                                <input type="text" name="tin_number" class="form-control" value="{{ old('tin_number', $settings->tin_number) }}" placeholder="123-456-789">
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Namba ya VRN (VAT)</label>
                                <input type="text" name="vrn_number" class="form-control" value="{{ old('vrn_number', $settings->vrn_number) }}" placeholder="40-XXXXXX-X">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Barua Pepe (Email)</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $settings->email) }}" placeholder="info@njamapharmacy.co.tz">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Anwani & Mahali Ilipo</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $settings->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- Contacts: Dr. Njama & Abishai Evaristo -->
                <div class="card card-custom border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-people text-teal me-2"></i>Uongozi na Wataalamu wa Mfumo</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="p-3 bg-teal-subtle rounded-3 mb-3 border border-teal-subtle">
                            <h6 class="fw-bold text-teal mb-2"><i class="bi bi-person-check-fill me-1"></i>Daktari Msimamizi / Mmiliki</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Jina Kamili</label>
                                    <input type="text" name="owner_doctor_name" class="form-control" value="{{ old('owner_doctor_name', $settings->owner_doctor_name ?? 'Dr. Njama') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Namba ya Simu</label>
                                    <input type="text" name="owner_phone" class="form-control fw-bold" value="{{ old('owner_phone', $settings->owner_phone ?? '0715362428') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-light rounded-3 border">
                            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-cpu-fill text-teal me-1"></i>Mhandisi wa Mifumo / Technician</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Jina Kamili</label>
                                    <input type="text" name="technician_name" class="form-control" value="{{ old('technician_name', $settings->technician_name ?? 'Abishai Evaristo') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-muted">Namba ya Simu</label>
                                    <input type="text" name="technician_phone" class="form-control fw-bold" value="{{ old('technician_phone', $settings->technician_phone ?? '0656392400') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Currency, System Policies & Receipt Settings -->
            <div class="col-lg-6">
                <!-- Financial & Currency -->
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cash-coin text-teal me-2"></i>Fedha na Kodi (Currency & Tax)</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Sarafu (Currency Code)</label>
                                <input type="text" name="currency" class="form-control" value="{{ old('currency', $settings->currency ?? 'TZS') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Alama ya Fedha (Symbol)</label>
                                <input type="text" name="currency_symbol" class="form-control" value="{{ old('currency_symbol', $settings->currency_symbol ?? 'Tsh') }}" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Asilimia ya Kodi ya Mauzo (Tax Rate %)</label>
                            <input type="number" step="0.01" name="default_tax_percentage" class="form-control" value="{{ old('default_tax_percentage', $settings->default_tax_percentage ?? 0) }}">
                            <small class="text-muted">Weka 0 kama dawa hazina kodi ya ongezeko la thamani (exempted).</small>
                        </div>
                    </div>
                </div>

                <!-- Policies & Expiry Warnings -->
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-shield-lock text-teal me-2"></i>Sera za Usimamizi wa Stoo na TMDA</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="enforce_fefo" id="enforce_fefo" {{ old('enforce_fefo', $settings->enforce_fefo) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="enforce_fefo">
                                Lazimisha Kanuni ya FEFO (First-Expiry, First-Out)
                            </label>
                            <small class="d-block text-muted">Huzuia kutoa dawa mpya ikiwa ipo batch ya dawa hiyo hiyo inayokaribia kuisha muda wake.</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="block_expired_sales" id="block_expired_sales" {{ old('block_expired_sales', $settings->block_expired_sales) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="block_expired_sales">
                                Zuia Kabisa Uuzaji wa Dawa Zilizopitwa na Wakati (Block Expired Sales)
                            </label>
                            <small class="d-block text-muted">Mfumo utakataa kufanya mauzo yoyote ya dawa iliyopitwa na tarehe ya mwisho (TMDA compliance).</small>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="require_prescription_for_rx_drugs" id="require_prescription" {{ old('require_prescription_for_rx_drugs', $settings->require_prescription_for_rx_drugs) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="require_prescription">
                                Lazimisha Hati ya Daktari kwa Dawa za Rx na Vizuizi
                            </label>
                            <small class="d-block text-muted">Inahitaji namba ya hati au uthibitisho wa daktari kwa dawa zilizowekewa vizuizi (Misoprostol, Oxytocin, n.k.).</small>
                        </div>

                        <label class="form-label fw-semibold mt-2">Muda wa Tahadhari za Expiry (Siku)</label>
                        <div class="row g-2">
                            <div class="col-4">
                                <label class="small text-muted">Hatari (Near)</label>
                                <input type="number" name="expiry_warning_days_near" class="form-control" value="{{ old('expiry_warning_days_near', $settings->expiry_warning_days_near ?? 30) }}" required>
                            </div>
                            <div class="col-4">
                                <label class="small text-muted">Kati (Medium)</label>
                                <input type="number" name="expiry_warning_days_medium" class="form-control" value="{{ old('expiry_warning_days_medium', $settings->expiry_warning_days_medium ?? 60) }}" required>
                            </div>
                            <div class="col-4">
                                <label class="small text-muted">Mapema (Far)</label>
                                <input type="number" name="expiry_warning_days_far" class="form-control" value="{{ old('expiry_warning_days_far', $settings->expiry_warning_days_far ?? 90) }}" required>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Receipt Printing Settings -->
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-teal me-2"></i>Uchapishaji wa Risiti (Printer & Footers)</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Aina ya Printa ya Risiti</label>
                            <select name="receipt_printer_type" class="form-select">
                                <option value="browser" {{ old('receipt_printer_type', $settings->receipt_printer_type) === 'browser' ? 'selected' : '' }}>Printa ya Kawaida (Standard A4 / Browser)</option>
                                <option value="thermal_58" {{ old('receipt_printer_type', $settings->receipt_printer_type) === 'thermal_58' ? 'selected' : '' }}>Thermal POS Printer (58mm Paper Roll)</option>
                                <option value="thermal_80" {{ old('receipt_printer_type', $settings->receipt_printer_type) === 'thermal_80' ? 'selected' : '' }}>Thermal POS Printer (80mm Paper Roll - Inapendekezwa)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Ujumbe wa Mwisho wa Risiti (Receipt Footer Message)</label>
                            <textarea name="receipt_footer" class="form-control" rows="2">{{ old('receipt_footer', $settings->receipt_footer ?? 'Asante kwa kuja NJAMA PHARMACY. Dawa ni tiba, fuata maelekezo ya daktari.') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-teal btn-lg fw-bold shadow px-5">
                        <i class="bi bi-check2-circle me-1"></i> Hifadhi Mipangilio Yote
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
