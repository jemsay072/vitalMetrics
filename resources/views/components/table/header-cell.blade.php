<th
    scope="col"
    {{ $attributes->class([
        'px-6 py-3 font-bold text-md whitespace-nowrap tracking-wide
         bg-gray-50 dark:bg-gray-700
         text-gray-500 dark:text-gray-200
         border-b border-gray-200 dark:border-gray-600'
    ]) }}
>
    {{ $slot }}
</th>
