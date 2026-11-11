@extends('layouts.admin')

@section('title', 'Customer Reviews')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Client Product Reviews & Moderation ({{ count($reviews) }})</h4>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>Client</th>
                                <th>Botanical Product</th>
                                <th>Rating</th>
                                <th>Review Headline & Comment</th>
                                <th>Submitted Date</th>
                                <th>Moderation Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reviews as $r)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ asset($r['avatar']) }}" alt="{{ $r['author'] }}" class="avatar-xs rounded-circle object-fit-cover border">
                                        <span class="fw-semibold text-dark fs-13">{{ $r['author'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-medium text-primary fs-13">{{ $r['product_name'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center text-warning fs-13">
                                        @for($i = 0; $i < $r['rating']; $i++)
                                            <i class="bx bxs-star"></i>
                                        @endfor
                                        @for($i = $r['rating']; $i < 5; $i++)
                                            <i class="bx bx-star text-muted"></i>
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    <h6 class="fs-13 text-dark fw-bold mb-1">{{ $r['headline'] }}</h6>
                                    <p class="text-muted mb-0 fs-12 text-wrap" style="max-width: 380px;">{{ $r['comment'] }}</p>
                                </td>
                                <td>
                                    <span class="fs-12 text-muted">{{ $r['date'] }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $r['status_badge'] }}-subtle text-{{ $r['status_badge'] }} px-2 py-1 fs-12">
                                        {{ $r['status'] }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.reviews.toggleStatus', $r['id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        @if($r['status'] === 'Approved')
                                            <button type="submit" class="btn btn-sm btn-soft-warning" title="Hold / Unapprove">
                                                <iconify-icon icon="solar:shield-warning-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        @else
                                            <button type="submit" class="btn btn-sm btn-soft-success" title="Approve Review">
                                                <iconify-icon icon="solar:check-circle-broken" class="fs-16"></iconify-icon>
                                            </button>
                                        @endif
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No client reviews submitted.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer py-2 bg-light bg-opacity-50">
                <p class="mb-0 text-muted fs-12">Only approved reviews appear on public storefront product pages</p>
            </div>
        </div>
    </div>
</div>
@endsection
