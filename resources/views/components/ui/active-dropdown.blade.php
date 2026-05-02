@props(['active', 'dropdownId'])

@php
    $isActive = false;
    
    if ($active === true) {
        $isActive = true;
    } elseif (is_string($active)) {
        $isActive = request()->is($active) || request()->is($active . '/*');
    } elseif (is_array($active)) {
        foreach ($active as $pattern) {
            if (request()->is($pattern) || request()->is($pattern . '/*')) {
                $isActive = true;
                break;
            }
        }
    }
    
    // For dropdown child active states, we'll handle this via additional active patterns
    $isChildActive = false;
    if (isset($dropdownId)) {
        // This will be handled by passing specific child patterns in the active array
        // For now, we rely on the parent active state
    }
@endphp

<button type="button" onclick="toggleDropdown('{{ $dropdownId }}')" 
    class="flex items-center w-full p-3 {{ ($isActive || $isChildActive) 
        ? 'text-blue-600 bg-blue-50 dark:text-blue-400 dark:bg-blue-900/20' 
        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800' }} rounded-lg group transition-all duration-200">
    {{ $slot }}
</button>
