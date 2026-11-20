@props(['disabled' => false])

<input
    @disabled($disabled)
    {{ $attributes->merge([
        'class' => '
            border-gray-300
            bg-white
            text-gray-900
            placeholder-gray-400
            focus:border-indigo-500
            focus:ring-indigo-500
            rounded-md
            shadow-sm

            dark:border-gray-600
            dark:bg-gray-700
            dark:text-white
            dark:placeholder-gray-400
            dark:focus:border-indigo-500
            dark:focus:ring-indigo-500
        '
    ]) }}
>
