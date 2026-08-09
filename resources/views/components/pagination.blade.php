@props([
    'containerClass' => 'bg-white px-4 py-3 sm:px-6 rounded-xl border border-gray-200',
    'perPageOptions' => [10, 25, 50],
    'pageChangeEvent' => 'page-changed',
    'perPageChangeEvent' => 'per-page-changed',
])
<div
    x-show="lastPage > 1"
    {{ $attributes->merge([
        'class' => $containerClass
    ]) }}
>
    <div class="flex items-center justify-between">
        <!-- Mobile pagination -->
        <div class="flex flex-1 justify-between sm:hidden">
            <button
                x-on:click="$dispatch('{{ $pageChangeEvent }}', currentPage - 1)"
                :disabled="currentPage === 1"
                class="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Previous
            </button>
            <span class="text-sm text-gray-700 self-center">
                <span x-text="currentPage"></span> of <span x-text="lastPage"></span>
            </span>
            <button
                x-on:click="$dispatch('{{ $pageChangeEvent }}', currentPage + 1)"
                :disabled="currentPage === lastPage"
                class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                Next
            </button>
        </div>

        <!-- Desktop pagination -->
        <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <p class="text-sm text-gray-700">
                    Showing <span class="font-medium" x-text="(currentPage - 1) * perPage + 1"></span> to
                    <span class="font-medium" x-text="Math.min(currentPage * perPage, total)"></span> of
                    <span class="font-medium" x-text="total"></span> results
                </p>
                <div class="relative" @click.outside="perPageOpen = false">

                    <!-- Current per-page value -->
                    <button
                        type="button"
                        x-on:click="perPageOpen = !perPageOpen"
                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-white px-2.5 py-1.5 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1"
                    >
                        <span x-text="perPage"></span>
                        <svg
                            class="h-4 w-4"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="m19 9-7 7-7-7"
                            />
                        </svg>
                    </button>

                    <!-- Dropdown -->
                    <div
                        x-show="perPageOpen"
                        x-cloak
                        class="absolute left-0 bottom-11 z-50 mt-2 w-20 rounded-md border border-gray-200 bg-white shadow-lg"
                    >
                        <template x-for="option in {{ json_encode($perPageOptions) }}" :key="option">

                            <button
                                type="button"
                                x-on:click="$dispatch('{{ $perPageChangeEvent }}', option); perPageOpen = false"
                                :class="perPage === option
                                ? 'bg-gray-50 font-medium text-gray-900'
                                : 'text-gray-700 hover:bg-gray-50'"
                                class="flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-50"
                            >
                                <span
                                    x-show="perPage === option"
                                    class="flex h-4 w-4 shrink-0 items-center justify-center"
                                >
                                    ✓
                                </span>

                                <span class="w-4 shrink-0" x-show="perPage !== option"></span>

                                <span x-text="option"></span>
                            </button>

                        </template>
                    </div>

                </div>
            </div>
            <div>
                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                    <button
                        x-on:click="$dispatch('{{ $pageChangeEvent }}', currentPage - 1)"
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
                            x-on:click="$dispatch('{{ $pageChangeEvent }}', page)"
                            :class="page === currentPage ? 'relative z-10 inline-flex items-center bg-blue-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600' : 'relative inline-flex items-center px-4 py-2 text-sm font-semibold text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:z-20 focus:outline-offset-0'"
                            x-text="page"
                        ></button>
                    </template>

                    <button
                        x-on:click="$dispatch('{{ $pageChangeEvent }}', currentPage + 1)"
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
