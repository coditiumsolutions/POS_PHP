@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2>Monthly Supply Overrides</h2>
    <div class="card"><div class="card-body">
        <p class="text-muted">Date-specific quantity overrides</p>
        <a href="{{ route('monthly-supply.index') }}" class="btn btn-secondary">← Back to Templates</a>
    </div></div>
</div>
@endsection