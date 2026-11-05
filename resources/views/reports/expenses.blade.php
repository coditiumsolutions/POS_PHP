@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Expense Report</h2>
    <div class="card"><div class="card-body">
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3"><label>From Date</label><input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}"></div>
            <div class="col-md-3"><label>To Date</label><input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}"></div>
            <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-secondary"><i class="fas fa-search"></i> Filter</button></div>
        </form>
        <p class="text-muted">Expense report data will display here</p>
    </div></div>
</div>
@endsection