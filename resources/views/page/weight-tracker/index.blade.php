<x-app-layout>
    <div x-data="searchFilter({ endpoint: '/api/weight', extractFilename: 'weight.csv', exportLabel: 'Extract', extractFields: [
        { key: 'weight', label: 'Weight' },
        { key: 'measurement_date', label: 'Measurement Date' },
    ]})">
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
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search Weight..."
                        @input.debounce.500="fetchData()"
                        class="bg-white border border-gray-300 text-gray-900 placeholder:text-gray-500 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 sm:p-2.5 rounded-xl text-base shadow-sm"
                    >
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-3">
                    <button
                        type="button"
                        x-on:click="extractData()"
                        :disabled="loading || !results.length"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-500 bg-blue-500 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-600 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <i class="fa-solid fa-file-export"></i>
                        <span x-text="exportLabel"></span>
                    </button>
                    <!-- Result summary removed for now -->
                </div>
            </div>

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
                            <span class="font-medium ml-1" x-text="item.measurement_date || 'N/A'"></span>
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
                    <table class="w-full text-sm text-left rtl:text-right text-body text-gray-600 min-w-[600px]">
                        <thead class="bg-gray-100 border-b border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">Weight</th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">Notes</th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">Date</th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-default">
                            <template x-for="item in results" :key="item.id">
                                <tr
                                    x-data
                                    x-on:click="
                                        $dispatch('set-weight-id', item.id);
                                        $dispatch('open-modal', 'weight-view');
                                    "
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    <td class="px-6 py-4 font-medium" x-text="item.weight"></td>
                                    <td class="px-6 py-4 font-medium" x-text="item.notes"></td>
                                    <td class="px-6 py-4 font-medium" x-text="item.created_at"></td>
                                    <td class="px-6 py-4 font-medium">
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
                                    </td>
                                </tr>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No bps found.
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
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
                    <div class="form" id="delete-bp-form">
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
