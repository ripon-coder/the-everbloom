@extends('layouts.admin')

@section('title', 'CMS Pages | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">CMS Pages</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Create Page</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cms.pages.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" class="form-control" placeholder="e.g. about, privacy-policy, custom-page">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content (HTML)</label>
                        <textarea name="content" class="form-control" rows="5"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea name="meta_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" checked id="page_active">
                        <label class="form-check-label" for="page_active">Published</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Page</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Existing Pages</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Title</th><th>Slug</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        @forelse($pages as $p)
                            <tr>
                                <td class="fw-semibold">{{ $p->title }}</td>
                                <td><code>{{ $p->slug }}</code></td>
                                <td><span class="badge {{ $p->is_active ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }}">{{ $p->is_active ? 'Active' : 'Draft' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.cms.pages.edit', $p) }}" class="btn btn-sm btn-soft-primary">Edit</a>
                                    <form method="POST" action="{{ route('admin.cms.pages.destroy', $p) }}" class="d-inline" onsubmit="return confirm('Delete page?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No CMS pages created yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pages->hasPages())
                <div class="card-footer">{{ $pages->links() }}</div>
            @endif
        </div>
    </div>
</div>
@endsection

