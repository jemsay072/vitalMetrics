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

                    {{-- View/Grid Toggle --}}
                    <div class="hidden md:inline-flex">
                        <x-view-toggle />
                    </div>
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
                        class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md hover:border-blue-200 transition-all duration-200 cursor-pointer"
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
                                    class="h-12 w-12 shrink-0 rounded-full object-cover ring-2 ring-gray-100"
                                >

                                {{-- Initials Fallback --}}
                                <div
                                    x-show="!item.profile?.avatar"
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 ring-2 ring-blue-50"
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
                                        class="text-sm font-semibold text-gray-900 break-words"
                                        x-text="item.name"
                                    ></h3>

                                    <p
                                        class="text-xs text-gray-500 mt-1 break-all"
                                        x-text="item.email"
                                    ></p>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-1 shrink-0">

                                {{-- Edit --}}
                                <button
                                    type="button"
                                    x-on:click.stop="
                                        $dispatch('set-edit-id', item.id);
                                        $dispatch('open-modal', 'users-edit');
                                    "
                                    title="Edit User"
                                    aria-label="Edit User"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>

                                {{-- Deactivate --}}
                                <button
                                    type="button"
                                    x-on:click.stop="
                                        $dispatch('set-deactivated-id', item.id);
                                        $dispatch('open-modal', 'users-deactivate');
                                    "
                                    title="Deactivate User"
                                    aria-label="Deactivate User"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                >
                                    <i class="fa-regular fa-eye-slash text-sm"></i>
                                </button>

                            </div>
                        </div>

                        {{-- Divider --}}
                        <div class="border-t border-gray-100 my-3"></div>

                        {{-- Role + Status --}}
                        <div class="flex items-center justify-between gap-3">

                            {{-- Role Badge --}}
                            <div>
                                <p class="text-xs text-gray-500 mb-2">Role</p>

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
                                <p class="text-xs text-gray-500 mb-2">Status</p>

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
                    class="md:col-span-2 lg:col-span-3 text-center py-10 px-4 bg-white rounded-xl border border-gray-200"
                >
                    <i class="fa-solid fa-users text-3xl text-gray-300 mb-3"></i>

                    <p class="text-sm font-medium text-gray-700">
                        No users found.
                    </p>

                    <p class="text-xs text-gray-500 mt-1">
                        Try adjusting your search.
                    </p>
                </div>
            </div>

            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden xl:block relative bg-white rounded-xl border border-gray-200" x-show="view === 'list'">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <x-table.container>
                    {{-- Table Header --}}
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <x-table.row>
                            @foreach ($columns as $column)
                                <x-table.header-cell>
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        {{ $column['label'] }}
                                    </span>
                                </x-table.header-cell>
                            @endforeach

                            <x-table.header-cell>
                                <span class="text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Actions
                                </span>
                            </x-table.header-cell>
                        </x-table.row>
                    </thead>

                    {{-- Table Body --}}
                    <tbody class="divide-y divide-gray-100">
                        <template x-for="item in results" :key="item.id">
                            <x-table.row
                                class="bg-white hover:bg-blue-50/50 transition-colors duration-150 cursor-pointer"
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
                                                        class="h-10 w-10 shrink-0 rounded-full object-cover ring-2 ring-gray-100"
                                                    >

                                                    {{-- Initials Fallback --}}
                                                    <div
                                                        x-show="!item.profile?.avatar"
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 ring-2 ring-blue-50"
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
                                                        class="text-sm font-medium text-gray-800"
                                                        x-text="item.name"
                                                    ></span>

                                                </div>
                                            </x-table.cell>

                                        {{-- Other Text Columns --}}
                                        @else
                                            <x-table.cell>
                                                <span
                                                    class="text-sm text-gray-700"
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
                                                    class="text-sm text-gray-700"
                                                    x-text="item['{{ $column['key'] }}']?.['{{ $column['field'] }}'] ?? '-'"
                                                ></span>
                                            </x-table.cell>
                                        @endif

                                    @endif

                                @endforeach

                                {{-- Action Buttons --}}
                                <x-table.actions
                                    edit-event="set-edit-id"
                                    edit-modal="users-edit"
                                    deactivated-event="set-deactivated-id"
                                    deactivated-modal="users-deactivate"
                                />

                            </x-table.row>
                        </template>
                    </tbody>
                </x-table.container>
            </div>

            <!-- Modal deactivated-->
            <x-modal name="users-deactivated" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="{usersId: null}"
                    x-on:set-deactivated-id.window="
                        usersId = $event.detail;
                        fetch('/admin/users/' + usersId)
                            .then(res => res.json())
                            .then(data => users = data);
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Deactivate User</h2>
                    <div class="form" id="deactivated-users-form">
                        <form :action="'/admin/users/' + usersId" method="post">
                            @csrf
                            @method('PUT')
                            <p>Are you sure you want to deactivate this user?</p>
                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'users-deactivated')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md"
                                >
                                    Deactivate Workout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
