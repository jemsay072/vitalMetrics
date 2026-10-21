@props([
    'editEvent' => null,
    'editModal'=> null,
    'deleteEvent' => null,
    'deleteModal' => null,
    'deactivatedEvent' => null,
    'deactivatedModal' => null,
])

<x-table.cell >
    <div class="flex items-center space-x-2 justify-end">
        <!-- Edit Modal -->
        @if ($editEvent && $editModal)
            <button
                x-on:click.stop="
                    $dispatch('{{ $editEvent }}', item.id);
                    $dispatch('open-modal', '{{ $editModal }}');
                "
                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
            >
                <i class="fa-solid fa-pen"></i>
            </button>
        @endif

        <!-- Delete Modal -->
        @if ($deleteEvent && $deleteModal)
            <button
                x-on:click.stop="
                    $dispatch('{{ $deleteEvent }}', item.id);
                    $dispatch('open-modal', '{{ $deleteModal }}');
                "
                class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
            >
                <i class="fa-regular fa-trash-can"></i>
            </button>
        @endif

        <!-- Deactivate -->
        @if ($deactivatedEvent && $deactivatedModal )
            <button
                x-on:click.stop="
                    $dispatch('{{ $deactivatedEvent }}', item.id);
                    $dispatch('open-modal', '{{ $deactivatedModal }}');
                "
                class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
            >
                <i class="fa-regular fa-eye-slash"></i>
            </button>
        @endif
    </div>
</x-table.cell>
