<x-app-layout>
    <div
        x-data="searchFilter({
            endpoint: '/api/workouts',
            exportEndpoint: '/api/workouts/export',
            extractFilename: 'workouts.csv',
            exportLabel: 'Extract',
            extractFields: [
                { key: 'activity_type', label: 'Activity Type' },
                { key: 'duration_minutes', label: 'Duration' },
                { key: 'distance_km', label: 'Distance' },
                { key: 'calories_burned', label: 'Calories Burned' }
            ]
        })"
        x-on:page-changed="goToPage($event.detail)"
        x-on:per-page-changed="changePerPage($event.detail)"
    >
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="Workout"
                description="Track your fitness activities, record exercises, and monitor active energy burn metrics."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'workout-form')"
                        type="button"
                        class="inline-flex items-center justify-center gap-2 px-4 h-[45px] bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded-xl text-sm transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                    >
                        <i class="fa-solid fa-plus text-[11px]"></i> <span>Workout</span>
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
                    placeholder="Search workouts..."
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
                                x-on:click="extractCurrentData()"
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
                        x-data
                        x-on:click="
                            $dispatch('set-workout-id', item.id);
                            $dispatch('open-modal', 'workout-view');
                        "
                        class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm hover:shadow-md transition-shadow cursor-pointer"
                    >
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="text-lg font-semibold text-gray-900" x-text="item.activity_type"></h3>
                            <div class="flex space-x-2">
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-edit-id', item.id);
                                        $dispatch('open-modal', 'workout-edit');
                                    "
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg"
                                >
                                    <i class="fa-solid fa-pen text-sm"></i>
                                </button>
                                <button
                                    x-on:click.stop="
                                        $dispatch('set-delete-id', item.id);
                                        $dispatch('open-modal', 'workout-delete');
                                    "
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg"
                                >
                                    <i class="fa-regular fa-trash-can text-sm"></i>
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Duration:</span>
                                <span class="font-medium ml-1" x-text="item.duration_minutes + ' min'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Distance:</span>
                                <span class="font-medium ml-1" x-text="item.distance_km ? item.distance_km + ' km' : 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Calories:</span>
                                <span class="font-medium ml-1" x-text="item.calories_burned || 'N/A'"></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Steps:</span>
                                <span class="font-medium ml-1">N/A</span>
                            </div>
                        </div>
                    </div>
                </template>
                <div x-show="results.length === 0 && !loading" class="text-center py-8 text-gray-500">
                    No workouts found.
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
                                        $dispatch('set-workout-id', item.id);
                                        $dispatch('open-modal', 'workout-view');
                                    "
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    @foreach ($columns as $column)
                                        @if ($column['type'] === 'text')
                                            <x-table.cell x-text="item['{{ $column['key'] }}'] || 'N/A'"></x-table.cell>
                                        @endif
                                    @endforeach
                                    <x-table.actions
                                        edit-event="set-edit-id"
                                        edit-modal="workout-edit"
                                        delete-event="set-delete-id"
                                        delete-modal="workout-delete"
                                    />
                                </x-table.row>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No workouts found.
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
            <x-modal name="workout-form" :show="false" maxWidth="lg">
                <div class="p-6 bg-white rounded-2xl">
                    <!-- Modal: Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fa-solid fa-person-running text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Create Workout</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Log your physical activity sessions and energy expenditure</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'workout-form')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div id="form" class="form">
                        <form action="{{route('workout-tracker.store')}}" method="post" class="space-y-4">
                            @csrf

                            <!-- Activity Type (Dropdown Upgrade) -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Activity Type</label>
                                <div class="relative">
                                    <select name="activity_type" class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 bg-white shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition duration-150 appearance-none cursor-pointer">
                                        <option value="Treadmill">🏃‍♂️ Treadmill</option>
                                        <option value="Running">🛣️ Running</option>
                                        <option value="Walking">🚶‍♂️ Walking</option>
                                        <option value="Cycling">🚴‍♂️ Cycling</option>
                                    </select>
                                </div>
                            </div>

                            <!-- 3-Column Fitness Grid (Duration, Distance, Calories) -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Duration (min)</label>
                                    <input type="number" name="duration_minutes" required placeholder="30" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition duration-150">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Distance (km)</label>
                                    <input type="number" step="0.01" name="distance_km" placeholder="0.00" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition duration-150">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Calories (kcal)</label>
                                    <input type="number" name="calories_burned" placeholder="250" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 placeholder-gray-300 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 transition duration-150">
                                </div>
                            </div>

                            <!-- Buttons Layout -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'workout-form')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Save Workout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal View-->
            <x-modal name="workout-view" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl relative"
                    x-data="{ workoutId: null, workout: {}, loading: false }"
                    x-on:set-workout-id.window="
                        if(workoutId === $event.detail) return;

                        workoutId = $event.detail;
                        loading = true;
                        workout = {};

                        fetch('/workout-tracker/' + workoutId)
                            .then(res => res.json())
                            .then(data => {
                                workout = data;
                                loading = false;
                            });
                    "
                >
                    <!-- Modal: Header Layout (Corrected Title Name Typo) -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <i class="fa-solid fa-eye text-base"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Workout Details</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Performance statistics summary details</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'workout-view')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!--Loading State Overlay-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-indigo-600"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Loading workout data...</p>
                    </div>

                    <!--Form Performance Stats Information Density Grid-->
                    <div x-show="!loading && workout.id" x-cloak class="space-y-4" class="transition-all duration-200">

                        <!-- Activity Category Header Badge Row -->
                        <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-400 tracking-wider uppercase">Activity:</span>
                                <span class="text-sm font-black text-slate-800 tracking-tight" x-text="workout.activity_type"></span>
                            </div>
                            <div class="text-[10px] font-mono text-gray-400 bg-white border border-gray-200 px-2 py-0.5 rounded-md">
                                ID: #<span x-text="workout.id"></span>
                            </div>
                        </div>

                        <!-- COMPACT CARD BLOCK CELLS -->
                        <div class="grid grid-cols-3 gap-2.5">
                            <!-- Duration Metric Cell -->
                            <div class="p-3 bg-indigo-50/40 rounded-xl border border-indigo-50 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-indigo-500 tracking-wider uppercase block">Duration</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-indigo-900 tracking-tight" x-text="workout.duration_minutes"></span>
                                    <span class="text-[10px] font-bold text-indigo-400">mins</span>
                                </div>
                            </div>

                            <!-- Distance Metric Cell -->
                            <div class="p-3 bg-gray-50/70 rounded-xl border border-gray-100 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-gray-400 tracking-wider uppercase block">Distance</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-gray-900 tracking-tight" x-text="workout.distance_km || '0.00'"></span>
                                    <span class="text-[10px] font-bold text-gray-400">km</span>
                                </div>
                            </div>

                            <!-- Calories Metric Cell -->
                            <div class="p-3 bg-orange-50/40 rounded-xl border border-orange-50 text-center sm:text-left">
                                <span class="text-[10px] font-bold text-orange-500 tracking-wider uppercase block">Burned</span>
                                <div class="flex items-baseline justify-center sm:justify-start gap-0.5 mt-1">
                                    <span class="text-lg font-black text-orange-900 tracking-tight" x-text="workout.calories_burned || '0'"></span>
                                    <span class="text-[10px] font-bold text-orange-400">kcal</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions Button Row Block -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'workout-view')"
                                class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                            >
                                Close Summary
                            </button>
                        </div>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Edit-->
            <x-modal name="workout-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="{workoutId: null, workout: {}, loading: false}"
                    x-on:set-edit-id.window="
                        if(workoutId === $event.detail) return;

                        workoutId = $event.detail;
                        loading = true;
                        workout = {};

                        fetch('/workout-tracker/' + workoutId)
                            .then(res => res.json())
                            .then(data => {
                                workout = data;
                                loading = false;
                            });
                    "
                >
                    <!-- Modal: Header Layout Strip -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-4 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                                <i class="fa-solid fa-pen-to-square text-sm"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-black text-gray-900 tracking-tight">Edit Workout</h2>
                                <p class="text-[11px] font-medium text-gray-400 -mt-0.5">Modify and update your logged workout records</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'workout-edit')"
                            class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-50 hover:text-gray-600 transition cursor-pointer"
                        >
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <!--Loading State Spinner-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-12 gap-3" x-cloak>
                        <div class="animate-spin rounded-full h-8 w-8 border-2 border-gray-100 border-b-blue-600"></div>
                        <p class="text-xs font-semibold text-gray-400 tracking-wide">Loading workout data...</p>
                    </div>

                    <!--Form Input Fields Content-->
                    <div x-show="!loading && workout.id" x-cloak class="form" id="workout-edit-form">
                        <form :action="'/workout-tracker/' + workoutId" method="post" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <!-- Activity Type Dropdown Grid Row -->
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Activity Type</label>
                                <select name="activity_type" class="block w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-semibold text-gray-900 bg-white shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150 appearance-none cursor-pointer" x-model="workout.activity_type">
                                    <option value="Treadmill">🏃‍♂️ Treadmill</option>
                                    <option value="Running">🛣️ Running</option>
                                    <option value="Walking">🚶‍♂️ Walking</option>
                                    <option value="Cycling">🚴‍♂️ Cycling</option>
                                </select>
                            </div>

                            <!-- 3-COLUMN METRICS HORIZONTAL GRID LAYOUT -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Duration (min)</label>
                                    <input type="number" name="duration_minutes" x-model="workout.duration_minutes" required class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Distance (km)</label>
                                    <input type="number" step="0.01" name="distance_km" x-model="workout.distance_km" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Calories (kcal)</label>
                                    <input type="number" name="calories_burned" x-model="workout.calories_burned" class="block w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-900 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 transition duration-150">
                                </div>
                            </div>

                            <!-- FOOTER ACTIONS STRIP CONTROL LAYER -->
                            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 mt-6">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'workout-edit')"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center px-5 h-[42px] bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none"
                                >
                                    Update Workout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Delete-->
            <x-modal name="workout-delete" :show="false" maxWidth="md">
                <div
                    class="p-6 bg-white rounded-2xl"
                    x-data="{workoutId: null}"
                    x-on:set-delete-id.window="
                        workoutId = $event.detail;
                        fetch('/workout-tracker/' + workoutId)
                            .then(res => res.json())
                            .then(data => workout = data);
                    "
                >
                    <!-- Center-Aligned Warning Header Content Area -->
                    <div class="flex flex-col items-center text-center py-4">
                        <!-- Warning Circle Pulse Accent Hex Icon Wrapper -->
                        <div class="w-14 h-14 bg-red-50 text-red-500 rounded-full flex items-center justify-center mb-4 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
                        </div>

                        <h2 class="text-lg font-black text-gray-900 tracking-tight">Delete Workout Entry</h2>
                        <p class="text-sm font-medium text-gray-500 mt-1.5 max-w-xs leading-relaxed">
                            Are you absolutely sure you want to drop this workout session record? This persistent change cannot be undone.
                        </p>
                    </div>

                    <div class="form" id="delete-workout-form">
                        <form :action="'/workout-tracker/' + workoutId" method="post" class="flex items-center justify-center gap-3 pt-3 mt-4 border-t border-gray-50">
                            @csrf
                            @method('DELETE')

                            <button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'workout-delete')"
                                class="inline-flex items-center justify-center px-5 h-[42px] bg-white border border-gray-200 hover:bg-gray-50 text-slate-700 font-semibold rounded-xl text-xs transition duration-150 ease-in-out cursor-pointer shadow-sm focus:outline-none w-full sm:w-auto"
                            >
                                No, Keep Workout
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
