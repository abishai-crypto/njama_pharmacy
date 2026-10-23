@extends('layouts.app')

@section('title', 'Rekodi Hati ya Daktari (Prescription) — NJAMA PHARMACY')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <a href="{{ route('prescriptions.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Hati
            </a>
            <h3 class="fw-bold mb-0 text-dark"><i class="bi bi-prescription text-teal me-2"></i>Rekodi Hati Mpya ya Daktari (Prescription)</h3>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Tafadhali rekebisha makosa yafuatayo:</h6>
            <ul class="mb-0 small ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('prescriptions.store') }}" method="POST" id="prescriptionForm">
        @csrf
        <div class="row g-4">
            <!-- Left: Patient & Prescriber Details -->
            <div class="col-lg-5">
                <!-- Patient Info Card -->
                <div class="card card-custom border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-person-circle text-teal me-2"></i>Taarifa za Mgonjwa</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Namba ya Hati (Rx Reference) <span class="text-danger">*</span></label>
                                <input type="text" name="prescription_number" class="form-control bg-light fw-bold text-teal" value="{{ old('prescription_number', $nextRef) }}" required readonly>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Tarehe ya Hati <span class="text-danger">*</span></label>
                                <input type="date" name="prescription_date" class="form-control" value="{{ old('prescription_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Jina Kamili la Mgonjwa <span class="text-danger">*</span></label>
                                <input type="text" name="patient_name" class="form-control" placeholder="Mfano: Mary John" value="{{ old('patient_name') }}" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Umri (Miaka)</label>
                                <input type="number" name="patient_age" class="form-control" placeholder="Mfano: 28" min="0" max="130" value="{{ old('patient_age') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Jinsia</label>
                                <select name="patient_gender" class="form-select">
                                    <option value="">Chagua</option>
                                    <option value="male" {{ old('patient_gender') == 'male' ? 'selected' : '' }}>Mwanaume (Male)</option>
                                    <option value="female" {{ old('patient_gender') == 'female' ? 'selected' : '' }}>Mwanamke (Female)</option>
                                    <option value="other" {{ old('patient_gender') == 'other' ? 'selected' : '' }}>Nyingine (Other)</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Namba ya Simu ya Mgonjwa</label>
                                <input type="text" name="patient_phone" class="form-control" placeholder="07XXXXXXXX" value="{{ old('patient_phone') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Prescriber Info Card -->
                <div class="card card-custom border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-hospital text-teal me-2"></i>Taarifa za Daktari / Hospitali</h5>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-semibold">Jina la Daktari (Prescriber) <span class="text-danger">*</span></label>
                                <input type="text" name="prescriber_name" class="form-control" placeholder="Mfano: Dr. J. Mwita" value="{{ old('prescriber_name') }}" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Hospitali / Kliniki</label>
                                <input type="text" name="prescriber_hospital" class="form-control" placeholder="Mfano: Muhimbili / Amana Hospital" value="{{ old('prescriber_hospital') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Simu ya Daktari</label>
                                <input type="text" name="prescriber_phone" class="form-control" placeholder="07XXXXXXXX" value="{{ old('prescriber_phone') }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Namba ya Usajili (Reg No.)</label>
                                <input type="text" name="prescriber_reg_no" class="form-control" placeholder="MCT-XXXX" value="{{ old('prescriber_reg_no') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Uchunguzi / Ugonjwa (Diagnosis)</label>
                                <input type="text" name="diagnosis_summary" class="form-control" placeholder="Mfano: Hypertension, RTI..." value="{{ old('diagnosis_summary') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Maelezo ya Ziada (Notes)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Ushauri mwingine au mzio (allergy)...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Medicines Prescribed -->
            <div class="col-lg-7">
                <div class="card card-custom border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-capsule-pill text-teal me-2"></i>Dawa Zilizoagizwa (Prescribed Medicines)</h5>
                        <button type="button" class="btn btn-sm btn-outline-teal" id="addRxItemBtn">
                            <i class="bi bi-plus-circle me-1"></i> Ongeza Dawa
                        </button>
                    </div>
                    <div class="card-body pt-0">
                        <div class="table-responsive">
                            <table class="table align-middle" id="rxItemsTable">
                                <thead class="bg-light text-muted small text-uppercase">
                                    <tr>
                                        <th style="width: 38%;">Dawa</th>
                                        <th style="width: 32%;">Maelekezo (Dosage)</th>
                                        <th style="width: 14%;">Idadi</th>
                                        <th style="width: 10%;">Siku</th>
                                        <th style="width: 6%;"></th>
                                    </tr>
                                </thead>
                                <tbody id="rxItemsBody">
                                    <tr class="rx-row">
                                        <td>
                                            <select name="items[0][product_id]" class="form-select select-product" required>
                                                <option value="">-- Chagua Dawa --</option>
                                                @foreach($products as $prod)
                                                    <option value="{{ $prod->id }}" {{ $prod->requires_prescription ? 'data-rx="true"' : '' }}>
                                                        {{ $prod->generic_name }} ({{ $prod->brand_name }})
                                                        @if($prod->is_restricted) ⚠️ [RESTRICTED] @endif
                                                    </option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="items[0][dosage_instructions]" class="form-control form-control-sm" placeholder="Mf: 1x3 kwa siku 5" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][prescribed_quantity]" class="form-control form-control-sm text-center" min="1" value="1" required>
                                        </td>
                                        <td>
                                            <input type="number" name="items[0][duration_days]" class="form-control form-control-sm text-center" min="1" placeholder="5">
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row disabled" title="Futa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info-subtle border border-info-subtle mt-3 small">
                            <i class="bi bi-info-circle-fill text-info me-2"></i>
                            <strong>Kumbuka:</strong> Kwa mujibu wa miongozo ya TMDA na Wizara ya Afya Tanzania, dawa zote za daraja la Rx na zile zilizowekewa vizuizi (Misoprostol, Oxytocin, Pregabalin, n.k.) lazima zithibitishwe na kuwekewa kumbukumbu kamili ya mgonjwa na daktari kabla ya kutolewa kwa mteja.
                        </div>

                        <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('prescriptions.index') }}" class="btn btn-secondary px-4">Ghairi</a>
                            <button type="submit" class="btn btn-teal px-5 fw-bold shadow-sm">
                                <i class="bi bi-save me-1"></i> Hifadhi Hati ya Daktari
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let rowIndex = 1;
        const addBtn = document.getElementById('addRxItemBtn');
        const tbody = document.getElementById('rxItemsBody');

        // Options template from the first row
        const firstSelect = tbody.querySelector('.select-product');
        const optionsHtml = firstSelect ? firstSelect.innerHTML : '';

        addBtn.addEventListener('click', function () {
            const tr = document.createElement('tr');
            tr.className = 'rx-row';
            tr.innerHTML = `
                <td>
                    <select name="items[${rowIndex}][product_id]" class="form-select select-product" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="text" name="items[${rowIndex}][dosage_instructions]" class="form-control form-control-sm" placeholder="Mf: 1x3 kwa siku 5" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][prescribed_quantity]" class="form-control form-control-sm text-center" min="1" value="1" required>
                </td>
                <td>
                    <input type="number" name="items[${rowIndex}][duration_days]" class="form-control form-control-sm text-center" min="1" placeholder="5">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-remove-row" title="Futa">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
            rowIndex++;
            updateRemoveButtons();
        });

        tbody.addEventListener('click', function (e) {
            if (e.target.closest('.btn-remove-row')) {
                const btn = e.target.closest('.btn-remove-row');
                if (tbody.querySelectorAll('.rx-row').length > 1) {
                    btn.closest('.rx-row').remove();
                    updateRemoveButtons();
                }
            }
        });

        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll('.rx-row');
            rows.forEach(r => {
                const btn = r.querySelector('.btn-remove-row');
                if (rows.length === 1) {
                    btn.classList.add('disabled');
                } else {
                    btn.classList.remove('disabled');
                }
            });
        }
    });
</script>
@endpush
@endsection
