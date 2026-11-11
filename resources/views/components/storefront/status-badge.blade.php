@props([
    'status' => 'Processing',
])

@php
    $normalized = strtolower(trim($status));
    $classes = match($normalized) {
        'delivered' => 'border-[#304C3D] text-[#304C3D] bg-[#E8EEE5]/50',
        'shipped' => 'border-[#6E8B78] text-[#284033] bg-[#F2F5F3]',
        'processing' => 'border-[#A8B7A0] text-[#292D29] bg-white',
        'placed' => 'border-[#D5DBCF] text-[#585D55] bg-white',
        default => 'border-[#D5DBCF] text-[#585D55] bg-white',
    };
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium uppercase tracking-[0.2em] border rounded-none {{ $classes }}">
    {{ $status }}
</span>
