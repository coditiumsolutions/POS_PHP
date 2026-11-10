<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        :root {
            --ink: #0f172a;
            --muted: #64748b;
            --line: #cbd5e1;
            --soft: #f8fafc;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            margin: 0;
            background: #e2e8f0;
        }

        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 12px auto;
            padding: 14mm 14mm 16mm;
            background: #fff;
            box-shadow: 0 1px 8px rgba(15, 23, 42, 0.12);
        }

        .toolbar {
            width: 210mm;
            margin: 12px auto 0;
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .toolbar button {
            border: 1px solid #94a3b8;
            background: #0f172a;
            color: #fff;
            border-radius: 4px;
            padding: 0.4rem 0.9rem;
            font-size: 0.875rem;
            cursor: pointer;
        }

        .toolbar button.secondary {
            background: #fff;
            color: #0f172a;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            border-bottom: 2px solid var(--ink);
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }

        .header h1 {
            margin: 0;
            font-size: 1.45rem;
            letter-spacing: 0.02em;
        }

        .header .sub {
            margin-top: 0.2rem;
            color: var(--muted);
            font-size: 0.85rem;
        }

        .badge {
            border: 1px solid var(--line);
            background: var(--soft);
            padding: 0.35rem 0.55rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.55rem 1.25rem;
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        .label {
            color: var(--muted);
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            margin-bottom: 0.1rem;
        }

        .value { font-weight: 600; }
        .hint { color: var(--muted); font-size: 0.8rem; margin-top: 0.1rem; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.35rem;
        }

        th, td {
            border-bottom: 1px solid var(--line);
            padding: 0.4rem 0.3rem;
            font-size: 0.86rem;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: var(--soft);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            color: #334155;
        }

        .text-end { text-align: right; }

        .section-title {
            margin: 1.1rem 0 0.35rem;
            font-size: 0.95rem;
            font-weight: 700;
        }

        .totals {
            margin-top: 0.9rem;
            margin-left: auto;
            width: 62mm;
            font-size: 0.88rem;
        }

        .totals div {
            display: flex;
            justify-content: space-between;
            gap: 0.75rem;
            padding: 0.18rem 0;
        }

        .totals .net {
            font-weight: 700;
            font-size: 1rem;
            border-top: 1px solid var(--line);
            margin-top: 0.25rem;
            padding-top: 0.4rem;
        }

        .footer-note {
            margin-top: 1.25rem;
            color: var(--muted);
            font-size: 0.8rem;
            border-top: 1px dashed var(--line);
            padding-top: 0.5rem;
        }

        @@page {
            size: A4 portrait;
            margin: 10mm;
        }

        @@media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
@php
    $customerName = optional($invoice->customer)->customer_name ?: 'Walk-in Customer';
    $customerCode = optional($invoice->customer)->customer_code;
    $paymentName = optional($invoice->paymentMethod)->payment_method_name ?: 'Cash';
@endphp
    <div class="toolbar">
        <button type="button" class="secondary" onclick="window.close()">Close</button>
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <h1>LipSip Mart</h1>
                <div class="sub">0339-5470747</div>
            </div>
            <div class="badge">{{ $invoice->invoice_status ?: 'Completed' }} · {{ $invoice->payment_status ?: 'Paid' }}</div>
        </div>

        <div class="meta-grid">
            <div>
                <div class="label">Invoice No</div>
                <div class="value">{{ $invoice->invoice_no }}</div>
            </div>
            <div>
                <div class="label">Date</div>
                <div class="value">{{ optional($invoice->invoice_date)->format('d-M-Y H:i') }}</div>
            </div>
            <div>
                <div class="label">Customer</div>
                <div class="value">{{ $customerName }}</div>
                @if($customerCode)
                    <div class="hint">{{ $customerCode }}</div>
                @endif
            </div>
            <div>
                <div class="label">Payment</div>
                <div class="value">{{ $paymentName }}</div>
                <div class="hint">Cashier: {{ $invoice->user_name ?: '—' }}</div>
            </div>
            <div>
                <div class="label">Paid / Balance</div>
                <div class="value">{{ number_format((float) $invoice->paid_amount, 2) }} / {{ number_format((float) $invoice->balance_amount, 2) }}</div>
            </div>
            @if($invoice->customer_given_amount !== null)
            <div>
                <div class="label">Given / Change</div>
                <div class="value">{{ number_format((float) $invoice->customer_given_amount, 2) }} / {{ number_format((float) ($invoice->change_given_amount ?? 0), 2) }}</div>
            </div>
            @endif
        </div>

        <div class="section-title">Items</div>
        <table>
            <thead>
                <tr>
                    <th style="width:28px">#</th>
                    <th>Product</th>
                    <th class="text-end">Qty</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Discount</th>
                    <th class="text-end">Tax</th>
                    <th class="text-end">Line Total</th>
                </tr>
            </thead>
            <tbody>
            @forelse($invoice->details as $i => $line)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>
                        <div>{{ optional($line->product)->product_name ?: 'Item #'.$line->product_uid }}</div>
                        <div class="hint">{{ optional($line->product)->product_code }}</div>
                    </td>
                    <td class="text-end">{{ number_format((float) $line->quantity, 3) }}</td>
                    <td class="text-end">{{ number_format((float) $line->unit_price, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $line->discount_amount, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $line->tax_amount, 2) }}</td>
                    <td class="text-end">{{ number_format((float) $line->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="7">No line items.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="totals">
            <div><span>Sub Total</span><span>{{ number_format((float) $invoice->sub_total, 2) }}</span></div>
            <div><span>Discount</span><span>{{ number_format((float) $invoice->discount_amount, 2) }}</span></div>
            <div><span>Tax</span><span>{{ number_format((float) $invoice->tax_amount, 2) }}</span></div>
            <div><span>Other / Delivery</span><span>{{ number_format((float) ($invoice->other_charges ?? 0), 2) }}</span></div>
            <div class="net"><span>Net Amount</span><span>{{ number_format((float) $invoice->net_amount, 2) }}</span></div>
            <div><span>Paid</span><span>{{ number_format((float) $invoice->paid_amount, 2) }}</span></div>
            <div><span>Balance</span><span>{{ number_format((float) $invoice->balance_amount, 2) }}</span></div>
        </div>

        @if(!empty($invoice->remarks))
            <div class="footer-note">Remarks: {{ $invoice->remarks }}</div>
        @endif
    </div>
</body>
</html>
