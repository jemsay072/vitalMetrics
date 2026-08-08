@props([
    'placeholder' => 'Search ...'
])

<div class="flex-1 min-w-0">
    <input
        type="text"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge([
            'class' => 'bg-white border border-gray-300 text-gray-900 placeholder:text-gray-500 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 sm:p-2.5 rounded-xl text-base shadow-sm'
        ]) }}
    />
</div>
