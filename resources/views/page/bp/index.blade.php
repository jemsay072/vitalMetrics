<x-app-layout>
    <div
        x-data="searchFilter({
            endpoint: '/api/bp',
            exportEndpoint: '/api/bp/export',
            extractFilename: 'bp.csv',
            exportLabel: 'Extract',
            extractFields:[
                { key: 'systolic', label: 'Systolic' },
                { key: 'diastolic', label: 'Diastolic' },
                { key: 'pulse', label: 'Pulse' },
                { key: 'notes', label: 'Notes' },
                { key: 'formatted_reading', label: 'Reading Time' },
            ]
        })"
        x-on:page-changed="goToPage($event.detail)"
        x-on:per-page-changed="changePerPage($event.detail)"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="Blood Pressure"
                description="Log daily blood pressure readings, analyze trends, and maintain dynamic cardiovascular logs."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'bp-form')"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 h-[45px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
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
            <!---Search Filter -->
            <x-table.toolbar>
                <div class="flex-1 w-full min-w-0">
                    <x-table.search
                        x-model="search"
                        placeholder="Search Blood Pressure..."
                        @input.debounce.500="fetchData()"
                    />
                </div>

                <div class="shrink-0 w-full sm:w-auto flex items-center justify-end">
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <x-table.button type="button" icon="fa-solid fa-file-export">
                                <span class="text-xs font-bold" x-text="exportLabel"></span>
                                <i class="fa-solid fa-chevron-down text-[10px] ml-1 text-slate-400"></i>
                            </x-table.button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link href="#" x-on:click.prevent="extractCurrentData()" icon="fa-solid fa-file-lines">
                                Current Page
                            </x-dropdown-link>

                            <x-dropdown-link href="#" x-on:click.prevent="exportAll()" icon="fa-solid fa-file-export">
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
                        x-data="bpComponent"
                        x-on:click="
                            $dispatch('set-bp-id', item.id);
                            $dispatch('open-modal', 'bp-view');
                        "
                        class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                    >
                        <div
                            class="flex justify-between items-start mb-3"
                            :class="getColor(liveTerm())"
                        >
                            <h3 class="text-lg font-semibold text-gray-900" x-text="item.terms"></h3>
                            <div class="flex space-x-2">
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-edit-id', item.id);
                                        $dispatch('open-modal', 'bp-edit');
                                    "
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"
                                >
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-delete-id', item.id);
                                        $dispatch('open-modal', 'bp-delete');
                                    "
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                                >
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Systolic / Diastolic:</span>
                                <span class="font-medium ml-1" x-text="item.systolic + '/' + item.diastolic"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Heart Rate:</span>
                                <span class="font-medium ml-1" x-text="item.pulse"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Time:</span>
                                <span class="font-medium ml-1" x-text="item.formatted_time"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Risk Level:</span>
                                <span class="font-medium ml-1" x-text="item.risk_level"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Notes:</span>
                                <span class="font-medium ml-1" x-text="item.notes"></span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="results.length === 0 && !loading" class="text-center py-8 text-gray-500">
                    No bps found.
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

                                @foreach ($columns as $column )
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
                                        $dispatch('set-bp-id', item.id);
                                        $dispatch('open-modal', 'bp-view');
                                    "
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    @foreach ( $columns as $column )

                                        @if ($column['type'] === 'text' )
                                            <x-table.cell x-text="item['{{ $column['key'] }}']"></x-table.cell>
                                        @elseif ($column['type'] === 'reading' )
                                            <x-table.cell>
                                                <div class="flex flex-col">
                                                    <span class="text-gray-800" x-text="item.formatted_time"></span>
                                                    <span class="text-xs text-gray-500 italic" x-text="item.formatted_date"></span>
                                                </div>
                                            </x-table.cell>

                                        @elseif ($column['type'] === 'level' )
                                            <x-table.cell x-text="item.risk_level"></x-table.cell>
                                        @endif

                                    @endforeach
                                    <x-table.actions
                                        edit-event="set-edit-id"
                                        edit-modal="bp-edit"
                                        delete-event="set-delete-id"
                                        delete-modal="bp-delete"
                                    />
                                </x-table.row>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <x-table.row>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No bps found.
                                    </td>
                                </x-table.row>
                            </template>
                        </tbody>
                    </x-table.container>
                </div>
            </div>

            <!-- Pagination -->
            <x-pagination/>

            <!-- Modal toggle Create -->
            <x-modal name="bp-form" :show="false" maxWidth="lg">
                <div class="p-6 bg-white rounded-2xl" x-data="bpComponent">
                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <i class="fa-solid fa-heart-pulse text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Create Blood Pressure</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Log daily blood pressure readings and vitals</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'bp-form')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div id="bp-form-content">
                        <form action="{{route('bp.store')}}" method="post" class="space-y-4">
                            @csrf

                            <!-- 3-COLUMN INTUITIVE VITALS ROW METRIC GRID -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="systolic" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Systolic</label>
                                    <input type="number" name="systolic" id="systolic" x-model="form.systolic" required placeholder="120" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                                </div>
                                <div>
                                    <label for="diastolic" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Diastolic</label>
                                    <input type="number" name="diastolic" id="diastolic" x-model="form.diastolic" required placeholder="80" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                                </div>
                                <div>
                                    <label for="pulse" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Heart Rate</label>
                                    <input type="number" name="pulse" id="pulse" x-model="form.pulse" required placeholder="72" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                                </div>
                            </div>

                            <!-- READING TIME FIELD BOX -->
                            <div>
                                <label for="reading_time" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Reading Time / Timestamp</label>
                                <input type="time" name="reading_time" id="reading_time" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150">
                            </div>

                            <!-- PROGRESS HEALTH NOTES AREA -->
                            <div>
                                <label for="notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Context Notes</label>
                                <textarea name="notes" id="notes" rows="2" placeholder="e.g., Taken after resting for 5 minutes in the morning..." class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 placeholder-gray-400 shadow-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"></textarea>
                            </div>

                            <!-- PREMIUM EMBEDDED REAL-TIME RISK STATUS BANNER -->
                            <div x-show="liveTerm()" x-cloak class="mt-2 transition-all duration-200">
                                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Calculated Classification</span>
                                <div class="px-4 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-inner bg-opacity-10" :class="getColor(liveTerm())">
                                    <i class="fa-solid fa-circle-info text-xs"></i>
                                    <span x-text="liveTerm()"></span>
                                </div>
                                <input type="hidden" name="terms" :value="liveTerm()">
                            </div>

                            <!-- FOOTER ACTIONS ACTION STRIP -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <button type="button" class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none" x-on:click="$dispatch('close')">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center justify-center px-5 h-[42px] bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none">
                                    Save Vitals
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal View-->
            <x-modal name="bp-view" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl relative"
                    x-data="bpComponent()"
                    x-on:set-bp-id.window="
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500">
                                <i class="fa-solid fa-file-medical text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">View Blood Pressure</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Comprehensive vitals tracking log diagnostics</p>
                            </div>
                        </div>

                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'bp-view')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-blue-600"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Loading Blood Pressure data...</p>
                    </div>

                    <!--Content View Grid Layout-->
                    <div x-show="!loading" class="space-y-4" x-cloak>

                        <!-- Dynamic Risk Classification Banner Alert Header Area -->
                        <div x-show="liveEditTerm()" class="transition-all duration-200">
                            <div class="px-4 py-2.5 rounded-xl font-bold text-xs flex items-center gap-2 bg-opacity-15 shadow-sm border border-transparent" :class="getColor(liveEditTerm())">
                                <i class="fa-solid fa-heart text-[10px]"></i>
                                <span class="tracking-wide">Current Diagnosis Status:</span>
                                <span x-text="liveEditTerm()"></span>
                            </div>
                        </div>

                        <!-- COMPACT READINGS CARD INFORMATION CLUSTER CONTAINER -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                            <!-- Systolic Card Box -->
                            <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Systolic</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-gray-900 tracking-tight" x-text="bp.systolic"></span>
                                    <span class="text-[10px] font-bold text-gray-400">mmHg</span>
                                </div>
                            </div>

                            <!-- Diastolic Card Box -->
                            <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Diastolic</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-gray-900 tracking-tight" x-text="bp.diastolic"></span>
                                    <span class="text-[10px] font-bold text-gray-400">mmHg</span>
                                </div>
                            </div>

                            <!-- Pulse Card Box -->
                            <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Pulse Rate</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-gray-900 tracking-tight" x-text="bp.pulse"></span>
                                    <span class="text-[10px] font-bold text-gray-400">bpm</span>
                                </div>
                            </div>

                            <!-- Level Card Box -->
                            <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Risk Level</span>
                                <span class="block text-xs font-black text-gray-700 tracking-tight mt-1.5 uppercase" x-text="bp.risk_level || 'N/A'"></span>
                            </div>
                        </div>

                        <!-- FULL DESCRIPTION REMARKS CAPSULE -->
                        <div class="p-4 bg-gray-50/70 rounded-xl border border-gray-100">
                            <span class="text-xs font-bold text-gray-400 tracking-wide uppercase block mb-1.5 select-none">Log Entry Notes / Context</span>
                            <div class="flex items-start gap-2.5">
                                <i class="fa-regular fa-clipboard text-gray-400 text-sm mt-0.5 shrink-0"></i>
                                <p class="text-sm font-medium text-gray-700 leading-relaxed break-words" x-text="bp.notes || 'No description notes or custom patient context recorded for this log.'"></p>
                            </div>
                        </div>

                        <!-- MODAL ACTIONS WRAPPER -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                            <button type="button" class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none" x-on:click="$dispatch('close')">
                                Close Diagnostics
                            </button>
                        </div>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Edit-->
            <x-modal name="bp-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="bpComponent()"
                    x-on:set-edit-id.window="
                        weightId = $event.detail; {{-- Binago ko lang para sumunod sa property loading logic ng structure mo --}}
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <!-- Modal: Header Layout Strip -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Blood Pressure Edit</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Modify and correct your existing cardiovascular entries</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'bp-edit')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!--Loading State Spinner-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-blue-600"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Loading Blood Pressure data...</p>
                    </div>

                    <!--Form Input Fields Content-->
                    <div x-show="!loading" id="bp-edit-form" x-cloak>
                        <form :action="'/bp/' + bpId" method="post" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- 3-COLUMN VITALS INPUT SYNC MATRIX -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label for="bp_systolic" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Systolic</label>
                                    <input type="number" name="systolic" id="bp_systolic" x-model="bp.systolic" required class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                                <div>
                                    <label for="bp_diastolic" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Diastolic</label>
                                    <input type="number" name="diastolic" id="bp_diastolic" x-model="bp.diastolic" required class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                                <div>
                                    <label for="bp_pulse" class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Heart Rate</label>
                                    <input type="number" name="pulse" id="bp_pulse" x-model="bp.pulse" required class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                            </div>

                            <!-- READING TIME FIELD BOX -->
                            <div>
                                <label for="bp_reading_time" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Reading Time / Timestamp</label>
                                <input type="time" name="reading_time" id="bp_reading_time" x-model="bp.reading_time" required class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                            </div>

                            <!-- PROGRESS HEALTH NOTES AREA -->
                            <div>
                                <label for="bp_notes" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Context Notes</label>
                                <textarea name="notes" id="bp_notes" rows="2" x-model="bp.notes" class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150"></textarea>
                            </div>

                            <!-- LIVE REAL-TIME EDIT RISK STATUS BANNER -->
                            <div x-show="liveEditTerm()" class="mt-2 transition-all duration-200">
                                <span class="block text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Calculated Classification</span>
                                <div class="px-4 py-3 rounded-xl font-bold text-sm flex items-center gap-2 shadow-inner bg-opacity-10" :class="getColor(liveEditTerm())">
                                    <i class="fa-solid fa-circle-info text-xs"></i>
                                    <span x-text="liveEditTerm()"></span>
                                </div>
                                <input type="hidden" name="terms" x-model="bp.terms" :value="liveEditTerm()">
                            </div>

                            <!-- FOOTER ACTIONS ACTION STRIP -->
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
            <x-modal name="bp-delete" :show="false" maxWidth="md">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="bpComponent()"
                    x-on:set-delete-id.window="
                        weightId = $event.detail;
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <!-- Center-Aligned Warning Content Header Area -->
                    <div class="flex flex-col items-center text-center py-4">
                        <!-- Alert Warning Pulse Circle Hex Icon Wrapper -->
                        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        </div>

                        <h2 class="text-lg font-black text-gray-900 tracking-tight">Delete Blood Pressure Log</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1.5 max-w-xs leading-relaxed">
                            Are you absolutely sure you want to wipe out this blood pressure record entry? This destructive change cannot be undone.
                        </p>
                    </div>

                    <!-- Form Context Layout Controllers Footer Section Strip -->
                    <div id="delete-bp-form">
                        <form :action="'/bp/' + bpId" method="post" class="flex items-center justify-center gap-3 pt-3 mt-4 border-t border-gray-50">
                            @csrf
                            @method('DELETE')

                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'bp-delete')"
                                class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none w-full sm:w-auto"
                            >
                                No, Keep Log
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center justify-center px-5 h-[42px] bg-red-600 hover:bg-red-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none w-full sm:w-auto"
                            >
                                Yes, Delete Entry
                            </button>
                        </form>
                    </div>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>
