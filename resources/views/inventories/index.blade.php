@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Inventory Levels</h2>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Product</th><th>In Stock</th><th>Available</th></tr></thead>
            <tbody>
                @forelse($inventories as $inv)
                <tr>
                    <td>{{ $inv->product->product_name ?? 'N/A' }}</td>
                    <td class="text-end">{{ number_format($inv->quantity_in_stock, 3) }}</td>
                    <td class="text-end">{{ number_format($inv->quantity_available, 3) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-muted">No inventory records</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $inventories->links() }}
    </div></div>
</div>
@endsection