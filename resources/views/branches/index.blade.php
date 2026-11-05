@extends('layouts.app')
@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between mb-4">
        <h2>Branches</h2>
        <a href="{{ route('branches.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add Branch</a>
    </div>
    <div class="card"><div class="card-body">
        <table class="table table-hover">
            <thead><tr><th>Code</th><th>Branch Name</th><th>City</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse($branches as $branch)
                <tr>
                    <td>{{ $branch->branch_code }}</td>
                    <td>{{ $branch->branch_name }}</td>
                    <td>{{ $branch->city }}</td>
                    <td><a href="{{ route('branches.edit', $branch->uid) }}" class="btn btn-sm btn-warning">Edit</a></td>
                </tr>
                @empty
                <tr><td colspan="4" class="text-center py-4 text-muted">No branches found</td></tr>
                @endforelse
            </tbody>
        </table>
        {{ $branches->links() }}
    </div></div>
</div>
@endsection