@extends('layouts.app')

@section('title', 'POS Terminal')
@section('page_title', 'Point of Sale (POS) — NJAMA PHARMACY')

@section('styles')
<style>
    .pos-container {
        height: calc(100vh - 150px);
        min-height: 600px;
    }
    .search-results-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        z-index: 1050;
        max-height: 380px;
        overflow-y: auto;
    }
    .search-result-item {
        padding: 10px 14px;
        border-bottom: 1px solid #f1f5f9;
        cursor: pointer;
        transition: background-color 0.15s ease;
    }
    .search-result-item:hover, .search-result-item.selected {
        background-color: #f0fdfa;
    }
    .cart-table-wrapper {
        height: calc(100% - 130px);
        overflow-y: auto;
    }
    .quick-cash-btn {
        font-size: 0.8rem;
        font-weight: 600;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="row g-3 pos-container">
    <!-- Left Column: Search & Cart (8 cols) -->
    <div class="col-lg-8 d-flex flex-column h-100">
        <!-- Search & Barcode Input Card -->
        <div class="card card-custom p-3 mb-3 position-relative">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold small text-muted"><i class="bi bi-search text-emerald me-1"></i>Product Search / Barcode</span>
                    <span class="badge bg-light text-dark border"><span class="kbd-key">F2</span> Focus</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span id="connectionBadge" class="badge bg-success-subtle text-success border border-success-subtle">
                        <i class="bi bi-wifi me-1"></i>Online
                    </span>
                    <button id="syncOfflineBtn" class="btn btn-sm btn-outline-warning d-none" onclick="syncOfflineQueue()">
                        <i class="bi bi-arrow-repeat me-1"></i>Sync (<span id="offlineCount">0</span>)
                    </button>
                </div>
            </div>

            <div class="input-group input-group-lg">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-upc-scan fs-4 text-emerald"></i></span>
                <input type="text" id="posSearchInput" class="form-control border-start-0 ps-0" placeholder="Type generic name, brand, or scan barcode..." autocomplete="off">
                <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()"><i class="bi bi-x-lg"></i></button>
            </div>

            <!-- Live Search Results Dropdown -->
            <div id="searchResults" class="search-results-dropdown d-none"></div>
        </div>

        <!-- Cart Table Card -->
        <div class="card card-custom p-3 flex-grow-1 d-flex flex-column overflow-hidden">
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <h6 class="fw-bold mb-0"><i class="bi bi-cart3 text-emerald me-1"></i>Selected Items (<span id="cartItemCount">0</span>)</h6>
                <button class="btn btn-sm btn-outline-danger py-0 px-2" onclick="clearCart()">
                    <i class="bi bi-trash3 me-1"></i>Clear Cart
                </button>
            </div>

            <div class="cart-table-wrapper">
                <table class="table table-custom align-middle">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th class="text-center" style="width: 130px;">Quantity</th>
                            <th class="text-end" style="width: 120px;">Price (TZS)</th>
                            <th class="text-end" style="width: 130px;">Total (TZS)</th>
                            <th class="text-center" style="width: 50px;"></th>
                        </tr>
                    </thead>
                    <tbody id="cartTableBody">
                        <tr id="emptyCartRow">
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="bi bi-cart-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
                                Kikapu kipo wazi. Tafuta dawa au scan barcode hapo juu (F2).
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Cart Summary Footer -->
            <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Items: <strong id="summaryTotalItems">0</strong> | FEFO Auto Batch Allocation Enabled
                </div>
                <div class="text-end">
                    <span class="text-muted small me-2">Subtotal:</span>
                    <span class="h5 fw-bold mb-0 text-dark">Tsh <span id="cartSubtotalText">0</span></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Checkout & Payment (4 cols) -->
    <div class="col-lg-4 d-flex flex-column h-100">
        <div class="card card-custom p-3 flex-grow-1 d-flex flex-column">
            <!-- Shift & Customer Header -->
            <div class="mb-3 pb-2 border-bottom">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-muted text-uppercase">Customer Info (Optional)</span>
                    <button class="btn btn-xs btn-outline-primary py-0" type="button" data-bs-toggle="collapse" data-bs-target="#customerFields">
                        <i class="bi bi-person-plus"></i> Patient Details
                    </button>
                </div>
                <div class="collapse" id="customerFields">
                    <div class="mb-2">
                        <input type="text" id="customerNameInput" class="form-control form-control-sm" placeholder="Patient/Customer Name">
                    </div>
                    <div class="mb-2">
                        <input type="text" id="customerPhoneInput" class="form-control form-control-sm" placeholder="Phone Number (e.g. 0715...)">
                    </div>
                </div>
            </div>

            <!-- Payable Display -->
            <div class="p-3 rounded-3 mb-3 text-center" style="background: linear-gradient(135deg, #092c28, #0f766e); color: white;">
                <div class="small text-uppercase text-white-50 fw-bold letter-spacing-1">Total Amount Payable</div>
                <h2 class="display-6 fw-bold my-1 text-white">Tsh <span id="grandTotalDisplay">0</span></h2>
                <small class="text-light opacity-75">Tanzanian Shillings</small>
            </div>

            <!-- Payment Method Selection -->
            <div class="mb-3">
                <label class="form-label small fw-bold text-muted text-uppercase">Payment Method</label>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="payCash" value="cash" checked onchange="updatePayMethod()">
                        <label class="btn btn-outline-secondary w-100 text-start py-2" for="payCash">
                            <i class="bi bi-cash me-1 text-success"></i> Cash
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="payMpesa" value="mobile_money" onchange="updatePayMethod()">
                        <label class="btn btn-outline-secondary w-100 text-start py-2" for="payMpesa">
                            <i class="bi bi-phone me-1 text-danger"></i> Mobile Money
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="payBank" value="bank_transfer" onchange="updatePayMethod()">
                        <label class="btn btn-outline-secondary w-100 text-start py-2" for="payBank">
                            <i class="bi bi-bank me-1 text-primary"></i> Bank (NMB/CRDB)
                        </label>
                    </div>
                    <div class="col-6">
                        <input type="radio" class="btn-check" name="payMethod" id="paySplit" value="split" onchange="updatePayMethod()">
                        <label class="btn btn-outline-secondary w-100 text-start py-2" for="paySplit">
                            <i class="bi bi-pie-chart me-1 text-warning"></i> Split Payment
                        </label>
                    </div>
                </div>
            </div>

            <!-- Cash Tendered & Change Calculation -->
            <div id="cashTenderedSection" class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small fw-bold text-muted text-uppercase mb-0">Amount Received (Tsh)</label>
                    <button type="button" class="btn btn-xs btn-link p-0 text-decoration-none" onclick="setExactAmount()">Exact</button>
                </div>
                <input type="number" id="amountPaidInput" class="form-control form-control-lg fw-bold text-end" placeholder="0" oninput="calculateChange()">

                <!-- Quick Presets -->
                <div class="d-flex flex-wrap gap-1 mt-2">
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(1000)">+1,000</button>
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(2000)">+2,000</button>
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(5000)">+5,000</button>
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(10000)">+10,000</button>
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(20000)">+20,000</button>
                    <button type="button" class="btn btn-sm btn-light border quick-cash-btn flex-fill" onclick="addCash(50000)">+50,000</button>
                </div>

                <!-- Change Due Banner -->
                <div class="p-2 mt-3 rounded-2 text-center" style="background-color: #ecfdf5; border: 1px solid #a7f3d0;">
                    <span class="small text-muted">Change Due:</span>
                    <h5 class="fw-bold mb-0 text-success">Tsh <span id="changeDueDisplay">0</span></h5>
                </div>
            </div>

            <!-- Checkout Action Button -->
            <div class="mt-auto pt-2">
                <button type="button" id="completeSaleBtn" class="btn btn-emerald w-100 py-3 fw-bold fs-5 shadow" onclick="submitSale()">
                    <i class="bi bi-check-circle-fill me-2"></i> Complete Sale <span class="badge bg-light text-dark ms-2 fs-6">F9</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Receipt Modal -->
<div class="modal fade" id="receiptModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-emerald text-white" style="background: linear-gradient(135deg, #0d9488, #0f766e);">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-check2-circle me-2"></i>Sale Completed!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center p-4">
                <i class="bi bi-receipt text-success display-3 mb-3"></i>
                <h5 class="fw-bold mb-1">Invoice: <span id="modalInvoiceNumber" class="text-emerald"></span></h5>
                <p class="text-muted">Total: <strong>Tsh <span id="modalTotalPaid"></span></strong></p>

                <div class="d-grid gap-2 mt-4">
                    <a id="modalPrintBtn" href="#" target="_blank" class="btn btn-emerald py-2 fw-bold">
                        <i class="bi bi-printer-fill me-2"></i>Print Thermal Receipt (58/80mm)
                    </a>
                    <a id="modalDigitalBtn" href="#" target="_blank" class="btn btn-outline-secondary py-2">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>View Digital PDF Receipt
                    </a>
                    <button type="button" class="btn btn-light border py-2 mt-2" data-bs-dismiss="modal">
                        Start Next Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];
    let searchTimeout = null;

    const searchInput = document.getElementById('posSearchInput');
    const searchResults = document.getElementById('searchResults');
    const cartTableBody = document.getElementById('cartTableBody');
    const emptyCartRow = document.getElementById('emptyCartRow');
    const cartItemCount = document.getElementById('cartItemCount');
    const cartSubtotalText = document.getElementById('cartSubtotalText');
    const grandTotalDisplay = document.getElementById('grandTotalDisplay');
    const amountPaidInput = document.getElementById('amountPaidInput');
    const changeDueDisplay = document.getElementById('changeDueDisplay');
    const completeSaleBtn = document.getElementById('completeSaleBtn');

    // Focus search on F2, Checkout on F9
    window.addEventListener('keydown', function(e) {
        if (e.key === 'F2') {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        } else if (e.key === 'F9') {
            e.preventDefault();
            submitSale();
        } else if (e.key === 'Escape') {
            clearSearch();
        }
    });

    // Live search event
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length === 0) {
            clearSearch();
            return;
        }

        searchTimeout = setTimeout(() => {
            fetch(`/pos/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(data => {
                    renderSearchResults(data);
                });
        }, 200);
    });

    function renderSearchResults(items) {
        if (!items || items.length === 0) {
            searchResults.innerHTML = '<div class="p-3 text-center text-muted">Hakuna dawa iliyopatikana kwa utafutaji huo.</div>';
            searchResults.classList.remove('d-none');
            return;
        }

        let html = '';
        items.forEach((p, idx) => {
            const stockColor = p.stock <= 0 ? 'text-danger fw-bold' : (p.stock <= 5 ? 'text-warning fw-bold' : 'text-success fw-bold');
            const rxBadge = p.requires_prescription ? '<span class="badge bg-danger-subtle text-danger ms-1">Rx Only</span>' : '';
            const restrictedBadge = p.is_restricted ? '<span class="badge bg-dark text-white ms-1">Restricted</span>' : '';

            html += `
                <div class="search-result-item d-flex justify-content-between align-items-center" onclick="addToCart(${JSON.stringify(p).replace(/"/g, '&quot;')})">
                    <div>
                        <div class="fw-bold text-dark">${p.name} ${rxBadge} ${restrictedBadge}</div>
                        <small class="text-muted">Batch: <strong>${p.earliest_batch_no}</strong> | Expiry: <strong>${p.earliest_expiry}</strong></small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-emerald">Tsh ${new Intl.NumberFormat().format(p.selling_price)}</div>
                        <small class="${stockColor}">Stoo: ${p.stock} ${p.unit}</small>
                    </div>
                </div>
            `;
        });

        searchResults.innerHTML = html;
        searchResults.classList.remove('d-none');
    }

    function clearSearch() {
        searchInput.value = '';
        searchResults.classList.add('d-none');
        searchResults.innerHTML = '';
    }

    function addToCart(product) {
        if (product.stock <= 0) {
            alert(`Dawa hii (${product.name}) imeisha stoo kabisa!`);
            return;
        }

        const existing = cart.find(item => item.id === product.id);
        if (existing) {
            if (existing.quantity + 1 > product.stock) {
                alert(`Huwezi kuongeza zaidi ya stoo iliyopo (${product.stock} ${product.unit}).`);
                return;
            }
            existing.quantity += 1;
        } else {
            cart.push({
                id: product.id,
                name: product.name,
                unit: product.unit,
                unit_price: product.selling_price,
                stock: product.stock,
                quantity: 1,
                discount: 0,
                requires_prescription: product.requires_prescription,
                is_restricted: product.is_restricted,
            });
        }

        clearSearch();
        renderCart();
        searchInput.focus();
    }

    function renderCart() {
        if (cart.length === 0) {
            emptyCartRow.classList.remove('d-none');
            cartTableBody.innerHTML = '';
            cartTableBody.appendChild(emptyCartRow);
            cartItemCount.innerText = '0';
            document.getElementById('summaryTotalItems').innerText = '0';
            cartSubtotalText.innerText = '0';
            grandTotalDisplay.innerText = '0';
            calculateChange();
            return;
        }

        emptyCartRow.classList.add('d-none');
        cartTableBody.innerHTML = '';

        let subtotal = 0;
        let totalItems = 0;

        cart.forEach((item, index) => {
            const itemTotal = (item.quantity * item.unit_price) - item.discount;
            subtotal += itemTotal;
            totalItems += item.quantity;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="fw-bold text-dark">${item.name}</div>
                    <small class="text-muted">Unit: ${item.unit} | Max: ${item.stock}</small>
                </td>
                <td class="text-center">
                    <div class="input-group input-group-sm justify-content-center">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, -1)">-</button>
                        <input type="number" class="form-control text-center" style="max-width: 60px;" value="${item.quantity}" min="1" max="${item.stock}" onchange="setQty(${index}, this.value)">
                        <button class="btn btn-outline-secondary" type="button" onclick="updateQty(${index}, 1)">+</button>
                    </div>
                </td>
                <td class="text-end fw-semibold">Tsh ${new Intl.NumberFormat().format(item.unit_price)}</td>
                <td class="text-end fw-bold text-dark">Tsh ${new Intl.NumberFormat().format(itemTotal)}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-link text-danger p-0" onclick="removeFromCart(${index})">
                        <i class="bi bi-x-circle-fill fs-5"></i>
                    </button>
                </td>
            `;
            cartTableBody.appendChild(tr);
        });

        cartItemCount.innerText = cart.length;
        document.getElementById('summaryTotalItems').innerText = totalItems;
        cartSubtotalText.innerText = new Intl.NumberFormat().format(subtotal);
        grandTotalDisplay.innerText = new Intl.NumberFormat().format(subtotal);

        // Auto-set cash if empty
        if (!amountPaidInput.value || parseFloat(amountPaidInput.value) === 0) {
            amountPaidInput.value = subtotal;
        }

        calculateChange();
    }

    function updateQty(index, delta) {
        const item = cart[index];
        const newQty = item.quantity + delta;
        if (newQty <= 0) {
            removeFromCart(index);
            return;
        }
        if (newQty > item.stock) {
            alert(`Stoo iliyopo ni ${item.stock} pekee.`);
            return;
        }
        item.quantity = newQty;
        renderCart();
    }

    function setQty(index, val) {
        const item = cart[index];
        let qty = parseFloat(val);
        if (isNaN(qty) || qty <= 0) qty = 1;
        if (qty > item.stock) {
            alert(`Stoo iliyopo ni ${item.stock} pekee.`);
            qty = item.stock;
        }
        item.quantity = qty;
        renderCart();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        renderCart();
    }

    function clearCart() {
        if (cart.length > 0 && confirm('Unataka kufuta dawa zote kwenye kikapu?')) {
            cart = [];
            renderCart();
        }
    }

    function calculateChange() {
        const subtotal = getSubtotal();
        const paid = parseFloat(amountPaidInput.value) || 0;
        const change = Math.max(0, paid - subtotal);
        changeDueDisplay.innerText = new Intl.NumberFormat().format(change);
    }

    function getSubtotal() {
        return cart.reduce((sum, item) => sum + ((item.quantity * item.unit_price) - item.discount), 0);
    }

    function setExactAmount() {
        amountPaidInput.value = getSubtotal();
        calculateChange();
    }

    function addCash(amount) {
        const current = parseFloat(amountPaidInput.value) || 0;
        amountPaidInput.value = current + amount;
        calculateChange();
    }

    function updatePayMethod() {
        const method = document.querySelector('input[name="payMethod"]:checked').value;
        const cashSection = document.getElementById('cashTenderedSection');
        if (method === 'cash') {
            cashSection.classList.remove('d-none');
        } else {
            // For digital/bank payments, amount paid defaults to exact
            amountPaidInput.value = getSubtotal();
            calculateChange();
        }
    }

    // Submit Sale to backend
    function submitSale() {
        if (cart.length === 0) {
            alert('Kikapu hakina dawa yoyote. Tafuta dawa kwanza.');
            return;
        }

        const subtotal = getSubtotal();
        const amountPaid = parseFloat(amountPaidInput.value) || 0;
        const method = document.querySelector('input[name="payMethod"]:checked').value;

        if (amountPaid < subtotal && method === 'cash') {
            alert(`Pesa iliyolipwa (Tsh ${amountPaid}) haitoshi jumla ya bili (Tsh ${subtotal}).`);
            return;
        }

        const payload = {
            items: cart.map(item => ({
                product_id: item.id,
                quantity: item.quantity,
                unit_price: item.unit_price,
                discount: item.discount,
            })),
            payment_method: method,
            amount_paid: amountPaid,
            customer_name: document.getElementById('customerNameInput').value.trim(),
            customer_phone: document.getElementById('customerPhoneInput').value.trim(),
            offline_uuid: 'pos-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9),
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        };

        completeSaleBtn.disabled = true;
        completeSaleBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Processing Sale...';

        // Online or Offline handling
        if (!navigator.onLine) {
            queueOfflineSale(payload);
            return;
        }

        fetch('/pos/checkout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            completeSaleBtn.disabled = false;
            completeSaleBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Complete Sale <span class="badge bg-light text-dark ms-2 fs-6">F9</span>';

            if (data.success) {
                // Open Receipt Modal
                document.getElementById('modalInvoiceNumber').innerText = data.invoice_number;
                document.getElementById('modalTotalPaid').innerText = new Intl.NumberFormat().format(data.total_amount);
                document.getElementById('modalPrintBtn').href = data.receipt_url;
                document.getElementById('modalDigitalBtn').href = data.digital_receipt_url;

                const modal = new bootstrap.Modal(document.getElementById('receiptModal'));
                modal.show();

                // Clear POS for next sale
                cart = [];
                renderCart();
                document.getElementById('customerNameInput').value = '';
                document.getElementById('customerPhoneInput').value = '';
            } else {
                alert('Hitilafu: ' + data.message);
            }
        })
        .catch(err => {
            completeSaleBtn.disabled = false;
            completeSaleBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Complete Sale <span class="badge bg-light text-dark ms-2 fs-6">F9</span>';
            // If network failed mid-flight, offer offline save
            if (confirm('Hitilafu ya mtandao imetokea. Je, ungependa kuweka mauzo haya kwenye foleni ya Offline ili yasawazishwe mtandao ukirudi?')) {
                queueOfflineSale(payload);
            }
        });
    }

    // Offline storage queue
    function queueOfflineSale(payload) {
        let queue = JSON.parse(localStorage.getItem('njama_offline_sales') || '[]');
        queue.push(payload);
        localStorage.setItem('njama_offline_sales', JSON.stringify(queue));

        alert('Mauzo yamehifadhiwa kwenye kumbukumbu ya kompyuta (Offline Queue). Mtandao ukirejea yatatumwa moja kwa moja.');
        cart = [];
        renderCart();
        updateOfflineUI();

        completeSaleBtn.disabled = false;
        completeSaleBtn.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i> Complete Sale <span class="badge bg-light text-dark ms-2 fs-6">F9</span>';
    }

    function updateOfflineUI() {
        const queue = JSON.parse(localStorage.getItem('njama_offline_sales') || '[]');
        const badge = document.getElementById('connectionBadge');
        const syncBtn = document.getElementById('syncOfflineBtn');
        const countSpan = document.getElementById('offlineCount');

        if (!navigator.onLine) {
            badge.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
            badge.innerHTML = '<i class="bi bi-wifi-off me-1"></i>Offline Mode';
        } else {
            badge.className = 'badge bg-success-subtle text-success border border-success-subtle';
            badge.innerHTML = '<i class="bi bi-wifi me-1"></i>Online';
        }

        if (queue.length > 0) {
            syncBtn.classList.remove('d-none');
            countSpan.innerText = queue.length;
        } else {
            syncBtn.classList.add('d-none');
        }
    }

    function syncOfflineQueue() {
        const queue = JSON.parse(localStorage.getItem('njama_offline_sales') || '[]');
        if (queue.length === 0) return;

        fetch('/pos/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ sales: queue })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                localStorage.removeItem('njama_offline_sales');
                alert(`Mauzo ${data.synced_count} ya Offline yamesawazishwa kwenye database kikamilifu.`);
                updateOfflineUI();
            }
        });
    }

    window.addEventListener('online', updateOfflineUI);
    window.addEventListener('offline', updateOfflineUI);
    updateOfflineUI();
</script>
@endsection
