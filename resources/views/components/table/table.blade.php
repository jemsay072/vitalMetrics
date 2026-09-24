<div class="overflow-x-auto">
    <table
        {{
            $attributes->merge([
                'class' => 'w-full text-sm text-left'
            ])
        }}
    >
        {{ $slot }}
    </table>
</div>
