@props(['href', 'active', 'exact' => false])

@php
    $isActive = false;
    
    if ($active === true) {
        $isActive = true;
    } elseif (is_string($active)) {
        if ($exact) {
            $isActive = request()->is($active);
        } else {
            $isActive = request()->is($active) || request()->is($active . '/*');
        }
    } elseif (is_array($active)) {
        foreach ($active as $pattern) {
            if ($exact) {
                if (request()->is($pattern)) {
                    $isActive = true;
                    break;
                }
            } else {
                if (request()->is($pattern) || request()->is($pattern . '/*')) {
                    $isActive = true;
                    break;
                }
            }
        }
    }
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => ($isActive 
    ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' 
    : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800') . ' ' . $attributes->get('class', '')]) }}>
    {{ $slot }}
</a>
