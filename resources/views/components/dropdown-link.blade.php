@props(['icon' => null])
{{-- <a {{ $attributes->merge(['class' => 'block w-full px-4 py-3 text-start text-sm leading-6 text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out']) }}>
    @if ($icon)
        <i class="{{$icon}} mr-2 text-gray-500"></i>
    @endif
    {{ $slot }}
</a> --}}

<a {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-xl px-3.5 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition duration-150']) }}>
    @if($icon)
        <i class="{{ $icon }} text-gray-400 group-hover:text-slate-500 w-4 text-center transition duration-150"></i>
    @endif
    <span>{{ $slot }}</span>
</a>

