@props([
    'status' => 'Processing',
])

@php
    $normalized = strtolower(trim($status));
    $classes = match($normalized) {
        'delivered' => 'border-[#304C3D] text-[#304C3D]',
        'shipped' => 'border-[#6E8B78] text-[#284033]',
        'processing' => 'border-[#A8B7A0] text-[#424741]',
        'placed' => 'border-[#C2D1C7] text-[#585D55]',
        default => 'border-[#C2D1C7] text-[#585D55]',
    };
@endphp

<span class="inline-flex items-center px-2 py-0.5 text-[10px] sm:text-[11px] font-medium uppercase tracking-wider border rounded-none {{ $classes }}">
    {{ $status }}
</span>
