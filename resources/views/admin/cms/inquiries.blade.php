@extends('layouts.admin')

@section('title', 'Contact Inquiries | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Contact Inquiries ({{ $inquiries->total() }})</h4>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <ul class="nav nav-pills card-header-pills">
            @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read', 'replied' => 'Replied'] as $k => $lbl)
                <li class="nav-item">
                    <a class="nav-link {{ ($statusFilter ?: 'all') === $k ? 'active' : '' }}" href="{{ route('admin.cms.inquiries', ['status' => $k]) }}">{{ $lbl }}</a>
                </li>
            @endforeach
        </ul>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead><tr><th>Sender</th><th>Message</th><th>Status</th><th>Date</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse($inquiries as $inq)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $inq->name }}</div>
                                <small class="text-muted">{{ $inq->email }}</small>
                            </td>
                            <td style="max-width:380px;">
                                <div>{{ $inq->message }}</div>
                                @if($inq->admin_reply)
                                    <div class="mt-1 p-2 bg-light rounded fs-12"><strong>Reply:</strong> {{ $inq->admin_reply }}</div>
                                @endif
                                <form method="POST" action="{{ route('admin.cms.inquiries.reply', $inq) }}" class="mt-2 d-flex gap-1">
                                    @csrf
                                    <input type="text" name="admin_reply" class="form-control form-control-sm" placeholder="Send email reply..." required>
                                    <button type="submit" class="btn btn-sm btn-primary">Reply</button>
                                </form>
                            </td>
                            <td><span class="badge bg-info-subtle text-info text-uppercase">{{ $inq->status }}</span></td>
                            <td class="fs-12 text-muted">{{ $inq->created_at?->format('M d, Y H:i') }}</td>
                            <td class="text-end">
                                @if($inq->status === 'unread')
                                    <form method="POST" action="{{ route('admin.cms.inquiries.read', $inq) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-soft-success">Mark Read</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.cms.inquiries.destroy', $inq) }}" class="d-inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No contact inquiries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($inquiries->hasPages())<div class="card-footer">{{ $inquiries->links() }}</div>@endif
</div>
@endsection

