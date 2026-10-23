@extends('layouts.app')

@section('title', 'Edit Medicine — ' . $product->generic_name)
@section('page_title', 'Edit Medicine Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card card-custom p-4">
            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                <div>
                    <h5 class="fw-bold mb-1"><i class="bi bi-pencil-square text-emerald me-2"></i>Hariri: {{ $product->generic_name }}</h5>
                    <small class="text-muted">Badilisha taarifa za dawa au rekebisha bei.</small>
                </div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Dawa
                </a>
            </div>

            <form action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Product Code (SKU)</label>
                        <input type="text" name="item_code" class="form-control" value="{{ old('item_code', $product->item_code) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Generic Name <span class="text-danger">*</span></label>
                        <input type="text" name="generic_name" class="form-control" value="{{ old('generic_name', $product->generic_name) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Brand Name</label>
                        <input type="text" name="brand_name" class="form-control" value="{{ old('brand_name', $product->brand_name) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kundi (Category) <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ (old('category_id', $product->category_id) == $cat->id) ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kipimo (Unit) <span class="text-danger">*</span></label>
                        <select name="unit_id" class="form-select" required>
                            @foreach($units as $u)
                                <option value="{{ $u->id }}" {{ (old('unit_id', $product->unit_id) == $u->id) ? 'selected' : '' }}>{{ $u->code }} ({{ $u->name }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Muundo (Dosage Form)</label>
                        <input type="text" name="dosage_form" class="form-control" value="{{ old('dosage_form', $product->dosage_form) }}">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Nguvu (Strength)</label>
                        <input type="text" name="strength" class="form-control" value="{{ old('strength', $product->strength) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Barcode</label>
                        <input type="text" name="barcode" class="form-control" value="{{ old('barcode', $product->barcode) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Kiwango cha Chini cha Stoo</label>
                        <input type="number" name="minimum_stock_alert" class="form-control" value="{{ old('minimum_stock_alert', $product->minimum_stock_alert) }}">
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-cash-coin text-emerald me-2"></i>Bei za Dawa (Pricing & History)</h6>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Bei ya Kununua (Wholesale Cost)</label>
                        <div class="input-group">
                            <span class="input-group-text">Tsh</span>
                            <input type="number" name="purchase_cost" class="form-control" value="{{ old('purchase_cost', (int)$product->purchase_cost) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Bei ya Kuuzia (Retail Price)</label>
                        <div class="input-group">
                            <span class="input-group-text">Tsh</span>
                            <input type="number" name="selling_price" class="form-control fw-bold text-success" value="{{ old('selling_price', (int)$product->selling_price) }}" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold small">Sababu ya Kubadili Bei (Reason)</label>
                        <input type="text" name="price_change_reason" class="form-control" placeholder="mfano Bei ya msambazaji ilipanda">
                    </div>
                </div>

                <hr class="my-4">
                <h6 class="fw-bold mb-3"><i class="bi bi-shield-check text-emerald me-2"></i>Vigezo vya Kisheria</h6>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="requires_prescription" id="rxSwitch" {{ old('requires_prescription', $product->requires_prescription) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="rxSwitch">Inahitaji Hati ya Daktari (Rx Only)</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_restricted" id="restSwitch" {{ old('is_restricted', $product->is_restricted) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold text-danger" for="restSwitch">Dawa Iliyowekewa Vikwazo (Restricted)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold small">Masharti ya Uhifadhi</label>
                        <input type="text" name="storage_conditions" class="form-control" value="{{ old('storage_conditions', $product->storage_conditions) }}">
                    </div>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-emerald px-4 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check2-circle me-1"></i> Hifadhi Mabadiliko (Save Changes)
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
