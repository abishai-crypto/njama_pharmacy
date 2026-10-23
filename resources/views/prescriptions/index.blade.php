@extends('layouts.app')

@section('title', __('app.prescriptions') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-file-earmark-medical text-teal me-2"></i>{{ __('app.prescriptions') }}</h3>
            <p class="text-muted small mb-0">Usimamizi na uhakiki wa maagizo ya daktari na utoaji wa dawa (Dispensing)</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('prescriptions.dispensing') }}" class="btn btn-outline-secondary">
                <i class="bi bi-journal-check me-1"></i> Kumbukumbu za Dispensing
            </a>
            <a href="{{ route('prescriptions.create') }}" class="btn btn-teal shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Rekodi Hati Mpya (Rx)
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters & Search -->
    <div class="card card-custom border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('prescriptions.index') }}" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Tafuta kwa Na. ya Rx, jina la mgonjwa au daktari..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Hali Zote (All Statuses)</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Inasubiri (Pending)</option>
                        <option value="partially_dispensed" {{ request('status') === 'partially_dispensed' ? 'selected' : '' }}>Imetolewa Kiasi (Partial)</option>
                        <option value="dispensed" {{ request('status') === 'dispensed' ? 'selected' : '' }}>Imetolewa Yote (Dispensed)</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Imefutwa (Cancelled)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-teal w-100"><i class="bi bi-funnel me-1"></i> Chuja</button>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('prescriptions.index') }}" class="btn btn-outline-secondary w-100">Weka Upya</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Na. ya Hati (Rx #)</th>
                            <th>Tarehe</th>
                            <th>Mgonjwa</th>
                            <th>Daktari & Hospitali</th>
                            <th>Dawa Zilizoagizwa</th>
                            <th>Hali</th>
                            <th>Mhakiki</th>
                            <th class="text-end pe-4">Vitendo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($prescriptions as $rx)
                        <tr>
                            <td class="ps-4 fw-bold text-teal">
                                <a href="{{ route('prescriptions.show', $rx) }}" class="text-decoration-none text-teal">
                                    {{ $rx->prescription_number }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($rx->prescription_date)->format('d/m/Y') }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $rx->patient_name }}</div>
                                <small class="text-muted">
                                    @if($rx->patient_age) {{ $rx->patient_age }} yrs @endif
                                    @if($rx->patient_gender) ({{ ucfirst($rx->patient_gender) }}) @endif
                                    @if($rx->patient_phone) • {{ $rx->patient_phone }} @endif
                                </small>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $rx->prescriber_name }}</div>
                                <small class="text-muted">{{ $rx->prescriber_hospital ?? 'Kliniki / Binafsi' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-capsule me-1 text-teal"></i>{{ $rx->items->count() }} dawa
                                </span>
                            </td>
                            <td>
                                @if($rx->status === 'dispensed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Imetolewa</span>
                                @elseif($rx->status === 'partially_dispensed')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Kiasi</span>
                                @elseif($rx->status === 'pending')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">Inasubiri</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Imefutwa</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $rx->verifier ? $rx->verifier->name : '—' }}</small>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-sm btn-outline-teal">
                                    <i class="bi bi-eye me-1"></i> Tazama
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-file-earmark-medical fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Hakuna hati ya daktari (Prescription) iliyopatikana.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($prescriptions->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $prescriptions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
