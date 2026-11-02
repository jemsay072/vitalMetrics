<x-app-layout>
    <div
        x-data="
        searchFilter({
            endpoint: '/api/weight',
            exportEndpoint: '/api/weight/export',
            extractFilename: 'weight.csv',
            exportLabel: 'Extract',
            extractFields: [
                { key: 'weight', label: 'Weight' },
                { key: 'formatted_date', label: 'Measurement Date' },
            ]
        })"
        x-on:page-changed="goToPage($event.detail)"
        x-on:per-page-changed="changePerPage($event.detail)"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                    title="Weight Tracker"
                    description="Monitor body mass fluctuations, log daily weights, and track long-term health progress."
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/'],
                    ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'wt-form')"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 h-[45px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                    >
                        <i class="fa-solid fa-plus text-[11px]"></i>
                        <span>Weight Tracker</span>
                    </button>

                    {{-- View/Grid Toggle --}}
                    <div class="hidden md:inline-flex">
                        <x-view-toggle />
                    </div>
                </x-slot>
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!---Search Filter -->
            <x-table.toolbar>

                <x-table.search
                    x-model="search"
                    placeholder="Search Weight..."
                    @input.debounce.500="fetchData()"
                />

                <div class="shrink-0 w-full sm:w-auto flex items-center justify-end">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <x-table.button
                                type="button"
                                icon="fa-solid fa-file-export"
                            >
                                <span x-text="exportLabel"></span>
                                <i class="fa-solid fa-chevron-down text-[10px] ml-1 text-slate-400"></i>
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
                </div>

            </x-table.toolbar>


            <!-- Mobile Card View (hidden on md+) -->
            <div
                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4"
                :class="view === 'list' ? 'md:hidden' : ''"
            >
                <template x-for="item in results" :key="item.id">
                    <div
                        x-data="weightComponent"
                        x-on:click="
                            $dispatch('set-weight-id', item.id);
                            $dispatch('open-modal', 'weight-view');
                        "
                        class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                    >
                        <div
                            class="flex justify-between items-start mb-3"
                            :class="getColor(liveTerm())"
                        >
                            <div class="flex space-x-2">
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-edit-id', item.id);
                                        $dispatch('open-modal', 'weight-edit');
                                    "
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"
                                >
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-delete-id', item.id);
                                        $dispatch('open-modal', 'weight-delete');
                                    "
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                                >
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>


                        <div>
                            <span class="text-gray-500">Weight:</span>
                            <span class="font-medium ml-1" x-text="item.weight"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Measurement Date:</span>
                            <span class="font-medium ml-1" x-text="item.formatted_date || 'N/A'"></span>
                        </div>
                        <div>
                            <span class="text-gray-500">Notes:</span>
                            <span class="font-medium ml-1" x-text="item.notes || 'N/A'"></span>
                        </div>
                    </div>
                </template>
                <div x-show="results.length === 0 && !loading" class="text-center py-8 text-gray-500">
                    No Weight found.
                </div>
            </div>
            <!-- Desktop Table View (hidden on mobile) -->
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200" x-show="view === 'list'">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div class="overflow-x-auto">
                    <x-table.container>
                        <thead class="bg-gray-100 border-b border-default">
                            <x-table.row>
                                @foreach ($columns as $column)
                                    <x-table.header-cell> {{ $column['label'] }} </x-table.header-cell>
                                @endforeach
                                <x-table.header-cell> Action </x-table.header-cell>
                            </x-table.row>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-default">
                            <template x-for="item in results" :key="item.id">
                                <x-table.row
                                    x-data
                                    x-on:click="
                                        $dispatch('set-weight-id', item.id);
                                        $dispatch('open-modal', 'weight-view');
                                    "
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    @foreach ( $columns as $column )

                                        @if ($column['type'] === 'text')
                                            <x-table.cell x-text="item['{{ $column['key'] }}']"></x-table.cell>
                                        @else
                                            <x-table.cell x-text="item.formatted_date"></x-table.cell>
                                        @endif
                                    @endforeach

                                    <x-table.actions
                                        edit-event="set-edit-id"
                                        edit-modal="weight-edit"
                                        delete-event="set-delete-id"
                                        delete-modal="weight-delete"
                                    />

                                </x-table.row>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No bps found.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </x-table.container>
                </div>
            </div>

            <!-- Pagination -->
            <x-pagination/>

            <!-- Modal toggle Create -->
            <x-modal name="wt-form" :show="false" maxWidth="lg">
                <div class="p-6 bg-white rounded-2xl" x-data="weightComponent">
                    <!-- Header Strip Layer -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-plus text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Create Weight</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Add a new record metric entry to your health log</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'wt-form')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div id="wt-form-content">
                        <form action="{{route('weight.store')}}" method="post" class="space-y-4">
                            @csrf
                            <div>
                                <label for="weight" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" id="weight" x-model="form.weight" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                            </div>
                            <div>
                                <label for="measurement_date" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Measured Time / Date</label>
                                <input type="time" name="measurement_date" id="measurement_date" x-model="form.measurement_date" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                            </div>
                            <div>
                                <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Progress Notes</label>
                                <textarea name="notes" id="notes" x-model="form.notes" rows="3" placeholder="Add optional daily context or remarks..." class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"></textarea>
                            </div>

                            <!-- Footer Buttons Strip -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none" x-on:click="$dispatch('close')">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center px-5 h-[42px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none">
                                    Submit Entry
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal View-->
            <x-modal name="weight-view" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl relative"
                    x-data="weightComponent()"
                    x-on:set-weight-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId);
                    "
                >
                    <!-- MODAL HEADER SECTION -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500">
                                <i class="fa-solid fa-weight-scale text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Weight Details</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Logs and health records history</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'weight-view')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!-- LOADING STATE OVERLAY -->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-orange-500"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Retrieving data logs...</p>
                    </div>

                    <!-- MAIN GRID CONTENT AREA -->
                    <div x-show="!loading" class="space-y-4" x-cloak>
                        <!-- Highlight Main Metric Container Row -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Weight Card Badge Box -->
                            <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-100 flex flex-col justify-between min-h-[90px]">
                                <span class="text-xs font-bold text-gray-400 tracking-wide uppercase select-none">Current Weight</span>
                                <div class="flex items-baseline gap-1 mt-1">
                                    <span class="text-2xl font-black text-gray-900 tracking-tight" x-text="weight.weight || '0.00'"></span>
                                    <span class="text-xs font-bold text-gray-500">kg</span>
                                </div>
                            </div>

                            <!-- Date Card Badge Box -->
                            <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-100 flex flex-col justify-between min-h-[90px]">
                                <span class="text-xs font-bold text-gray-400 tracking-wide uppercase select-none">Log Date / Time</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <i class="fa-regular fa-calendar text-gray-400 text-sm"></i>
                                    <span class="text-sm font-bold text-gray-800 tracking-tight" x-text="weight.measurement_date || 'No Date'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Full-Width Notes Capsule Section -->
                        <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                            <span class="text-xs font-bold text-gray-400 tracking-wide uppercase select-none block mb-1.5">User / Admin Notes</span>
                            <div class="flex items-start gap-2.5">
                                <i class="fa-regular fa-comment-dots text-gray-400 text-sm mt-0.5 shrink-0"></i>
                                <p class="text-sm font-medium text-gray-700 leading-relaxed break-words" x-text="weight.notes || 'No description or remarks provided for this entry.'"></p>
                            </div>
                        </div>

                        <!-- MODAL ACTIONS SLOT WRAPPER BUTTONS -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'weight-view')"
                                class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                            >
                                Close View
                            </button>
                        </div>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Edit-->
            <x-modal name="weight-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="weightComponent()"
                    x-on:set-edit-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId);
                    "
                >
                    <!-- Header Strip Layer -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Weight Edit</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Modify and correct your existing health record logs</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'weight-edit')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-blue-600"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Loading Weight data...</p>
                    </div>

                    <!--Content-->
                    <div x-show="!loading" id="weight-edit-form" x-cloak>
                        <form :action="'/weight/' + weightId" method="post" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label for="weight_edit" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Weight (kg)</label>
                                <input type="number" step="0.01" name="weight" id="weight_edit" x-model="weight.weight" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                            </div>
                            <div>
                                <label for="measurement_date_edit" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Measured Time / Date</label>
                                <input type="time" name="measurement_date" id="measurement_date_edit" x-model="weight.measurement_date" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                            </div>
                            <div>
                                <label for="notes_edit" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Progress Notes</label>
                                <textarea name="notes" id="notes_edit" x-model="weight.notes" rows="3" class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150"></textarea>
                            </div>

                            <!-- Footer Buttons Strip -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none" x-on:click="$dispatch('close')">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center px-5 h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>


            <!-- Modal Delete-->
            <x-modal name="weight-delete" :show="false" maxWidth="md">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="weightComponent()"
                    x-on:set-delete-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId)
                    "
                >
                    <!-- Content Wrapper Layer -->
                    <div class="flex flex-col items-center text-center py-4">
                        <!-- Warning Accent Hex Icon Marker Circle -->
                        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        </div>

                        <h2 class="text-lg font-black text-gray-900 tracking-tight">Delete Weight Entry</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1.5 max-w-xs leading-relaxed">
                            Are you absolutely sure you want to delete this weight log record? This action cannot be reversed.
                        </p>
                    </div>

                    <!-- Forms Action Controllers Footer Strip Section -->
                    <form :action="'/weight/' + weightId" method="post" class="flex items-center justify-center gap-3 pt-3 mt-4 border-t border-gray-50">
                        @csrf
                        @method('DELETE')

                        <button
                            type="button"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none w-full sm:w-auto"
                            x-on:click="$dispatch('close-modal', 'weight-delete')"
                        >
                            No, Keep It
                        </button>
                        <button
                            type="submit"
                            class="inline-flex items-center justify-center px-5 h-[42px] bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none w-full sm:w-auto"
                        >
                            Yes, Delete Log
                        </button>
                    </form>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>
