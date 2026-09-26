@props([
    'editEvent',
    'editModal',
    'deleteEvent',
    'deleteModal',
])

<x-table.cell >
    <div class="flex items-center space-x-2 justify-end">
        <button
            x-on:click.stop="
                $dispatch('{{ $editEvent }}', item.id)
                $dispatch('open-modal', '{{ $editModal }}');
            "
            class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
        >
            <i class="fa-solid fa-pen"></i>
        </button>
        <button
            x-on:click.stop="
                $dispatch('{{ $deleteEvent }}', item.id)
                $dispatch('open-modal', '{{ $deleteModal }}');
            "
            class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
        >
            <i class="fa-regular fa-trash-can"></i>
        </button>
    </div>
</x-table.cell>
