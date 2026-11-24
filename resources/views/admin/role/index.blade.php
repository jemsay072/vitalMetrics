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
                    description="Define system permission boundaries, create new user types, and audit access groups."
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ]"
                >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'roles-form')"
                        class="inline-flex items-center justify-center gap-2 px-4 h-[45px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                        type="button"
                    >
                        <i class="fa-solid fa-plus text-[11px]"></i>
                        <span>Add Record</span>
                    </button>

                    {{-- View/Grid Toggle
                    <div class="hidden md:inline-flex">
                        <x-view-toggle />
                    </div>--}}
                </x-slot>
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button
                        button
                        @click="switchTabs('roles')"
                        :class="activeTab === 'roles'
                            ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-semibold'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"

                        class="px-4 py-3 text-sm border-b-2 transition-colors"
                    >
                        Roles
                    </button>

                    <button
                        type="button"
                        @click="switchTabs('trash')"
                        :class="activeTab === 'trash'
                            ? 'border-b-2 border-blue-600 text-blue-600 dark:text-blue-400 font-semibold'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"

                        class="px-4 py-3 text-sm border-b-2 transition-colors"
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
            <div class="hidden xl:block relative overflow-hidden bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200/75 dark:bg-gray-900/75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>

                <x-table.container>
                    <div
                        x-show="activeTab === 'trash'"
                        x-cloak
                        class="mb-4 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20 p-4"
                    >
                        <div class="flex items-start gap-3">
                            <span class="text-amber-600 dark:text-amber-400 text-lg">⚠</span>

                            <div>
                                <h3 class="font-semibold text-amber-800 dark:text-amber-300">
                                    Trash History
                                </h3>
                                <p class="text-sm text-amber-700 dark:text-amber-400">
                                    These roles have been moved to trash.
                                    You can restore them or permanently delete them.
                                </p>
                            </div>
                        </div>
                    </div>
                    {{-- Table Header --}}
                    <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                        <x-table.row>
                            <template x-for="column in activeColumns" :key="column.key">
                                <x-table.header-cell>
                                    <span
                                        class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                        x-text="column.label"
                                    ></span>
                                </x-table.header-cell>
                            </template>

                            <x-table.header-cell class="!px-3 py-3 text-center whitespace-nowrap">
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300">
                                    Action
                                </span>
                            </x-table.header-cell>
                        </x-table.row>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 border-t border-gray-200 dark:border-gray-700">

                        <template x-for="item in results" :key="item.id">
                            <tr
                                @click="$dispatch('set-view-id', item.id); $dispatch('open-modal', 'roles-view')"
                                class="odd:bg-white even:bg-gray-50 dark:odd:bg-gray-800 dark:even:bg-gray-700/50 border-b border-gray-100 dark:border-gray-700 hover:bg-blue-50 dark:hover:bg-gray-700 transition-colors"
                            >

                                <template x-for="column in activeColumns" :key="column.key">
                                    <td class="px-6 py-4 font-medium">

                                        {{-- Status --}}
                                        <template x-if="column.key === 'status'">
                                            <div x-on:click.stop class="inline-block">

                                                {{-- Trash History: Static Badge --}}
                                                <template x-if="activeTab === 'trash'">
                                                    <span
                                                        x-text="item.status"
                                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wider"
                                                        :class="item.status === 'active'
                                                            ? 'bg-green-100 text-green-700'
                                                            : 'bg-red-100 text-red-700'"
                                                    ></span>
                                                </template>

                                                {{-- Role List: Interactive Dropdown --}}
                                                <template x-if="activeTab === 'roles'">
                                                    <select
                                                        x-model="item.status"
                                                        @change="
                                                            let previousStatus = item.status === 'active'
                                                                ? 'inactive'
                                                                : 'active';

                                                            let selectedStatus = $event.target.value;

                                                            fetch(`/admin/api/roles/${item.id}/status`, {
                                                                method: 'PUT',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                                                },
                                                                body: JSON.stringify({ status: selectedStatus })
                                                            })
                                                            .then(async res => {
                                                                const data = await res.json();

                                                                if (!res.ok) {
                                                                    item.status = previousStatus;
                                                                    alert(data.message || 'Error updating role status.');
                                                                }
                                                            })
                                                            .catch(err => {
                                                                console.error('Error updating status:', err);
                                                                item.status = previousStatus;
                                                            });
                                                        "
                                                        class="block w-28 rounded-md py-1 pl-2.5 pr-8 text-xs font-bold uppercase tracking-wider border focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition duration-150 cursor-pointer shadow-sm focus:outline-none"
                                                        :class="item.status === 'active'
                                                            ? 'bg-green-100 text-green-700 focus:ring-green-500 border-green-200'
                                                            : 'bg-red-100 text-red-700 focus:ring-red-500 border-red-200'"

                                                    >
                                                        <option value="active">Active</option>
                                                        <option value="inactive">Inactive</option>
                                                    </select>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- List / Permissions --}}
                                        <template x-if="column.type === 'list'">
                                            <div class="flex flex-wrap gap-1">
                                                <template
                                                    x-for="(value, index) in (item[column.key] ?? [])"
                                                    :key="index"
                                                >
                                                    <span
                                                        x-text="value"
                                                        class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-700 dark:text-blue-300"
                                                    ></span>
                                                </template>

                                                <span
                                                    x-show="!(item[column.key] ?? []).length"
                                                    class="text-gray-400 dark:text-gray-500"
                                                >
                                                    No permissions
                                                </span>
                                            </div>
                                        </template>

                                        {{-- Text and Number --}}
                                        <template x-if="column.key !== 'status' && column.type !== 'list'">
                                            <span
                                                x-text="item[column.key] ?? (column.type === 'number' ? 0 : '-')"
                                                class="text-sm text-gray-700 dark:text-gray-300"
                                            ></span>
                                        </template>

                                    </td>
                                </template>
                                {{-- <template x-if="column.key !== 'status' && column.type !== 'list'">
                                    <span
                                        x-text="item[column.key] ?? (column.type === 'number' ? 0 : '-')"
                                        class="text-sm text-gray-700"
                                    ></span>
                                </template> --}}

                                {{-- Actions --}}
                                <td class="px-3 py-4 text-center whitespace-nowrap">
                                    <x-table.actions
                                        :as-cell="false"
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
                                    class="text-center py-8 text-gray-500 dark:text-gray-400"
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

            {{-- Mobile Card View --}}
            <div class="xl:hidden space-y-4">

                {{-- Loading --}}
                <div
                    x-show="loading"
                    class="flex justify-center py-6"
                >
                    <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 dark:border-gray-700 border-t-blue-500 dark:border-t-emerald-500"></div>
                </div>

                {{-- Trash Notice --}}
                <div
                    x-show="activeTab === 'trash'"
                    x-cloak
                    class="rounded-xl border border-amber-200 dark:border-amber-800/60 bg-amber-50 dark:bg-amber-900/20 p-4"
                >
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-triangle-exclamation text-amber-600 dark:text-amber-400 mt-1"></i>

                        <div>
                            <h3 class="font-semibold text-amber-800 dark:text-amber-300">
                                Trash History
                            </h3>

                            <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                                These roles have been moved to trash.
                                You can restore them or permanently delete them.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Role Cards --}}
                <template x-for="item in results" :key="item.id">
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-4 space-y-4 transition-colors duration-200">

                        {{-- Role Information --}}
                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">
                                <h3
                                    class="font-semibold text-gray-900 dark:text-white text-base"
                                    x-text="item.name"
                                ></h3>

                                <p
                                    class="text-sm text-gray-500 dark:text-gray-400 mt-1 break-words"
                                    x-text="item.description || 'No description provided.'"
                                ></p>
                            </div>

                            {{-- Role Status --}}
                            <select
                                x-model="item.status"
                                @change="
                                    let selectedStatus = $event.target.value;
                                    let previousStatus = selectedStatus === 'active'
                                        ? 'inactive'
                                        : 'active';

                                    fetch(`/admin/api/roles/${item.id}/status`, {
                                        method: 'PUT',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                                        },
                                        body: JSON.stringify({ status: selectedStatus })
                                    })
                                    .then(async res => {
                                        const data = await res.json();

                                        if (!res.ok) {
                                            item.status = previousStatus;
                                            alert(data.message || 'Error updating role status.');
                                        }
                                    })
                                    .catch(err => {
                                        console.error('Error updating status:', err);
                                        item.status = previousStatus;
                                    });
                                "
                                class="w-28 shrink-0 rounded-lg px-2 py-1.5 text-xs font-bold uppercase border focus:outline-none focus:ring-2 focus:ring-offset-1 dark:focus:ring-offset-gray-800 transition-colors"
                                :class="item.status === 'active'
                                    ? 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-300 border-green-200 dark:border-green-800 focus:ring-green-500'
                                    : 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 border-red-200 dark:border-red-800 focus:ring-red-500'"
                            >
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>

                        </div>

                        {{-- Users --}}
                        <div class="flex items-center justify-between text-sm border-t border-gray-100 dark:border-gray-700 pt-3">
                            <span class="text-gray-500 dark:text-gray-400">
                                Assigned Users
                            </span>

                            <span
                                class="font-semibold text-gray-800 dark:text-gray-200"
                                x-text="item.users_count ?? 0"
                            ></span>
                        </div>

                        {{-- Permissions --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-3">

                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Permissions
                            </p>

                            <div class="flex flex-wrap gap-1.5">

                                <template
                                    x-for="(permission, index) in (item.permissions ?? [])"
                                    :key="index"
                                >
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 border border-blue-200/60 dark:border-blue-800/50"
                                        x-text="permission"
                                    ></span>
                                </template>

                                <span
                                    x-show="!(item.permissions ?? []).length"
                                    class="text-sm text-gray-400 dark:text-gray-500"
                                >
                                    No permissions
                                </span>

                            </div>
                        </div>

                        {{-- Deleted At --}}
                        <div
                            x-show="activeTab === 'trash'"
                            x-cloak
                            class="border-t border-gray-100 dark:border-gray-700 pt-3"
                        >
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Deleted At
                            </p>

                            <p
                                class="text-sm font-medium text-gray-800 dark:text-gray-200 mt-1"
                                x-text="item.deleted_at
                                    ? new Date(item.deleted_at).toLocaleString()
                                    : '-'"
                            ></p>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-wrap items-center justify-end gap-2 border-t border-gray-100 dark:border-gray-700 pt-3">

                            {{-- Edit --}}
                            <button
                                x-show="activeTab === 'roles'"
                                type="button"
                                @click="$dispatch('set-edit-id', item.id); $dispatch('open-modal', 'roles-edit')"
                                class="inline-flex items-center gap-2 rounded-lg bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800/50 px-3 py-2 text-sm font-medium text-blue-600 dark:text-blue-300 hover:bg-blue-100 dark:hover:bg-blue-900/50 transition cursor-pointer"
                            >
                                <i class="fa-solid fa-pen"></i>
                                Edit
                            </button>

                            {{-- Move to Trash --}}
                            <button
                                x-show="activeTab === 'roles'"
                                type="button"
                                @click="$dispatch('set-trash-id', item.id); $dispatch('open-modal', 'roles-trash')"
                                class="inline-flex items-center gap-2 rounded-lg bg-amber-50 dark:bg-amber-900/30 border border-amber-100 dark:border-amber-800/50 px-3 py-2 text-sm font-medium text-amber-600 dark:text-amber-300 hover:bg-amber-100 dark:hover:bg-amber-900/50 transition cursor-pointer"
                            >
                                <i class="fa-solid fa-trash"></i>
                                Trash
                            </button>

                            {{-- Restore --}}
                            <button
                                x-show="activeTab === 'trash'"
                                type="button"
                                @click="$dispatch('set-restore-id', item.id); $dispatch('open-modal', 'roles-restore')"
                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-100 dark:border-emerald-800/50 px-3 py-2 text-sm font-medium text-emerald-600 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition cursor-pointer"
                            >
                                <i class="fa-solid fa-rotate-left"></i>
                                Restore
                            </button>

                            {{-- Permanent Delete --}}
                            <button
                                x-show="activeTab === 'trash'"
                                type="button"
                                @click="$dispatch('set-force-delete-id', item.id); $dispatch('open-modal', 'roles-force-delete')"
                                class="inline-flex items-center gap-2 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/50 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-300 hover:bg-red-100 dark:hover:bg-red-900/50 transition cursor-pointer"
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
                    class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 py-10 text-center"
                >
                    <i class="fa-solid fa-folder-open text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>

                    <p
                        class="text-sm text-gray-500 dark:text-gray-400"
                        x-text="activeTab === 'roles'
                            ? 'No roles found.'
                            : 'No trash history found.'"
                    ></p>
                </div>

            </div>

            <!-- Modal: Create -->
            <x-modal name="roles-form" :show="false" maxWidth="lg">
                <div class="p-6 bg-white dark:bg-gray-800 rounded-2xl" x-data="roleComponent">
                    <!-- Modal: Header Layout -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-gray-700 flex items-center justify-center text-slate-700 dark:text-gray-200 border border-gray-100 dark:border-gray-600">
                                <i class="fa-solid fa-user-shield text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tigh">Create New Role</h2>
                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">Define role identifiers and privilege assignment states</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-form')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div id="roles-form-content">
                        <form action="{{ route('admin.role.store') }}" method="POST" class="space-y-4">
                            @csrf

                            <!-- Role Name Input -->
                            <div>
                                <label for="role_name" class="block text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                                    Role Name <span class="text-emerald-500">*</span>
                                </label>
                                <x-text-input
                                    id="role_name"
                                    name="name"
                                    type="text"
                                    x-model="form.name"
                                    class="block w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"
                                    placeholder="e.g., Content Editor, Support Team"
                                    required
                                    autofocus
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                            </div>

                            <!-- Description Textarea -->
                            <div>
                                <label for="role_description" class="block text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                                    Description
                                </label>
                                <textarea
                                    id="role_description"
                                    name="description"
                                    x-model="form.description"
                                    rows="3"
                                    class="block w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"
                                    placeholder="Describe the operational scope or limitations of this access profile..."
                                ></textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('description')" />
                            </div>

                            <!-- Vitals / Permissions Selection Area -->
                            <div class="pt-2">
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 mb-3">
                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Module Permissions</span>

                                    <!-- Select All Toggle Component Box -->
                                    <label class="inline-flex items-center text-xs font-bold text-slate-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 cursor-pointer select-none transition">
                                        <input
                                            type="checkbox"
                                            x-model="selectAll"
                                            class="rounded-md border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-700 text-emerald-600 focus:ring-emerald-500 mr-2 h-4 w-4 shadow-sm transition"
                                        >
                                        <span>Select All Modules</span>
                                    </label>
                                </div>

                                <!-- Scrollable Grid Field Matrix Card -->
                                <div class="bg-gray-50/60 dark:bg-gray-700/40 border border-gray-100/80 dark:border-gray-600 rounded-xl p-3.5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-6 text-sm text-gray-700 dark:text-gray-300 max-h-[200px] overflow-y-auto pr-1 custom-scrollbar">
                                        @foreach ( $permissions as $permission )
                                            <label class="inline-flex items-center cursor-pointer select-none py-1 px-2 rounded-lg hover:bg-white hover:shadow-xs dark:hover:bg-slate-700 dark:hover:shadow-none transition">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission['key'] }}"
                                                    x-model="form.permissions['{{ $permission['key'] }}']"
                                                    class="rounded-md border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-700 text-emerald-600 focus:ring-emerald-500 mr-3 h-4 w-4 shadow-sm transition"
                                                >
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 tracking-tight">{{ $permission['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Action Buttons -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'roles-form')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Create Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal: Edit -->
            <x-modal name="roles-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl"
                    x-data="roleComponent"
                    x-on:set-edit-id.window="
                        roleId = $event.detail;
                        loadRole(roleId)
                    "
                >
                    <!-- Modal: Header Layout -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-gray-700 flex items-center justify-center text-slate-700 dark:text-gray-200 border border-gray-100 dark:border-gray-600">
                                <i class="fa-solid fa-user-gear text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tigh">Edit Role Configuration</h2>
                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">Modify role identifiers and update privilege assignment states</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-edit')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Modal: Content Form Area -->
                    <div id="roles-edit-content">
                    <form :action="`/admin/roles/${roleId}`" method="POST" @submit.prevent="updateRole" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- Role Name Field -->
                            <div>
                                <label for="role_name_edit" class="block text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                                    Role Name <span class="text-emerald-500">*</span>
                                </label>
                                <x-text-input
                                    id="role_name_edit"
                                    name="name"
                                    type="text"
                                    x-model="form.name"
                                    class="block w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"
                                    placeholder="Enter role name"
                                    required
                                    autofocus
                                />
                                <x-input-error class="mt-1" :messages="$errors->get('name')" />
                                <p
                                    x-show="errors.name"
                                    x-text="errors.name?.[0]"
                                    class="mt-1 text-xs font-semibold text-red-600 tracking-tight"
                                ></p>
                            </div>

                            <!-- Description Field -->
                            <div>
                                <label for="role_description_edit" class="block text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider mb-1.5">
                                    Description
                                </label>
                                <textarea
                                    id="role_description_edit"
                                    name="description"
                                    x-model="form.description"
                                    rows="3"
                                    class="block w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-700 placeholder-gray-400 dark:placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"
                                    placeholder="Enter role description (optional)"
                                ></textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('description')" />
                            </div>

                            <!-- Module Permissions Mapping Area -->
                            <div class="pt-2">
                                <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-2 mb-3">
                                    <span class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Module Permissions</span>

                                    <!-- Select All Action Matrix Toggle Component Box -->
                                    <label class="inline-flex items-center text-xs font-bold text-slate-600 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 cursor-pointer select-none transition">
                                        <input
                                            type="checkbox"
                                            x-model="selectAll"
                                            class="rounded-md border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-700 text-emerald-600 focus:ring-emerald-500 mr-2 h-4 w-4 shadow-sm transition"
                                        >
                                        <span>Select All Modules</span>
                                    </label>
                                </div>

                                <!-- Encapsulated Custom Scrollable Grid Frame -->
                                <div class="bg-gray-50/60 dark:bg-gray-700/40 border border-gray-100/80 dark:border-gray-600 rounded-xl p-3.5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2.5 gap-x-6 text-sm text-gray-700 dark:text-gray-300 max-h-[200px] overflow-y-auto pr-1 custom-scrollbar">
                                        @foreach ( $permissions as $permission )
                                            <label class="inline-flex items-center cursor-pointer select-none py-1 px-2 rounded-lg hover:bg-white hover:shadow-xs dark:hover:bg-slate-700 dark:hover:shadow-none transition">
                                                <input
                                                    type="checkbox"
                                                    name="permissions[]"
                                                    value="{{ $permission['key'] }}"
                                                    x-model="form.permissions['{{ $permission['key'] }}']"
                                                    class="rounded-md border-gray-300 dark:border-gray-500 bg-white dark:bg-gray-700 text-emerald-600 focus:ring-emerald-500 mr-3 h-4 w-4 shadow-sm transition"
                                                >
                                                <span class="text-xs font-semibold text-gray-700 dark:text-gray-200 tracking-tight">{{ $permission['label'] }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Footer Operations Control Strip Section -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'roles-edit')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Save Configuration
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal: View -->
            <x-modal name="roles-view" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl relative"
                    x-data="roleComponent"
                    x-on:set-view-id.window="
                        roleId = $event.detail;
                        loadRole(roleId)
                    "
                >

                    <!-- Modal: Header Layout -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-slate-50 dark:bg-gray-700 flex items-center justify-center text-slate-700 dark:text-gray-200 border border-gray-100 dark:border-gray-600">
                                <i class="fa-solid fa-id-card text-sm"></i>
                            </div>

                            <div>
                                <div class="flex items-center gap-2">
                                    <h2
                                        class="text-lg font-black text-gray-900 dark:text-white tracking-tight"
                                        x-text="role.name || 'Role Details'"
                                    ></h2>

                                    <!-- Role Status Badge -->
                                    <template x-if="role.status">
                                        <span
                                            x-text="role.status"
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider border"
                                            :class="role.status === 'active'
                                                ? 'bg-green-50 text-green-700 border-green-200 dark:bg-green-900/30 dark:text-green-300 dark:border-green-800'
                                                : 'bg-red-50 text-red-700 border-red-200 dark:bg-red-900/30 dark:text-red-300 dark:border-red-800'"
                                        ></span>
                                    </template>
                                </div>

                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Overview of assigned privileges and associated user accounts
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-view')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Loading State Spinner -->
                    <div
                        x-show="loading"
                        class="flex flex-col justify-center items-center py-12 gap-3"
                        x-cloak
                    >
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-200 dark:border-gray-700 border-b-slate-600 dark:border-b-emerald-500"></div>

                        <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 tracking-wide">
                            Loading role details...
                        </p>
                    </div>

                    <!-- Modal: Content View -->
                    <div
                        id="role-view-content"
                        x-show="!loading"
                        class="space-y-5"
                        x-cloak
                    >

                        <!-- Summary Information Micro Cards -->
                        <div class="grid grid-cols-2 gap-3">

                            <!-- Users Count -->
                            <div class="p-3 bg-slate-50/60 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-700 shadow-xs border border-gray-100 dark:border-gray-600 flex items-center justify-center text-slate-500 dark:text-gray-300">
                                    <i class="fa-solid fa-users text-xs"></i>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">
                                        Active Users
                                    </span>

                                    <span
                                        class="text-sm font-black text-gray-800 dark:text-white"
                                        x-text="(role.users_count ?? 0) + ' Accounts'"
                                    ></span>
                                </div>
                            </div>

                            <!-- Permissions Count -->
                            <div class="p-3 bg-slate-50/60 dark:bg-gray-700/50 border border-gray-100 dark:border-gray-600 rounded-xl flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-white dark:bg-gray-700 shadow-xs border border-gray-100 dark:border-gray-600 flex items-center justify-center text-slate-500 dark:text-gray-300">
                                    <i class="fa-solid fa-toggle-on text-xs"></i>
                                </div>

                                <div>
                                    <span class="block text-[10px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">
                                        Permissions
                                    </span>

                                    <span
                                        class="text-sm font-black text-gray-800 dark:text-white"
                                        x-text="(role.permissions_count ?? 0) + ' Enabled'"
                                    ></span>
                                </div>
                            </div>
                        </div>

                        <!-- Description Frame -->
                        <div class="p-3.5 bg-gray-50/40 dark:bg-gray-700/40 border border-gray-100/70 dark:border-gray-600 rounded-xl">
                            <span class="block text-[11px] font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-1">
                                Operational Scope
                            </span>

                            <p
                                class="text-sm font-medium text-gray-600 dark:text-gray-300 leading-relaxed"
                                x-text="role.description || 'No custom description notes provided for this management profile.'"
                            ></p>
                        </div>

                        <!-- Module Permissions Section -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2.5">
                                Enabled Modules
                            </h3>

                            <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pr-1">
                                <template
                                    x-for="permission in (role.permissions ?? [])"
                                    :key="permission.name"
                                >
                                    <span
                                        class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold bg-gray-100 dark:bg-gray-700 border border-gray-200/60 dark:border-gray-600 text-gray-700 dark:text-gray-200 tracking-tight"
                                        x-text="permission.label"
                                    ></span>
                                </template>

                                <span
                                    x-show="!(role.permissions ?? []).length"
                                    class="text-xs font-medium text-gray-400 dark:text-gray-500 bg-gray-50 dark:bg-gray-700/50 px-2.5 py-1 rounded-lg border border-dashed border-gray-200 dark:border-gray-600"
                                >
                                    No permissions assigned.
                                </span>
                            </div>
                        </div>

                        <!-- Assigned Users Section -->
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4">
                            <h3 class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider mb-2.5">
                                Assigned Members
                            </h3>

                            <div class="space-y-2 max-h-40 overflow-y-auto pr-1 custom-scrollbar">

                                <template
                                    x-for="user in (role.users ?? [])"
                                    :key="user.id"
                                >
                                    <div class="flex items-center gap-3 rounded-xl border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-700/50 p-2.5 hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-150">

                                        <!-- Initials Avatar -->
                                        <div
                                            class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-100 dark:bg-gray-600 text-[10px] font-extrabold text-slate-700 dark:text-gray-200 uppercase border border-slate-200/40 dark:border-gray-500"
                                            x-text="user.name.split(' ').map(part => part[0]).slice(0, 2).join('').toUpperCase()"
                                        ></div>

                                        <div class="min-w-0">
                                            <p
                                                class="text-xs font-bold text-gray-800 dark:text-gray-100"
                                                x-text="user.name"
                                            ></p>

                                            <p
                                                class="text-[11px] font-medium text-gray-400 dark:text-gray-400 truncate -mt-0.5"
                                                x-text="user.email"
                                            ></p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Empty State -->
                                <div
                                    x-show="!(role.users ?? []).length"
                                    class="text-center py-4 bg-gray-50/50 dark:bg-gray-700/40 border border-dashed border-gray-100 dark:border-gray-600 rounded-xl"
                                >
                                    <p class="text-xs font-medium text-gray-400 dark:text-gray-500">
                                        No team accounts linked to this role yet.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal: Footer Actions -->
                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 mt-6 pt-4">

                        <button
                            type="button"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                            x-on:click="$dispatch('close-modal', 'roles-view')"
                        >
                            Close Details
                        </button>

                        <button
                            type="button"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-slate-900 dark:bg-emerald-600 hover:bg-slate-800 dark:hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                            x-on:click="
                                $dispatch('close-modal', 'roles-view');
                                $nextTick(() => {
                                    $dispatch('open-modal', 'roles-edit');
                                    window.dispatchEvent(
                                        new CustomEvent('set-edit-id', { detail: roleId })
                                    );
                                })
                            "
                        >
                            Edit Role
                        </button>
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
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl"
                >

                    {{-- Modal: Header Layout --}}
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">

                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center text-amber-600 dark:text-amber-400 border border-amber-100/50 dark:border-amber-800/50">
                                <i class="fa-solid fa-trash-can text-sm"></i>
                            </div>

                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                                    Move to Trash
                                </h2>

                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Archive inactive user roles temporarily to the trash log
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-trash')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- Modal: Content Area --}}
                    <div class="space-y-4">

                        {{-- Warning Box --}}
                        <div class="p-4 bg-amber-50/40 dark:bg-amber-900/20 border border-amber-100/60 dark:border-amber-800/60 rounded-xl flex gap-3.5">

                            <div class="text-amber-500 dark:text-amber-400 text-xl shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-tight">
                                    Are you sure you want to soft-delete this profile?
                                </p>

                                {{-- Highlighted Target Entity Badge --}}
                                <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-gray-700 border border-amber-200/60 dark:border-gray-600 rounded-lg shadow-xs">

                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 dark:bg-amber-400"></span>

                                    <span
                                        class="text-xs font-black text-slate-700 dark:text-gray-100 tracking-tight uppercase"
                                        x-text="trashRoleName || 'Target Role Profile'"
                                    ></span>
                                </div>

                                <p class="mt-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 leading-normal">
                                    This role's authorization capabilities will be suspended, but you can securely restore it later from your
                                    <strong class="text-gray-700 dark:text-gray-200 font-bold">
                                        Trash History
                                    </strong>
                                    tab.
                                </p>

                            </div>
                        </div>

                        {{-- Dynamic Error Alert Banner --}}
                        <div
                            x-show="trashError"
                            x-cloak
                            class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 p-3.5 text-xs font-semibold text-red-700 dark:text-red-300 tracking-tight flex items-center gap-2"
                        >
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            <span x-text="trashError"></span>
                        </div>

                    </div>

                    {{-- Modal: Footer Actions --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 mt-6 pt-4">

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-trash')"
                            x-bind:disabled="trashLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            x-on:click="moveToTrash()"
                            x-bind:disabled="trashLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-amber-600 hover:bg-amber-700 dark:bg-amber-600 dark:hover:bg-amber-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >

                            <span x-show="!trashLoading" class="flex items-center gap-1.5">
                                <i class="fa-regular fa-trash-can text-[11px]"></i>
                                Move to Trash
                            </span>

                            <span x-show="trashLoading" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>

                                Moving...
                            </span>

                        </button>
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
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl"
                >

                    {{-- Modal: Header Layout --}}
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">

                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-800/50">
                                <i class="fa-solid fa-rotate-left text-sm"></i>
                            </div>

                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                                    Restore Role
                                </h2>

                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Recover and reactivate an archived permission profile
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-restore')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- Modal: Content Area --}}
                    <div class="space-y-4">

                        {{-- Recovery Confirmation Box --}}
                        <div class="p-4 bg-emerald-50/40 dark:bg-emerald-900/20 border border-emerald-100/60 dark:border-emerald-800/60 rounded-xl flex gap-3.5">

                            <div class="text-emerald-500 dark:text-emerald-400 text-xl shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-check"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-tight">
                                    Confirm profile reactivation
                                </p>

                                {{-- Highlighted Target Entity Badge --}}
                                <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-gray-700 border border-emerald-200/60 dark:border-gray-600 rounded-lg shadow-xs">

                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 dark:bg-emerald-400"></span>

                                    <span
                                        class="text-xs font-black text-slate-700 dark:text-gray-100 tracking-tight uppercase"
                                        x-text="restoreRoleName || 'Target Role Profile'"
                                    ></span>
                                </div>

                                <p class="mt-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 leading-normal">
                                    This operation will immediately return this profile to your main
                                    <strong class="text-gray-700 dark:text-gray-200 font-bold">
                                        Role List
                                    </strong>,
                                    re-enabling all its assigned access states.
                                </p>

                            </div>
                        </div>

                        {{-- Dynamic Error Alert Banner --}}
                        <div
                            x-show="restoreError"
                            x-cloak
                            class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-800/50 p-3.5 text-xs font-semibold text-red-700 dark:text-red-300 tracking-tight flex items-center gap-2"
                        >
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            <span x-text="restoreError"></span>
                        </div>

                    </div>

                    {{-- Modal: Footer Actions --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 mt-6 pt-4">

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-restore')"
                            x-bind:disabled="restoreLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            x-on:click="restoreRole()"
                            x-bind:disabled="restoreLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-600 dark:hover:bg-emerald-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >

                            <span x-show="!restoreLoading" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-rotate-left text-[11px]"></i>
                                Restore Role
                            </span>

                            <span x-show="restoreLoading" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>

                                Restoring...
                            </span>

                        </button>
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
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl"
                >

                    {{-- Modal: Header Layout --}}
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">

                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 border border-red-100/50 dark:border-red-800/50">
                                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                            </div>

                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                                    Permanent Delete
                                </h2>

                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Irreversible destructive action to wipe out role history
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-force-delete')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    {{-- Modal: Content Area --}}
                    <div class="space-y-4">

                        {{-- Severe Alert Warning Box --}}
                        <div class="p-4 bg-red-50/40 dark:bg-red-900/20 border border-red-100/60 dark:border-red-800/60 rounded-xl flex gap-3.5">

                            <div class="text-red-500 dark:text-red-400 text-xl shrink-0 mt-0.5">
                                <i class="fa-solid fa-skull-crossbones"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-black text-red-700 dark:text-red-300 leading-tight tracking-tight">
                                    Warning: This action cannot be undone!
                                </p>

                                <p class="mt-2 text-xs font-semibold text-gray-700 dark:text-gray-200 leading-normal">
                                    Are you completely certain you want to purge this role structure from the databases?
                                </p>

                                {{-- Highlighted Destructive Target Badge --}}
                                <div class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1 bg-white dark:bg-gray-700 border border-red-200/60 dark:border-gray-600 rounded-lg shadow-xs">

                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 dark:bg-red-400"></span>

                                    <span
                                        class="text-xs font-black text-red-700 dark:text-red-300 tracking-tight uppercase"
                                        x-text="forceDeleteRoleName || 'Target Role Profile'"
                                    ></span>
                                </div>

                                <p class="mt-2.5 text-xs font-medium text-gray-500 dark:text-gray-400 leading-normal">
                                    Every system security privilege linked here will be permanently destroyed. Linked historical audit codes might break if active associations exist.
                                </p>

                            </div>
                        </div>

                        {{-- Dynamic Error Alert Banner --}}
                        <div
                            x-show="forceDeleteError"
                            x-cloak
                            class="rounded-xl bg-red-50 dark:bg-red-900/30 border border-red-100 dark:border-red-800/50 p-3.5 text-xs font-semibold text-red-700 dark:text-red-300 tracking-tight flex items-center gap-2"
                        >
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            <span x-text="forceDeleteError"></span>
                        </div>

                    </div>

                    {{-- Modal: Footer Actions --}}
                    <div class="flex justify-end gap-3 border-t border-gray-100 dark:border-gray-700 mt-6 pt-4">

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'roles-force-delete')"
                            x-bind:disabled="forceDeleteLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Cancel
                        </button>

                        <button
                            type="button"
                            x-on:click="forceDeleteRole()"
                            x-bind:disabled="forceDeleteLoading"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-red-600 hover:bg-red-700 dark:bg-red-600 dark:hover:bg-red-500 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                        >

                            <span x-show="!forceDeleteLoading" class="flex items-center gap-1.5">
                                <i class="fa-solid fa-dumpster-fire text-[11px]"></i>
                                Delete Permanently
                            </span>

                            <span x-show="forceDeleteLoading" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3 w-3 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle
                                        class="opacity-25"
                                        cx="12"
                                        cy="12"
                                        r="10"
                                        stroke="currentColor"
                                        stroke-width="4"
                                    ></circle>

                                    <path
                                        class="opacity-75"
                                        fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                    ></path>
                                </svg>

                                Deleting...
                            </span>

                        </button>
                    </div>

                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>
