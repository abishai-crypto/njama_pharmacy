@extends('layouts.app')

@section('title', 'Purchases & Invoices')
@section('page_title', __('app.purchases'))

@section('content')
<div class="card card-custom p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-truck text-emerald me-2"></i>Mapokezi ya Dawa & Ankara za Wasambazaji (Purchases)</h5>
            <small class="text-muted">Dhibiti manunuzi ya dawa kutoka kwa wasambazaji, madeni, na uingizaji wa stoo.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-building me-1"></i> Wasambazaji (Suppliers)
            </a>
            <a href="{{ route('purchases.create') }}" class="btn btn-emerald btn-sm d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-plus-circle-fill"></i> Pokea Mzigo Mpya (Receive Goods)
            </a>
        </div>
    </div>
</div>

<!-- Purchases Table -->
<div class="card card-custom overflow-hidden">
    <div class="table-responsive">
        <table class="table table-custom align-middle">
            <thead>
                <tr>
                    <th>Ankara # (Invoice)</th>
                    <th>Msambazaji (Supplier)</th>
                    <th>Tarehe ya Mzigo</th>
                    <th class="text-end">Jumla ya Gharama</th>
                    <th class="text-end">Iliyolipwa</th>
                    <th class="text-end">Deni Lililobaki</th>
                    <th class="text-center">Hali ya Malipo</th>
                    <th>Mpokeaji</th>
                    <th class="text-center" style="width: 100px;">Kitendo</th>
                </tr>
            </thead>
            <tbody>
                @forelse($purchases as $p)
                <tr>
                    <td class="fw-bold text-dark">
                        <a href="{{ route('purchases.show', $p->id) }}" class="text-decoration-none text-dark">
                            {{ $p->invoice_number }}
                        </a>
                    </td>
                    <td>
                        <div class="fw-semibold text-dark">{{ $p->supplier ? $p->supplier->name : 'N/A' }}</div>
                        <small class="text-muted">{{ $p->supplier ? $p->supplier->phone : '' }}</small>
                    </td>
                    <td>{{ $p->purchase_date->format('d/m/Y') }}</td>
                    <td class="text-end fw-bold text-dark">Tsh {{ number_format($p->total_amount) }}</td>
                    <td class="text-end text-success">Tsh {{ number_format($p->paid_amount) }}</td>
                    <td class="text-end fw-bold {{ $p->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                        Tsh {{ number_format($p->balance_due) }}
                    </td>
                    <td class="text-center">
                        @if($p->payment_status === 'paid')
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Paid Full</span>
                        @elseif($p->payment_status === 'partial')
                            <span class="badge bg-warning-subtle text-warning text-dark border">Partial</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Unpaid</span>
                        @endif
                    </td>
                    <td class="small text-muted">{{ $p->receiver ? $p->receiver->name : 'N/A' }}</td>
                    <td class="text-center">
                        <a href="{{ route('purchases.show', $p->id) }}" class="btn btn-sm btn-outline-secondary py-1" title="View Details">
                            <i class="bi bi-eye"></i> Tazama
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5 text-muted">Hakuna ununuzi uliorekodiwa bado.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-3 border-top bg-white d-flex justify-content-between align-items-center">
        <small class="text-muted">Inaonyesha {{ $purchases->firstItem() ?? 0 }} hadi {{ $purchases->lastItem() ?? 0 }} kati ya {{ $purchases->total() }}</small>
        {{ $purchases->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
