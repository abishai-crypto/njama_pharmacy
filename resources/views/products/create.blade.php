@extends('layouts.app')

@section('title', 'Add New Medicine')
@section('page_title', 'Register New Medicine')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-capsule-plus text-emerald me-2"></i>Fomu ya Kusajili Dawa Mpya</h5>
                    <small class="text-muted">Jaza taarifa zote kwa usahihi kwa ajili ya orodha ya famasi.</small>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Dawa
                </a>
            </div>

            <form action="{{ route('products.store') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Product Code (SKU) <span class="text-danger">*</span></label>
                        <input type="text" name="item_code" class="form-control" value="{{ old('item_code', $nextCode) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Generic Name (Jina Halisi la Dawa) <span class="text-danger">*</span></label>
                        <input type="text" name="generic_name" class="form-control" placeholder="mfano Paracetamol" value="{{ old('generic_name') }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Brand Name (Jina la Biashara / Ufungashaji)</label>
                        <input type="text" name="brand_name" class="form-control" placeholder="mfano Panadol, Fevadol 10x10" value="{{ old('brand_name') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kundi la Dawa (Category) <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chagua Kundi --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kipimo cha Kitengo (Unit) <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select" required>
                            <option value="">-- Chagua Unit --</option>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ old('unit_id') == $u->id ? 'selected' : '' }}>{{ $u->code }} ({{ $u->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Muundo wa Dawa (Dosage Form)</label>
                        <input type="text" name="dosage_form" class="form-control" placeholder="Tablet, Syrup, Injection, Cream, Drops" value="{{ old('dosage_form') }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nguvu ya Dawa (Strength)</label>
                        <input type="text" name="strength" class="form-control" placeholder="mfano 500mg, 250mg/5ml" value="{{ old('strength') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Barcode (Hiari)</label>
                        <input type="text" name="barcode" class="form-control" placeholder="Scan barcode hapa" value="{{ old('barcode') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kiwango cha Chini cha Stoo (Alert)</label>
                        <input type="number" name="minimum_stock_alert" class="form-control" value="{{ old('minimum_stock_alert', 5) }}" min="0">
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cash-coin text-emerald me-2"></i>Bei za Dawa (Pricing)</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Bei ya Kununua (Wholesale Cost - Tsh) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Tsh</span>
                            <input type="number" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', 0) }}" min="0" step="50" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Bei ya Kuuzia (Retail Selling Price - Tsh) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Tsh</span>
                            <input type="number" name="selling_price" class="form-control fw-bold text-success" value="{{ old('selling_price', 0) }}" min="0" step="50" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check text-emerald me-2"></i>Vigezo vya Kisheria na Uhifadhi</h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="requires_prescription" id="rxSwitch" {{ old('requires_prescription') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="rxSwitch">Inahitaji Hati ya Daktari (Prescription Required - Rx)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_restricted" id="restSwitch" {{ old('is_restricted') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-danger" for="restSwitch">Dawa Iliyowekewa Vikwazo Maalum (Restricted / Controlled Drug)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Masharti ya Uhifadhi (Storage Conditions)</label>
                        <input type="text" name="storage_conditions" class="form-control" placeholder="Store below 30°C, Keep in dry place" value="{{ old('storage_conditions', 'Store in a cool dry place below 30°C') }}">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-emerald px-4 py-2 fw-bold shadow-sm">
                        <i class="bi bi-save2-fill me-1"></i> Sajili Dawa Kwenye Mfumo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
