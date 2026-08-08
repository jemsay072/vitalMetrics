<x-app-layout>
    <div x-data="searchFilter({ endpoint: '/api/workouts', extractFilename: 'workouts.csv', exportLabel: 'Extract', extractFields: [
            { key: 'activity_type', label: 'Activity Type' },
            { key: 'duration_minutes', label: 'Duration' },
            { key: 'distance_km', label: 'Distance' },
            { key: 'calories_burned', label: 'Calories Burned' }
        ] })">
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
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div class="flex-1 min-w-0">
                    <input
                        type="text"
                        x-model="search"
                        placeholder="Search workouts..."
                        @input.debounce.500="fetchData()"
                        class="bg-white border border-gray-300 text-gray-900 placeholder:text-gray-500 focus:ring-blue-500 focus:border-blue-500 block w-full p-3 sm:p-2.5 rounded-xl text-base shadow-sm"
                    >
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3 gap-3">
                    <button
                        type="button"
                        x-on:click="extractCurrentData()"
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
                    <table class="w-full text-sm text-left rtl:text-right text-body text-gray-600 min-w-[600px]">
                        <thead class="bg-gray-100 border-b border-default">
                            <tr>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Activity Type
                                </th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Duration (min)
                                </th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Distance (km)
                                </th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Calories Burned
                                </th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Steps
                                </th>
                                <th scope="col" class="px-6 py-3 font-bold whitespace-nowrap">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 border-t border-default">
                            <template x-for="item in results" :key="item.id">
                                <tr
                                    x-data
                                    x-on:click="
                                        $dispatch('set-workout-id', item.id);
                                        $dispatch('open-modal', 'workout-view');
                                    "
                                    class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                                >
                                    <td class="px-6 py-4 font-medium" x-text="item.activity_type"></td>
                                    <td class="px-6 py-4" x-text="item.duration_minutes"></td>
                                    <td class="px-6 py-4" x-text="item.distance_km || 'N/A'"></td>
                                    <td class="px-6 py-4" x-text="item.calories_burned || 'N/A'"></td>
                                    <td class="px-6 py-4">N/A</td>

                                    <td class="px-6 py-4">
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
                                    </td>
                                </tr>
                            </template>
                            <template x-if="results.length === 0 && !loading">
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-500">
                                        No workouts found.
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
