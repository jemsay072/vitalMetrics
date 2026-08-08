<x-app-layout>
    <div x-data="searchFilter({
        endpoint: '/api/bp',
        exportEndpoint: '/api/bp/export',
        extractFilename: 'bp.csv',
        exportLabel: 'Extract',
        extractFields:[
            { key: 'systolic', label: 'Systolic' },
            { key: 'diastolic', label: 'Diastolic' },
            { key: 'pulse', label: 'Pulse' },
            { key: 'notes', label: 'Notes' },
            { key: 'reading_time', label: 'Reading Time' },
        ]})">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="Blood Pressure"
                description="Blood Pressure Description"
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'bp-form')"
                        type="button"
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                    >
                        + Blood Pressure
                    </button>
                </x-slot>
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!---Search Filter -->
            <x-table.toolbar>
                <x-table.search
                    x-model="search"
                    placeholder="Search Blood Pressure..."
                    @input.debounce.500="fetchData()"
                />
                <x-table.button
                    type="button"
                    x-on:click="extractCurrentData()"
                    x-bind:disabled="loading || !results.length"
                    icon="fa-solid fa-file-export"
                >
                    <span x-text="exportLabel"></span>
                </x-table.button>
            </x-table.toolbar>

            <!-- Mobile Card View (hidden on md+) -->
            <div class="md:hidden space-y-4">
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
                                <span class="text-gray-500">Calories:</span>
                                <span class="font-medium ml-1" x-text="item.calories_burned || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Time:</span>
                                <span class="font-medium ml-1" x-text="item.reading_time"></span>
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
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div class="overflow-x-auto">
                    <x-table.container>
                        <thead class="bg-gray-100 border-b border-default">
                            <x-table.row>
                                <x-table.header-cell> Systolic </x-table.header-cell>
                                <x-table.header-cell> Diastolic </x-table.header-cell>
                                <x-table.header-cell> Heart Rate </x-table.header-cell>
                                <x-table.header-cell> Reading Time </x-table.header-cell>
                                <x-table.header-cell> Level </x-table.header-cell>
                                <x-table.header-cell> Notes </x-table.header-cell>
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
                                    <x-table.cell x-text="item.systolic"></x-table.cell>
                                    <x-table.cell x-text="item.diastolic"></x-table.cell>
                                    <x-table.cell x-text="item.pulse"></x-table.cell>
                                    <x-table.cell x-text="item.reading_time"></x-table.cell>
                                    <x-table.cell x-text="item.risk_level"></x-table.cell>
                                    <x-table.cell x-text="item.notes"></x-table.cell>
                                    <x-table.cell >
                                        <div class="flex items-center space-x-2 justify-end">
                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-edit-id', item.id)
                                                    $dispatch('open-modal', 'bp-edit');
                                                "
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-delete-id', item.id)
                                                    $dispatch('open-modal', 'bp-delete');
                                                "
                                                class="p-2 text-red-600 hover:bg-red-100 rounded-lg"
                                            >
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </div>
                                    </x-table.cell>
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
            <div x-show="lastPage > 1" class="bg-white px-4 py-3 sm:px-6 rounded-xl border border-gray-200">
                <div class="flex items-center justify-between">
                    <!-- Mobile pagination -->
                    <div class="flex flex-1 justify-between sm:hidden">
                        <button
                            x-on:click="prevPage()"
                            :disabled="currentPage === 1"
                            class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Previous
                        </button>
                        <span class="text-sm text-gray-700 self-center">
                            <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
                        </span>
                        <button
                            x-on:click="nextPage()"
                            :disabled="currentPage === lastPage"
                            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next
                        </button>
                    </div>

                    <!-- Desktop pagination -->
                    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm text-gray-700">
                                Showing <span class="font-medium" x-text="(currentPage - 1) * perPage + 1"></span> to
                                <span class="font-medium" x-text="Math.min(currentPage * perPage, total)"></span> of
                                <span class="font-medium" x-text="total"></span> results
                            </p>
                        </div>
                        <div>
                            <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                <button
                                    x-on:click="prevPage()"
                                    :disabled="currentPage === 1"
                                    class="relative inline-flex items-center rounded-l-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Previous</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M12.79 5.23a.75.75 0 01-.02 1.06L8.832 10l3.938 3.71a.75.75 0 11-1.04 1.08l-4.5-4.25a.75.75 0 010-1.08l4.5-4.25a.75.75 0 011.06.02z" clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <template x-for="page in Array.from({length: Math.min(5, lastPage)}, (_, i) => {
                                    const startPage = Math.max(1, currentPage - 2);
                                    return startPage + i;
                                }).filter(p => p <= lastPage)" :key="page">
                                    <button
                                        x-on:click="goToPage(page)"
                                        :class="page === currentPage ? 'relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'"
                                        x-text="page"
                                    ></button>
                                </template>

                                <button
                                    x-on:click="nextPage()"
                                    :disabled="currentPage === lastPage"
                                    class="relative inline-flex items-center rounded-r-md px-2 py-2 text-gray-400 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span class="sr-only">Next</span>
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal toggle Create -->
            <x-modal name="bp-form" :show="false" maxWidth="lg">
                <div class="p-6" x-data="bpComponent">
                    <h2 class="text-lg font-semibold mb-4">Blood Pressure Form</h2>
                    <div id="bp-form-content">
                        <!-- Form content will be loaded here via AJAX -->
                        <form action="{{route('bp.store')}}" method="post">
                            @csrf
                            <div class="mb-4">
                                <label for="systolic" class="block text-sm font-medium text-gray-700">Systolic</label>
                                <input type="number" name="systolic" id="systolic" x-model="form.systolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="diastolic" class="block text-sm font-medium text-gray-700">Diastolic</label>
                                <input type="number" name="diastolic" id="diastolic" x-model="form.diastolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="pulse" class="block text-sm font-medium text-gray-700">Heart Rate</label>
                                <input type="number" name="pulse" id="pulse" x-model="form.pulse" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="reading_time" class="block text-sm font-medium text-gray-700">Reading Time</label>
                                <input type="time" name="reading_time" id="reading_time" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            </div>
                            <div class="mb-4">
                                <p class="mt-2 font-bold"
                                    :class="getColor(liveTerm())"
                                    x-show="liveTerm()"
                                    x-text="liveTerm()">
                                </p>
                                <input type="hidden" name="terms" :value="liveTerm()">
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2">
                                <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded text-sm px-4 py-2.5" x-on:click="$dispatch('close')">Cancel</button>
                                <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

             <!-- Modal View-->
             <x-modal name="bp-view" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="bpComponent()"
                    x-on:set-bp-id.window="
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Blood Pressure View</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading Blood Pressure data...</p>
                    </div>

                    <!--Content-->
                    <div x-show="!loading">
                        {{-- <span x-init="console.log(bp)"></span> --}}
                        <p class="mt-2 font-bold"
                            :class="getColor(liveEditTerm())"
                            x-show="liveEditTerm()"
                            x-text="liveEditTerm()">
                        </p>
                        <div class="grid grid-cols-2 gap-4 text-sm mt-2">
                            <div>
                                {{-- <p>ID: <span x-text="bp.id"></span></p> --}}
                                <span>Systolic: </span>
                                <span x-text="bp.systolic"></span>
                            </div>
                            <div>
                                <span>Diastolic:</span>
                                <span x-text="bp.diastolic"></span>
                            </div>
                            <div>
                               <span>Pulse:</span>
                               <span x-text="bp.pulse"></span>
                            </div>
                            <div>
                               <span>Level:</span>
                               <span x-text="bp.risk_level"></span>
                            </div>
                        </div>
                        <div class="flex flex-col mt-2">
                            <p>Notes:</p>
                            <p x-text="bp.notes"></p>
                        </div>
                        <div class="flex items-end justify-end">
                            <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded text-sm px-4 py-2.5" x-on:click="$dispatch('close')">Cancel</button>
                        </div>
                    </div>
                </div>
             </x-modal>

             <!-- Modal Edit-->
             <x-modal name="bp-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="bpComponent()"
                    x-on:set-edit-id.window="
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Blood Pressure Edit</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading Blood Pressure data...</p>
                    </div>

                    <!--Content-->
                    <div x-show="!loading" id="bp-edit-form">
                        <form :action="'/bp/' + bpId" method="post">
                            @csrf
                            @method('PUT')
                            <!-- Activity Type -->
                            <div class="mb-4">
                                <label for="systolic" class="block text-sm font-medium text-gray-700">Systolic</label>
                                <input type="number" name="systolic" id="systolic" x-model="bp.systolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="diastolic" class="block text-sm font-medium text-gray-700">Diastolic</label>
                                <input type="number" name="diastolic" id="diastolic" x-model="bp.diastolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="pulse" class="block text-sm font-medium text-gray-700">Heart Rate</label>
                                <input type="number" name="pulse" id="pulse" x-model="bp.pulse" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="reading_time" class="block text-sm font-medium text-gray-700">Reading Time</label>
                                <input type="time" name="reading_time" id="reading_time" x-model="bp.reading_time" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3" x-model="bp.notes" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                            </div>
                            <div class="mb-4">
                                <p class="mt-2 font-bold"
                                    :class="getColor(liveEditTerm())"
                                    x-show="liveEditTerm()"
                                    x-text="liveEditTerm()">
                                </p>
                                <input type="hidden" name="terms" x-model="bp.terms"  :value="liveEditTerm()">
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2">
                                <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded text-sm px-4 py-2.5" x-on:click="$dispatch('close')">Cancel</button>
                                <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded text-sm px-4 py-2.5">Save</button>
                            </div>
                        </form>
                    </div>

                </div>
             </x-modal>

             <!-- Modal Delete-->
             <x-modal name="bp-delete" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="bpComponent()"
                    x-on:set-delete-id.window="
                        bpId = $event.detail;
                        loadBp(bpId)
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Delete Blood Pressure</h2>
                    <div class="form" id="delete-bp-form">
                        <form :action="'/bp/' + bpId" method="post">
                            @csrf
                            @method('DELETE')
                            <p>Are you sure you want to delete this Blood Pressure?</p>
                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'bp-delete')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md"
                                >
                                    Delete Blood Pressure
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
             </x-modal>
        </div>
    </div>
</x-app-layout>
