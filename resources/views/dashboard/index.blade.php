@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', __('app.dashboard'))

@section('content')
<!-- Date Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div class="d-flex align-items-center gap-2">
            <span class="fw-bold text-muted small text-uppercase"><i class="bi bi-funnel me-1"></i>Period Filter:</span>
            <div class="btn-group btn-group-sm">
                <a href="{{ route('dashboard', ['period' => 'today']) }}" class="btn {{ $period == 'today' ? 'btn-emerald' : 'btn-outline-secondary' }}">Today</a>
                <a href="{{ route('dashboard', ['period' => 'yesterday']) }}" class="btn {{ $period == 'yesterday' ? 'btn-emerald' : 'btn-outline-secondary' }}">Yesterday</a>
                <a href="{{ route('dashboard', ['period' => 'week']) }}" class="btn {{ $period == 'week' ? 'btn-emerald' : 'btn-outline-secondary' }}">This Week</a>
                <a href="{{ route('dashboard', ['period' => 'month']) }}" class="btn {{ $period == 'month' ? 'btn-emerald' : 'btn-outline-secondary' }}">This Month</a>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-light text-dark border px-3 py-2">
                <i class="bi bi-calendar3 me-1 text-emerald"></i>
                {{ $startDate->format('d/m/Y') }} &mdash; {{ $endDate->format('d/m/Y') }}
            </span>
            @if(auth()->user()->isCashier() || auth()->user()->isPharmacist() || auth()->user()->isAdmin())
            <a href="{{ route('pos.index') }}" class="btn btn-emerald btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-cart-plus-fill"></i> Open POS Terminal (F2)
            </a>
            @endif
        </div>
    </div>
</div>

<!-- Critical Expiry & Shift Alerts -->
<div class="row g-3 mb-4">
    @if($expiredBatchesCount > 0)
    <div class="col-md-6 col-lg-4">
        <div class="alert alert-danger mb-0 d-flex align-items-center justify-content-between p-3 border-0 shadow-sm" style="background-color: #fef2f2; border-left: 4px solid #ef4444 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-octagon-fill fs-3 text-danger"></i>
                <div>
                    <h6 class="mb-0 fw-bold text-danger">{{ $expiredBatchesCount }} Expired Batches!</h6>
                    <small class="text-muted">Requires immediate quarantine.</small>
                </div>
            </div>
            <a href="{{ route('batches.index', ['expiry' => 'expired']) }}" class="btn btn-sm btn-danger">Quarantine Now</a>
        </div>
    </div>
    @endif

    @if($expiringSoonCount > 0)
    <div class="col-md-6 col-lg-4">
        <div class="alert alert-warning mb-0 d-flex align-items-center justify-content-between p-3 border-0 shadow-sm" style="background-color: #fffbeb; border-left: 4px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-clock-history fs-3 text-warning"></i>
                <div>
                    <h6 class="mb-0 fw-bold text-warning">{{ $expiringSoonCount }} Batches Expiring Soon</h6>
                    <small class="text-muted">Expiring in &lt; 90 Days. Prioritize FEFO sale.</small>
                </div>
            </div>
            <a href="{{ route('batches.index', ['expiry' => '90']) }}" class="btn btn-sm btn-warning text-dark">Review FEFO</a>
        </div>
    </div>
    @endif

    @if($lowStockProducts->count() > 0)
    <div class="col-md-6 col-lg-4">
        <div class="alert alert-info mb-0 d-flex align-items-center justify-content-between p-3 border-0 shadow-sm" style="background-color: #f0fdfa; border-left: 4px solid #0d9488 !important;">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-box-seam fs-3 text-emerald" style="color: #0d9488;"></i>
                <div>
                    <h6 class="mb-0 fw-bold" style="color: #0f766e;">{{ $lowStockProducts->count() }} Low Stock Medicines</h6>
                    <small class="text-muted">At or below reorder threshold.</small>
                </div>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-sm btn-outline-secondary">View Stock</a>
        </div>
    </div>
    @endif
</div>

<!-- Primary Financial KPIs (6 Metrics) -->
<div class="row g-3 mb-4">
    <!-- Total Sales -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Sales Revenue</span>
                <div class="metric-icon-bubble" style="background-color: #ecfdf5; color: #0d9488;">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1" style="color: #0f766e;">Tsh {{ number_format($totalSales) }}</h4>
            <small class="text-muted">{{ $transactionsCount }} completed transactions</small>
        </div>
    </div>

    <!-- COGS -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Cost of Goods Sold</span>
                <div class="metric-icon-bubble" style="background-color: #f1f5f9; color: #475569;">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-secondary">Tsh {{ number_format($totalCogs) }}</h4>
            <small class="text-muted">Wholesale product cost</small>
        </div>
    </div>

    <!-- Gross Profit -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Gross Profit</span>
                <div class="metric-icon-bubble" style="background-color: #f0fdf4; color: #16a34a;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-success">Tsh {{ number_format($grossProfit) }}</h4>
            <small class="text-success fw-semibold">
                {{ $totalSales > 0 ? round(($grossProfit / $totalSales) * 100, 1) : 0 }}% Gross Margin
            </small>
        </div>
    </div>

    <!-- Operating Expenses -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Expenses</span>
                <div class="metric-icon-bubble" style="background-color: #fff1f2; color: #e11d48;">
                    <i class="bi bi-wallet2"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1 text-danger">Tsh {{ number_format($totalExpenses) }}</h4>
            <small class="text-muted">Rent, power, operational costs</small>
        </div>
    </div>

    <!-- Net Operating Profit -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Estimated Net</span>
                <div class="metric-icon-bubble" style="background-color: #eff6ff; color: #2563eb;">
                    <i class="bi bi-trophy"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1" style="color: {{ $netOperatingProfit >= 0 ? '#1d4ed8' : '#dc2626' }};">
                Tsh {{ number_format($netOperatingProfit) }}
            </h4>
            <small class="text-muted">Gross Profit - Expenses</small>
        </div>
    </div>

    <!-- Inventory Valuation -->
    <div class="col-sm-6 col-xl-4 col-xxl-2">
        <div class="card card-custom p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted small fw-bold text-uppercase">Stock Value (Cost)</span>
                <div class="metric-icon-bubble" style="background-color: #fdf4ff; color: #a855f7;">
                    <i class="bi bi-boxes"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-1" style="color: #7e22ce;">Tsh {{ number_format($inventoryCostValuation) }}</h4>
            <small class="text-muted">Retail Est: Tsh {{ number_format($inventoryRetailValuation) }}</small>
        </div>
    </div>
</div>

<!-- Charts & Fast Actions Row -->
<div class="row g-4 mb-4">
    <!-- 7-Day Trend Chart -->
    <div class="col-lg-8">
        <div class="card card-custom p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-activity text-emerald me-2"></i>7-Day Sales & Gross Profit Performance</h6>
                <span class="badge bg-light text-dark border">TZS Currency</span>
            </div>
            <div style="height: 280px;">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Payment Methods & Quick Operations -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 h-100 d-flex flex-column">
            <h6 class="fw-bold mb-3"><i class="bi bi-pie-chart-fill text-emerald me-2"></i>Sales by Payment Channel</h6>
            
            <div style="height: 180px;" class="mb-3">
                <canvas id="paymentMethodChart"></canvas>
            </div>

            <div class="mt-auto pt-3 border-top">
                <div class="small fw-bold text-muted text-uppercase mb-2">Quick Navigation:</div>
                <div class="d-grid gap-2">
                    <a href="{{ route('products.bulk-prices') }}" class="btn btn-outline-secondary btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-tags-fill me-2 text-warning"></i>Update Medicine Selling Prices</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('purchases.create') }}" class="btn btn-outline-secondary btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-truck me-2 text-info"></i>Receive Supplier Delivery</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm text-start d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-receipt me-2 text-danger"></i>Record Operating Expense</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Tables Row -->
<div class="row g-4">
    <!-- Recent Sales -->
    <div class="col-lg-7">
        <div class="card card-custom overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt text-emerald me-2"></i>Recent Sales Transactions</h6>
                <a href="{{ route('reports.sales') }}" class="small text-decoration-none fw-semibold">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Invoice #</th>
                            <th>Time</th>
                            <th>Cashier</th>
                            <th>Payment</th>
                            <th class="text-end">Amount</th>
                            <th class="text-center">Receipt</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSales as $sale)
                        <tr>
                            <td class="fw-bold text-dark">{{ $sale->invoice_number }}</td>
                            <td class="text-muted">{{ $sale->created_at->format('H:i') }}</td>
                            <td>{{ $sale->cashier ? $sale->cashier->name : 'N/A' }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-emerald">Tsh {{ number_format($sale->total_amount) }}</td>
                            <td class="text-center">
                                <a href="{{ route('pos.receipt', $sale->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Print Receipt">
                                    <i class="bi bi-printer"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No sales recorded yet for this period. Open POS to make first sale!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Medicines & Stock Alerts -->
    <div class="col-lg-5">
        <div class="card card-custom overflow-hidden">
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-capsule-pill text-emerald me-2"></i>Stock Approaching Minimum Threshold</h6>
                <a href="{{ route('products.index') }}" class="small text-decoration-none fw-semibold">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Medicine</th>
                            <th class="text-center">Stock</th>
                            <th class="text-center">Threshold</th>
                            <th class="text-end">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockProducts->take(6) as $prod)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark text-truncate" style="max-width: 170px;">{{ $prod->generic_name }}</div>
                                <small class="text-muted">{{ $prod->brand_name }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">{{ $prod->total_stock }}</span>
                            </td>
                            <td class="text-center text-muted">{{ $prod->minimum_stock_alert }}</td>
                            <td class="text-end fw-semibold">Tsh {{ number_format($prod->selling_price) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">All stock levels are currently healthy!</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Sales & Profit Trend Chart
    const ctxTrend = document.getElementById('salesTrendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartLabels) !!},
            datasets: [
                {
                    label: 'Sales Revenue (TZS)',
                    data: {!! json_encode($chartSales) !!},
                    backgroundColor: 'rgba(13, 148, 136, 0.85)',
                    borderRadius: 6,
                },
                {
                    label: 'Gross Profit (TZS)',
                    data: {!! json_encode($chartProfit) !!},
                    backgroundColor: 'rgba(34, 197, 94, 0.85)',
                    borderRadius: 6,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': Tsh ' + new Intl.NumberFormat().format(context.raw);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return 'Tsh ' + (value >= 1000 ? (value / 1000) + 'k' : value);
                        }
                    }
                }
            }
        }
    });

    // Payment Methods Breakdown Chart
    const ctxPay = document.getElementById('paymentMethodChart').getContext('2d');
    const paymentMethodsData = {!! json_encode($salesByPaymentMethod) !!};
    
    let payLabels = paymentMethodsData.map(p => p.payment_method.toUpperCase());
    let payTotals = paymentMethodsData.map(p => p.total);

    if (payLabels.length === 0) {
        payLabels = ['CASH (SAMPLE)'];
        payTotals = [1];
    }

    new Chart(ctxPay, {
        type: 'doughnut',
        data: {
            labels: payLabels,
            datasets: [{
                data: payTotals,
                backgroundColor: ['#0d9488', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'],
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 12 } }
            }
        }
    });
</script>
@endsection
