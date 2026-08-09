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
                description="Workout Description"
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                ]"
            >
                <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'workout-form')"
                        type="button"
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                    >
                        + Workout
                    </button>
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
            <div class="md:hidden space-y-4">
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
            <div class="hidden md:block relative overflow-hidden bg-white rounded-xl border border-gray-200">
                <!---Loading Overlay -->
                <div x-show="loading" class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center z-10">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                </div>
                <div class="overflow-x-auto">
                    <x-table.container>
                        <thead class="bg-gray-100 border-b border-default">
                            <x-table.row>
                                <x-table.header-cell> Activity Type </x-table.header-cell>
                                <x-table.header-cell> Duration (min) </x-table.header-cell>
                                <x-table.header-cell> Distance (km) </x-table.header-cell>
                                <x-table.header-cell> Calories Burned </x-table.header-cell>
                                <x-table.header-cell> Steps </x-table.header-cell>
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
                                    <x-table.cell x-text="item.activity_type"></x-table.cell>
                                    <x-table.cell x-text="item.duration_minutes"></x-table.cell>
                                    <x-table.cell x-text="item.distance_km || 'N/A'"></x-table.cell>
                                    <x-table.cell x-text="item.calories_burned || 'N/A'"></x-table.cell>
                                    <x-table.cell x-text="item.steps || 'N/A'"></x-table.cell>
                                    <x-table.cell>
                                        <div class="flex items-center space-x-2 justify-end">
                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-edit-id', item.id);
                                                    $dispatch('open-modal', 'workout-edit');
                                                "
                                                class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg"
                                            >
                                                <i class="fa-solid fa-pen"></i>
                                            </button>

                                            <button
                                                x-on:click.stop="
                                                    $dispatch('set-delete-id', item.id);
                                                    $dispatch('open-modal', 'workout-delete');
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
                <div class="p-6">
                    <h2 class="text-lg font-semibold mb-4">Workout Form</h2>
                    <div id="form" class="form">
                        <form action="{{route('workout-tracker.store')}}" method="post">
                            @csrf
                                <!-- Activity Type -->
                                <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Activity Type</label>
                                <select name="activity_type" class="w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="Treadmill">Treadmill</option>
                                    <option value="Running">Running</option>
                                    <option value="Walking">Walking</option>
                                    <option value="Cycling">Cycling</option>
                                </select>
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                                <input type="number" name="duration_minutes" class="w-full border-gray-300 rounded-md shadow-sm" required>
                            </div>

                            <!-- Distance -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Distance (km)</label>
                                <input type="number" step="0.01" name="distance_km" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Calories -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Calories Burned</label>
                                <input type="number" name="calories_burned" class="w-full border-gray-300 rounded-md shadow-sm">
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'workout-form')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md"
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
                    class="p-6"
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
                    <h2 class="text-lg font-semibold mb-4">View</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading workout data...</p>
                    </div>

                    <!--Form-->
                    <div x-show="!loading && workout.id" x-cloak class="space-y-2 mb-6">
                        <span x-init="console.log(workout)"></span>
                        <p>ID: <span x-text="workout.id"></span></p>
                        <p>Activity: <span x-text="workout.activity_type"></span></p>
                        <p>Duration: <span x-text="workout.duration_minutes"></span></p>
                        <p>Distance: <span x-text="workout.distance_km"></span></p>
                        <p>Calories: <span x-text="workout.calories_burned"></span></p>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button
                            type="button"
                            x-on:click="$dispatch('close-modal', 'workout-view')"
                            class="px-4 py-2 bg-gray-200 rounded-md"
                        >
                            Close
                        </button>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Edit-->
            <x-modal name="workout-edit" :show="false" maxWidth="lg">
                <div
                    class="p-6"
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
                    <h2 class="text-lg font-semibold mb-4">Edit Workout</h2>

                    <!--Loading State-->
                    <div x-show="loading" class="flex flex-col justify-center items-center py-10 gap-2">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-3"></div>
                        <p class="text-sm text-gray-500">Loading workout data...</p>
                    </div>


                    <div x-show="!loading && workout.id" x-cloak class="form" id="workout-edit-form">
                        <form :action="'/workout-tracker/' + workoutId" method="post">
                            @csrf
                            @method('PUT')
                            <!-- Activity Type -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Activity Type</label>
                                <select name="activity_type" class="w-full border-gray-300 rounded-md shadow-sm" x-model="workout.activity_type">
                                    <option value="Treadmill">Treadmill</option>
                                    <option value="Running">Running</option>
                                    <option value="Walking">Walking</option>
                                    <option value="Cycling">Cycling</option>
                                </select>
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Duration (minutes)</label>
                                <input type="number" name="duration_minutes" class="w-full border-gray-300 rounded-md shadow-sm" x-model="workout.duration_minutes" required>
                            </div>

                            <!-- Distance -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Distance (km)</label>
                                <input type="number" step="0.01" name="distance_km" class="w-full border-gray-300 rounded-md shadow-sm" x-model="workout.distance_km">
                            </div>

                            <!-- Calories -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium mb-1">Calories Burned</label>
                                <input type="number" name="calories_burned" class="w-full border-gray-300 rounded-md shadow-sm" x-model="workout.calories_burned">
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'workout-edit')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md"
                                >
                                    Update Workout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

            <!-- Modal Delete-->
            <x-modal name="workout-delete" :show="false" maxWidth="lg">
                <div
                    class="p-6"
                    x-data="{workoutId: null}"
                    x-on:set-delete-id.window="
                        workoutId = $event.detail;
                        fetch('/workout-tracker/' + workoutId)
                            .then(res => res.json())
                            .then(data => workout = data);
                    "
                >
                    <h2 class="text-lg font-semibold mb-4">Delete Workout</h2>
                    <div class="form" id="delete-workout-form">
                        <form :action="'/workout-tracker/' + workoutId" method="post">
                            @csrf
                            @method('DELETE')
                            <p>Are you sure you want to delete this workout?</p>
                            <!-- Buttons -->
                            <div class="flex justify-end space-x-2 mt-4">
                                <button
                                    type="button"
                                    x-on:click="$dispatch('close-modal', 'workout-delete')"
                                    class="px-4 py-2 bg-gray-200 rounded-md"
                                >
                                    Cancel
                                </button>

                                <button
                                    type="submit"
                                    class="px-4 py-2 bg-red-600 text-white rounded-md"
                                >
                                    Delete Workout
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </x-modal>

        </div>
    </div>
</x-app-layout>
