@props(['active'])

@php
$classes = ($active ?? false)
            ? 'flex items-center px-4 py-3 text-white bg-white/10 rounded-xl transition-all duration-200'
            : 'flex items-center px-4 py-3 text-gray-500 hover:text-white hover:bg-white/5 rounded-xl transition-all duration-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>