<x-app-layout>
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
    </div>
</x-app-layout>
