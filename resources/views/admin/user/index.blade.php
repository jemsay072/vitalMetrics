<x-app-layout>
    <div
        x-data="searchFilter({
            endpoint: '/admin/api/users'
        })"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="User"
                description="Manage system access, assign application roles, and monitor user activation controls."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                ]"
            >
                <x-slot name="actions">
                    <button
                        class="inline-flex items-center justify-center gap-2 px-4 h-[45px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                        x-on:click="window.location.href='{{ route('admin.users.create') }}'"
                    >
                        <i class="fa-solid fa-plus text-[11px]"></i>
                        <span>Add Record</span>
                    </button>

                    {{-- View/Grid Toggle --}}
                    <div class="hidden md:inline-flex">
                        <x-view-toggle />
                    </div>
                </x-slot>
            </x-page-header>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Search Filter --}}
            <x-table.toolbar>
                <x-table.search
                    x-model="search"
                    placeholder="Search User..."
                    @input.debounce.500="fetchData()"
                />
            </x-table.toolbar>

            {{-- Mobile / Card View --}}
            <div
                class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4"
                :class="view === 'list' ? 'xl:hidden' : ''"
            >
                <template x-for="item in results" :key="item.id">
                    <div
                        x-on:click="
                            window.location.href = '{{ route('admin.users.show', ['user' => '__USER_ID__']) }}'.replace('__USER_ID__', item.id)
                        "
                        class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4 shadow-sm hover:shadow-md hover:border-blue-200 dark:hover:border-blue-500 transition-all duration-200 cursor-pointer"
                    >

                        {{-- User Header --}}
                        <div class="flex items-start justify-between gap-3 mb-4">

                            {{-- Avatar + Name + Email --}}
                            <div class="flex items-center gap-3 min-w-0">

                                {{-- Uploaded Avatar --}}
                                <img
                                    x-show="item.profile?.avatar"
                                    x-bind:src="item.profile?.avatar
                                        ? '/storage/' + item.profile.avatar
                                        : ''"
                                    x-bind:alt="item.name"
                                    class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                >

                                {{-- Initials Fallback --}}
                                <div
                                    x-show="!item.profile?.avatar"
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-sm font-semibold text-blue-700 dark:text-blue-300 ring-2 ring-blue-50 dark:ring-blue-900/30"
                                    x-text="(item.name ?? 'U')
                                        .trim()
                                        .split(/\s+/)
                                        .map(part => part.charAt(0))
                                        .slice(0, 2)
                                        .join('')
                                        .toUpperCase()"
                                ></div>

                                <div class="min-w-0">
                                    <h3
                                        class="text-sm font-semibold text-gray-900 dark:text-white break-words"
                                        x-text="item.name"
                                    ></h3>

                                    <p
                                        class="text-xs text-gray-500 dark:text-gray-400 mt-1 break-all"
                                        x-text="item.email"
                                    ></p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 shrink-0">

                                {{-- Edit --}}
                                <a
                                    x-bind:href="'{{ route('admin.users.show', ['user' => '__USER_ID__']) }}'.replace('__USER_ID__', item.id) + '?edit=1'"
                                    x-on:click.stop
                                    title="Edit User"
                                    aria-label="Edit User"
                                    class="p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors"
                                >
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </a>

                                {{-- Deactivate --}}
                                <button
                                    type="button"
                                    x-show="item.status?.toLowerCase() === 'active' && item.id !== {{ auth()->id() }} && item.role?.name?.toLowerCase() !== 'admin'"
                                    x-cloak
                                    x-on:click.stop="
                                        $dispatch('set-deactivated-id', item.id);
                                        $dispatch('open-modal', 'users-deactivated');
                                    "
                                    title="Deactivate User"
                                    aria-label="Deactivate User"
                                    class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                >
                                    <i class="fa-regular fa-eye-slash text-sm"></i>
                                </button>


                                {{-- Re-activate --}}
                                <button
                                    type="button"
                                    x-show="item.status?.toLowerCase() === 'inactive'"
                                    x-cloak
                                    x-on:click.stop="
                                        $dispatch('set-reactivate-id', item.id);
                                        $dispatch('open-modal', 'users-reactivated');
                                    "
                                    title="Re-activate User"
                                    aria-label="Re-activate User"
                                    class="p-2 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 rounded-lg transition-colors"
                                >
                                    <i class="fa-solid fa-user-check text-sm"></i>
                                </button>

                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 my-3"></div>

                        {{-- Role + Status --}}
                        <div class="flex items-center justify-between gap-3">

                            {{-- Role Badge --}}
                            <div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Role
                                </p>

                                <span
                                    x-bind:class="{
                                        'bg-purple-100 text-purple-700 ring-purple-600/20':
                                            item.role?.name?.toLowerCase() === 'admin',
                                        'bg-blue-100 text-blue-700 ring-blue-600/20':
                                            item.role?.name?.toLowerCase() === 'user',
                                        'bg-gray-100 text-gray-600 ring-gray-500/20':
                                            !['admin', 'user'].includes(item.role?.name?.toLowerCase())
                                    }"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 ring-inset"
                                    x-text="item.role?.name ?? 'Unassigned'"
                                ></span>
                            </div>

                            {{-- Status Badge --}}
                            <div class="text-right">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                    Status
                                </p>

                                <span
                                    x-bind:class="{
                                        'bg-green-100 text-green-700 ring-green-600/20':
                                            item.status?.toLowerCase() === 'active',
                                        'bg-red-100 text-red-700 ring-red-600/20':
                                            item.status?.toLowerCase() === 'inactive',
                                        'bg-gray-100 text-gray-600 ring-gray-500/20':
                                            !['active', 'inactive'].includes(item.status?.toLowerCase())
                                    }"
                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 ring-inset"
                                    x-text="item.status ?? 'Unknown'"
                                ></span>
                            </div>

                        </div>
                    </div>
                </template>
                {{-- Empty State --}}
                <div
                    x-show="results.length === 0 && !loading"
                    class="md:col-span-2 lg:col-span-3 text-center py-10 px-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700"
                >
                    <i class="fa-solid fa-users text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>

                    <p class="text-sm font-medium text-gray-700 dark:text-gray-200">
                        No users found.
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Try adjusting your search.
                    </p>
                </div>
            </div>

            {{-- Desktop Table View --}}
            <div
                class="hidden xl:block relative bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700"
                x-show="view === 'list'"
            >

                {{-- Loading Overlay --}}
                <div
                    x-show="loading"
                    class="absolute inset-0 bg-gray-200/75 dark:bg-gray-900/75 flex items-center justify-center z-10"
                >
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>

                <x-table.container>

                    {{-- Table Header --}}
                    <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
                        <x-table.row>
                            @foreach ($columns as $column)
                                <x-table.header-cell>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        {{ $column['label'] }}
                                    </span>
                                </x-table.header-cell>
                            @endforeach

                            <x-table.header-cell>
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Actions
                                </span>
                            </x-table.header-cell>
                        </x-table.row>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                        <template x-for="item in results" :key="item.id">
                            <x-table.row
                                class="bg-white dark:bg-gray-800 hover:bg-blue-50/50 dark:hover:bg-gray-700/50 transition-colors duration-150 cursor-pointer"
                                x-on:click="
                                    window.location.href = '{{ route('admin.users.show', ['user' => '__USER_ID__']) }}'.replace('__USER_ID__', item.id)
                                "
                            >
                                @foreach ($columns as $column)

                                    {{-- Text Columns --}}
                                    @if ($column['type'] === 'text')

                                        {{-- User Status --}}
                                        @if ($column['key'] === 'status')
                                            <x-table.cell>
                                                <span
                                                    x-bind:class="{
                                                        'bg-green-100 text-green-700 ring-green-600/20':
                                                            item.status?.toLowerCase() === 'active',
                                                        'bg-red-100 text-red-700 ring-red-600/20':
                                                            item.status?.toLowerCase() === 'inactive',
                                                        'bg-gray-100 text-gray-600 ring-gray-500/20':
                                                            !['active', 'inactive'].includes(item.status?.toLowerCase())
                                                    }"
                                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 ring-inset"
                                                    x-text="item.status ?? 'Unknown'"
                                                ></span>
                                            </x-table.cell>

                                        {{-- User Name + Avatar --}}
                                        @elseif ($column['key'] === 'name')
                                            <x-table.cell>
                                                <div class="flex items-center gap-3">

                                                    {{-- Uploaded Avatar --}}
                                                    <img
                                                        x-show="item.profile?.avatar"
                                                        x-bind:src="item.profile?.avatar
                                                            ? '/storage/' + item.profile.avatar
                                                            : ''"
                                                        x-bind:alt="item.name"
                                                        class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-gray-100 dark:ring-gray-700"
                                                    >

                                                    {{-- Initials Fallback --}}
                                                    <div
                                                        x-show="!item.profile?.avatar"
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 dark:bg-blue-900/40 text-sm font-semibold text-blue-700 dark:text-blue-300 ring-2 ring-blue-50 dark:ring-blue-900/30"
                                                        x-text="(item.name ?? 'U')
                                                            .trim()
                                                            .split(/\s+/)
                                                            .map(part => part.charAt(0))
                                                            .slice(0, 2)
                                                            .join('')
                                                            .toUpperCase()"
                                                    ></div>

                                                    {{-- Name --}}
                                                    <span
                                                        class="text-sm font-medium text-gray-800 dark:text-gray-100"
                                                        x-text="item.name"
                                                    ></span>

                                                </div>
                                            </x-table.cell>

                                        {{-- Other Text Columns --}}
                                        @else
                                            <x-table.cell>
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300"
                                                    x-text="item['{{ $column['key'] }}'] ?? '-'"
                                                ></span>
                                            </x-table.cell>
                                        @endif

                                    {{-- Relationship Columns --}}
                                    @elseif ($column['type'] === 'relation')

                                        {{-- Role Badge --}}
                                        @if ($column['key'] === 'role')
                                            <x-table.cell>
                                                <span
                                                    x-bind:class="{
                                                        'bg-purple-100 text-purple-700 ring-purple-600/20':
                                                            item.role?.name?.toLowerCase() === 'admin',
                                                        'bg-blue-100 text-blue-700 ring-blue-600/20':
                                                            item.role?.name?.toLowerCase() === 'user',
                                                        'bg-gray-100 text-gray-600 ring-gray-500/20':
                                                            !['admin', 'user'].includes(item.role?.name?.toLowerCase())
                                                    }"
                                                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-wide ring-1 ring-inset"
                                                    x-text="item.role?.name ?? 'Unassigned'"
                                                ></span>
                                            </x-table.cell>

                                        {{-- Other Relationship Columns --}}
                                        @else
                                            <x-table.cell>
                                                <span
                                                    class="text-sm text-gray-700 dark:text-gray-300"
                                                    x-text="item['{{ $column['key'] }}']?.['{{ $column['field'] }}'] ?? '-'"
                                                ></span>
                                            </x-table.cell>
                                        @endif

                                    @endif

                                @endforeach

                                {{-- Action Buttons --}}
                                <x-table.actions
                                    :edit-url="route('admin.users.show', ['user' => '__USER_ID__']) . '?edit=1'"
                                    deactivated-event="set-deactivated-id"
                                    deactivated-modal="users-deactivated"
                                    deactivated-show-when="item.status?.toLowerCase() === 'active' && item.id !== {{ auth()->id() }} && item.role?.name?.toLowerCase() !== 'admin'"
                                    reactivate-event="set-reactivate-id"
                                    reactivate-modal="users-reactivated"
                                    reactivate-show-when="item.status?.toLowerCase() === 'inactive'"
                                />

                            </x-table.row>
                        </template>

                    </tbody>
                </x-table.container>
            </div>

            {{-- Deactivate User Modal --}}
            <x-modal name="users-deactivated" :show="false" maxWidth="md">
                <div
                    class=" bg-white dark:bg-gray-800 rounded-2xl"
                    x-data="{ usersId: null }"
                    x-on:set-deactivated-id.window="usersId = $event.detail"
                >
                    <!-- Modal Header Strip -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 px-6 pt-6 pb-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/30 flex items-center justify-center text-red-600 dark:text-red-400 shrink-0">
                                <i class="fa-solid fa-user-slash text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                                    Deactivate User
                                </h2>
                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Restrict system access for this account
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'users-deactivated')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Form Content -->
                    <div class="form p-6" id="deactivated-users-form">
                        <form
                            x-bind:action="'/admin/users/' + usersId + '/deactivate'"
                            method="POST"
                        >
                            @csrf
                            @method('PUT')

                            <!-- Warning Callout Box -->
                            <div class="p-4 rounded-xl bg-red-50/60 dark:bg-red-900/15 border border-red-100 dark:border-red-900/40 flex items-start gap-3">
                                <i class="fa-solid fa-triangle-exclamation text-red-500 dark:text-red-400 text-sm mt-0.5 shrink-0"></i>
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        Are you sure you want to deactivate this user?
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        This account will immediately lose access to the system, though their existing records and logs will remain preserved.
                                    </p>
                                </div>
                            </div>

                            <!-- Footer Actions Strip -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'users-deactivated')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 h-[42px] bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    <i class="fa-solid fa-user-slash text-[11px]"></i>
                                    <span>Confirm Deactivate</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            {{-- Re-activate User Modal --}}
            <x-modal name="users-reactivated" :show="false" maxWidth="md">
                <div
                    class="p-6 bg-white dark:bg-gray-800 rounded-2xl"
                    x-data="{ usersId: null }"
                    x-on:set-reactivate-id.window="usersId = $event.detail"
                >
                    <!-- Modal Header Strip -->
                    <div class="flex items-center justify-between border-b border-gray-100 dark:border-gray-700 pb-4 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shrink-0">
                                <i class="fa-solid fa-user-check text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 dark:text-white tracking-tight">
                                    Re-activate User
                                </h2>
                                <p class="text-[11px] font-medium text-gray-400 dark:text-gray-400 -mt-0.5">
                                    Restore system access for this account
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'users-reactivated')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-600 dark:hover:text-gray-200 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- Form Content -->
                    <div class="form" id="reactivated-users-form">
                        <form
                            x-bind:action="'/admin/users/' + usersId + '/reactivate'"
                            method="POST"
                        >
                            @csrf
                            @method('PUT')

                            <!-- Info Callout Box -->
                            <div class="p-4 rounded-xl bg-emerald-50/60 dark:bg-emerald-900/15 border border-emerald-100 dark:border-emerald-900/40 flex items-start gap-3">
                                <i class="fa-solid fa-circle-info text-emerald-600 dark:text-emerald-400 text-sm mt-0.5 shrink-0"></i>
                                <div class="space-y-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                        Are you sure you want to re-activate this user?
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                                        This account will immediately regain access to log in and use the system with their existing data.
                                    </p>
                                </div>
                            </div>

                            <!-- Footer Actions Strip -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'users-reactivated')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-slate-700 dark:text-gray-200 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 h-[42px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    <i class="fa-solid fa-user-check text-[11px]"></i>
                                    <span>Confirm Re-activate</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>
