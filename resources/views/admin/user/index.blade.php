<x-app-layout>
    <div
        x-data="searchFilter({
            endpoint: '/admin/api/users'
        })"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                    title="User"
                    description="User Description"
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ]"
                >
                <x-slot name="actions">
                    <button
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                        x-on:click="window.location.href='{{ route('admin.users.create') }}'"
                    >
                        + User
                    </button>
                </x-slot>
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{--Search Filter--}}
            <x-table.toolbar>

                <x-table.search
                    x-model="search"
                    placeholder="Search User..."
                    @input.debounce.500="fetchData()"
                />

                {{-- <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-3">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <x-table.button
                                type="button"
                                icon="fa-solid fa-file-export"
                            >
                                <span x-text="exportLabel"></span>
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </x-table.button>
                        </x-slot>
                        <x-slot name="content">
                            <x-dropdown-link
                                href="#"
                                x-on:click.prevent="extractCurrentData()"
                                icon="fa-solid fa-file-lines"
                            >
                                Current Page
                            </x-dropdown-link>

                            <x-dropdown-link
                                href="#"
                                x-on:click.prevent="exportAll()"
                                icon="fa-solid fa-file-export"
                            >
                                Export All
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div> --}}

            </x-table.toolbar>

            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <x-table.container>
                    <thead class="bg-gray-100 border-b border-default">
                        <x-table.row>
                            @foreach ( $columns as $column )
                                <x-table.header-cell> {{ $column['label'] }} </x-table.header-cell>
                            @endforeach
                            <x-table.header-cell> Action </x-table.header-cell>
                        </x-table.row>
                    </thead>
                    <tbody class="divide-y divide-gray-100 border-t border-default">
                        <template x-for="item in results" :key="item.id">
                            <x-table.row
                                class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                x-on:click="
                                    window.location.href = '{{ route('admin.users.show', ['user' => '__USER_ID__']) }}'.replace('__USER_ID__', item.id)
                                "
                            >
                                @foreach ( $columns as $column)
                                    @if ($column['type'] === 'text')
                                        <x-table.cell x-text="item['{{ $column['key'] }}']"></x-table.cell>
                                    @elseif ($column['type'] === 'relation')
                                        <x-table.cell x-text="item['{{ $column['key'] }}']['{{ $column['field'] }}']"></x-table.cell>
                                    @endif
                                @endforeach
                                <x-table.actions
                                    edit-event="set-edit-id"
                                    edit-modal="user-edit"
                                    delete-event="set-delete-id"
                                    delete-modal="user-delete"
                                />
                            </x-table.row>
                        </template>
                    </tbody>
                </x-table.container>
            </div>
        </div>
    </div>
</x-app-layout>
