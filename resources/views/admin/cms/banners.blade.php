@extends('layouts.admin')

@section('title', 'Banners & Homepage Sections | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Banners & Homepage Sections</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Add Banner</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cms.banners.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-2"><label class="form-label">Title</label><input type="text" name="title" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Subtitle</label><input type="text" name="subtitle" class="form-control"></div>
                    <div class="mb-2">
                        <label class="form-label">Position</label>
                        <select name="position" class="form-select">
                            <option value="hero">Hero Banner</option>
                            <option value="promo">Promo Banner</option>
                            <option value="trust_badge">Trust Badge</option>
                        </select>
                    </div>
                    <div class="mb-2"><label class="form-label">Link URL</label><input type="text" name="link_url" class="form-control" placeholder="/shop"></div>
                    <div class="mb-2"><label class="form-label">Button Text</label><input type="text" name="button_text" class="form-control" placeholder="Shop Now"></div>
                    <div class="mb-2"><label class="form-label">Image (WebP)</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                    <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" value="0" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Create Banner</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Banners ({{ $banners->count() }})</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Title</th><th>Position</th><th>Order</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                    <tbody>
                        @forelse($banners as $b)
                            <tr>
                                <td>{{ $b->title }}</td>
                                <td><span class="badge bg-info-subtle text-info">{{ $b->position }}</span></td>
                                <td>{{ $b->sort_order }}</td>
                                <td>{{ $b->is_active ? 'Active' : 'Hidden' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.cms.banners.destroy', $b) }}" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-3 text-muted">No banners added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header"><h5 class="card-title mb-0">Homepage Sections</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cms.sections.store') }}" class="row g-2 mb-3">
                    @csrf
                    <div class="col-md-4"><input type="text" name="key" class="form-control form-control-sm" placeholder="Key (e.g. hero, promo)" required></div>
                    <div class="col-md-5"><input type="text" name="title" class="form-control form-control-sm" placeholder="Section title"></div>
                    <div class="col-md-3"><button type="submit" class="btn btn-sm btn-primary w-100">Add Section</button></div>
                </form>
                <table class="table table-sm mb-0">
                    <thead><tr><th>Key</th><th>Title</th><th>Active</th></tr></thead>
                    <tbody>
                        @forelse($sections as $s)
                            <tr>
                                <td><code>{{ $s->key }}</code></td>
                                <td>{{ $s->title }}</td>
                                <td>{{ $s->is_active ? 'Yes' : 'No' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-muted">No custom sections configured.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

