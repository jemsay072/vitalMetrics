<x-app-layout>
    <div>
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                    title="System Settings"
                    description="Configure system-wide settings"
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ]"
                >
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class=" p-6 bg-white shadow sm:rounded-lg"
                x-data="{ activeTab: 'general' }">

                <!-- Header Title Component -->
                {{-- <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">System Settings</h2>
                    <p class="text-sm text-gray-500">Configure system-wide settings</p>
                </div> --}}

                <!-- Main Settings Form Wrapper -->
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- TAB NAVIGATION BUTTONS MATRIX -->
                    <div class="border-b border-gray-200 mb-6">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <!-- General Tab Trigger -->
                            <button
                                type="button"
                                @click="activeTab = 'general'"
                                :class="activeTab === 'general' ? 'border-amber-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                General
                            </button>

                            <!-- Auth Tab Trigger (Disabled or Placeholder for now) -->
                            <button
                                type="button"
                                @click="activeTab = 'auth'"
                                :class="activeTab === 'auth' ? 'border-amber-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                Auth
                            </button>

                            <!-- Payment Tab Trigger (Disabled or Placeholder for now) -->
                            <button
                                type="button"
                                @click="activeTab = 'payment'"
                                :class="activeTab === 'payment' ? 'border-amber-500 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                Payment
                            </button>
                        </nav>
                    </div>

                    <!-- TAB CONTENT WORKSPACE HOUSING -->
                    <div class="space-y-6 min-h-[200px]">

                        <!-- 1. GENERAL TAB CONTENT WINDOW -->
                        <div x-show="activeTab === 'general'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 transform translate-y-1"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="grid grid-cols-1 gap-6"
                        >
                            <!-- App Name Input Box -->
                            <div>
                                <x-input-label for="app_name" :value="__('App Name')" />
                                <x-text-input id="app_name" name="app_name" type="text" class="mt-1 block w-full md:w-2/3" :value="old('app_name', $settings->app_name ?? config('app.name'))" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('app_name')" />
                            </div>

                            <!-- App Description Input Box -->
                            <div>
                                <x-input-label for="app_description" :value="__('App Description')" />
                                <textarea
                                    id="app_description"
                                    name="app_description"
                                    rows="4"
                                    maxlength="500"
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    placeholder="Enter application description..."
                                >{{ old('app_description', $settings->app_description ?? '') }}</textarea>

                                <x-input-error
                                    class="mt-2"
                                    :messages="$errors->get('app_description')"
                                />
                            </div>
                        </div>

                        <!-- 2. AUTH TAB CONTENT WINDOW (Empty Core Placeholder) -->
                        <div x-show="activeTab === 'auth'" x-cloak class="text-sm text-gray-500 p-4 bg-gray-50 rounded-lg border border-dashed">
                            Authentication security threshold protocols configurations will be placed here.
                        </div>

                        <!-- 3. PAYMENT TAB CONTENT WINDOW (Empty Core Placeholder) -->
                        <div x-show="activeTab === 'payment'" x-cloak class="text-sm text-gray-500 p-4 bg-gray-50 rounded-lg border border-dashed">
                            Payment gateway API merchant tokens integration rules will be placed here.
                        </div>

                    </div>

                    <!-- GLOBAL SUBMIT BUTTON -->
                    <div class="mt-8 pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <x-secondary-button type="button" onclick="window.history.back()">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                        <x-primary-button type="submit">
                            {{ __('Save Settings') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
