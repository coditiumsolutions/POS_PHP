@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4">Audit Logs</h2>
    <div class="card">
        <div class="card-body">
            <form method="GET" class="row g-3 mb-4">
                <div class="col-md-2">
                    <select name="user_uid" class="form-select">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->uid }}" {{ request('user_uid') == $user->uid ? 'selected' : '' }}>{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="action_type" class="form-select">
                        <option value="">All Actions</option>
                        @foreach($actionTypes as $type)
                        <option value="{{ $type }}" {{ request('action_type') == $type ? 'selected' : '' }}>{{ $type }}</option>
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
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Search...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Filter</button>
                </div>
            </form>
            <table class="table table-sm table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Table</th>
                        <th>Description</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td><small>{{ \Carbon\Carbon::parse($log->action_date)->format('d M Y H:i') }}</small></td>
                        <td><small>{{ $log->user->full_name ?? 'System' }}</small></td>
                        <td><span class="badge bg-info">{{ $log->action_type }}</span></td>
                        <td><small>{{ $log->table_name }}</small></td>
                        <td><small>{{ $log->description }}</small></td>
                        <td><small>{{ $log->ip_address }}</small></td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">No audit logs found</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
