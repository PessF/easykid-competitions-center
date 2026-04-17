@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-3 border-l-4 border-blue-500 text-start text-base font-medium text-blue-400 bg-blue-500/10 focus:outline-none focus:text-blue-300 focus:bg-blue-500/20 focus:border-blue-400 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-3 border-l-4 border-transparent text-start text-base font-medium text-gray-400 hover:text-gray-200 hover:bg-white/5 hover:border-gray-600 focus:outline-none focus:text-gray-200 focus:bg-white/5 focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>