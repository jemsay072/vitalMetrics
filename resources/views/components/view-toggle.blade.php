<div class="inline-flex rounded-xl border border-gray-200 bg-white shadow-sm p-1 items-center">
    <!-- List Layout Element Button -->
    <button
        type="button"
        x-on:click="view = 'list'"
        :class="view === 'list' ?
        'bg-blue-600 text-white font-semibold shadow-sm' :
        'text-gray-700 hover:bg-gray-100 hover:text-gray-900'"
        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition duration-150 cursor-pointer"
    >
        <i class="fa-solid fa-list text-xs"></i>
        <span>List</span>
    </button>

    <!-- Grid Layout Element Button -->
    <button
        type="button"
        x-on:click="view = 'grid'"
        :class="view === 'grid' ?
        'bg-blue-600 text-white font-semibold shadow-sm' :
        'text-gray-700 hover:bg-gray-100 hover:text-gray-900'"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-medium rounded-lg transition duration-150 cursor-pointer"
    >
        <i class="fa-solid fa-table-cells-large text-xs"></i>
        <span>Grid</span>
    </button>
</div>
