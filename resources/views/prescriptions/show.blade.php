@extends('layouts.app')

@section('title', 'Hati ya Daktari ' . $prescription->prescription_number . ' — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Orodha ya Hati
            </a>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-file-earmark-medical text-teal me-2"></i>Hati ya Daktari: {{ $prescription->prescription_number }}</h3>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> Chapisha Hati
            </button>
            @if(in_array($prescription->status, ['pending', 'partially_dispensed']) && in_array(auth()->user()->role, ['admin', 'pharmacist', 'cashier']))
            <a href="{{ route('pos.index') }}" class="btn btn-teal shadow-sm">
                <i class="bi bi-cart-check me-1"></i> Toa Dawa Kupitia POS
            </a>
            @endif
        </div>
    </div>

    <div class="row g-4">
        <!-- Patient & Doctor Cards -->
        <div class="col-lg-4">
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-circle text-teal me-2"></i>Taarifa za Mgonjwa</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Jina Kamili:</span>
                            <span class="fw-bold text-dark">{{ $prescription->patient_name }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Umri:</span>
                            <span class="fw-semibold">{{ $prescription->patient_age ? $prescription->patient_age . ' miaka' : '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Jinsia:</span>
                            <span class="fw-semibold">{{ ucfirst($prescription->patient_gender ?? '—') }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Simu ya Mgonjwa:</span>
                            <span class="fw-semibold text-teal">{{ $prescription->patient_phone ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Tarehe ya Hati:</span>
                            <span class="fw-semibold">{{ \Carbon\Carbon::parse($prescription->prescription_date)->format('d/m/Y') }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Hali:</span>
                            <span>
                                @if($prescription->status === 'dispensed')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Imetolewa Yote</span>
                                @elseif($prescription->status === 'partially_dispensed')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Kiasi Kimebaki</span>
                                @elseif($prescription->status === 'pending')
                                    <span class="badge bg-info-subtle text-info border border-info-subtle">Inasubiri Kutolewa</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border">Imefutwa</span>
                                @endif
                            </span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-hospital text-teal me-2"></i>Taarifa za Mwandishi (Daktari)</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-group list-group-flush small">
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Daktari:</span>
                            <span class="fw-bold text-dark">{{ $prescription->prescriber_name }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Hospitali / Kituo:</span>
                            <span class="fw-semibold">{{ $prescription->prescriber_hospital ?? 'Haikutajwa' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Simu ya Daktari:</span>
                            <span class="fw-semibold">{{ $prescription->prescriber_phone ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Namba ya Usajili (MCT):</span>
                            <span class="fw-semibold">{{ $prescription->prescriber_reg_no ?? '—' }}</span>
                        </li>
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Ugonjwa / Diagnosis:</span>
                            <span class="fw-semibold text-dark">{{ $prescription->diagnosis_summary ?? 'Hakuna' }}</span>
                        </li>
                        @if($prescription->notes)
                        <li class="list-group-item px-0">
                            <span class="text-muted d-block mb-1">Maelezo ya Ziada (Notes):</span>
                            <span class="text-dark">{{ $prescription->notes }}</span>
                        </li>
                        @endif
                        <li class="list-group-item px-0 d-flex justify-content-between">
                            <span class="text-muted">Mhakiki (Mfamasia):</span>
                            <span class="fw-semibold text-teal">{{ $prescription->verifier ? $prescription->verifier->name : 'Bila mhakiki' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Prescribed Medicines & Dispensing History -->
        <div class="col-lg-8">
            <!-- Items Prescribed -->
            <div class="card card-custom border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-capsule text-teal me-2"></i>Dawa Zilizoagizwa (Prescribed Medicines)</h5>
                    <span class="badge bg-teal rounded-pill">{{ $prescription->items->count() }} dawa</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Jina la Dawa</th>
                                    <th>Maelekezo (Dosage)</th>
                                    <th class="text-center">Kiasi Kilichoagizwa</th>
                                    <th class="text-center">Kiasi Kilichotolewa</th>
                                    <th>Muda (Siku)</th>
                                    <th class="text-end pe-4">Hali</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prescription->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $item->product ? $item->product->generic_name : 'Dawa Haijulikani' }}</div>
                                        <small class="text-muted">
                                            {{ $item->product ? $item->product->brand_name : '' }} • 
                                            {{ $item->product && $item->product->unit ? $item->product->unit->name : '' }}
                                        </small>
                                        @if($item->product && $item->product->is_restricted)
                                            <span class="badge bg-danger ms-1" style="font-size: 0.65rem;">RESTRICTED</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $item->dosage_instructions }}</span></td>
                                    <td class="text-center fw-bold">{{ $item->prescribed_quantity }}</td>
                                    <td class="text-center fw-bold text-success">{{ $item->dispensed_quantity }}</td>
                                    <td>{{ $item->duration_days ? $item->duration_days . ' siku' : '—' }}</td>
                                    <td class="text-end pe-4">
                                        @if($item->dispensed_quantity >= $item->prescribed_quantity)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Imekamilika</span>
                                        @elseif($item->dispensed_quantity > 0)
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Kiasi</span>
                                        @else
                                            <span class="badge bg-info-subtle text-info border border-info-subtle">Bado</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Dispensing Records History -->
            <div class="card card-custom border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-medical text-teal me-2"></i>Kumbukumbu za Utoaji (Dispensing Log)</h5>
                    <span class="badge bg-secondary rounded-pill">{{ $prescription->dispensingRecords->count() }} utoaji</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4">Tarehe & Saa</th>
                                    <th>Dawa</th>
                                    <th>Kiasi Kilichotolewa</th>
                                    <th>Mtoa Dawa (Dispenser)</th>
                                    <th class="pe-4">Maelezo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescription->dispensingRecords as $record)
                                <tr>
                                    <td class="ps-4 text-muted small">{{ $record->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="fw-semibold">{{ $record->product ? $record->product->generic_name : '—' }}</td>
                                    <td><span class="badge bg-teal">{{ $record->quantity_dispensed }}</span></td>
                                    <td><span class="text-dark">{{ $record->dispenser ? $record->dispenser->name : 'N/A' }}</span></td>
                                    <td class="pe-4 text-muted small">{{ $record->remarks ?? '—' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Bado hakuna dawa iliyotolewa kwenye hati hii.</td>
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
