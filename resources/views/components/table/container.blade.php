@props([
    'head',
    'body'
])

<div class="overflow-x-auto">
    <table class="w-full text-sm text-left rtl:text-right text-body text-gray-600 min-w-[600px]">
        {{ $slot }}
    </table>
</div>
