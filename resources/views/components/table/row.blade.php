@props([
    'hover' => true,
])

<tr
    {{ $attributes->class([
        'odd:bg-white even:bg-gray-50
         dark:odd:bg-gray-800 dark:even:bg-gray-700
         border-b border-gray-200 dark:border-gray-600
         hover:cursor-pointer tracking-wider
         hover:bg-blue-50 dark:hover:bg-gray-600
         transition-colors duration-150',
    ]) }}
>
    {{ $slot }}
</tr>
