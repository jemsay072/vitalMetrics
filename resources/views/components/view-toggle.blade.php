<div class="inline-flex rounded-lg border border-gray-200 p-1">
    <button
        type="button"
        x-on:click="view = 'list'"
        :class="view === 'list' ?
        'bg-blue-500 text-white shadow-sm' :
        'text-gray-700 hover:bg-gray-100'"
        class="px-3 py-2 rounded-md"
    >
        <i class="fa-solid fa-list"></i>
        List
    </button>
    <button
        type="button"
        x-on:click="view = 'grid'"
        :class="view === 'grid' ?
        'bg-blue-500 text-white shadow-sm' :
        'text-gray-700 hover:bg-gray-100'"
        class="px-3 py-2 rounded-md"
    >
        <i class="fa-solid fa-table-cells-large"></i>
        Grid
    </button>
</div>
