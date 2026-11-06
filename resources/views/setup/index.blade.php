@extends('layouts.app')
@section('title', 'Setup')
@section('content')
<div class="modules-page-head">
    <h1 class="page-heading">Setup</h1>
</div>
<div class="row g-3">
    @foreach($modules as $module)
        <div class="col-6 col-md-4 col-lg-3">
            <a class="text-decoration-none" href="{{ route($module['route']) }}">
                <div class="metric-card h-100">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ $module['icon'] }}" style="color:#ea580c;font-size:1.25rem;"></i>
                        <span class="fw-semibold text-dark">{{ $module['name'] }}</span>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
</div>
@endsection
