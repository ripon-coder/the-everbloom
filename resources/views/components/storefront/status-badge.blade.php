@props([
    'status' => 'Processing',
])

@php
    $normalized = strtolower(trim($status));
    $classes = match($normalized) {
        'delivered' => 'border-[#304C3D] bg-[#E8EEE5] text-[#304C3D]',
        'shipped' => 'border-[#6E8B78] bg-[#E8EEE5] text-[#284033]',
        'processing' => 'border-[#A8B7A0] bg-[#E8DFD0] text-[#292D29]',
        'placed' => 'border-[#D5DBCF] bg-white text-[#585D55]',
        default => 'border-[#D5DBCF] bg-white text-[#585D55]',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium uppercase tracking-wider border rounded-none {{ $classes }}">
    {{ $status }}
</span>
