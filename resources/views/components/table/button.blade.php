@props([
    'icon' => null,
    'type' => 'button',
])

<!-- FIXED HEIGHT LOCK: Nilagyan ng h-[42px] o py-2.5 para selyadong pantay sa search element line layout -->
<button
    type="{{ $type }}"
    {{
        $attributes->merge([
            'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-3.5 text-sm font-semibold text-slate-700 hover:bg-gray-50 hover:text-slate-900 shadow-sm transition duration-150 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-[46px] box-border focus:outline-none'
        ])
    }}
>
    @if ($icon)
        <i class="{{ $icon }} text-slate-400 group-hover:text-slate-600 text-xs"></i>
    @endif
    {{ $slot }}
</button>
