@extends('layouts.app')

@section('title', 'Kumbukumbu za Utoaji Dawa (Dispensing) — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Hati za Daktari
            </a>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-check text-teal me-2"></i>Kumbukumbu za Utoaji Dawa (Dispensing Records)</h3>
            <p class="text-muted small mb-0">Rejesta rasmi ya dawa zilizotolewa kwa maagizo ya daktari na wataalamu walioidhinisha</p>
        </div>
        <div>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-teal shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Rekodi Hati Mpya (Rx)
            </a>
        </div>
    </div>

    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Tarehe & Saa</th>
                            <th>Na. ya Hati (Rx #)</th>
                            <th>Jina la Mgonjwa</th>
                            <th>Dawa Iliyotolewa</th>
                            <th class="text-center">Kiasi</th>
                            <th>Mfamasia / Mtoa Dawa</th>
                            <th class="pe-4">Maelezo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $rec)
                        <tr>
                            <td class="ps-4 text-muted small">
                                <i class="bi bi-clock me-1 text-teal"></i>{{ $rec->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td>
                                @if($rec->prescription)
                                <a href="{{ route('prescriptions.show', $rec->prescription) }}" class="fw-bold text-teal text-decoration-none">
                                    {{ $rec->prescription->prescription_number }}
                                </a>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rec->prescription ? $rec->prescription->patient_name : '—' }}</div>
                                <small class="text-muted">{{ $rec->prescription && $rec->prescription->prescriber_name ? 'Daktari: ' . $rec->prescription->prescriber_name : '' }}</small>
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rec->product ? $rec->product->generic_name : '—' }}</div>
                                <small class="text-muted">{{ $rec->product ? $rec->product->brand_name : '' }}</small>
                                @if($rec->product && $rec->product->is_restricted)
                                    <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">RESTRICTED</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge bg-teal rounded-pill px-3">{{ $rec->quantity_dispensed }} {{ $rec->product && $rec->product->unit ? $rec->product->unit->symbol : '' }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><i class="bi bi-person-check text-teal me-1"></i>{{ $rec->dispenser ? $rec->dispenser->name : 'N/A' }}</div>
                            </td>
                            <td class="pe-4 text-muted small">{{ $rec->remarks ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna kumbukumbu za utoaji dawa (dispensing) zilizopatikana.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $records->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
