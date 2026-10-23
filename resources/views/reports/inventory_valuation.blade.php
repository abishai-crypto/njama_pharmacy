@extends('layouts.app')

@section('title', __('app.inventory_valuation') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-box-seam text-teal me-2"></i>{{ __('app.inventory_valuation') }}</h3>
            <p class="text-muted small mb-0">Thamani halisi ya stoo ya dawa zote 180 kwa bei ya ununuzi wa jumla na bei ya rejareja</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('products.bulk-prices') }}" class="btn btn-teal shadow-sm">
                <i class="bi bi-tags me-1"></i> Sasisha Bei za Rejareja kwa Mkupuo
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Chapisha Thamani
            </button>
        </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom border-0 shadow-sm p-4 border-start border-secondary border-4">
                <span class="text-muted small text-uppercase fw-semibold">Thamani kwa Bei ya Jumla (Cost Value)</span>
                <h3 class="fw-bold text-dark mt-2 mb-0">Tsh {{ number_format($totalCost) }}</h3>
                <small class="text-muted mt-1"><i class="bi bi-cart me-1"></i>Jumla ya mtaji uliowekezwa kwenye dawa</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom border-0 shadow-sm p-4 border-start border-teal border-4">
                <span class="text-muted small text-uppercase fw-semibold">Thamani kwa Bei ya Rejareja (Retail Value)</span>
                <h3 class="fw-bold text-teal mt-2 mb-0">Tsh {{ number_format($totalRetail) }}</h3>
                <small class="text-teal mt-1"><i class="bi bi-cash-stack me-1"></i>Mapato yanayotarajiwa baada ya kuuza zote</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-custom border-0 shadow-sm p-4 border-start border-success border-4">
                <span class="text-muted small text-uppercase fw-semibold">Faida Ghafi Inayotarajiwa (Gross Margin)</span>
                <h3 class="fw-bold text-success mt-2 mb-0">Tsh {{ number_format($potentialMargin) }}</h3>
                <small class="text-success mt-1">
                    <i class="bi bi-graph-up me-1"></i>{{ $totalCost > 0 ? round(($potentialMargin / $totalCost) * 100, 1) . '% Markup ya wastani' : '0%' }}
                </small>
            </div>
        </div>
    </div>

    <!-- Valuation Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-table text-teal me-2"></i>Thamani ya Kila Aina ya Dawa (Stock Item Valuation)</h5>
            <span class="badge bg-teal rounded-pill">{{ count($products) }} Aina za Dawa</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="max-height: 600px;">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase sticky-top">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Jina la Dawa</th>
                            <th>Kundi</th>
                            <th class="text-center">Stoo Iliyopo</th>
                            <th class="text-end">Gharama/Kipimo</th>
                            <th class="text-end">Bei ya Kuuza</th>
                            <th class="text-end">Thamani ya Mtaji (TZS)</th>
                            <th class="text-end">Thamani ya Rejareja (TZS)</th>
                            <th class="text-end pe-4">Faida Inayotarajiwa (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $index => $prod)
                        @php
                            $margin = $prod->retail_valuation - $prod->cost_valuation;
                        @endphp
                        <tr>
                            <td class="ps-4 text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('products.show', $prod) }}" class="fw-bold text-teal text-decoration-none">
                                    {{ $prod->generic_name }}
                                </a>
                                @if($prod->brand_name && $prod->brand_name !== $prod->generic_name)
                                    <small class="text-muted d-block">({{ $prod->brand_name }})</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $prod->category ? $prod->category->name : 'Nyingine' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $prod->current_stock > 0 ? 'bg-teal' : 'bg-danger' }} rounded-pill px-2">
                                    {{ $prod->current_stock }} {{ $prod->unit ? $prod->unit->symbol : '' }}
                                </span>
                            </td>
                            <td class="text-end text-muted small">Tsh {{ number_format($prod->purchase_price) }}</td>
                            <td class="text-end fw-semibold">Tsh {{ number_format($prod->selling_price) }}</td>
                            <td class="text-end text-secondary fw-semibold">Tsh {{ number_format($prod->cost_valuation) }}</td>
                            <td class="text-end text-teal fw-bold">Tsh {{ number_format($prod->retail_valuation) }}</td>
                            <td class="text-end pe-4 fw-bold {{ $margin >= 0 ? 'text-success' : 'text-danger' }}">
                                Tsh {{ number_format($margin) }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">Hakuna dawa zilizopatikana kwenye mfumo.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                        <tr>
                            <td colspan="6" class="ps-4 text-uppercase">Jumla Kuu (Grand Total)</td>
                            <td class="text-end text-secondary">Tsh {{ number_format($totalCost) }}</td>
                            <td class="text-end text-teal">Tsh {{ number_format($totalRetail) }}</td>
                            <td class="text-end pe-4 text-success">Tsh {{ number_format($potentialMargin) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
