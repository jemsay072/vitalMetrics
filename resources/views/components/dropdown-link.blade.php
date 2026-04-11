@props(['icon' => null])
<a {{ $attributes->merge(['class' => 'block w-full px-4 py-3 text-start text-sm leading-6 text-gray-700 hover:bg-gray-50 focus:outline-none focus:bg-gray-50 transition duration-150 ease-in-out']) }}>
    @if ($icon)
        <i class="{{$icon}} mr-2 text-gray-500"></i>
    @endif
    {{ $slot }}
</a>
