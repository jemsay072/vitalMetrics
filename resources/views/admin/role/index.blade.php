<x-app-layout>
    <div
        x-data="searchFilter({
            endpoint: '/admin/api/roles'
        })"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                    title="Role"
                    description="Role Description"
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ]"
                >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'roles-form')"
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                        type="button"
                    >
                        + Role
                    </button>

                    {{-- View/Grid Toggle
                    <div class="hidden md:inline-flex">
                        <x-view-toggle />
                    </div>--}}
                </x-slot>
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            {{--Search Filter--}}
            <x-table.toolbar>
                <x-table.search
                    x-model="search"
                    placeholder="Search Role..."
                    @input.debounce.500="fetchData()"
                />
            </x-table.toolbar>

            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div class="overflow-x-auto">

                    <x-table.container>
                        {{-- Table Header --}}
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <x-table.row>
                                @foreach ( $columns as $column )
                                    <x-table.header-cell>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                            {{ $column['label'] }}
                                        </span>
                                    </x-table.header-cell>
                                @endforeach
                                <x-table.header-cell> Action </x-table.header-cell>
                            </x-table.row>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-default">
                            <template x-for="item in results" :key="item.id">
                                ddd
                                <x-table.row
                                    x-data
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    @foreach ( $columns as $column )
                                        @if ( $column['type'] == 'text')
                                            <x-table.cell x-text="item['{{ $column['key'] }}']" ></x-table.cell>
                                        @endif
                                    @endforeach
                                </x-table.row>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <x-table.row>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No roles found.
                                    </td>
                                </x-table.row>
                            </template>
                        </tbody>
                    </x-table.container>
                </div>
            </div>

            <!-- Modal: Create -->
            <x-modal name="roles-form" :show="false" maxWidth="lg">
                <div class="p-6" x-data="roleComponent">
                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-xl font-bold text-gray-900">Create Role</h2>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-form')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>
                    <div id="roles-form-content">
                        <form action="" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="role_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                                    Role Name <span class="text-red-500">*</span>
                                </label>
                                <x-text-input
                                    id="role_name"
                                    name="name"
                                    type="text"
                                    x-model="form.name"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    placeholder="Enter role name"
                                    required
                                    autofocus
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <label for="role_description" class="block text-sm font-semibold text-gray-700 mb-1">
                                    Description
                                </label>
                                <textarea
                                    id="role_description"
                                    name="description"
                                    x-model="form.description"
                                    rows="3"
                                    class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    placeholder="Enter role description (optional)"
                                ></textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('description')" />
                            </div>
                            <div class="pt-2">
                                <div class="flex items-center justify-between border-b border-gray-100 pb-2 mb-4">
                                    <span class="text-sm font-bold text-gray-800">Permissions</span>

                                    <!-- Select All Action Matrix Toggle Component Box -->
                                    <label class="inline-flex items-center text-sm text-gray-600 cursor-pointer select-none">
                                        <input
                                            type="checkbox"
                                            x-model="selectAll"
                                            class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-2 h-4 w-4 shadow-sm"
                                        >
                                        <span>Select All</span>
                                    </label>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6 text-sm text-gray-600 max-h-[220px] overflow-y-auto pr-1">
                                    <!-- Permission row element -->
                                    @foreach ( $permissions as $permission )

                                        <label class="inline-flex items-center cursor-pointer select-none py-0.5">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission['key'] }}" x-model="form.permissions['{{ $permission['key'] }}']" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3 h-4 w-4 shadow-sm">
                                            <span>{{ $permission['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <x-secondary-button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'roles-form')"
                                    class="px-5 py-2.5"
                                >
                                    {{ __('Cancel') }}
                                </x-secondary-button>
                                <x-primary-button
                                    type="submit"
                                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow-sm transition">
                                    {{ __('Submit') }}
                                </x-primary-button>
                            </div>

                        </form>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
