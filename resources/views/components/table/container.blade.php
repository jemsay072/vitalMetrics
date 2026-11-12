@props([
    'head',
    'body'
])

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left rtl:text-right text-gray-600 dark:text-gray-300 min-w-[600px]">
        {{ $slot }}
    </table>
</div>
