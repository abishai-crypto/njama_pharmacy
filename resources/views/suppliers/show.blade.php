@extends('layouts.app')

@section('title', $supplier->name . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('suppliers.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwa Wasambazaji
            </a>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-building text-teal me-2"></i>{{ $supplier->name }}</h3>
        </div>
        <div class="d-flex gap-2">
            @if(in_array(auth()->user()->role, ['admin', 'inventory_officer']))
            <a href="{{ route('purchases.create') }}?supplier_id={{ $supplier->id }}" class="btn btn-teal shadow-sm">
                <i class="bi bi-cart-plus me-1"></i> Ankara Mpya ya Manunuzi
            </a>
            @endif
        </div>
    </div>

    <!-- Overview Stats Cards -->
    @php
        $totalPurchased = $supplier->purchases->sum('total_amount');
        $totalPaid = $supplier->purchases->sum('paid_amount');
        $outstanding = $totalPurchased - $totalPaid;
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Jumla ya Ankara</span>
                <h4 class="fw-bold text-dark mt-2 mb-0">{{ $supplier->purchases->count() }}</h4>
                <small class="text-teal mt-1"><i class="bi bi-receipt me-1"></i>Kumbukumbu zote</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Jumla ya Manunuzi</span>
                <h4 class="fw-bold text-dark mt-2 mb-0">Tsh {{ number_format($totalPurchased) }}</h4>
                <small class="text-muted mt-1">Gharama za jumla</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Kiasi Kilicholipwa</span>
                <h4 class="fw-bold text-success mt-2 mb-0">Tsh {{ number_format($totalPaid) }}</h4>
                <small class="text-success mt-1"><i class="bi bi-check-all me-1"></i>Imeridhiwa</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3">
                <span class="text-muted small text-uppercase fw-semibold">Deni Lililobaki</span>
                <h4 class="fw-bold {{ $outstanding > 0 ? 'text-danger' : 'text-secondary' }} mt-2 mb-0">
                    Tsh {{ number_format($outstanding) }}
                </h4>
                <small class="{{ $outstanding > 0 ? 'text-danger' : 'text-muted' }} mt-1">
                    {{ $outstanding > 0 ? 'Inasubiri kulipwa' : 'Hakuna deni' }}
                </small>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Supplier Contact Card -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-info-circle text-teal me-2"></i>Taarifa za Msambazaji</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Mtu wa Mawasiliano:</span>
                            <span class="fw-semibold">{{ $supplier->contact_person ?? 'Haikuwekwa' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Namba ya Simu:</span>
                            <span class="fw-semibold text-teal">{{ $supplier->phone ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Barua Pepe:</span>
                            <span class="fw-semibold">{{ $supplier->email ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Namba ya TIN:</span>
                            <span class="fw-semibold">{{ $supplier->tin_number ?? 'Bila TIN' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Masharti ya Malipo:</span>
                            <span class="fw-semibold">{{ $supplier->payment_terms ?? 'Makubaliano' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Hali:</span>
                            <span>{!! $supplier->is_active ? '<span class="badge bg-success-subtle text-success border border-success-subtle">Hai</span>' : '<span class="badge bg-danger-subtle text-danger">Imesitishwa</span>' !!}</span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Anwani:</span>
                            <span class="fw-semibold">{{ $supplier->address ?? 'Haikuwekwa' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Invoices & Purchase History -->
        <div class="col-lg-8">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-teal me-2"></i>Historia ya Ankara za Manunuzi</h5>
                    <span class="badge bg-teal rounded-pill">{{ $supplier->purchases->count() }} Ankara</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Rejea</th>
                                    <th>Ankara Na.</th>
                                    <th>Tarehe</th>
                                    <th>Idadi ya Dawa</th>
                                    <th>Jumla (TZS)</th>
                                    <th>Hali ya Malipo</th>
                                    <th class="text-end pe-4">Kitendo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplier->purchases as $purchase)
                                <tr>
                                    <td class="ps-4 fw-bold text-teal">{{ $purchase->reference_number }}</td>
                                    <td>{{ $purchase->invoice_number ?? '—' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $purchase->items->count() }} aina</span></td>
                                    <td class="fw-bold">Tsh {{ number_format($purchase->total_amount) }}</td>
                                    <td>
                                        @if($purchase->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Imelipwa Yote</span>
                                        @elseif($purchase->payment_status === 'partial')
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Imelipwa Kiasi</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Haijalipwa</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('purchases.show', $purchase) }}" class="btn btn-sm btn-outline-teal">
                                            <i class="bi bi-eye me-1"></i> Tazama
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">Hakuna ankara za manunuzi zilizorekodiwa kwa msambazaji huyu.</td>
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
