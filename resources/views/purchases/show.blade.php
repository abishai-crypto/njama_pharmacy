@extends('layouts.app')

@section('title', 'Purchase Invoice #' . $purchase->invoice_number)
@section('page_title', 'Purchase Invoice Details')

@section('content')
<div class="row g-4">
    <!-- Main Column (8 cols) -->
    <div class="col-lg-8">
        <!-- Invoice Header Card -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <span class="badge bg-light text-dark border mb-2">Purchase Invoice</span>
                    <h3 class="fw-bold mb-1 text-dark">{{ $purchase->invoice_number }}</h3>
                    <div class="text-muted small">Tarehe ya Mzigo: {{ $purchase->purchase_date->format('d/m/Y') }}</div>
                </div>
                <div class="d-flex gap-2">
                    @if($purchase->balance_due > 0)
                        <button type="button" class="btn btn-sm btn-emerald" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="bi bi-cash-coin me-1"></i> Rekodi Malipo (Pay Debt)
                        </button>
                    @endif
                    <a href="{{ route('purchases.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Rudi
                    </a>
                </div>
            </div>

            <div class="row g-3 text-center border-top pt-3">
                <div class="col-sm-4 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Jumla ya Gharama</span>
                    <h4 class="fw-bold mb-0 text-dark">Tsh {{ number_format($purchase->total_amount) }}</h4>
                </div>
                <div class="col-sm-4 border-end">
                    <span class="text-muted small fw-bold text-uppercase">Iliyolipwa</span>
                    <h4 class="fw-bold mb-0 text-success">Tsh {{ number_format($purchase->paid_amount) }}</h4>
                </div>
                <div class="col-sm-4">
                    <span class="text-muted small fw-bold text-uppercase">Deni Lililobaki</span>
                    <h4 class="fw-bold mb-0 {{ $purchase->balance_due > 0 ? 'text-danger' : 'text-muted' }}">
                        Tsh {{ number_format($purchase->balance_due) }}
                    </h4>
                </div>
            </div>
        </div>

        <!-- Delivered Items Table -->
        <div class="card card-custom overflow-hidden mb-4">
            <div class="p-3 border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="bi bi-box-seam text-emerald me-2"></i>Dawa Zilizopokelewa kwenye Ankara Hii</h6>
                <span class="badge bg-light text-dark border">{{ $purchase->items->count() }} Items</span>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Dawa / Bidhaa</th>
                            <th>Batch #</th>
                            <th>Tarehe ya Kuisha</th>
                            <th class="text-center">Idadi</th>
                            <th class="text-end">Bei ya Kununua</th>
                            <th class="text-end">Jumla</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($purchase->items as $item)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $item->product ? $item->product->generic_name : 'N/A' }}</div>
                                <small class="text-muted">{{ $item->product ? $item->product->brand_name : '' }}</small>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ $item->batch_number }}</span></td>
                            <td>{{ $item->expiry_date ? $item->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                            <td class="text-center fw-bold">{{ $item->quantity }} {{ $item->product && $item->product->unit ? $item->product->unit->code : '' }}</td>
                            <td class="text-end">Tsh {{ number_format($item->unit_cost) }}</td>
                            <td class="text-end fw-bold text-dark">Tsh {{ number_format($item->subtotal) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Payment History Table -->
        <div class="card card-custom overflow-hidden">
            <div class="p-3 border-bottom bg-white">
                <h6 class="fw-bold mb-0"><i class="bi bi-clock-history text-emerald me-2"></i>Kumbukumbu za Malipo (Payment History)</h6>
            </div>
            <div class="table-responsive">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Voucher #</th>
                            <th>Tarehe</th>
                            <th>Njia ya Malipo</th>
                            <th>Rejea (Reference)</th>
                            <th class="text-end">Kiasi (TZS)</th>
                            <th>Mhasibu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($purchase->payments as $pay)
                        <tr>
                            <td class="fw-bold text-dark">{{ $pay->payment_voucher }}</td>
                            <td>{{ $pay->payment_date->format('d/m/Y') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pay->payment_method)) }}</td>
                            <td class="text-muted small">{{ $pay->reference_number ?? 'N/A' }}</td>
                            <td class="text-end fw-bold text-success">Tsh {{ number_format($pay->amount) }}</td>
                            <td class="small text-muted">{{ $pay->recorder ? $pay->recorder->name : 'Staff' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Hakuna malipo yaliyorekodiwa bado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Supplier Column (4 cols) -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Msambazaji (Supplier)</h6>
            @if($purchase->supplier)
                <h5 class="fw-bold text-dark mb-1">{{ $purchase->supplier->name }}</h5>
                <div class="text-muted small mb-3">Mawasiliano: {{ $purchase->supplier->phone ?? 'N/A' }}</div>

                <ul class="list-unstyled small mb-0">
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Mtu wa Mawasiliano:</span>
                        <span>{{ $purchase->supplier->contact_person ?? 'N/A' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Barua Pepe:</span>
                        <span>{{ $purchase->supplier->email ?? 'N/A' }}</span>
                    </li>
                    <li class="py-2 border-bottom d-flex justify-content-between">
                        <span class="text-muted">Jumla ya Deni Analodai:</span>
                        <strong class="text-danger">Tsh {{ number_format($purchase->supplier->outstanding_balance) }}</strong>
                    </li>
                    <li class="py-2 d-flex justify-content-between">
                        <span class="text-muted">Masharti ya Malipo:</span>
                        <span>{{ $purchase->supplier->payment_terms ?? 'Immediate' }}</span>
                    </li>
                </ul>
            @else
                <div class="text-muted">Msambazaji hajatambuliwa.</div>
            @endif
        </div>

        <div class="card card-custom p-4">
            <h6 class="fw-bold mb-3 border-bottom pb-2">Taarifa za Kupokea Mzigo</h6>
            <ul class="list-unstyled small mb-0">
                <li class="py-2 border-bottom d-flex justify-content-between">
                    <span class="text-muted">Afisa Mpokeaji:</span>
                    <strong>{{ $purchase->receiver ? $purchase->receiver->name : 'N/A' }}</strong>
                </li>
                <li class="py-2 border-bottom d-flex justify-content-between">
                    <span class="text-muted">Tarehe ya Kuingizwa:</span>
                    <span>{{ $purchase->created_at->format('d/m/Y H:i') }}</span>
                </li>
                <li class="py-2">
                    <span class="text-muted d-block mb-1">Maelezo (Notes):</span>
                    <div class="p-2 rounded bg-light text-muted">{{ $purchase->notes ?? 'Hakuna maelezo ya ziada.' }}</div>
                </li>
            </ul>
        </div>
    </div>
</div>

<!-- Record Payment Modal -->
@if($purchase->balance_due > 0)
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-emerald text-white" style="background: linear-gradient(135deg, #0d9488, #0f766e);">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-cash-stack me-2"></i>Rekodi Malipo ya Ankara</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('purchases.payment', $purchase->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="p-3 rounded mb-3" style="background-color: #fef2f2; border: 1px solid #fecaca;">
                        <div class="small text-muted">Deni Lililobaki Kwenye Ankara Hii:</div>
                        <h4 class="fw-bold mb-0 text-danger">Tsh {{ number_format($purchase->balance_due) }}</h4>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Kiasi Unacholipa Sasa (Tsh) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control form-control-lg fw-bold" max="{{ $purchase->balance_due }}" min="1" value="{{ $purchase->balance_due }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Tarehe ya Malipo <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Njia ya Malipo <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="bank_transfer">Bank Transfer (NMB / CRDB)</option>
                            <option value="cash">Pesa Taslimu (Cash)</option>
                            <option value="mobile_money">Pesa ya Mtandao (Mobile Money)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Namba ya Muamala / Reference</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="mfano Ref ya benki au M-Pesa code">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold">Maelezo (Notes)</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Maelezo ya malipo..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Ghairi</button>
                    <button type="submit" class="btn btn-emerald btn-sm fw-bold">Thibitisha Malipo</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
