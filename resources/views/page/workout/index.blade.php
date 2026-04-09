<x-app-layout>
    <div class="max-w-7xl mx-auto  flex items-center justify-between">
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
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!--table-->
        <div class="relative overflow-hidden bg-white rounded-xl border border-gray-200">
            <table class="w-full text-sm text-left rtl:text-right text-body text-gray-600">
                <thead class="bg-gray-100 border-b border-default">
                    <tr>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Activity Type
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Duration (min)
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Distance ( /km)
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Calories Burned
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Step
                        </th>
                        <th scope="col" class="px-6 py-3 font-bold">
                            Action
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 border-t border-default">
                     @forelse($workoutList as $workout)
                        <tr 
                            x-data
                            x-on:click="
                                $dispatch('set-workout-id', {{ $workout->id }});
                                $dispatch('open-modal', 'workout-view');
                            "
                            class="odd:bg-white even:bg-gray-50 border-b border-default hover:cursor-pointer hover:bg-blue-50 tracking-wider"
                        >
                            <th scope="row" class="px-6 py-4 font-medium text-heading whitespace-nowrap">
                                {{ $workout->activity_type }}
                            </th>
                            <td class="px-6 py-4">
                                {{ $workout->duration_minutes }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $workout->distance_km }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $workout->calories_burned }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $workout->calories_burned }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 justify-end ">
                                    <span
                                        x-data
                                        x-on:click.stop="
                                            $dispatch('set-edit-id',  {{ $workout->id }});
                                            $dispatch('open-modal', 'workout-edit' );
                                        "
                                        class="p-2 text-blue-600 hover:bg-blue-100 rounded-lg transition-colors"
                                        type="button"
                                    ><i class="fa-solid fa-pen"></i></span>
                                    <span
                                        x-data
                                        x-on:click.stop="
                                            $dispatch('set-delete-id',  {{ $workout->id }});
                                            $dispatch('open-modal', 'workout-delete' );
                                        "
                                        class="p-2 text-red-600 hover:bg-red-100 rounded-lg transition-colors"
                                        type="button"
                                    ><i class="fa-regular fa-trash-can"></i> </span>
                                </div>
                            </td>
                        </tr>
                     @empty
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                No workouts yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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
</x-app-layout>