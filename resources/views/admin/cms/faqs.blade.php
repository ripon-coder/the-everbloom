@extends('layouts.admin')

@section('title', 'FAQs | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box"><h4 class="page-title">FAQ Management</h4></div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Add FAQ</h5></div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.cms.faqs.store') }}">
                    @csrf
                    <div class="mb-2"><label class="form-label">Category</label><input type="text" name="category" value="General" class="form-control"></div>
                    <div class="mb-2"><label class="form-label">Question</label><input type="text" name="question" class="form-control" required></div>
                    <div class="mb-2"><label class="form-label">Answer</label><textarea name="answer" class="form-control" rows="4" required></textarea></div>
                    <div class="mb-3"><label class="form-label">Sort Order</label><input type="number" name="sort_order" value="0" class="form-control"></div>
                    <button type="submit" class="btn btn-primary">Add FAQ</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">All FAQs ({{ $faqs->total() }})</h5></div>
            <div class="card-body p-0">
                <table class="table mb-0 align-middle">
                    <thead><tr><th>Category</th><th>Question</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                    <tbody>
                        @forelse($faqs as $faq)
                            <tr>
                                <td><span class="badge bg-light text-dark">{{ $faq->category }}</span></td>
                                <td>
                                    <div class="fw-semibold">{{ $faq->question }}</div>
                                    <small class="text-muted">{{ \Illuminate\Support\Str::limit($faq->answer, 90) }}</small>
                                </td>
                                <td>{{ $faq->is_active ? 'Active' : 'Hidden' }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.cms.faqs.destroy', $faq) }}" class="d-inline" onsubmit="return confirm('Delete FAQ?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-soft-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-muted">No FAQs added yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($faqs->hasPages())<div class="card-footer">{{ $faqs->links() }}</div>@endif
        </div>
    </div>
</div>
@endsection

