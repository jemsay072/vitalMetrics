@props([
    'placeholder' => 'Search ...'
])

<div class="flex-1 min-w-0">
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-gray-900 dark:text-gray-100 placeholder:text-gray-500 dark:placeholder:text-gray-400 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 sm:p-2.5 rounded-xl text-base shadow-sm transition-colors duration-300'
        ]) }}
    />
</div>
