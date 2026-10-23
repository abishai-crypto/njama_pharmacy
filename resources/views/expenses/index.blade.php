@extends('layouts.app')

@section('title', __('app.expenses') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-wallet2 text-teal me-2"></i>{{ __('app.expenses') }}</h3>
            <p class="text-muted small mb-0">Usimamizi wa matumizi ya uendeshaji (kodi, umeme, mishahara, leseni, n.k.)</p>
        </div>
        <button class="btn btn-teal shadow-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
            <i class="bi bi-plus-circle me-1"></i> Rekodi Matumizi Mapya
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Total Expenses Metric Card -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-custom border-0 shadow-sm p-4">
                <span class="text-muted small text-uppercase fw-semibold">Jumla ya Matumizi Kwenye Orodha Hii</span>
                <h3 class="fw-bold text-danger mt-2 mb-0">Tsh {{ number_format($totalExpenses) }}</h3>
                <small class="text-muted mt-1"><i class="bi bi-calculator me-1"></i>Kutoka kwenye vigezo vilivyochaguliwa</small>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('expenses.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <select name="category_id" class="form-select">
                        <option value="">Aina Zote za Matumizi (All Categories)</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}" placeholder="Tarehe ya Kuanzia">
                </div>
                <div class="col-md-3">
                    <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}" placeholder="Tarehe ya Mwisho">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-funnel me-1"></i> Chuja</button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary">Weka Upya</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Expenses Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Rejea</th>
                            <th>Tarehe</th>
                            <th>Aina ya Matumizi</th>
                            <th>Maelezo / Sababu</th>
                            <th>Njia ya Malipo</th>
                            <th>Alirekodi</th>
                            <th class="text-end pe-4">Kiasi (TZS)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                        <tr>
                            <td class="ps-4 fw-bold text-teal">{{ $exp->expense_number }}</td>
                            <td>{{ \Carbon\Carbon::parse($exp->expense_date)->format('d/m/Y') }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $exp->category ? $exp->category->name : 'Nyingine' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $exp->title }}</div>
                                @if($exp->notes)
                                    <small class="text-muted">{{ $exp->notes }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary border">
                                    {{ ucfirst(str_replace('_', ' ', $exp->payment_method)) }}
                                </span>
                            </td>
                            <td><small class="text-muted">{{ $exp->recorder ? $exp->recorder->name : '—' }}</small></td>
                            <td class="text-end pe-4 fw-bold text-danger">Tsh {{ number_format($exp->amount) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-wallet2 fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna matumizi yaliyorekodiwa kwa vigezo hivi.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $expenses->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Add Expense Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <form action="{{ route('expenses.store') }}" method="POST">
                @csrf
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title fw-bold"><i class="bi bi-wallet2 me-2"></i>Rekodi Matumizi Mapya</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Aina ya Matumizi (Category) <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- Chagua Aina --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Maelezo ya Matumizi (Title) <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="Mfano: Umeme Luku ya Pharmacy au Kodi ya Mwezi" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kiasi (TZS) <span class="text-danger">*</span></label>
                            <input type="number" name="amount" class="form-control fw-bold" placeholder="50000" min="1" step="any" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tarehe <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Njia ya Malipo <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash">Pesa Taslimu (Cash)</option>
                            <option value="bank_transfer">Benki (Bank Transfer)</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="tigo_pesa">Tigo Pesa</option>
                            <option value="airtel_money">Airtel Money</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Maelezo ya Ziada (Notes / Risiti)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Mfano: Risiti namba au kumbukumbu"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-teal"><i class="bi bi-check-circle me-1"></i>Hifadhi Matumizi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
