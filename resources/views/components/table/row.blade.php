@props([
    'hover' => true,
])

<tr
    {{ $attributes->class([
        'odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer tracking-wider', 'hover:bg-blue-50 ' => $hover,
    ]) }}
>
    {{ $slot }}
</tr>
