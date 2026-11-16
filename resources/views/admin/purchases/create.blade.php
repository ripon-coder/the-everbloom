@extends('layouts.admin')

@section('title', 'Record Wholesale Purchase Batch')

@section('content')
<div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">Record Wholesale Purchase Batch</h4>
            <p class="text-muted mb-0 fs-13">New batches automatically recalculate weighted average buy price over available stock only.</p>
        </div>
        <a href="{{ route('admin.purchases.index') }}" class="btn btn-outline-secondary btn-sm">&larr; Back to Purchases</a>
    </div>
</div>

<form action="{{ route('admin.purchases.store') }}" method="POST">
    @csrf
    <div class="card mb-3">
        <div class="card-header"><h5 class="card-title mb-0">Vendor &amp; Batch Details</h5></div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Vendor Name *</label>
                    <input type="text" name="vendor_name" list="vendor-list" class="form-control" placeholder="e.g. Pacific Botanical Labs" required>
                    <datalist id="vendor-list">
                        @foreach($vendors as $v)
                            <option value="{{ $v->name }}">
                        @endforeach
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Reference / Invoice No</label>
                    <input type="text" name="reference_no" class="form-control" placeholder="e.g. PO-2026-0042">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Purchase Date *</label>
                    <input type="date" name="purchased_at" class="form-control" value="{{ now()->toDateString() }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <input type="text" name="notes" class="form-control" placeholder="Optional batch notes">
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Batch Line Items</h5>
            <button type="button" class="btn btn-sm btn-soft-primary" onclick="addBatchRow()">+ Add Product Row</button>
        </div>
        <div class="card-body">
            <div id="batch-rows">
                <div class="row g-3 align-items-end batch-row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Product *</label>
                        <select name="items[0][product_id]" class="form-select" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Avail: {{ $p->stock_count }}, Avg Buy: ${{ number_format((float)$p->avg_buy_price, 2) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Batch Lot</label>
                        <input type="text" name="items[0][batch_lot]" class="form-control" placeholder="LOT-2026-NEW">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Buy Qty *</label>
                        <input type="number" name="items[0][qty]" class="form-control" min="1" value="10" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Unit Buy Price ($) *</label>
                        <input type="number" step="0.01" min="0" name="items[0][unit_buy_price]" class="form-control" value="25.00" required>
                    </div>
                </div>
            </div>
            <div class="mt-3 text-end">
                <button type="submit" class="btn btn-primary">Save Wholesale Batch</button>
            </div>
        </div>
    </div>
</form>

<script>
let batchIdx = 1;
function addBatchRow() {
    const container = document.getElementById('batch-rows');
    const first = container.querySelector('.batch-row');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${batchIdx}]`);
        if (el.tagName === 'INPUT' && el.type === 'text') el.value = '';
    });
    container.appendChild(clone);
    batchIdx++;
}
</script>
@endsection

