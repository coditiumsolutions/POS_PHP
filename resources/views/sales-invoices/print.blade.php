<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print {{ $invoice->invoice_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111; margin: 24px; }
        h1 { font-size: 18px; margin: 0 0 4px; }
        .muted { color: #666; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { border: 1px solid #ccc; padding: 6px 8px; }
        th { background: #f5f5f5; text-align: left; }
        .text-end { text-align: right; }
        .meta { width: 100%; margin-bottom: 12px; }
        .meta td { border: none; padding: 2px 8px 2px 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
@php
    $branchName = is_object($invoice->branch) ? ($invoice->branch->branch_name ?? '—') : ($invoice->branch ?: '—');
@endphp
    <div class="no-print" style="margin-bottom: 12px;">
        <button onclick="window.print()">Print</button>
        <button onclick="window.close()">Close</button>
    </div>

    <h1>Sales Invoice</h1>
    <div class="muted">{{ $invoice->invoice_no }}</div>

    <table class="meta">
        <tr>
            <td><strong>Date:</strong> {{ optional($invoice->invoice_date)->format('d-M-Y H:i') }}</td>
            <td><strong>Branch:</strong> {{ $branchName }}</td>
        </tr>
        <tr>
            <td><strong>Customer:</strong> {{ optional($invoice->customer)->customer_name ?: 'Walk-in' }}</td>
            <td><strong>Payment:</strong> {{ optional($invoice->paymentMethod)->payment_method_name ?: '—' }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-end">Qty</th>
                <th class="text-end">Price</th>
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
        <tfoot>
            <tr>
                <td colspan="4" class="text-end"><strong>Net Amount</strong></td>
                <td class="text-end"><strong>Rs. {{ number_format((float) $invoice->net_amount, 2) }}</strong></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
