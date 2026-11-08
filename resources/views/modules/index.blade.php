@extends('layouts.app')
@section('title', 'Modules')
@section('content')
<div class="modules-page-head">
    <h1 class="page-heading">Modules</h1>
</div>
@foreach($sections as $section)
    <div class="mb-4">
        <h2 class="h5 mb-1">{{ $section['title'] }}</h2>
        @if(!empty($section['subtitle']))
            <p class="text-muted small mb-3">{{ $section['subtitle'] }}</p>
        @endif
        <div class="row g-3">
            @foreach($section['modules'] as $module)
                <div class="col-6 col-md-4 col-lg-3">
                    @php
                        $moduleHref = route($module['route']);
                        $nestedKey = $module['controller'] ?? null;
                        if (in_array($nestedKey, ['Reports', 'Employees', 'Products', 'Vendors', 'Purchases', 'Inventories', 'Expenses', 'SalesInvoice'], true)) {
                            $moduleHref = route('nav.select', ['key' => $nestedKey, 'to' => $module['route']]);
                        }
                    @endphp
                    <a class="text-decoration-none" href="{{ $moduleHref }}">
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
    </div>
@endforeach
@endsection
