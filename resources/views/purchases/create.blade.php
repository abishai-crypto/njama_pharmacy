@extends('layouts.app')

@section('title', 'Receive New Stock')
@section('page_title', 'Receive Supplier Delivery (Goods Receipt)')

@section('content')
<div class="card card-custom p-4 mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-box-arrow-in-down text-emerald me-2"></i>Mapokezi ya Dawa kutoka kwa Msambazaji</h5>
            <small class="text-muted">Kuingiza ankara mpya kutaongeza stoo ya dawa na kutengeneza batch mpya kiotomatiki.</small>
        </div>
        <a href="{{ route('purchases.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Rudi kwenye Manunuzi
        </a>
    </div>
</div>

<form action="{{ route('purchases.store') }}" method="POST" id="purchaseForm">
    @csrf

    <!-- Invoice & Supplier Meta -->
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold mb-3 border-bottom pb-2">1. Taarifa za Ankara na Msambazaji</h6>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Msambazaji (Supplier) <span class="text-danger">*</span></label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">-- Chagua Msambazaji --</option>
                    @foreach($suppliers as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Namba ya Ankara (Supplier Invoice #) <span class="text-danger">*</span></label>
                <input type="text" name="invoice_number" class="form-control" placeholder="mfano INV-2026-001" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold small">Tarehe ya Kupokea <span class="text-danger">*</span></label>
                <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
        </div>
    </div>

    <!-- Delivered Items -->
    <div class="card card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h6 class="fw-bold mb-0">2. Dawa Zilizopokelewa (Delivered Items)</h6>
            <button type="button" class="btn btn-sm btn-outline-success" onclick="addRow()">
                <i class="bi bi-plus-circle me-1"></i> Ongeza Mstari wa Dawa
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-custom align-middle" id="itemsTable">
                <thead>
                    <tr>
                        <th style="width: 30%;">Dawa (Product)</th>
                        <th>Batch Namba</th>
                        <th>Tarehe ya Kuisha (Expiry)</th>
                        <th style="width: 110px;">Idadi (Qty)</th>
                        <th style="width: 140px;">Bei ya Kununua</th>
                        <th class="text-end" style="width: 140px;">Jumla (TZS)</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][product_id]" class="form-select form-select-sm product-select" required onchange="calcRow(this)">
                                <option value="">-- Chagua Dawa --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" data-cost="{{ $p->purchase_cost }}">{{ $p->generic_name }} ({{ $p->brand_name }})</option>
                                @endforeach
                            </select>
                        </td>
                        <td>
                            <input type="text" name="items[0][batch_number]" class="form-control form-control-sm" placeholder="Lot/Batch #" required>
                        </td>
                        <td>
                            <input type="date" name="items[0][expiry_date]" class="form-control form-control-sm">
                        </td>
                        <td>
                            <input type="number" name="items[0][quantity]" class="form-control form-control-sm text-center qty-input" value="1" min="1" step="0.5" required oninput="calcRow(this)">
                        </td>
                        <td>
                            <input type="number" name="items[0][unit_cost]" class="form-control form-control-sm text-end cost-input" value="0" min="0" step="50" required oninput="calcRow(this)">
                        </td>
                        <td class="text-end fw-bold text-dark row-subtotal">Tsh 0</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment & Submission -->
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold mb-3 border-bottom pb-2">3. Malipo ya Ankara</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Jumla ya Ankara (Total)</label>
                <div class="h4 fw-bold text-emerald mb-0" id="grandTotalText">Tsh 0</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Njia ya Malipo <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select" required>
                    <option value="bank_transfer">Bank Transfer (NMB / CRDB)</option>
                    <option value="cash">Pesa Taslimu (Cash)</option>
                    <option value="mobile_money">Pesa ya Mtandao (Mobile Money)</option>
                    <option value="credit">Deni / Mkopo (On Credit)</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Kiasi Kilicholipwa Sasa (Tsh) <span class="text-danger">*</span></label>
                <input type="number" name="paid_amount" id="paidAmountInput" class="form-control fw-bold" value="0" min="0" step="50" required oninput="calcBalance()">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Baki / Deni la Msambazaji</label>
                <div class="h4 fw-bold text-danger mb-0" id="balanceDueText">Tsh 0</div>
            </div>
            <div class="col-12 mt-3">
                <label class="form-label fw-semibold small">Maelezo ya Ziada (Notes)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Maelezo kuhusu delivery au risiti..."></textarea>
            </div>
        </div>

        <div class="text-end mt-4 pt-3 border-top">
            <button type="submit" class="btn btn-emerald px-4 py-2 fw-bold shadow-sm">
                <i class="bi bi-check2-circle me-1"></i> Pokea Mzigo & Ongeza Stoo (Save Delivery)
            </button>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
    let rowIndex = 1;
    const productsData = {!! json_encode($products->map(fn($p) => ['id' => $p->id, 'name' => $p->generic_name . ' (' . $p->brand_name . ')', 'cost' => (float)$p->purchase_cost])) !!};

    function addRow() {
        const tbody = document.getElementById('itemsBody');
        const tr = document.createElement('tr');
        tr.className = 'item-row';

        let optionsHtml = '<option value="">-- Chagua Dawa --</option>';
        productsData.forEach(p => {
            optionsHtml += `<option value="${p.id}" data-cost="${p.cost}">${p.name}</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="items[${rowIndex}][product_id]" class="form-select form-select-sm product-select" required onchange="calcRow(this)">
                    ${optionsHtml}
                </select>
            </td>
            <td>
                <input type="text" name="items[${rowIndex}][batch_number]" class="form-control form-control-sm" placeholder="Lot/Batch #" required>
            </td>
            <td>
                <input type="date" name="items[${rowIndex}][expiry_date]" class="form-control form-control-sm">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][quantity]" class="form-control form-control-sm text-center qty-input" value="1" min="1" step="0.5" required oninput="calcRow(this)">
            </td>
            <td>
                <input type="number" name="items[${rowIndex}][unit_cost]" class="form-control form-control-sm text-end cost-input" value="0" min="0" step="50" required oninput="calcRow(this)">
            </td>
            <td class="text-end fw-bold text-dark row-subtotal">Tsh 0</td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        rowIndex++;
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) {
            btn.closest('tr').remove();
            calcTotals();
        } else {
            alert('Lazima kuwe na dawa angalau moja.');
        }
    }

    function calcRow(el) {
        const row = el.closest('tr');
        const sel = row.querySelector('.product-select');
        const costInput = row.querySelector('.cost-input');

        if (el === sel && sel.selectedIndex > 0) {
            const cost = sel.options[sel.selectedIndex].getAttribute('data-cost');
            if (cost) costInput.value = cost;
        }

        const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
        const cost = parseFloat(costInput.value) || 0;
        const subtotal = qty * cost;

        row.querySelector('.row-subtotal').innerText = 'Tsh ' + new Intl.NumberFormat().format(subtotal);
        calcTotals();
    }

    function calcTotals() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            grand += (qty * cost);
        });

        document.getElementById('grandTotalText').innerText = 'Tsh ' + new Intl.NumberFormat().format(grand);

        // If paid amount wasn't manually edited, default to grand
        const paidInput = document.getElementById('paidAmountInput');
        if (parseFloat(paidInput.value) === 0 || paidInput.dataset.touched !== '1') {
            paidInput.value = grand;
        }

        calcBalance();
    }

    function calcBalance() {
        let grand = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
            grand += (qty * cost);
        });

        const paid = parseFloat(document.getElementById('paidAmountInput').value) || 0;
        const bal = Math.max(0, grand - paid);

        document.getElementById('balanceDueText').innerText = 'Tsh ' + new Intl.NumberFormat().format(bal);
    }

    document.getElementById('paidAmountInput').addEventListener('input', function() {
        this.dataset.touched = '1';
        calcBalance();
    });
</script>
@endsection
