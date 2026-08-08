@props([
    'icon' => null,
    'type' => 'button',
])

<div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-3">
    <button
        type="{{ $type }}"
        {{
            $attributes->merge([
                'class' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-blue-500 bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50'
            ])
        }}
    >
        @if ($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $slot }}
    </button>
</div>
