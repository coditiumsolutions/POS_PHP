#!/bin/bash
set -e
cd /workspace/resources/views

# Sales Returns Show
cat > sales-returns/show.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Sales Return Details</h2>
        <a href="{{ route('sales-returns.index') }}" class="btn btn-secondary">← Back</a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Return No:</strong> {{ $return->invoice_no }}</p>
                    <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($return->invoice_date)->format('d M Y') }}</p>
                    <p><strong>Customer:</strong> {{ $return->customer->customer_name ?? 'Walk-in' }}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>User:</strong> {{ $return->user_name }}</p>
                    <p><strong>Branch:</strong> {{ $return->branch }}</p>
                    <p><strong>Remarks:</strong> {{ $return->remarks }}</p>
                </div>
            </div>
            
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th class="text-end">Quantity</th>
                        <th class="text-end">Unit Price</th>
                        <th class="text-end">Discount</th>
                        <th class="text-end">Tax</th>
                        <th class="text-end">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->details as $detail)
                    <tr>
                        <td>{{ $detail->product->product_name }}</td>
                        <td class="text-end">{{ number_format($detail->quantity, 3) }}</td>
                        <td class="text-end">₨ {{ number_format($detail->unit_price, 2) }}</td>
                        <td class="text-end">₨ {{ number_format($detail->discount_amount, 2) }}</td>
                        <td class="text-end">₨ {{ number_format($detail->tax_amount, 2) }}</td>
                        <td class="text-end"><strong>₨ {{ number_format($detail->total_amount, 2) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="5" class="text-end">Sub Total:</th>
                        <th class="text-end">₨ {{ number_format($return->sub_total, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Discount:</th>
                        <th class="text-end">₨ {{ number_format($return->discount_amount, 2) }}</th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-end">Tax:</th>
                        <th class="text-end">₨ {{ number_format($return->tax_amount, 2) }}</th>
                    </tr>
                    <tr class="table-success">
                        <th colspan="5" class="text-end">Net Amount:</th>
                        <th class="text-end">₨ {{ number_format($return->net_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
VIEWEOF

# Customer Payments Index
cat > customer-payments/index.blade.php << 'VIEWEOF'
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Customer Payments</h2>
        <a href="{{ route('customer-payments.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> New Payment</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="customer_uid" class="form-select">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                        <option value="{{ $customer->uid }}" {{ request('customer_uid') == $customer->uid ? 'selected' : '' }}>{{ $customer->customer_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Payment No</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td><strong>{{ $payment->payment_no }}</strong></td>
                        <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('d M Y') }}</td>
                        <td>{{ $payment->customer->customer_name ?? 'N/A' }}</td>
                        <td class="text-end"><strong>₨ {{ number_format($payment->amount, 2) }}</strong></td>
                        <td><span class="badge bg-info">{{ $payment->payment_mode }}</span></td>
                        <td><small>{{ $payment->reference_no }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No payments found</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="3" class="text-end">Total:</th>
                        <th class="text-end">₨ {{ number_format($totalAmount ?? 0, 2) }}</th>
                        <th colspan="2"></th>
                    </tr>
                </tfoot>
            </table>
            {{ $payments->links() }}
        </div>
    </div>
</div>
@endsection
VIEWEOF

echo "✓ Created sales-returns and customer-payments views"

# Create simpler index/create/edit views for remaining modules
# This is a helper to speed up view generation

for module in "vendor-payments:Vendor Payments" "stock-adjustments:Stock Adjustments" "wastage:Wastage" "product-categories:Product Categories" "expense-categories:Expense Categories" "warehouses:Warehouses"; do
    IFS=':' read -r dir title <<< "$module"
    
    cat > "$dir/index.blade.php" << VIEWEOF
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>$title</h2>
        <a href="{{ route('${dir}.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <p class="text-muted">$title listing will display here</p>
        </div>
    </div>
</div>
@endsection
VIEWEOF

    cat > "$dir/create.blade.php" << VIEWEOF
@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Add $title</h2>
    <div class="card">
        <div class="card-body">
            <p class="text-muted">Form for creating $title</p>
            <a href="{{ route('${dir}.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
VIEWEOF

done

echo "✓ Created stub views for all modules"
echo "All views created successfully!"

