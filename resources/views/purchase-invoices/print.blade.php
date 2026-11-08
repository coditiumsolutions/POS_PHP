<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase {{ $invoice->purchase_invoice_no }}</title>
    <style>
        body { font-family: "Segoe UI", Arial, sans-serif; margin: 24px; color: #0f172a; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; text-align: left; }
        th { background: #f8fafc; }
        .text-end { text-align: right; }
        .toolbar { margin-bottom: 12px; }
        @@media print { .toolbar { display: none; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>
    <h1>Purchase Invoice {{ $invoice->purchase_invoice_no }}</h1>
    <p>Vendor: {{ optional($invoice->vendor)->vendor_name ?: '—' }}</p>
    <p>Date: {{ optional($invoice->purchase_date)->format('d-M-Y') }}</p>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Unit Cost</th>
                <th class="text-end">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->details as $i => $line)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ optional($line->product)->product_name ?: ('#'.$line->product_uid) }}</td>
                <td class="text-end">{{ number_format((float) $line->quantity, 3) }}</td>
                <td class="text-end">{{ number_format((float) $line->unit_price, 2) }}</td>
                <td class="text-end">{{ number_format((float) $line->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p><strong>Net Amount:</strong> {{ number_format((float) $invoice->net_amount, 2) }}</p>
</body>
</html>
