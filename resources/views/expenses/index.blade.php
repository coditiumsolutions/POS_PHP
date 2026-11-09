@extends('layouts.app')
@section('title', 'Expenses')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Expenses</h1>
        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Record Expense
        </a>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-3">
                    <select name="category_uid" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->uid }}" {{ request('category_uid') == $category->uid ? 'selected' : '' }}>{{ $category->category_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-secondary">Filter</button>
                </div>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Expense No</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Mode</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                        <tr>
                            <td>{{ $expense->expense_no }}</td>
                            <td>{{ $expense->expense_date->format('Y-m-d') }}</td>
                            <td>{{ $expense->category->category_name }}</td>
                            <td>Rs. {{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->payment_mode }}</td>
                            <td class="text-nowrap">
                                <a href="{{ route('expenses.edit', $expense->uid) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('expenses.destroy', $expense->uid) }}" class="d-inline"
                                      onsubmit="return confirm('Delete expense {{ $expense->expense_no }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center">No expenses found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $expenses->links() }}
            <div class="mt-3">
                <strong>Total: Rs. {{ number_format($totalAmount ?? 0, 2) }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
