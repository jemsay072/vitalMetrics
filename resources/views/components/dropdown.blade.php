@props(['align' => 'right', 'width' => '64', 'contentClasses' => 'bg-white'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$width = match ($width) {
    '48' => 'w-48',
    '64' => 'w-64',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false" @keydown.escape.window="open = false">
    <div @click="open = ! open" class="inline-block">
        {{ $trigger }}
    </div>

    <div x-cloak x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-1"
            class="absolute top-full right-0 z-50 mt-3 {{ $width }} rounded-[30px] bg-white shadow-2xl {{ $alignmentClasses }}"
            style="display: none;"
            @click.stop>
        <div class="rounded-[30px] ring-1 ring-black ring-opacity-5 {{ $contentClasses }} overflow-hidden">
            {{ $content }}
        </div>
    </div>
</div>
