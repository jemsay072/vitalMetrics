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
                    description="Weight Tracker Description"
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/'],
                    ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'wt-form')"
                        type="button"
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                    >
                        + Weight Tracker
                    </button>
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

                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-3">
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
                </div>

            </x-table.toolbar>


            <!-- Mobile Card View (hidden on md+) -->
            <div class="md:hidden space-y-4">
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
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div class="overflow-x-auto">
                    <x-table.container>
                        <thead class="bg-gray-100 border-b border-default">
                            <x-table.row>
                                <x-table.header-cell> Weight </x-table.header-cell>
                                <x-table.header-cell> Notes </x-table.header-cell>
                                <x-table.header-cell> Date </x-table.header-cell>
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
                                    <x-table.cell x-text="item.weight"></x-table.cell>
                                    <x-table.cell x-text="item.notes"></x-table.cell>
                                    <x-table.cell x-text="item.formatted_date"></x-table.cell>
                                    <x-table.cell>
                                        <div class="flex items-center space-x-2 justify-end">
                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-edit-id', item.id)
                                                    $dispatch('open-modal', 'weight-edit');
                                                "
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-delete-id', item.id)
                                                    $dispatch('open-modal', 'weight-delete');
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
                <div class="p-6" x-data="weightComponent">
                    <h2 class="text-lg font-semibold mb-4">Weight Tracker Form</h2>
                    <div id="wt-form-content">
                        <!-- Form content will be loaded here via AJAX -->
                        <form action="{{route('weight.store')}}" method="post">
                            @csrf
                            <div class="mb-4">
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight</label>
                                <input type="number" name="weight" id="weight" x-model="form.weight" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="measurement_date" class="block text-sm font-medium text-gray-700">Measured Time</label>
                                <input type="time" name="measurement_date" id="measurement_date" x-model="form.measurement_date" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" x-model="form.notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
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
            <x-modal name="weight-view" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="weightComponent()"
                    x-on:set-weight-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId);
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Weight View</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading Blood Pressure data...</p>
                    </div>

                    <!--Content-->
                    <div x-show="!loading">
                        <div>
                                <span>Weight: </span>
                            <span x-text="weight.weight"></span>
                        </div>
                        <div>
                                <span>Measurement Date: </span>
                            <span x-text="weight.measurement_date"></span>
                        </div>
                        <div>
                                <span>Notes: </span>
                            <span x-text="weight.notes"></span>
                        </div>
                        <div class="flex items-end justify-end">
                            <button type="button" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-300 font-medium rounded text-sm px-4 py-2.5" x-on:click="$dispatch('close')">Cancel</button>
                        </div>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Edit-->
            <x-modal name="weight-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="weightComponent()"
                    x-on:set-edit-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId);
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Weight Edit</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading Weight data...</p>
                    </div>
                    <!--Content-->
                    <div x-show="!loading" id="weight-edit-form">
                        <form :action="'/weight/' + weightId" method="post">
                            @csrf
                            @method('PUT')

                            <!-- Activity Type -->
                            <div class="mb-4">
                                <label for="weight" class="block text-sm font-medium text-gray-700">Weight</label>
                                <input type="number" name="weight" id="weight" x-model="weight.weight" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="measurement_date" class="block text-sm font-medium text-gray-700">Measured Time</label>
                                <input type="time" name="measurement_date" id="measurement_date" x-model="weight.measurement_date" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                            </div>
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" x-model="weight.notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
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
             <x-modal name="weight-delete" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="weightComponent()"
                    x-on:set-delete-id.window="
                        weightId = $event.detail;
                        loadWeight(weightId)
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Delete Weight</h2>
                    <div class="form" id="delete-weight-form">
                        <form :action="'/weight/' + weightId" method="post">
                            @csrf
                            @method('DELETE')
                            <p>Are you sure you want to delete this Weight?</p>
                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'weight-delete')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md"
                                >
                                    Delete Weight
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
             </x-modal>
        </div>
    </div>
</x-app-layout>
