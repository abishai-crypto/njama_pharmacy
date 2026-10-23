@extends('layouts.app')

@section('title', 'Stock Movement Ledger')
@section('page_title', 'Immutable Stock Movement Ledger')

@section('content')
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-journal-text text-emerald me-2"></i>Daftari la Mienendo ya Stoo (Stock Movement Ledger)</h5>
            <small class="text-muted">Kumbukumbu rasmi ya kila dawa inayoingia, kuuzwa, kutengwa au kuondolewa stoo ikiwa na balance inayobaki.</small>
        </div>
        <div>
            <a href="{{ route('batches.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Batches
            </a>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form action="{{ route('batches.movements') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-5">
            <select name="product_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Dawa Zote (All Medicines) --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->generic_name }} ({{ $p->brand_name }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select name="movement_type" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">-- Aina Zote za Mienendo (All Types) --</option>
                <option value="purchase_receipt" {{ request('movement_type') == 'purchase_receipt' ? 'selected' : '' }}>Mapokezi ya Mzigo (Purchase Receipt)</option>
                <option value="sale" {{ request('movement_type') == 'sale' ? 'selected' : '' }}>Mauzo ya Dawa (Sale)</option>
                <option value="write_off" {{ request('movement_type') == 'write_off' ? 'selected' : '' }}>Dawa Zilizotupwa (Write-Off)</option>
                <option value="quarantine" {{ request('movement_type') == 'quarantine' ? 'selected' : '' }}>Karantini (Quarantine)</option>
            </select>
        </div>
        <div class="col-md-3 text-end">
            <button type="submit" class="btn btn-sm btn-dark w-100">Filter Ledger</button>
        </div>
    </form>
</div>

<!-- Ledger Table -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Tarehe & Saa</th>
                    <th>Dawa / Bidhaa</th>
                    <th>Batch #</th>
                    <th>Aina ya Mwendo</th>
                    <th class="text-center">Kiasi (+ / -)</th>
                    <th class="text-center">Baki Baada ya Kitendo</th>
                    <th class="text-end">Gharama</th>
                    <th>Rejea (Reference)</th>
                    <th>Afisa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $m)
                <tr>
                    <td class="small">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $m->product ? $m->product->generic_name : 'N/A' }}</div>
                        <small class="text-muted">{{ $m->product ? $m->product->brand_name : '' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $m->batch ? $m->batch->batch_number : 'N/A' }}</span>
                    </td>
                    <td>
                        @if($m->movement_type === 'purchase_receipt')
                            <span class="badge bg-success-subtle text-success">Kupokea Mzigo</span>
                        @elseif($m->movement_type === 'sale')
                            <span class="badge bg-primary-subtle text-primary">Mauzo</span>
                        @elseif($m->movement_type === 'write_off')
                            <span class="badge bg-danger-subtle text-danger">Write-Off</span>
                        @elseif($m->movement_type === 'quarantine')
                            <span class="badge bg-warning-subtle text-warning text-dark">Quarantine</span>
                        @else
                            <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $m->movement_type)) }}</span>
                        @endif
                    </td>
                    <td class="text-center fw-bold {{ $m->quantity > 0 ? 'text-success' : 'text-danger' }}">
                        {{ $m->quantity > 0 ? '+' : '' }}{{ $m->quantity }}
                    </td>
                    <td class="text-center fw-bold text-dark">{{ $m->balance_after }}</td>
                    <td class="text-end text-muted small">Tsh {{ number_format($m->unit_cost) }}</td>
                    <td>
                        <div class="small fw-semibold text-dark">{{ $m->reference_type }}: {{ $m->reference_id }}</div>
                        <small class="text-muted">{{ $m->notes }}</small>
                    </td>
                    <td class="small text-muted">{{ $m->user ? $m->user->name : 'System' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">Hakuna kumbukumbu za mienendo kwa sasa.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Inaonyesha {{ $movements->firstItem() ?? 0 }} hadi {{ $movements->lastItem() ?? 0 }} kati ya {{ $movements->total() }}</small>
        {{ $movements->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
