@extends('layouts.admin')

@section('title', 'Customer Reviews')
@section('container_class', 'container-xxl')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            @php
                $reviewFilterCount = collect([
                    $search ?: null,
                    ($statusFilter && $statusFilter !== 'all') ? $statusFilter : null,
                    $ratingFilter ?: null,
                ])->filter()->count();
            @endphp
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <h4 class="card-title mb-0">Client Product Reviews &amp; Moderation ({{ $reviews->total() }})</h4>
                    @if($reviewFilterCount > 0)
                        <a href="{{ route('admin.reviews.index') }}" class="badge bg-soft-danger text-danger text-decoration-none fs-11">
                            <i class="bx bx-x align-middle"></i> Clear Filters
                        </a>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-soft-secondary d-flex align-items-center gap-1 position-relative" data-bs-toggle="offcanvas" data-bs-target="#reviewFilterDrawer" aria-controls="reviewFilterDrawer">
                    <iconify-icon icon="solar:filter-bold-duotone" class="fs-16"></iconify-icon>
                    <span>Filter</span>
                    @if($reviewFilterCount > 0)
                        <span class="badge bg-primary rounded-pill ms-1">{{ $reviewFilterCount }}</span>
                    @endif
                </button>
            </div>

            <form id="bulk-review-form" method="POST" action="{{ route('admin.reviews.bulk') }}">
                @csrf
                <div class="card-body border-bottom py-2 d-flex align-items-center gap-2 bg-light-subtle">
                    <select name="action" class="form-select form-select-sm" style="width:auto;" required>
                        <option value="">Bulk Action...</option>
                        <option value="approve">Approve Selected</option>
                        <option value="reject">Reject Selected</option>
                        <option value="delete">Delete Selected</option>
                    </select>
                    <button type="submit" class="btn btn-sm btn-secondary">Apply</button>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 table-hover table-centered">
                            <thead class="bg-light-subtle">
                                <tr>
                                    <th style="width:32px;"><input type="checkbox" id="select-all-reviews" class="form-check-input"></th>
                                    <th>Client</th>
                                    <th>Botanical Product</th>
                                    <th>Rating</th>
                                    <th>Review Headline & Comment</th>
                                    <th>Submitted</th>
                                    <th>Status</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviews as $r)
                                    @php
                                        $badge = $r->status === 'Approved' ? 'success' : ($r->status === 'Pending' ? 'warning' : 'danger');
                                        $avatar = $r->user?->avatar ?? 'admin-assets/assets/images/users/avatar-2.jpg';
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="ids[]" value="{{ $r->id }}" class="form-check-input review-checkbox">
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ asset($avatar) }}" alt="{{ $r->author }}" class="avatar-xs rounded-circle object-fit-cover border">
                                                <div>
                                                    <span class="fw-semibold text-dark fs-13 d-block">{{ $r->author }}</span>
                                                    @if($r->verified)
                                                        <span class="badge bg-info-subtle text-info fs-11">Verified Buyer</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-medium text-primary fs-13">{{ $r->product?->name ?? 'Botanical Formulation' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center text-warning fs-13">
                                                @for($i = 0; $i < $r->rating; $i++)
                                                    <i class="bx bxs-star"></i>
                                                @endfor
                                                @for($i = $r->rating; $i < 5; $i++)
                                                    <i class="bx bx-star text-muted"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td>
                                            <h6 class="fs-13 text-dark fw-bold mb-1">
                                                {{ $r->headline }}
                                                @if($r->is_featured_testimonial)
                                                    <span class="badge bg-primary-subtle text-primary ms-1">Featured</span>
                                                @endif
                                            </h6>
                                            <p class="text-muted mb-0 fs-12 text-wrap" style="max-width: 360px;">{{ $r->body }}</p>
                                            @if($r->admin_reply)
                                                <div class="mt-1 p-1 px-2 bg-light rounded fs-12 text-dark">
                                                    <strong>Reply:</strong> {{ $r->admin_reply }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fs-12 text-muted">{{ $r->created_at?->format('M d, Y') }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $badge }}-subtle text-{{ $badge }} px-2 py-1 fs-12">
                                                {{ $r->status }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                @if($r->status !== 'Approved')
                                                    <button type="submit" form="approve-form-{{ $r->id }}" class="btn btn-sm btn-soft-success" title="Approve">
                                                        <iconify-icon icon="solar:check-circle-broken" class="fs-16"></iconify-icon>
                                                    </button>
                                                @endif
                                                @if($r->status !== 'Rejected')
                                                    <button type="submit" form="reject-form-{{ $r->id }}" class="btn btn-sm btn-soft-warning" title="Reject">
                                                        <iconify-icon icon="solar:close-circle-broken" class="fs-16"></iconify-icon>
                                                    </button>
                                                @endif
                                                <button type="submit" form="featured-form-{{ $r->id }}" class="btn btn-sm {{ $r->is_featured_testimonial ? 'btn-primary' : 'btn-soft-info' }}" title="Toggle Featured Testimonial">
                                                    <iconify-icon icon="solar:star-bold-duotone" class="fs-16"></iconify-icon>
                                                </button>
                                                <button type="submit" form="delete-form-{{ $r->id }}" class="btn btn-sm btn-soft-danger" title="Delete" onclick="return confirm('Delete this review?');">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-broken" class="fs-16"></iconify-icon>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No client reviews submitted.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>

            @foreach($reviews as $r)
                <form id="approve-form-{{ $r->id }}" action="{{ route('admin.reviews.approve', $r->id) }}" method="POST" class="d-none">@csrf</form>
                <form id="reject-form-{{ $r->id }}" action="{{ route('admin.reviews.reject', $r->id) }}" method="POST" class="d-none">@csrf</form>
                <form id="featured-form-{{ $r->id }}" action="{{ route('admin.reviews.featured', $r->id) }}" method="POST" class="d-none">@csrf</form>
                <form id="delete-form-{{ $r->id }}" action="{{ route('admin.reviews.destroy', $r->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
            @endforeach

            <div class="card-footer py-2 bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                <p class="mb-0 text-muted fs-12">Only approved reviews appear on public storefront product pages</p>
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>
<x-admin.filter-drawer
    id="reviewFilterDrawer"
    title="Filter Reviews"
    :action="route('admin.reviews.index')"
    :reset-url="route('admin.reviews.index')">
    <div class="mb-3">
        <label for="review-filter-search" class="form-label fw-semibold">Search</label>
        <input type="text" id="review-filter-search" name="search" value="{{ $search }}" class="form-control" placeholder="Search author, product...">
    </div>

    <div class="mb-3">
        <label for="review-filter-status" class="form-label fw-semibold">Status</label>
        <select id="review-filter-status" name="status" class="form-select">
            <option value="all" {{ ($statusFilter ?: 'all') === 'all' ? 'selected' : '' }}>All Statuses</option>
            @foreach(['Pending', 'Approved', 'Rejected'] as $st)
                <option value="{{ $st }}" {{ $statusFilter === $st ? 'selected' : '' }}>{{ $st }}</option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label for="review-filter-rating" class="form-label fw-semibold">Rating</label>
        <select id="review-filter-rating" name="rating" class="form-select">
            <option value="">All Ratings</option>
            @for($r = 5; $r >= 1; $r--)
                <option value="{{ $r }}" {{ (int)$ratingFilter === $r ? 'selected' : '' }}>{{ $r }} Stars</option>
            @endfor
        </select>
    </div>
</x-admin.filter-drawer>
@endsection

@push('scripts')
<script>
document.getElementById('select-all-reviews')?.addEventListener('change', function (e) {
    document.querySelectorAll('.review-checkbox').forEach(cb => cb.checked = e.target.checked);
});
</script>
@endpush
