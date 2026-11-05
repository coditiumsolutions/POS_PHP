@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>System Configuration</h2>
        <a href="{{ route('configurations.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Setting</a>
    </div>
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('configurations.update') }}">
                @csrf
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th width="30%">Configuration Key</th>
                            <th width="50%">Value</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($configs as $index => $config)
                        <tr>
                            <td><strong>{{ $config->config_key }}</strong>
                                <input type="hidden" name="configs[{{ $index }}][key]" value="{{ $config->config_key }}">
                            </td>
                            <td><input type="text" name="configs[{{ $index }}][value]" class="form-control" value="{{ $config->config_value }}"></td>
                            <td><small class="text-muted">{{ $config->description }}</small></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No configuration settings found</td></tr>
                        @endforelse
                    </tbody>
                </table>
                @if($configs->count() > 0)
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save All Changes</button>
                @endif
            </form>
        </div>
    </div>
</div>
@endsection
