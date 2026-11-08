<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Balance</title>
    <style>
        :root { --ink: #0f172a; --muted: #64748b; --line: #cbd5e1; }
        * { box-sizing: border-box; }
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            margin: 0;
            background: #e2e8f0;
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
        .toolbar button.secondary { background: #fff; color: #0f172a; }
        .sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 12px auto;
            padding: 10mm 12mm 12mm;
            background: #fff;
            box-shadow: 0 1px 8px rgba(15, 23, 42, 0.12);
        }
        h1 { margin: 0; font-size: 1.25rem; }
        .sub { margin-top: 0.15rem; color: var(--muted); font-size: 0.8rem; }
        .header {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            border-bottom: 2px solid var(--ink);
            padding-bottom: 0.5rem;
            margin-bottom: 0.65rem;
        }
        .meta { text-align: right; font-size: 0.8rem; }
        table { width: 100%; border-collapse: collapse; font-size: 0.78rem; }
        th, td {
            border: 1px solid var(--line);
            padding: 0.2rem 0.4rem;
            vertical-align: middle;
            text-align: left;
            line-height: 1.25;
        }
        th { background: #f8fafc; text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.03em; }
        .text-end { text-align: right; }
        .col-branch { width: 28%; }
        .col-qty { width: 18%; }
        @@page { size: A4 portrait; margin: 10mm; }
        @@media print {
            body { background: #fff; }
            .toolbar { display: none !important; }
            .sheet { width: auto; min-height: auto; margin: 0; padding: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="toolbar">
        <button type="button" class="secondary" onclick="window.close()">Close</button>
        <button type="button" onclick="window.print()">Print</button>
    </div>

    <div class="sheet">
        <div class="header">
            <div>
                <h1>Inventory Balance</h1>
                <div class="sub">Product · Branch · Current Quantity</div>
            </div>
            <div class="meta">
                Printed: {{ now()->format('d-M-Y H:i') }}<br>
                Rows: {{ $inventories->count() }}
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width:28px">#</th>
                    <th>Product</th>
                    <th class="col-branch">Branch</th>
                    <th class="text-end col-qty">Current Qty</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inventories as $i => $inv)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $inv->product->product_name ?? ('#'.$inv->product_uid) }}</td>
                        <td>{{ $inv->branch_name }}</td>
                        <td class="text-end">{{ number_format((float) $inv->quantity_in_stock, 3) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No inventory records.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</body>
</html>
