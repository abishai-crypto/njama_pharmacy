@extends('layouts.app')

@section('title', __('app.shifts') . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="bi bi-clock-history text-teal me-2"></i>{{ __('app.shifts') }}</h3>
            <p class="text-muted small mb-0">Ufunguzi, ufungaji, na upatanisho wa fedha za mauzo ya madirisha (Shift Reconciliation)</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Active Shift Management Card -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            @if($currentShift)
                <!-- Current Open Shift: Close Form -->
                <div class="card card-custom border-0 shadow-sm border-start border-success border-4">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-badge text-success me-2"></i>Zamu Yako Ipo Wazi (Active Shift)</h5>
                        <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2">
                            <i class="bi bi-dot"></i> Inafanya Kazi Sasa
                        </span>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-2 mb-3 bg-light p-3 rounded-3">
                            <div class="col-6">
                                <span class="text-muted small">Muda Uliofungua:</span>
                                <div class="fw-bold text-dark">{{ $currentShift->opened_at->format('d/m/Y H:i') }}</div>
                            </div>
                            <div class="col-6">
                                <span class="text-muted small">Pesa ya Kuanzia (Opening Float):</span>
                                <div class="fw-bold text-teal">Tsh {{ number_format($currentShift->opening_cash) }}</div>
                            </div>
                        </div>

                        <form action="{{ route('shifts.close', $currentShift) }}" method="POST">
                            @csrf
                            <h6 class="fw-bold text-dark mb-2">Funga Zamu na Uhesabu Fedha za Droo</h6>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pesa Taslimu Zilizopo Kwenye Droo (Counted Cash) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Tsh</span>
                                    <input type="number" name="counted_cash" class="form-control form-control-lg fw-bold" placeholder="Weka jumla ya noti na sarafu zote" required min="0" step="any">
                                </div>
                                <small class="text-muted">Mfumo utakokotoa kiotomatiki mauzo ya taslimu + pesa ya kuanzia na kutoa taarifa ya tofauti (difference).</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sababu ya Tofauti (Ikiwa ipo)</label>
                                <textarea name="discrepancy_reason" class="form-control" rows="2" placeholder="Mfano: Malipo ya dharura ya usafiri au chenji fupi"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100 fw-bold py-2 shadow-sm" onclick="return confirm('Je, una uhakika unataka kufunga zamu yako sasa?')">
                                <i class="bi bi-lock-fill me-1"></i> Funga Zamu (Close & Reconcile Shift)
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <!-- No Open Shift: Open Form -->
                <div class="card card-custom border-0 shadow-sm border-start border-teal border-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-box-arrow-in-right text-teal me-2"></i>Fungua Zamu Mpya (Open Shift)</h5>
                    </div>
                    <div class="card-body pt-0">
                        <p class="text-muted small mb-3">
                            Kabla ya kuanza kuuza kwenye POS, ni lazima ufungue zamu yako na uingize kiasi cha chenji ya kuanzia ulichokabidhiwa (Opening Cash Float).
                        </p>
                        <form action="{{ route('shifts.open') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Chenji ya Kuanzia / Pesa Taslimu Drooni (TZS) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Tsh</span>
                                    <input type="number" name="opening_cash" class="form-control form-control-lg fw-bold text-teal" value="0" min="0" step="any" required>
                                </div>
                                <small class="text-muted">Ikiwa hakuna chenji ya kuanzia, weka 0.</small>
                            </div>
                            <button type="submit" class="btn btn-teal w-100 fw-bold py-2 shadow-sm">
                                <i class="bi bi-key-fill me-1"></i> Fungua Zamu Sasa
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <!-- Shift Guidelines Info -->
        <div class="col-lg-6">
            <div class="card card-custom border-0 shadow-sm h-100 bg-teal-subtle border border-teal-subtle">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-teal mb-3"><i class="bi bi-shield-check me-2"></i>Miongozo ya Upatanisho wa Zamu</h5>
                    <ul class="text-dark small mb-0 d-flex flex-column gap-2 ps-3">
                        <li><strong>Ulinzi wa Fedha:</strong> Kila cashier anawajibika kikamilifu kwa mauzo na pesa taslimu zilizopo kwenye droo yake wakati wa zamu.</li>
                        <li><strong>Kuhudumia Mteja:</strong> Uuzaji wote unapaswa kufanyika ndani ya mfumo wa POS ili rekodi zilingane moja kwa moja.</li>
                        <li><strong>Wakati wa Kufunga:</strong> Hesabu noti zote na sarafu kwa umakini kabla ya kuingiza kiasi. Tofauti zote (ziada au upungufu) zitaonekana wazi kwenye ripoti ya mkaguzi na mmiliki.</li>
                        <li><strong>Usimamizi:</strong> Dkt. Njama na Mkaguzi wa mahesabu wanapata taarifa kamili ya kila zamu iliyofungwa.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Shifts History Table -->
    <div class="card card-custom border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt-cutoff text-teal me-2"></i>Historia ya Zamu za Cashiers</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4">Cashier</th>
                            <th>Kufungua</th>
                            <th>Kufunga</th>
                            <th>Hali</th>
                            <th>Chenji ya Awali</th>
                            <th>Inayotarajiwa</th>
                            <th>Iliyohesabiwa</th>
                            <th>Tofauti</th>
                            <th class="pe-4">Maelezo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $shift->cashier ? $shift->cashier->name : 'N/A' }}</div>
                                <small class="text-muted">{{ $shift->cashier ? '@' . $shift->cashier->username : '' }}</small>
                            </td>
                            <td>{{ $shift->opened_at ? $shift->opened_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>{{ $shift->closed_at ? $shift->closed_at->format('d/m/Y H:i') : '—' }}</td>
                            <td>
                                @if($shift->status === 'open')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Wazi (Open)</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Imefungwa</span>
                                @endif
                            </td>
                            <td>Tsh {{ number_format($shift->opening_cash) }}</td>
                            <td>{{ $shift->expected_cash !== null ? 'Tsh ' . number_format($shift->expected_cash) : '—' }}</td>
                            <td class="fw-bold">{{ $shift->counted_cash !== null ? 'Tsh ' . number_format($shift->counted_cash) : '—' }}</td>
                            <td>
                                @if($shift->cash_difference !== null)
                                    @if($shift->cash_difference == 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Sawa (0)</span>
                                    @elseif($shift->cash_difference < 0)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                            Upungufu: Tsh {{ number_format(abs($shift->cash_difference)) }}
                                        </span>
                                    @else
                                        <span class="badge bg-info-subtle text-info border border-info-subtle">
                                            Ziada: Tsh {{ number_format($shift->cash_difference) }}
                                        </span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="pe-4 text-muted small">{{ $shift->discrepancy_reason ?? '—' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">Hakuna historia ya zamu iliyorekodiwa bado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($shifts->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $shifts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
