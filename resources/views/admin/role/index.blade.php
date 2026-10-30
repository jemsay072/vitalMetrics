<x-app-layout>
    <div
        x-data="{
            ...searchFilter({ endpoint: '/admin/api/roles' }),

            activeTab: 'roles',

            switchTabs(tab) {
                this.activeTab = tab;

                this.endpoint = tab === 'roles'
                    ? '/admin/api/roles'
                    : '/admin/api/roles/trash';

                this.search = '';
                this.fetchData();
            },

            tableColumns: {
                roles: @js($columns),
                trash: [
                    ...@js($columns),
                    {
                        key: 'deleted_at',
                        label: 'Deleted At',
                        type: 'text'
                    }
                ]
            },

            get activeColumns() {
                return this.tableColumns[this.activeTab];
            }
        }"
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
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        type="button"
                        @click="switchTabs('roles')"
                        :click="activeTab === 'roles' ? 'border-amber-500 text-slate-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out focus:outline-none"
                    >
                        Role List
                    </button>
                    <button
                        type="button"
                        @click="switchTabs('trash')"
                        :click="activeTab === 'trash' ? 'border-amber-500 text-slate-900 font-bold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out focus:outline-none"
                    >
                        Trash History
                    </button>
                </nav>
            </div>
            {{--Search Filter--}}
            <x-table.toolbar>
                <x-table.search
                    x-model="search"
                    ::placeholder="activeTab === 'roles' ? 'Search Role...' : 'Search Trash History...'"
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
                        <div
                            x-show="activeTab === 'trash'"
                            x-cloak
                            class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-4"
                        >
                            <div class="flex items-start gap-3">
                                <span class="text-amber-600 text-lg">⚠</span>

                                <div>
                                    <h3 class="font-semibold text-amber-800">
                                        Trash History
                                    </h3>
                                    <p class="text-sm text-amber-700">
                                        These roles have been moved to trash.
                                        You can restore them or permanently delete them.
                                    </p>
                                </div>
                            </div>
                        </div>
                        {{-- Table Header --}}
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <x-table.row>
                                <template x-for="column in activeColumns" :key="column.key">
                                    <x-table.header-cell>
                                        <span
                                            class="text-xs font-semibold uppercase tracking-wider text-gray-500"
                                            x-text="column.label"
                                        ></span>
                                    </x-table.header-cell>
                                </template>

                                <x-table.header-cell>
                                    Action
                                </x-table.header-cell>
                            </x-table.row>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-default">

                            <template x-for="item in results" :key="item.id">
                                <tr class="odd:bg-white even:bg-gray-50 border-b border-default hover:bg-blue-50">

                                    <template x-for="column in activeColumns" :key="column.key">
                                        <td class="px-6 py-4 font-medium">

                                            {{-- Text and Number --}}
                                            <span
                                                x-show="column.type !== 'list'"
                                                x-text="item[column.key] ?? (column.type === 'number' ? 0 : '-')"
                                                class="text-sm"
                                                :class="column.key === 'status'
                                                    ? (item.status === 'active'
                                                        ? 'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase bg-green-100 text-green-700'
                                                        : 'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase bg-red-100 text-red-700')
                                                    : ''"

                                            ></span>

                                            {{-- List / Permissions --}}
                                            <div
                                                x-show="column.type === 'list'"
                                                class="flex flex-wrap gap-1"
                                            >
                                                <template
                                                    x-for="(value, index) in (item[column.key] ?? [])"
                                                    :key="index"
                                                >
                                                    <span
                                                        x-text="value"
                                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700"
                                                    ></span>
                                                </template>

                                                <span
                                                    x-show="!(item[column.key] ?? []).length"
                                                    class="text-gray-400"
                                                >
                                                    No permissions
                                                </span>
                                            </div>

                                        </td>
                                    </template>

                                    {{-- Actions --}}
                                    <td class="px-6 py-4 text-center">
                                        <x-table.actions
                                            edit-event="set-edit-id"
                                            edit-modal="roles-edit"
                                            edit-show-when="activeTab === 'roles'"

                                            delete-event="set-trash-id"
                                            delete-modal="roles-trash"
                                            delete-show-when="activeTab === 'roles'"

                                            restore-event="set-restore-id"
                                            restore-modal="roles-restore"
                                            restore-show-when="activeTab === 'trash'"

                                            force-delete-event="set-force-delete-id"
                                            force-delete-modal="roles-force-delete"
                                            force-delete-show-when="activeTab === 'trash'"
                                        />
                                    </td>

                                </tr>
                            </template>

                            {{-- Empty State --}}
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td
                                        :colspan="activeColumns.length + 1"
                                        class="text-center py-8 text-gray-500"
                                    >
                                        <span
                                            x-text="activeTab === 'roles'
                                                ? 'No roles found.'
                                                : 'No trash history found.'"
                                        ></span>
                                    </td>
                                </tr>
                            </template>

                        </tbody>

                    </x-table.container>
                </div>
            </div>

            {{-- Mobile Card View --}}
            <div class="md:hidden space-y-4">

                {{-- Loading --}}
                <div
                    x-show="loading"
                    class="flex justify-center py-6"
                >
                    <div class="animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-blue-500"></div>
                </div>

                {{-- Trash Notice --}}
                <div
                    x-show="activeTab === 'trash'"
                    x-cloak
                    class="rounded-lg border border-amber-200 bg-amber-50 p-4"
                >
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 mt-1"></i>

                        <div>
                            <h3 class="font-semibold text-amber-800">
                                Trash History
                            </h3>

                            <p class="text-sm text-amber-700">
                                These roles have been moved to trash.
                                You can restore them or permanently delete them.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Role Cards --}}
                <template x-for="item in results" :key="item.id">
                    <div
                        class="bg-white border border-gray-200 rounded-xl shadow-sm p-4 space-y-4"
                    >

                        {{-- Role Information --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <h3
                                    class="font-semibold text-gray-900 text-base"
                                    x-text="item.name"
                                ></h3>

                                <p
                                    class="text-sm text-gray-500 mt-1 break-words"
                                    x-text="item.description || 'No description provided.'"
                                ></p>
                            </div>

                            <span
                                class="shrink-0 inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold uppercase"
                                :class="item.status === 'active'
                                    ? 'bg-green-100 text-green-700'
                                    : 'bg-red-100 text-red-700'"
                                x-text="item.status"
                            ></span>

                        </div>

                        {{-- Users --}}
                        <div class="flex items-center justify-between text-sm border-t border-gray-100 pt-3">
                            <span class="text-gray-500">Assigned Users</span>

                            <span
                                class="font-semibold text-gray-800"
                                x-text="item.users_count ?? 0"
                            ></span>
                        </div>

                        {{-- Permissions --}}
                        <div class="border-t border-gray-100 pt-3">

                            <p class="text-sm font-semibold text-gray-700 mb-2">
                                Permissions
                            </p>

                            <div class="flex flex-wrap gap-1.5">

                                <template
                                    x-for="(permission, index) in (item.permissions ?? [])"
                                    :key="index"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-blue-100 text-blue-700"
                                        x-text="permission"
                                    ></span>
                                </template>

                                <span
                                    x-show="!(item.permissions ?? []).length"
                                    class="text-sm text-gray-400"
                                >
                                    No permissions
                                </span>

                            </div>
                        </div>

                        {{-- Deleted At --}}
                        <div
                            x-show="activeTab === 'trash'"
                            x-cloak
                            class="border-t border-gray-100 pt-3"
                        >
                            <p class="text-sm text-gray-500">
                                Deleted At
                            </p>

                            <p
                                class="text-sm font-medium text-gray-800 mt-1"
                                x-text="item.deleted_at
                                    ? new Date(item.deleted_at).toLocaleString()
                                    : '-'"
                            ></p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">

                            {{-- Edit --}}
                            <button
                                x-show="activeTab === 'roles'"
                                type="button"
                                @click="$dispatch('set-edit-id', item.id); $dispatch('open-modal', 'roles-edit')"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-50 px-3 py-2 text-sm font-medium text-blue-600 hover:bg-blue-100"
                            >
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </button>

                            {{-- Move to Trash --}}
                            <button
                                x-show="activeTab === 'roles'"
                                type="button"
                                @click="$dispatch('set-trash-id', item.id); $dispatch('open-modal', 'roles-trash')"
                                class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100"
                            >
                                <i class="fa-solid fa-trash"></i>
                                Trash
                            </button>

                            {{-- Restore --}}
                            <button
                                x-show="activeTab === 'trash'"
                                type="button"
                                @click="$dispatch('set-restore-id', item.id); $dispatch('open-modal', 'roles-restore')"
                                class="inline-flex items-center gap-2 rounded-lg bg-green-50 px-3 py-2 text-sm font-medium text-green-600 hover:bg-green-100"
                            >
                                <i class="fa-solid fa-rotate-left"></i>
                                Restore
                            </button>

                            {{-- Permanent Delete --}}
                            <button
                                x-show="activeTab === 'trash'"
                                type="button"
                                @click="$dispatch('set-force-delete-id', item.id); $dispatch('open-modal', 'roles-force-delete')"
                                class="inline-flex items-center gap-2 rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100"
                            >
                                <i class="fa-solid fa-trash-can"></i>
                                Delete
                            </button>

                        </div>

                    </div>
                </template>

                {{-- Empty State --}}
                <div
                    x-show="results.length === 0 && !loading"
                    class="rounded-xl border border-gray-200 bg-white py-10 text-center"
                >
                    <i class="fa-solid fa-folder-open text-3xl text-gray-300 mb-3"></i>

                    <p
                        class="text-sm text-gray-500"
                        x-text="activeTab === 'roles'
                            ? 'No roles found.'
                            : 'No trash history found.'"
                    ></p>
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
                        <form action="{{ route('admin.role.store') }}" method="POST">
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

            <!-- Modal: Edit -->
            <x-modal name="roles-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="roleComponent"
                    x-on:set-edit-id.window="
                        roleId = $event.detail;
                        loadRole(roleId)
                    "
                >
                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-xl font-bold text-gray-900">Edit Role</h2>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-edit')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal: Content -->
                    <div id="roles-edit-content">
                       <form :action="`/admin/roles/${roleId}`" method="POST" @submit.prevent="updateRole">
                            @csrf
                            @method('PUT')
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
                                <p
                                    x-show="errors.name"
                                    x-text="errors.name?.[0]"
                                    class="mt-1 text-sm text-red-600"
                                ></p>
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
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $permission['key'] }}"
                                                x-model="form.permissions['{{ $permission['key'] }}']"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 mr-3 h-4 w-4 shadow-sm"
                                            >

                                            <span>{{ $permission['label'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <x-secondary-button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'roles-edit')"
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

            <!-- Modal: View -->
            <x-modal name="roles-view" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="roleComponent"
                    x-on:set-view-id.window="
                        roleId = $event.detail;
                        loadRole(roleId)
                    "
                >

                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-xl font-bold text-gray-900" x-text="form.name || 'Role Details'"></h2>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-edit')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <svg xmlns="http://w3.org" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Loading -->
                    <div x-show="loading" class="py-6 text-center text-sm text-gray-500"> Loading role details... </div>

                    <!-- Modal: Content -->
                    <div id="role-view-content" x-show="!loading">
                        <!-- Description -->
                        <p class="text-sm text-gray-500 mb-4" x-text="form.description || 'No description provided.'" ></p>
                        <!-- Summary -->
                        <div class="flex items-center gap-6 text-sm text-gray-600 mb-5">
                            <span> <strong x-text="role.users_count ?? 0"></strong> Users </span>
                            <span> <strong x-text="role.permissions_count ?? 0"></strong> Permissions </span>
                        </div>
                        <!-- Permissions --> <div class="border-t border-gray-100 pt-4 mb-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3"> Permissions </h3>
                            <div class="flex flex-wrap gap-2">
                                <template x-for="permission in (role.permissions ?? [])" :key="permission.name" >
                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium bg-blue-100 text-blue-700" x-text="permission.label" ></span>
                                </template>
                                <span x-show="!(role.permissions ?? []).length" class="text-sm text-gray-400" > No permissions assigned. </span>
                            </div>
                        </div>
                        <!-- Assigned Users -->
                        <div class="border-t border-gray-100 pt-4">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3"> Assigned Users </h3>
                            <div class="space-y-2 max-h-48 overflow-y-auto">
                                <template x-for="user in (role.users ?? [])" :key="user.id" >
                                    <div class="flex items-center gap-3 rounded-lg border border-gray-100 p-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-blue-100 text-xs font-semibold text-blue-700" x-text="user.name .split(' ') .map(part => part[0]) .slice(0, 2) .join('') .toUpperCase()" ></div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-800" x-text="user.name" ></p>
                                            <p class="text-xs text-gray-500 truncate" x-text="user.email" ></p>
                                        </div>
                                    </div>
                                </template>
                                <p x-show="!(role.users ?? []).length" class="text-sm text-gray-400" > No users assigned. </p>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Footer -->
                    <div class="flex justify-end gap-3 border-t border-gray-100 mt-5 pt-4">
                        <x-secondary-button type="button" x-on:click="$dispatch('close-modal', 'roles-view')" > Close </x-secondary-button>
                        <x-primary-button type="button" x-on:click=" $dispatch('close-modal', 'roles-view'); $nextTick(() => { $dispatch('open-modal', 'roles-edit'); window.dispatchEvent( new CustomEvent('set-edit-id', { detail: roleId }) ); }) " > Edit Role </x-primary-button>
                    </div>
                </div>
            </x-modal>

            {{-- Modal: Move to Trash --}}
            <x-modal name="roles-trash" :show="false" maxWidth="lg">

                <div
                    x-data="{
                        trashRoleId: null,
                        trashRoleName: '',
                        trashError: '',
                        trashLoading: false,

                        async moveToTrash() {
                            if (!this.trashRoleId) return;

                            this.trashLoading = true;
                            this.trashError = '';

                            try {
                                const response = await fetch(
                                    `/admin/roles/${this.trashRoleId}/trash`,
                                    {
                                        method: 'PUT',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                    }
                                );

                                const data = await response.json();

                                if (!response.ok) {
                                    throw new Error(data.message || 'Unable to move role to trash.');
                                }

                                this.$dispatch('close-modal', 'roles-trash');

                                this.trashRoleId = null;
                                this.trashRoleName = '';

                                this.fetchData();

                            } catch (error) {
                                this.trashError = error.message;
                            } finally {
                                this.trashLoading = false;
                            }
                        }
                    }"
                    x-on:set-trash-id.window="
                        trashRoleId = $event.detail;
                        trashRoleName = results.find(role => role.id === $event.detail)?.name ?? '';
                        trashError = '';
                    "
                    class="p-6"
                >

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">

                        <h2 class="text-xl font-bold text-gray-900">
                            Move to Trash
                        </h2>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-trash')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>

                    </div>

                    {{-- Content --}}
                    <div class="space-y-4">

                        <div class="flex items-start gap-3">

                            <div class="text-amber-500 text-2xl">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                            </div>

                            <div>
                                <p class="text-gray-700">
                                    Are you sure you want to move this role to trash?
                                </p>

                                <p
                                    class="mt-2 font-semibold text-gray-900"
                                    x-text="trashRoleName"
                                ></p>

                                <p class="mt-2 text-sm text-gray-500">
                                    You can restore this role later from Trash History.
                                </p>
                            </div>

                        </div>

                        {{-- Error Message --}}
                        <div
                            x-show="trashError"
                            x-cloak
                            class="rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700"
                            x-text="trashError"
                        ></div>

                    </div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 mt-5 pt-4">

                        <x-secondary-button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-trash')"
                            x-bind:disabled="trashLoading"
                        >
                            Cancel
                        </x-secondary-button>

                        <x-danger-button
                            type="button"
                            x-on:click="moveToTrash()"
                            x-bind:disabled="trashLoading"
                        >
                            <span x-show="!trashLoading">
                                Move to Trash
                            </span>

                            <span x-show="trashLoading">
                                Moving...
                            </span>
                        </x-danger-button>

                    </div>

                </div>

            </x-modal>

            {{-- Modal: Restore Role --}}
            <x-modal name="roles-restore" :show="false" maxWidth="lg">

                <div
                    x-data="{
                        restoreRoleId: null,
                        restoreRoleName: '',
                        restoreError: '',
                        restoreLoading: false,

                        async restoreRole() {
                            if (!this.restoreRoleId) return;

                            this.restoreLoading = true;
                            this.restoreError = '';

                            try {
                                const response = await fetch(
                                    `/admin/roles/${this.restoreRoleId}/restore`,
                                    {
                                        method: 'PUT',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                    }
                                );

                                const data = await response.json();

                                if (!response.ok) {
                                    throw new Error(data.message || 'Unable to restore role.');
                                }

                                this.$dispatch('close-modal', 'roles-restore');

                                this.restoreRoleId = null;
                                this.restoreRoleName = '';

                                this.fetchData();

                            } catch (error) {
                                this.restoreError = error.message;
                            } finally {
                                this.restoreLoading = false;
                            }
                        }
                    }"
                    x-on:set-restore-id.window="
                        restoreRoleId = $event.detail;
                        restoreRoleName = results.find(role => role.id === $event.detail)?.name ?? '';
                        restoreError = '';
                    "
                    class="p-6"
                >

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-xl font-bold text-gray-900">
                            Restore Role
                        </h2>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-restore')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="flex items-start gap-3">

                        <div class="text-green-500 text-2xl">
                            <i class="fa-solid fa-rotate-left"></i>
                        </div>

                        <div>
                            <p class="text-gray-700">
                                Are you sure you want to restore this role?
                            </p>

                            <p
                                class="mt-2 font-semibold text-gray-900"
                                x-text="restoreRoleName"
                            ></p>

                            <p class="mt-2 text-sm text-gray-500">
                                This role will be returned to the Role List.
                            </p>
                        </div>

                    </div>

                    {{-- Error --}}
                    <div
                        x-show="restoreError"
                        x-cloak
                        class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700"
                        x-text="restoreError"
                    ></div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 mt-5 pt-4">

                        <x-secondary-button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-restore')"
                            x-bind:disabled="restoreLoading"
                        >
                            Cancel
                        </x-secondary-button>

                        <x-primary-button
                            type="button"
                            x-on:click="restoreRole()"
                            x-bind:disabled="restoreLoading"
                        >
                            <span x-show="!restoreLoading">Restore</span>
                            <span x-show="restoreLoading">Restoring...</span>
                        </x-primary-button>

                    </div>

                </div>

            </x-modal>

            {{-- Modal: Permanent Delete Role --}}
            <x-modal name="roles-force-delete" :show="false" maxWidth="lg">

                <div
                    x-data="{
                        forceDeleteRoleId: null,
                        forceDeleteRoleName: '',
                        forceDeleteError: '',
                        forceDeleteLoading: false,

                        async forceDeleteRole() {
                            if (!this.forceDeleteRoleId) return;

                            this.forceDeleteLoading = true;
                            this.forceDeleteError = '';

                            try {
                                const response = await fetch(
                                    `/admin/roles/${this.forceDeleteRoleId}/force-delete`,
                                    {
                                        method: 'DELETE',
                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                        },
                                    }
                                );

                                const data = await response.json();

                                if (!response.ok) {
                                    throw new Error(data.message || 'Unable to permanently delete role.');
                                }

                                this.$dispatch('close-modal', 'roles-force-delete');

                                this.forceDeleteRoleId = null;
                                this.forceDeleteRoleName = '';

                                this.fetchData();

                            } catch (error) {
                                this.forceDeleteError = error.message;
                            } finally {
                                this.forceDeleteLoading = false;
                            }
                        }
                    }"
                    x-on:set-force-delete-id.window="
                        forceDeleteRoleId = $event.detail;
                        forceDeleteRoleName = results.find(role => role.id === $event.detail)?.name ?? '';
                        forceDeleteError = '';
                    "
                    class="p-6"
                >

                    {{-- Header --}}
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-5">
                        <h2 class="text-xl font-bold text-gray-900">
                            Permanent Delete
                        </h2>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-force-delete')"
                            class="text-gray-400 hover:text-gray-600 transition"
                        >
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    {{-- Content --}}
                    <div class="flex items-start gap-3">

                        <div class="text-red-500 text-2xl">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div>
                            <p class="font-semibold text-red-700">
                                Warning: This action cannot be undone!
                            </p>

                            <p class="mt-2 text-gray-700">
                                Are you sure you want to permanently delete this role?
                            </p>

                            <p
                                class="mt-2 font-semibold text-gray-900"
                                x-text="forceDeleteRoleName"
                            ></p>

                            <p class="mt-2 text-sm text-gray-500">
                                This role will be permanently removed and cannot be restored.
                            </p>
                        </div>

                    </div>

                    {{-- Error --}}
                    <div
                        x-show="forceDeleteError"
                        x-cloak
                        class="mt-4 rounded-lg bg-red-50 border border-red-200 p-3 text-sm text-red-700"
                        x-text="forceDeleteError"
                    ></div>

                    {{-- Footer --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 mt-5 pt-4">

                        <x-secondary-button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-force-delete')"
                            x-bind:disabled="forceDeleteLoading"
                        >
                            Cancel
                        </x-secondary-button>

                        <x-danger-button
                            type="button"
                            x-on:click="forceDeleteRole()"
                            x-bind:disabled="forceDeleteLoading"
                        >
                            <span x-show="!forceDeleteLoading">Delete Permanently</span>
                            <span x-show="forceDeleteLoading">Deleting...</span>
                        </x-danger-button>

                    </div>

                </div>

            </x-modal>
        </div>
    </div>
</x-app-layout>
