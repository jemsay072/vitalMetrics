@props([
    'active' => false,
    'variant' => 'default',
    'icon' => null
])

@php
$classes = $active
            ? 'border-indigo-400 text-gray-900 focus:border-indigo-700'
            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:text-gray-700 focus:border-gray-300';
@endphp

@php
$variants = [

    'default' => $active
        ? 'border-indigo-400 text-gray-900'
        : 'border-transparent text-gray-500 hover:text-gray-700',

    'sidebar' => $active
        ? 'flex items-center gap-3 px-3 py-4 text-slate-100 bg-[#4c4c4c]'
        : 'flex items-center gap-3 px-3 py-4 text-slate-300 hover:bg-[#4c4c4c] hover:text-slate-100',

];

$classes = $variants[$variant];
@endphp

<a {{ $attributes->merge(['class' => $classes . ' inline-flex items-center px-1 border-b-2 border-[#3b3535] text-sm font-medium leading-5 focus:outline-none focus:outline-none
transition duration-150 ease-in-out']) }}>
    @if ($icon)
        <i class="{{$icon}} mr-2"></i>
    @endif
    {{ $slot }}
</a>
