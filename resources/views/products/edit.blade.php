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
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-emerald me-2"></i>Usimamizi na Marekebisho ya Idadi ya Dawa Stoo (Stock Adjustment)</h6>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 fs-6">
                        Stoo Iliyopo Sasa: <strong>{{ (float)$product->total_stock }} {{ $product->unit ? $product->unit->code : 'PCS' }}</strong>
                    </span>
                </div>

                <div class="p-3 bg-light rounded-3 border mb-3">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-dark">Rekebisha Idadi ya Dawa (Current Stock) <span class="text-primary">*</span></label>
                            <input type="number" name="stock_quantity" class="form-control form-control-lg fw-bold text-primary" value="{{ old('stock_quantity', (float)$product->total_stock) }}" min="0" step="any" required>
                            <small class="text-muted">Andika idadi mpya ya dawa zilizopo stoo hapa.</small>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold small">Sababu ya Kurekebisha Idadi (Adjustment Reason)</label>
                            <input type="text" name="stock_adjustment_reason" class="form-control" placeholder="Mfano: Marekebisho ya stoo, Kuhesabu upya, Dawa ziliongezwa" value="{{ old('stock_adjustment_reason') }}">
                            <small class="text-muted">Sababu itawekwa kwenye rekodi za ukaguzi (Stock Movements & Audit Log).</small>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold small">Tarehe ya Kuisha (Expiry Date)</label>
                            @php
                                $earliestBatch = $product->eligibleBatchesFefo()->first();
                            @endphp
                            <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $earliestBatch && $earliestBatch->expiry_date ? $earliestBatch->expiry_date->format('Y-m-d') : '') }}">
                            <small class="text-muted">Hiari: Sasisha tarehe ya kuisha kwa batch inayotumika.</small>
                        </div>
                    </div>

                    @if($product->batches->count() > 0)
                    <div class="mt-3 pt-3 border-top">
                        <span class="small fw-bold text-muted text-uppercase d-block mb-2">Mabatch Yaliyopo (Existing Batches):</span>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered bg-white mb-0 align-middle">
                                <thead class="table-light small">
                                    <tr>
                                        <th>Batch #</th>
                                        <th>Expiry Date</th>
                                        <th class="text-center">Zilizoingia</th>
                                        <th class="text-center">Zilizobaki</th>
                                        <th class="text-center">Hali</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->batches as $b)
                                    <tr class="small">
                                        <td class="fw-semibold">{{ $b->batch_number }}</td>
                                        <td>{{ $b->expiry_date ? $b->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                                        <td class="text-center">{{ (float)$b->quantity_received }}</td>
                                        <td class="text-center fw-bold text-success">{{ (float)$b->quantity_remaining }}</td>
                                        <td class="text-center">
                                            @if($b->status === 'active')
                                                <span class="badge bg-success-subtle text-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($b->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
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
