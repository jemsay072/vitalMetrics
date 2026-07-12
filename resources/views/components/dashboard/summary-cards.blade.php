@props([
    'name',
    'maxWidth' => '2xl',
    'icon' => null
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div class="{{$maxWidth}} bg-white dark:bg-gray-800 rounded-lg p-6 shadow">
     <div class="font-bold text-lg mb-2 text-gray-900 dark:text-white">
        @if ($icon)
            <i class="{{$icon}} mr-2 text-gray-500"></i>
        @endif
        {{ $name }}
     </div>
     <div class="text-gray-600 dark:text-gray-300">
         {{$slot}}
     </div>
</div>
