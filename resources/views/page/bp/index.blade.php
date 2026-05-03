<x-app-layout>
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <x-page-header
            title="Blood Pressure"
            description="Blood Pressure Description"
            :breadcrumbs="[
                ['label' => 'Home', 'url' => '/'],
            ]"
        >
            <x-slot name="actions">
                <button
                    x-data
                    x-on:click="$dispatch('open-modal', 'bp-form')"
                    type="button"
                    class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                >
                    + Blood Pressure
                </button>
            </x-slot>
        </x-page-header>
    </div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        <!-- Modal toggle Create -->
        <x-modal name="bp-form" :show="false" maxWidth="lg">
            <div class="p-6" x-data="bpComponent">
                <h2 class="text-lg font-semibold mb-4">Blood Pressure Form</h2>
                <div id="bp-form-content">
                    <!-- Form content will be loaded here via AJAX -->
                    <form action="{{route('bp.store')}}" method="post">
                        @csrf
                        <div class="mb-4">
                            <label for="systolic" class="block text-sm font-medium text-gray-700">Systolic</label>
                            <input type="number" name="systolic" id="systolic" x-model="form.systolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="diastolic" class="block text-sm font-medium text-gray-700">Diastolic</label>
                            <input type="number" name="diastolic" id="diastolic" x-model="form.diastolic" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="pulse" class="block text-sm font-medium text-gray-700">Heart Rate</label>
                            <input type="number" name="pulse" id="pulse" x-model="form.pulse" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="reading_time" class="block text-sm font-medium text-gray-700">Reading Time</label>
                            <input type="time" name="reading_time" id="reading_time" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                        </div>
                        <div class="mb-4">
                            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                            <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                        </div>
                        <div class="mb-4">
                            <p class="mt-2 font-bold"
                                :class="getColor(liveTerm())"
                                x-show="liveTerm()"
                                x-text="liveTerm()">
                            </p>
                            <input type="text" name="terms" :value="liveTerm()">
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
