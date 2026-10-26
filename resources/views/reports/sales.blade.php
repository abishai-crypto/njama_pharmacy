@extends('layouts.app')

@section('title', __('app.sales_report') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-graph-up-arrow text-teal me-2"></i>{{ __('app.sales_report') }}</h3>
            <p class="text-muted small mb-0">Uchambuzi wa mauzo, faida ghafi (gross profit), na mapato ya NJAMA PHARMACY</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline-success">
                <i class="bi bi-file-earmark-spreadsheet me-1"></i> Pakua CSV (Excel)
            </a>
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Chapisha
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.sales') }}" class="row g-2 align-items-center">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tarehe ya Kuanzia</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Tarehe ya Mwisho</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">Njia ya Malipo</label>
                    <select name="payment_method" class="form-select">
                        <option value="">Zote (All Methods)</option>
                        <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="mpesa" {{ request('payment_method') === 'mpesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="tigo_pesa" {{ request('payment_method') === 'tigo_pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                        <option value="airtel_money" {{ request('payment_method') === 'airtel_money' ? 'selected' : '' }}>Airtel Money</option>
                        <option value="bank_card" {{ request('payment_method') === 'bank_card' ? 'selected' : '' }}>Card / Benki</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-funnel me-1"></i> Chuja</button>
                    <a href="{{ route('reports.sales') }}" class="btn btn-outline-secondary">Weka Upya</a>
                </div>
            </form>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-teal border-4">
                <span class="text-muted small text-uppercase fw-semibold">Jumla ya Mauzo (Revenue)</span>
                <h4 class="fw-bold text-dark mt-2 mb-0">Tsh {{ number_format($totalSales) }}</h4>
                <small class="text-teal mt-1"><i class="bi bi-receipt me-1"></i>{{ number_format($totalTransactions) }} miamala</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-secondary border-4">
                <span class="text-muted small text-uppercase fw-semibold">Gharama za Bidhaa (COGS)</span>
                <h4 class="fw-bold text-secondary mt-2 mb-0">Tsh {{ number_format($totalCogs) }}</h4>
                <small class="text-muted mt-1">Gharama ya dawa za jumla</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-success border-4">
                <span class="text-muted small text-uppercase fw-semibold">Faida Ghafi (Gross Profit)</span>
                <h4 class="fw-bold text-success mt-2 mb-0">Tsh {{ number_format($grossProfit) }}</h4>
                <small class="text-success mt-1">
                    {{ $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) . '% margin' : '0%' }}
                </small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 border-start border-info border-4">
                <span class="text-muted small text-uppercase fw-semibold">Wastani kwa Muamala</span>
                <h4 class="fw-bold text-info mt-2 mb-0">
                    Tsh {{ $totalTransactions > 0 ? number_format($totalSales / $totalTransactions) : '0' }}
                </h4>
                <small class="text-muted mt-1">Kila risiti</small>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check text-teal me-2"></i>Miamala ya Mauzo (Sales Ledger)</h5>
            <span class="badge bg-teal rounded-pill">{{ $sales->total() }} miamala</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Ankara #</th>
                            <th>Tarehe & Saa</th>
                            <th>Cashier</th>
                            <th>Mteja</th>
                            <th>Njia ya Malipo</th>
                            <th class="text-end">Jumla (TZS)</th>
                            <th class="text-end">COGS (TZS)</th>
                            <th class="text-end">Faida (TZS)</th>
                            <th class="text-end pe-4">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sales as $sale)
                        <tr>
                            <td class="ps-4 fw-bold text-teal">
                                <a href="{{ route('pos.digital-receipt', $sale) }}" class="text-teal text-decoration-none" target="_blank">
                                    {{ $sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $sale->created_at->format('d/m/Y H:i') }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $sale->cashier ? $sale->cashier->name : 'N/A' }}</span></td>
                            <td>{{ $sale->customer ? $sale->customer->name : 'Mteja wa Kawaida' }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                </span>
                            </td>
                            <td class="text-end fw-bold">Tsh {{ number_format($sale->total_amount) }}</td>
                            <td class="text-end text-muted">Tsh {{ number_format($sale->cost_of_goods_sold) }}</td>
                            <td class="text-end fw-bold text-success">Tsh {{ number_format($sale->gross_profit) }}</td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('pos.receipt', $sale) }}" class="btn btn-sm btn-outline-teal" target="_blank" title="Thermal Print">
                                        <i class="bi bi-printer"></i>
                                    </a>
                                    <a href="{{ route('pos.digital-receipt', $sale) }}" class="btn btn-sm btn-outline-secondary" target="_blank" title="Digital PDF">
                                        <i class="bi bi-file-text"></i>
                                    </a>
                                    @if(auth()->user()->isAdmin())
                                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteSaleModal{{ $sale->id }}" title="Futa Mauzo">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        @if(auth()->user()->isAdmin())
                        <!-- Delete Sale Modal -->
                        <div class="modal fade" id="deleteSaleModal{{ $sale->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <form action="{{ route('sales.destroy', $sale) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <div class="modal-body p-4 text-center">
                                            <div class="text-danger mb-3" style="font-size: 2.8rem;">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                            </div>
                                            <h5 class="fw-bold text-dark mb-2">Thibitisha Kufuta Mauzo</h5>
                                            <p class="text-muted small mb-2">Je, una uhakika unataka kufuta kabisa muamala wa mauzo ya ankara <strong>{{ $sale->invoice_number }}</strong> ya thamani ya <strong>Tsh {{ number_format($sale->total_amount) }}</strong>?</p>
                                            <div class="alert alert-warning py-2 px-3 small text-start mb-0">
                                                <i class="bi bi-info-circle-fill me-1"></i> Dawa zote zilizouzwa kwenye muamala huu zitarudishwa moja kwa moja kwenye stoo (Restock).
                                            </div>
                                        </div>
                                        <div class="modal-footer bg-light justify-content-center border-0 pt-0 pb-3">
                                            <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">Ghairi</button>
                                            <button type="submit" class="btn btn-danger px-4"><i class="bi bi-trash me-1"></i> Ndiyo, Futa Mauzo</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna mauzo yaliyofanyika kwa kipindi hiki.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sales->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $sales->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
