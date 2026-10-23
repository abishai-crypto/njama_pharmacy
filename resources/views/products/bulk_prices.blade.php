@extends('layouts.app')

@section('title', 'Bulk Selling Price Updater')
@section('page_title', 'Bulk Medicine Selling Price Updater')

@section('content')
<div class="card card-custom p-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-tags-fill text-emerald me-2"></i>Sasisha Bei za Kuuzia (Selling Prices)</h5>
            <p class="text-muted small mb-0">
                Ukurasa huu unakuruhusu kurekebisha bei za kuuzia za dawa zote 180 kwa urahisi mara moja. Bei za sasa zimewekwa kwa wastani wa Dar es Salaam (Unaweza kubadili na kuhifadhi mara moja).
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Dawa
            </a>
            <button type="submit" form="bulkPriceForm" class="btn btn-emerald btn-sm px-4 fw-bold shadow-sm">
                <i class="bi bi-check2-all me-1"></i> Hifadhi Bei Zote (Save All)
            </button>
        </div>
    </div>
</div>

<form id="bulkPriceForm" action="{{ route('products.save-bulk-prices') }}" method="POST">
    @csrf

    <div class="card card-custom overflow-hidden">
        <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
            <span class="small fw-bold text-muted text-uppercase">Jumla ya Dawa: {{ $products->count() }}</span>
            <div class="small text-muted">
                <span class="badge bg-info-subtle text-info border">Kadirio la Faida</span> linaonyeshwa kiotomatiki unapobadili bei.
            </div>
        </div>

        <div class="table-responsive" style="max-height: 700px;">
            <table class="table table-custom align-middle">
                <thead class="sticky-top bg-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Jina la Dawa (Generic Name)</th>
                        <th>Brand / Ufungashaji</th>
                        <th>Kipimo (Unit)</th>
                        <th class="text-end" style="width: 150px;">Bei ya Kununua (Wholesale)</th>
                        <th class="text-end" style="width: 180px;">Bei ya Kuuzia (Retail Price)</th>
                        <th class="text-end" style="width: 130px;">Faida Kila Kitengo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $idx => $p)
                    <tr>
                        <td class="text-muted small">{{ $idx + 1 }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $p->generic_name }}</div>
                            <small class="text-muted">Code: {{ $p->item_code }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $p->brand_name }}</span>
                            @if($p->strength)
                                <small class="text-muted ms-1">{{ $p->strength }}</small>
                            @endif
                        </td>
                        <td>{{ $p->unit ? $p->unit->code : 'PCS' }}</td>
                        <td class="text-end fw-semibold text-secondary">
                            Tsh {{ number_format($p->purchase_cost) }}
                        </td>
                        <td>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Tsh</span>
                                <input type="number" 
                                       name="prices[{{ $p->id }}]" 
                                       class="form-control text-end fw-bold price-input" 
                                       data-cost="{{ $p->purchase_cost }}" 
                                       data-id="{{ $p->id }}"
                                       value="{{ (int) $p->selling_price }}" 
                                       min="0" 
                                       step="50" 
                                       oninput="updateMargin({{ $p->id }})">
                            </div>
                        </td>
                        <td class="text-end">
                            <span id="margin-{{ $p->id }}" class="fw-bold {{ ($p->selling_price - $p->purchase_cost) >= 0 ? 'text-success' : 'text-danger' }}">
                                Tsh {{ number_format($p->selling_price - $p->purchase_cost) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 border-top bg-white text-end">
            <button type="submit" class="btn btn-emerald px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-check2-all me-1"></i> Hifadhi Bei Zote Zilizobadilishwa (Save All Changes)
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    function updateMargin(id) {
        const input = document.querySelector(`input[name="prices[${id}]"]`);
        const cost = parseFloat(input.getAttribute('data-cost')) || 0;
        const sell = parseFloat(input.value) || 0;
        const marginSpan = document.getElementById(`margin-${id}`);
        const profit = sell - cost;

        marginSpan.innerText = 'Tsh ' + new Intl.NumberFormat().format(profit);
        if (profit >= 0) {
            marginSpan.className = 'fw-bold text-success';
        } else {
            marginSpan.className = 'fw-bold text-danger';
        }
    }
</script>
@endsection
