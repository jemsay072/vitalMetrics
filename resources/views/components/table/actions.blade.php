@props([
    'editEvent' => null,
    'editModal' => null,

    'deleteEvent' => null,
    'deleteModal' => null,

    'deactivatedEvent' => null,
    'deactivatedModal' => null,

    'restoreEvent' => null,
    'restoreModal' => null,

    'forceDeleteEvent' => null,
    'forceDeleteModal' => null,

    // Optional visibility conditions
    'editShowWhen' => null,
    'deleteShowWhen' => null,
    'restoreShowWhen' => null,
    'forceDeleteShowWhen' => null,
])

<x-table.cell>
    <div class="flex items-center space-x-2 justify-end">

        {{-- Edit Modal --}}
        @if ($editEvent && $editModal)
            <button
                @if ($editShowWhen)
                    x-show="{{ $editShowWhen }}"
                    x-cloak
                @endif
                x-on:click.stop="
                    $dispatch('{{ $editEvent }}', item.id);
                    $dispatch('open-modal', '{{ $editModal }}');
                "
                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                title="Edit"
            >
                <i class="fa-solid fa-pen"></i>
            </button>
        @endif

        {{-- Delete Modal --}}
        @if ($deleteEvent && $deleteModal)
            <button
                @if ($deleteShowWhen)
                    x-show="{{ $deleteShowWhen }}"
                    x-cloak
                @endif
                x-on:click.stop="
                    $dispatch('{{ $deleteEvent }}', item.id);
                    $dispatch('open-modal', '{{ $deleteModal }}');
                "
                class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
                title="Move to Trash"
            >
                <i class="fa-regular fa-trash-can"></i>
            </button>
        @endif

        {{-- Restore --}}
        @if ($restoreEvent && $restoreModal)
            <button
                @if ($restoreShowWhen)
                    x-show="{{ $restoreShowWhen }}"
                    x-cloak
                @endif
                x-on:click.stop="
                    $dispatch('{{ $restoreEvent }}', item.id);
                    $dispatch('open-modal', '{{ $restoreModal }}');
                "
                class="p-2 text-green-600 hover:bg-green-100 rounded-lg"
                title="Restore Role"
            >
                <i class="fa-solid fa-rotate-left"></i>
            </button>
        @endif

        {{-- Force Delete --}}
        @if ($forceDeleteEvent && $forceDeleteModal)
            <button
                @if ($forceDeleteShowWhen)
                    x-show="{{ $forceDeleteShowWhen }}"
                    x-cloak
                @endif
                x-on:click.stop="
                    $dispatch('{{ $forceDeleteEvent }}', item.id);
                    $dispatch('open-modal', '{{ $forceDeleteModal }}');
                "
                class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
                title="Permanently Delete"
            >
                <i class="fa-regular fa-trash-can"></i>
            </button>
        @endif

    </div>
</x-table.cell>
