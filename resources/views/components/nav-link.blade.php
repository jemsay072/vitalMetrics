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
        ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
        : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out',

    'sidebar' => $active
        ? 'flex items-center gap-3 px-4 py-3 mx-2.5 text-sm font-bold text-white bg-white/10 rounded-xl shadow-sm tracking-wide transition duration-150 ease-in-out'
        : 'flex items-center gap-3 px-4 py-3 mx-2.5 text-sm font-semibold text-slate-400 hover:bg-white/5 hover:text-slate-200 rounded-xl tracking-wide transition duration-150 ease-in-out',

];

$classes = $variants[$variant];
@endphp

<a {{ $attributes->merge(['class' => $classes . ' focus:outline-none']) }}>
    @if ($icon)
        <!-- W-4 Centering Utility para siguradong pantay ang icon grids pababa -->
        <i class="{{ $icon }} w-4 text-center text-sm transition duration-150 {{ $active ? 'text-white' : 'text-slate-400 group-hover:text-slate-200' }}"></i>
    @endif
    <span>{{ $slot }}</span>
</a>

