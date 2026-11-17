@extends('layouts.admin')

@section('title', 'SEO Management | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">SEO Overview & Slug Redirects</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn btn-soft-info btn-sm">View Sitemap XML</a>
                <form method="POST" action="{{ route('admin.seo.sitemap.flush') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary btn-sm">Flush Sitemap Cache</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Products Missing Meta</p>
            <h4 class="mb-0">{{ $stats['products_missing_meta'] }} <small class="text-muted fs-13">/ {{ $stats['products_total'] }}</small></h4>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">Categories Missing Meta</p>
            <h4 class="mb-0">{{ $stats['categories_missing_meta'] }} <small class="text-muted fs-13">/ {{ $stats['categories_total'] }}</small></h4>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card"><div class="card-body">
            <p class="text-muted mb-1">CMS Pages Missing Meta</p>
            <h4 class="mb-0">{{ $stats['pages_missing_meta'] }} <small class="text-muted fs-13">/ {{ $stats['pages_total'] }}</small></h4>
        </div></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Add Slug Redirect</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.seo.redirects.store') }}">
                    @csrf
                    <div class="mb-2">
                        <label class="form-label">Old Path</label>
                        <input type="text" name="old_path" class="form-control" placeholder="/shop/product/old-slug" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">New Path</label>
                        <input type="text" name="new_path" class="form-control" placeholder="/shop/product/new-slug" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">HTTP Status</label>
                        <select name="status_code" class="form-select">
                            <option value="301">301 Permanent</option>
                            <option value="302">302 Temporary</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Redirect</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Active Redirects ({{ $redirects->total() }})</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Old Path</th><th>New Path</th><th>Code</th><th>Hits</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        @forelse($redirects as $r)
                            <tr>
                                <td><code>{{ $r->old_path }}</code></td>
                                <td><code>{{ $r->new_path }}</code></td>
                                <td><span class="badge bg-info-subtle text-info">{{ $r->status_code }}</span></td>
                                <td>{{ $r->hits }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.seo.redirects.destroy', $r) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-4 text-muted">No redirects configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($redirects->hasPages())<div class="card-footer">{{ $redirects->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection

