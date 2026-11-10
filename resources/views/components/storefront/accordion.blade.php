@props([
    'items' => [], // [['title' => '', 'content' => '']]
    'allowMultiple' => false,
])

<div 
    x-data="{ 
        active: {{ $allowMultiple ? '[]' : 'null' }},
        toggle(index) {
            @if($allowMultiple)
                if (this.active.includes(index)) {
                    this.active = this.active.filter(i => i !== index);
                } else {
                    this.active.push(index);
                }
            @else
                this.active = (this.active === index ? null : index);
            @endif
        },
        isOpen(index) {
            @if($allowMultiple)
                return this.active.includes(index);
            @else
                return this.active === index;
            @endif
        }
    }" 
    {{ $attributes->merge(['class' => 'divide-y divide-beige/70 border-t border-b border-beige/70']) }}
>
    @if(!empty($items))
        @foreach($items as $index => $item)
            @php $uniqueId = 'accordion-' . $index . '-' . uniqid(); @endphp
            <div class="py-4">
                <button 
                    type="button" 
                    @click="toggle({{ $index }})"
                    class="w-full flex items-center justify-between text-left font-serif text-base sm:text-lg text-ink hover:text-forest transition-colors py-1 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-forest cursor-pointer"
                    :aria-expanded="isOpen({{ $index }})"
                    aria-controls="{{ $uniqueId }}"
                >
                    <span>{{ $item['title'] ?? $item['q'] ?? '' }}</span>
                    <span class="ml-4 shrink-0 text-muted transition-transform duration-200" :class="{ 'rotate-180': isOpen({{ $index }}) }">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>
                <div 
                    id="{{ $uniqueId }}" 
                    x-show="isOpen({{ $index }})" 
                    x-collapse 
                    class="text-sm text-muted leading-relaxed pt-2 pb-3 font-sans"
                    x-cloak
                >
                    {!! nl2br(e($item['content'] ?? $item['a'] ?? '')) !!}
                </div>
            </div>
        @endforeach
    @else
        {{ $slot }}
    @endif
</div>
