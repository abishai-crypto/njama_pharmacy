<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Digital Receipt — {{ $sale->invoice_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f8fafc; font-family: system-ui, -apple-system, sans-serif; }
        .receipt-card { max-width: 680px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.06); border: 1px solid #e2e8f0; }
        @media print {
            body { background: #fff; }
            .receipt-card { box-shadow: none; border: none; margin: 0 auto; width: 100%; max-width: 100%; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="receipt-card p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4 no-print pb-3 border-bottom">
                <a href="{{ route('pos.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to POS</a>
                <button onclick="window.print()" class="btn btn-sm btn-primary"><i class="bi bi-printer me-1"></i> Print / Save as PDF</button>
            </div>

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3 class="fw-bold text-success mb-1">{{ $settings->pharmacy_name }}</h3>
                    <div class="text-muted small">{{ $settings->address ?? 'Dar es Salaam, Tanzania' }}</div>
                    <div class="text-muted small">Doctor: {{ $settings->owner_doctor_name }} ({{ $settings->owner_phone }})</div>
                    <div class="text-muted small">Tech: {{ $settings->technician_name }} ({{ $settings->technician_phone }})</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 border border-success-subtle">OFFICIAL DIGITAL RECEIPT</span>
                    <div class="mt-2 fw-bold">{{ $sale->invoice_number }}</div>
                    <div class="text-muted small">{{ $sale->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>

            <hr>

            <!-- Customer & Cashier Info -->
            <div class="row mb-4 small">
                <div class="col-6">
                    <span class="text-muted text-uppercase fw-bold">Customer:</span>
                    <div class="fw-bold">{{ $sale->customer ? $sale->customer->name : 'Walk-in Customer' }}</div>
                    @if($sale->customer && $sale->customer->phone)
                        <div>Phone: {{ $sale->customer->phone }}</div>
                    @endif
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted text-uppercase fw-bold">Served By:</span>
                    <div class="fw-bold">{{ $sale->cashier ? $sale->cashier->name : 'Staff' }}</div>
                    <div class="text-muted">Payment: {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</div>
                </div>
            </div>

            <!-- Items Table -->
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Quantity</th>
                        <th class="text-end">Unit Price (TZS)</th>
                        <th class="text-end">Total (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $item->product->generic_name }}</div>
                            <small class="text-muted">{{ $item->product->brand_name }}</small>
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">Tsh {{ number_format($item->unit_price) }}</td>
                        <td class="text-end fw-bold">Tsh {{ number_format($item->total) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end fw-bold">Subtotal:</td>
                        <td class="text-end fw-bold">Tsh {{ number_format($sale->subtotal) }}</td>
                    </tr>
                    @if($sale->discount_amount > 0)
                    <tr>
                        <td colspan="3" class="text-end text-danger">Discount:</td>
                        <td class="text-end text-danger">- Tsh {{ number_format($sale->discount_amount) }}</td>
                    </tr>
                    @endif
                    <tr class="table-success">
                        <td colspan="3" class="text-end fw-bold fs-5">TOTAL PAID:</td>
                        <td class="text-end fw-bold fs-5 text-success">Tsh {{ number_format($sale->total_amount) }}</td>
                    </tr>
                </tfoot>
            </table>

            <div class="mt-4 p-3 rounded bg-light text-center small text-muted">
                {{ $settings->receipt_footer }}
                <div class="mt-1">Verification UUID: <code>{{ $sale->offline_uuid }}</code></div>
            </div>
        </div>
    </div>
</body>
</html>
