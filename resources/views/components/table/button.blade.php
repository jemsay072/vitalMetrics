@props([
    'icon' => null,
    'type' => 'button',
])

<button
    type="{{ $type }}"
    {{
        $attributes->merge([
            'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-3.5 text-sm font-semibold text-slate-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-slate-900 dark:hover:text-white shadow-sm transition duration-150 disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer h-[46px] box-border focus:outline-none'
        ])
    }}
>
    @if ($icon)
        <i class="{{ $icon }} text-slate-400 dark:text-gray-400 group-hover:text-slate-600 dark:group-hover:text-gray-200 text-xs"></i>
    @endif
    {{ $slot }}
</button>
