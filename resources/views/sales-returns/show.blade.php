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
