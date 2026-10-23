@extends('layouts.app')

@section('title', __('app.profit_loss') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-pie-chart text-teal me-2"></i>{{ __('app.profit_loss') }} (P&L)</h3>
            <p class="text-muted small mb-0">Taarifa kamili ya mapato, gharama za bidhaa, matumizi ya uendeshaji na faida halisi</p>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Chapisha Taarifa
            </button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit-loss') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Tarehe ya Kuanzia</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate->toDateString() }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">Tarehe ya Mwisho</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate->toDateString() }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-funnel me-1"></i> Sasisha Kipindi</button>
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-outline-secondary">Mwezi Huu</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- P&L Financial Statement Card -->
        <div class="col-lg-7">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt text-teal me-2"></i>Taarifa ya Faida na Hasara (Financial Statement)</h5>
                    <small class="text-muted">Kipindi: {{ $startDate->format('d/m/Y') }} hadi {{ $endDate->format('d/m/Y') }}</small>
                </div>
                <div class="card-body p-0">
                    <table class="table align-middle mb-0">
                        <tbody>
                            <!-- Revenue Section -->
                            <tr class="table-light">
                                <th colspan="2" class="ps-4 text-uppercase text-muted small fw-bold">1. Mapato ya Mauzo (Revenue)</th>
                            </tr>
                            <tr>
                                <td class="ps-4">Jumla ya Mauzo ya Dawa (Gross Sales)</td>
                                <td class="text-end pe-4 fw-bold text-dark">Tsh {{ number_format($totalSales) }}</td>
                            </tr>
                            <tr>
                                <td class="ps-4 text-muted ps-5">(-) Gharama za Dawa Zilizouzwa (COGS - Wholesale Cost)</td>
                                <td class="text-end pe-4 text-secondary">Tsh ({{ number_format($totalCogs) }})</td>
                            </tr>
                            <tr class="bg-success-subtle border-top border-bottom">
                                <td class="ps-4 fw-bold text-success fs-6">FAIDA GHAFI (GROSS PROFIT)</td>
                                <td class="text-end pe-4 fw-bold text-success fs-6">Tsh {{ number_format($grossProfit) }}</td>
                            </tr>

                            <!-- Operating Expenses Section -->
                            <tr class="table-light">
                                <th colspan="2" class="ps-4 text-uppercase text-muted small fw-bold">2. Matumizi ya Uendeshaji (Operating Expenses)</th>
                            </tr>
                            @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-4 text-muted ps-5">{{ $exp->category ? $exp->category->name : 'Nyingine' }}</td>
                                <td class="text-end pe-4 text-danger">Tsh {{ number_format($exp->total) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td class="ps-4 text-muted ps-5 fst-italic">Hakuna matumizi yaliyorekodiwa kipindi hiki</td>
                                <td class="text-end pe-4 text-muted">Tsh 0</td>
                            </tr>
                            @endforelse
                            <tr class="border-top">
                                <td class="ps-4 fw-semibold text-danger">Jumla ya Matumizi ya Uendeshaji</td>
                                <td class="text-end pe-4 fw-bold text-danger">Tsh ({{ number_format($totalExpenses) }})</td>
                            </tr>

                            <!-- Net Profit Section -->
                            <tr class="{{ $netProfit >= 0 ? 'bg-teal text-white' : 'bg-danger text-white' }}">
                                <td class="ps-4 py-3 fw-bold fs-5">
                                    {{ $netProfit >= 0 ? 'FAIDA HALISI (NET PROFIT)' : 'HASARA HALISI (NET LOSS)' }}
                                </td>
                                <td class="text-end pe-4 py-3 fw-bold fs-5">
                                    Tsh {{ number_format($netProfit) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small">Kiwango cha Faida Halisi (Net Profit Margin):</span>
                        <span class="fw-bold fs-6 {{ $netProfit >= 0 ? 'text-teal' : 'text-danger' }}">
                            {{ $totalSales > 0 ? round(($netProfit / $totalSales) * 100, 2) . '%' : '0.00%' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Top 15 Profitable Medicines -->
        <div class="col-lg-5">
            <div class="card card-custom border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-trophy text-teal me-2"></i>Dawa Zenye Faida Kubwa (Top Profitable)</h5>
                    <small class="text-muted">Dawa 15 zilizoongoza kwa kuleta faida ghafi</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-3">Dawa</th>
                                    <th class="text-end">Mauzo</th>
                                    <th class="text-end pe-3">Faida</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topProfitableMedicines as $top)
                                <tr>
                                    <td class="ps-3">
                                        <div class="fw-semibold text-dark">{{ $top->product ? $top->product->generic_name : 'Dawa #' . $top->product_id }}</div>
                                        <small class="text-muted">{{ $top->product ? $top->product->brand_name : '' }}</small>
                                    </td>
                                    <td class="text-end small">Tsh {{ number_format($top->revenue) }}</td>
                                    <td class="text-end pe-3 fw-bold text-success">Tsh {{ number_format($top->profit) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted">Hakuna mauzo yaliyofanyika bado.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
