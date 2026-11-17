@extends('layouts.admin')

@section('title', 'Flash Sale Products | The Everbloom Admin')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title">Manage Products — {{ $flashSale->title }}</h4>
            <div class="page-title-right d-flex gap-2">
                <a href="{{ route('admin.flash-sales.show', $flashSale) }}" class="btn btn-soft-info btn-sm">Analytics</a>
                <a href="{{ route('admin.flash-sales.index') }}" class="btn btn-soft-secondary btn-sm">Back to List</a>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <span class="fw-semibold">Window:</span> {{ $flashSale->starts_at?->format('M d, Y H:i') }} — {{ $flashSale->ends_at?->format('M d, Y H:i') }}
            <span class="badge bg-info-subtle text-info ms-2 text-uppercase">{{ $flashSale->effectiveStatus() }}</span>
        </div>
        <button type="button" id="add-flash-row" class="btn btn-soft-primary btn-sm">+ Add Product</button>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.flash-sales.products.sync', $flashSale) }}">
            @csrf
            <div class="table-responsive">
                <table class="table align-middle" id="flash-products-table">
                    <thead>
                        <tr>
                            <th style="min-width:240px;">Product</th>
                            <th>Regular Price</th>
                            <th>Avail. Stock</th>
                            <th>Sale Price <span class="text-danger">*</span></th>
                            <th>Sale Stock <span class="text-danger">*</span></th>
                            <th>Max / Customer</th>
                            <th>Sold</th>
                            <th>Active</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($flashSale->items as $idx => $item)
                            @php
                                $regPrice = $item->variant ? (float) $item->variant->price : (float) ($item->product?->price ?? 0);
                                $availStock = $item->variant ? (int) $item->variant->stock_count : (int) ($item->product?->stock_count ?? 0);
                            @endphp
                            <tr>
                                <td>
                                    <select name="items[{{ $idx }}][product_id]" class="form-select form-select-sm product-select" required>
                                        @foreach($allProducts as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock_count }}" {{ $item->product_id === $p->id ? 'selected' : '' }}>
                                                {{ $p->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if($item->product_variant_id)
                                        <input type="hidden" name="items[{{ $idx }}][product_variant_id]" value="{{ $item->product_variant_id }}">
                                        <small class="text-muted">Variant: {{ $item->variant?->title ?: $item->variant?->size }}</small>
                                    @endif
                                </td>
                                <td class="regular-price">{{ number_format($regPrice, 2) }}</td>
                                <td class="avail-stock">{{ $availStock }}</td>
                                <td><input type="number" step="0.01" min="0.01" name="items[{{ $idx }}][sale_price]" value="{{ old("items.$idx.sale_price", $item->sale_price) }}" class="form-control form-control-sm" required></td>
                                <td><input type="number" min="1" name="items[{{ $idx }}][sale_stock]" value="{{ old("items.$idx.sale_stock", $item->sale_stock) }}" class="form-control form-control-sm" required></td>
                                <td><input type="number" min="1" name="items[{{ $idx }}][max_qty_per_customer]" value="{{ old("items.$idx.max_qty_per_customer", $item->max_qty_per_customer) }}" class="form-control form-control-sm" placeholder="No limit"></td>
                                <td>{{ $item->sold_qty }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $idx }}][is_active]" value="0">
                                    <input type="checkbox" name="items[{{ $idx }}][is_active]" value="1" class="form-check-input" {{ $item->is_active ? 'checked' : '' }}>
                                </td>
                                <td><button type="button" class="btn btn-soft-danger btn-sm remove-row">&times;</button></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Flash Sale Products</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#flash-products-table tbody');
    let rowIdx = {{ $flashSale->items->count() }};
    const productOptions = `@foreach($allProducts as $p)<option value="{{ $p->id }}" data-price="{{ $p->price }}" data-stock="{{ $p->stock_count }}">{{ addslashes($p->name) }}</option>@endforeach`;

    document.getElementById('add-flash-row')?.addEventListener('click', function () {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><select name="items[${rowIdx}][product_id]" class="form-select form-select-sm product-select" required><option value="">Select product...</option>${productOptions}</select></td>
            <td class="regular-price">-</td>
            <td class="avail-stock">-</td>
            <td><input type="number" step="0.01" min="0.01" name="items[${rowIdx}][sale_price]" class="form-control form-control-sm" required></td>
            <td><input type="number" min="1" name="items[${rowIdx}][sale_stock]" value="10" class="form-control form-control-sm" required></td>
            <td><input type="number" min="1" name="items[${rowIdx}][max_qty_per_customer]" class="form-control form-control-sm" placeholder="No limit"></td>
            <td>0</td>
            <td><input type="hidden" name="items[${rowIdx}][is_active]" value="0"><input type="checkbox" name="items[${rowIdx}][is_active]" value="1" class="form-check-input" checked></td>
            <td><button type="button" class="btn btn-soft-danger btn-sm remove-row">&times;</button></td>
        `;
        tbody.appendChild(tr);
        rowIdx++;
    });

    tbody?.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const opt = e.target.selectedOptions[0];
            const row = e.target.closest('tr');
            row.querySelector('.regular-price').textContent = opt?.dataset.price || '-';
            row.querySelector('.avail-stock').textContent = opt?.dataset.stock || '-';
        }
    });

    tbody?.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-row')) {
            e.target.closest('tr').remove();
        }
    });
});
</script>
@endsection

