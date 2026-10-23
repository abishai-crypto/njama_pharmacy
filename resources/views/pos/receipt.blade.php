<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Receipt #{{ $sale->invoice_number }} — NJAMA PHARMACY</title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            width: 80mm;
            max-width: 80mm;
            margin: 0 auto;
            padding: 8px 6px;
            color: #000;
            font-size: 11px;
            line-height: 1.25;
            background: #fff;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 2px dashed #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table th, table td {
            padding: 2px 0;
            font-size: 10px;
        }
        .barcode {
            margin: 6px auto;
            letter-spacing: 4px;
            font-family: monospace;
            font-size: 13px;
            font-weight: bold;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px; text-align: center; padding: 8px; background: #f1f5f9; border-radius: 6px;">
        <button onclick="window.print()" style="padding: 6px 14px; font-weight: bold; cursor: pointer; background: #0d9488; color: white; border: none; border-radius: 4px;">
            🖨️ Chapa Risiti (Print Receipt)
        </button>
        <button onclick="window.close()" style="padding: 6px 12px; cursor: pointer; margin-left: 6px;">Funga (Close)</button>
    </div>

    <div class="text-center">
        <div class="fw-bold" style="font-size: 15px;">{{ $settings->pharmacy_name }}</div>
        <div>{{ $settings->address ?? 'Dar es Salaam, Tanzania' }}</div>
        <div>Daktari: {{ $settings->owner_doctor_name }} ({{ $settings->owner_phone }})</div>
        <div>Ufundi: {{ $settings->technician_name }} ({{ $settings->technician_phone }})</div>
        @if($settings->tin_number)
            <div>TIN: {{ $settings->tin_number }}</div>
        @endif
        @if($settings->registration_number)
            <div>Reg No: {{ $settings->registration_number }}</div>
        @endif
    </div>

    <div class="divider"></div>

    <div>
        <div>Risiti #: <strong>{{ $sale->invoice_number }}</strong></div>
        <div>Tarehe: {{ $sale->created_at->format('d/m/Y H:i:s') }}</div>
        <div>Kashia: {{ $sale->cashier ? $sale->cashier->name : 'Staff' }}</div>
        @if($sale->customer)
            <div>Mteja: {{ $sale->customer->name }} ({{ $sale->customer->phone ?? 'N/A' }})</div>
        @endif
    </div>

    <div class="divider"></div>

    <table>
        <thead>
            <tr>
                <th style="text-align: left;">Dawa / Bidhaa</th>
                <th style="text-align: center;">Idd</th>
                <th style="text-align: right;">Bei</th>
                <th style="text-align: right;">Jumla</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td colspan="4" class="fw-bold" style="padding-top: 4px;">
                    {{ $item->product->generic_name }}
                    @if($item->product->brand_name && $item->product->brand_name !== $item->product->generic_name)
                        ({{ $item->product->brand_name }})
                    @endif
                </td>
            </tr>
            <tr>
                <td style="color: #444; font-size: 9px;">
                    @foreach($item->batchAllocations as $alloc)
                        B:{{ $alloc->batch->batch_number }} 
                    @endforeach
                </td>
                <td style="text-align: center;">{{ $item->quantity }}</td>
                <td style="text-align: right;">{{ number_format($item->unit_price) }}</td>
                <td style="text-align: right;" class="fw-bold">{{ number_format($item->total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="double-divider"></div>

    <table>
        <tr>
            <td>Jumla Ndogo (Subtotal):</td>
            <td class="text-end fw-bold">Tsh {{ number_format($sale->subtotal) }}</td>
        </tr>
        @if($sale->discount_amount > 0)
        <tr>
            <td>Punguzo (Discount):</td>
            <td class="text-end text-danger">- Tsh {{ number_format($sale->discount_amount) }}</td>
        </tr>
        @endif
        <tr style="font-size: 13px;">
            <td class="fw-bold">JUMLA YA KULIPWA:</td>
            <td class="text-end fw-bold">Tsh {{ number_format($sale->total_amount) }}</td>
        </tr>
        <tr>
            <td>Njia ya Malipo:</td>
            <td class="text-end">{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}</td>
        </tr>
        <tr>
            <td>Kiasi Kilicholipwa:</td>
            <td class="text-end">Tsh {{ number_format($sale->amount_paid) }}</td>
        </tr>
        <tr>
            <td>Chenji / Baki:</td>
            <td class="text-end fw-bold">Tsh {{ number_format($sale->change_due) }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="text-center barcode">*{{ $sale->invoice_number }}*</div>

    <div class="text-center" style="font-size: 9px; margin-top: 6px;">
        {{ $settings->receipt_footer ?? 'Asante kwa kuja NJAMA PHARMACY. Afya Yako, Wajibu Wetu!' }}
        <div style="margin-top: 4px;">Mfumo umetengenezwa na: 0656392400 (Abishai)</div>
    </div>

    <script>
        window.addEventListener('load', function() {
            // Auto trigger print when loaded directly
            // window.print();
        });
    </script>
</body>
</html>
