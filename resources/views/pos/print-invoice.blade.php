<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invoice {{ $invoice->invoice_no }}</title>
    <style>
        :root {
            /* Receipt roll width. Change to 58mm for a smaller printer. */
            --paper: 80mm;
            --pad: 3mm;
            --ink: #000;
            --muted: #333;
            --line: #999;
        }

        * { box-sizing: border-box; }

        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            margin: 0;
            background: #e2e8f0;
            font-size: 12px;
            line-height: 1.3;
        }

        .sheet {
            width: var(--paper);
            margin: 10px auto;
            padding: var(--pad);
            background: #fff;
            box-shadow: 0 1px 8px rgba(15, 23, 42, 0.12);
        }

        .toolbar {
            width: var(--paper);
            margin: 10px auto 0;
            display: flex;
            gap: 0.4rem;
            justify-content: center;
        }

        .toolbar button {
            border: 1px solid #94a3b8;
            background: #0f172a;
            color: #fff;
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
            cursor: pointer;
        }

        .toolbar button.secondary {
            background: #fff;
            color: #0f172a;
        }

        .header {
            text-align: center;
            border-bottom: 1px dashed var(--line);
            padding-bottom: 6px;
            margin-bottom: 6px;
        }

        .header h1 {
            margin: 0;
            font-size: 15px;
            letter-spacing: 0.02em;
        }

        .header .sub { color: var(--muted); font-size: 11px; }

        .badge {
            display: inline-block;
            margin-top: 3px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
        }

        .meta { font-size: 11px; margin-bottom: 6px; }

        .meta .row {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 1px 0;
        }

        .meta .label { color: var(--muted); }
        .meta .value { font-weight: 600; text-align: right; }

        .divider { border-top: 1px dashed var(--line); margin: 6px 0; }

        .items { width: 100%; }

        .item { padding: 3px 0; border-bottom: 1px dotted var(--line); }
        .item-name { font-weight: 600; font-size: 11px; }
        .item-code { color: var(--muted); font-size: 10px; }

        .item-line {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            font-size: 11px;
        }

        .item-adj { color: var(--muted); font-size: 10px; }

        .totals { margin-top: 6px; font-size: 11px; }

        .totals div {
            display: flex;
            justify-content: space-between;
            gap: 0.5rem;
            padding: 1px 0;
        }

        .totals .net {
            font-weight: 700;
            font-size: 13px;
            border-top: 1px solid var(--ink);
            border-bottom: 1px solid var(--ink);
            margin: 3px 0;
            padding: 3px 0;
        }

        .footer-note {
            margin-top: 8px;
            text-align: center;
            color: var(--muted);
            font-size: 10px;
            border-top: 1px dashed var(--line);
            padding-top: 6px;
        }

        @@page {
            size: var(--paper) auto;
            margin: 0;
        }

        @@media print {
            body { background: #fff; font-size: 12px; }
            .toolbar { display: none !important; }
            .sheet {
                width: auto;
                margin: 0;
                padding: 0 1mm;
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
            <h1>LipSip Mart</h1>
            <div class="sub">0339-5470747</div>
            <div class="badge">{{ $invoice->invoice_status ?: 'Completed' }} · {{ $invoice->payment_status ?: 'Paid' }}</div>
        </div>

        <div class="meta">
            <div class="row"><span class="label">Invoice</span><span class="value">{{ $invoice->invoice_no }}</span></div>
            <div class="row"><span class="label">Date</span><span class="value">{{ optional($invoice->invoice_date)->format('d-M-Y H:i') }}</span></div>
            <div class="row"><span class="label">Customer</span><span class="value">{{ $customerName }}@if($customerCode) ({{ $customerCode }})@endif</span></div>
            <div class="row"><span class="label">Payment</span><span class="value">{{ $paymentName }}</span></div>
            <div class="row"><span class="label">Cashier</span><span class="value">{{ $invoice->user_name ?: '—' }}</span></div>
        </div>

        <div class="divider"></div>

        <div class="items">
        @forelse($invoice->details as $i => $line)
            <div class="item">
                <div class="item-name">{{ $i + 1 }}. {{ optional($line->product)->product_name ?: 'Item #'.$line->product_uid }}</div>
                @if(optional($line->product)->product_code)
                    <div class="item-code">{{ $line->product->product_code }}</div>
                @endif
                <div class="item-line">
                    <span>{{ rtrim(rtrim(number_format((float) $line->quantity, 3), '0'), '.') }} x {{ number_format((float) $line->unit_price, 2) }}</span>
                    <span>{{ number_format((float) $line->total_amount, 2) }}</span>
                </div>
                @if((float) $line->discount_amount != 0 || (float) $line->tax_amount != 0)
                    <div class="item-line item-adj">
                        <span>Disc {{ number_format((float) $line->discount_amount, 2) }} · Tax {{ number_format((float) $line->tax_amount, 2) }}</span>
                        <span></span>
                    </div>
                @endif
            </div>
        @empty
            <div class="item">No line items.</div>
        @endforelse
        </div>

        <div class="totals">
            <div><span>Sub Total</span><span>{{ number_format((float) $invoice->sub_total, 2) }}</span></div>
            <div><span>Discount</span><span>{{ number_format((float) $invoice->discount_amount, 2) }}</span></div>
            <div><span>Tax</span><span>{{ number_format((float) $invoice->tax_amount, 2) }}</span></div>
            <div><span>Other / Delivery</span><span>{{ number_format((float) ($invoice->other_charges ?? 0), 2) }}</span></div>
            <div class="net"><span>Net Amount</span><span>{{ number_format((float) $invoice->net_amount, 2) }}</span></div>
            <div><span>Paid</span><span>{{ number_format((float) $invoice->paid_amount, 2) }}</span></div>
            <div><span>Balance</span><span>{{ number_format((float) $invoice->balance_amount, 2) }}</span></div>
            @if($invoice->customer_given_amount !== null)
            <div><span>Given</span><span>{{ number_format((float) $invoice->customer_given_amount, 2) }}</span></div>
            <div><span>Change</span><span>{{ number_format((float) ($invoice->change_given_amount ?? 0), 2) }}</span></div>
            @endif
        </div>

        @if(!empty($invoice->remarks))
            <div class="footer-note">Remarks: {{ $invoice->remarks }}</div>
        @else
            <div class="footer-note">Thank you for shopping!</div>
        @endif
    </div>
</body>
</html>
