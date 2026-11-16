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
                <div class="row g-2 align-items-end batch-row mb-3 border-bottom pb-3">
                    <div class="col-md-3">
                        <label class="form-label fs-12">Product *</label>
                        <select name="items[0][product_id]" class="form-select form-select-sm product-select" onchange="updateRowPreview(this)" required>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-stock="{{ $p->stock_count }}" data-avg="{{ $p->avg_buy_price }}" data-price="{{ $p->price }}" data-variants='@json($p->productVariants->map(fn($v) => ["id" => $v->id, "label" => $v->title ?: $v->size]))'>
                                    {{ $p->name }} (Avail: {{ $p->stock_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12">Variant (Optional)</label>
                        <select name="items[0][product_variant_id]" class="form-select form-select-sm variant-select">
                            <option value="">All / Primary</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12">Batch Lot</label>
                        <input type="text" name="items[0][batch_lot]" class="form-control form-control-sm" placeholder="LOT-2026-NEW">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label fs-12">Qty *</label>
                        <input type="number" name="items[0][qty]" class="form-control form-control-sm qty-input" min="1" value="10" oninput="updateRowPreview(this)" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fs-12">Unit Buy ({{ $currencySymbol }}) *</label>
                        <input type="number" step="0.01" min="0" name="items[0][unit_buy_price]" class="form-control form-control-sm price-input" value="25.00" oninput="updateRowPreview(this)" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-between gap-2">
                        <small class="text-muted fs-11 preview-text">New Avg: —</small>
                        <button type="button" class="btn btn-sm btn-soft-danger" onclick="removeBatchRow(this)" title="Remove Row">&times;</button>
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
const currencySymbol = @js($currencySymbol);
let batchIdx = 1;
function populateVariants(row) {
    const prodSelect = row.querySelector('.product-select');
    const varSelect = row.querySelector('.variant-select');
    const opt = prodSelect.options[prodSelect.selectedIndex];
    const variants = JSON.parse(opt.dataset.variants || '[]');
    varSelect.innerHTML = '<option value="">All / Primary</option>';
    variants.forEach(v => {
        const o = document.createElement('option');
        o.value = v.id;
        o.textContent = v.label;
        varSelect.appendChild(o);
    });
}
function updateRowPreview(el) {
    const row = el.closest('.batch-row');
    if (el.classList.contains('product-select')) {
        populateVariants(row);
    }
    const prodSelect = row.querySelector('.product-select');
    const opt = prodSelect.options[prodSelect.selectedIndex];
    const avail = parseInt(opt.dataset.stock || '0', 10);
    const curAvg = parseFloat(opt.dataset.avg || '0');
    const sellPrice = parseFloat(opt.dataset.price || '0');
    const qty = parseInt(row.querySelector('.qty-input').value || '0', 10);
    const unitBuy = parseFloat(row.querySelector('.price-input').value || '0');
    const totalQty = avail + qty;
    const newAvg = totalQty > 0 ? ((avail * curAvg) + (qty * unitBuy)) / totalQty : unitBuy;
    const unitProfit = sellPrice - newAvg;
    row.querySelector('.preview-text').textContent = `Avg: ${currencySymbol}${newAvg.toFixed(2)} | Profit: ${unitProfit >= 0 ? '+' + currencySymbol : '-' + currencySymbol}${Math.abs(unitProfit).toFixed(2)}`;
}
function addBatchRow() {
    const container = document.getElementById('batch-rows');
    const first = container.querySelector('.batch-row');
    const clone = first.cloneNode(true);
    clone.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\[\d+\]/, `[${batchIdx}]`);
        if (el.tagName === 'INPUT' && el.type === 'text') el.value = '';
    });
    container.appendChild(clone);
    updateRowPreview(clone.querySelector('.product-select'));
    batchIdx++;
}
function removeBatchRow(btn) {
    const rows = document.querySelectorAll('#batch-rows .batch-row');
    if (rows.length <= 1) return;
    btn.closest('.batch-row').remove();
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('#batch-rows .batch-row').forEach(row => {
        updateRowPreview(row.querySelector('.product-select'));
    });
});
</script>
@endsection
