@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'flex items-center px-4 py-3 rounded-lg text-white bg-indigo-700/70 mb-2 backdrop-blur-sm shadow-lg transform transition-all duration-300 hover:scale-105 hover:bg-indigo-600/80 hover:shadow-indigo-500/30'
    : 'flex items-center px-4 py-3 rounded-lg text-indigo-100 hover:text-white hover:bg-indigo-700/50 mb-2 transition-all duration-300 hover:translate-x-1 hover:shadow-md hover:shadow-indigo-500/20';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a> 