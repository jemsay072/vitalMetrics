<div class="inline-flex rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm p-1 items-center transition-colors duration-300">

    {{-- List View Button --}}
    <button
        type="button"
        x-on:click="view = 'list'"
        :class="view === 'list'
            ? 'bg-blue-600 text-white font-semibold shadow-sm'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition duration-150 cursor-pointer"
    >
        <i class="fa-solid fa-list text-xs"></i>
        <span>List</span>
    </button>

    {{-- Grid View Button --}}
    <button
        type="button"
        x-on:click="view = 'grid'"
        :class="view === 'grid'
            ? 'bg-blue-600 text-white font-semibold shadow-sm'
            : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white'"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition duration-150 cursor-pointer"
    >
        <i class="fa-solid fa-table-cells-large text-xs"></i>
        <span>Grid</span>
    </button>

</div>
