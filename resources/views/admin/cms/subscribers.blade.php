@extends('layouts.admin')

@section('title', 'Newsletter Subscribers | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Newsletter Subscribers ({{ $subscribers->total() }})</h4>
            <div class="page-title-right">
                <a href="{{ route('admin.cms.subscribers.export') }}" class="btn btn-primary btn-sm">Export Active CSV</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" action="{{ route('admin.cms.subscribers') }}" class="d-flex gap-2" style="max-width:360px;">
            <input type="text" name="search" value="{{ $search }}" class="form-control form-control-sm" placeholder="Search email...">
            <button type="submit" class="btn btn-sm btn-soft-secondary">Search</button>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table mb-0 align-middle">
            <thead><tr><th>Email</th><th>Status</th><th>Subscribed At</th><th class="text-end">Action</th></tr></thead>
            <tbody>
                @forelse($subscribers as $sub)
                    <tr>
                        <td>{{ $sub->email }}</td>
                        <td><span class="badge {{ $sub->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $sub->is_active ? 'Active' : 'Unsubscribed' }}</span></td>
                        <td class="fs-12 text-muted">{{ $sub->created_at?->format('M d, Y H:i') }}</td>
                        <td class="text-end">
                            <form method="POST" action="{{ route('admin.cms.subscribers.toggle', $sub) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-soft-warning">{{ $sub->is_active ? 'Unsubscribe' : 'Reactivate' }}</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No newsletter subscribers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($subscribers->hasPages())<div class="card-footer">{{ $subscribers->links() }}</div>@endif
</div>
@endsection

