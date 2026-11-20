<x-app-layout>
    <div>
        <!-- Page Header -->
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="System Settings"
                description="Configure core application frameworks, security controls, and global system parameters."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ['label' => 'Admin Settings',],
                ]"
            />
        </div>

        <!-- Main Settings Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div
                class="p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-100 dark:border-gray-700"
                x-data="{ activeTab: 'general' }"
            >
                <!-- Main Settings Form -->
                <form action="{{ route('admin.settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 mb-6">
                        <nav class="-mb-px flex space-x-8 overflow-x-auto" aria-label="Tabs">

                            <!-- General Tab -->
                            <button
                                type="button"
                                @click="activeTab = 'general'"
                                :class="activeTab === 'general'
                                    ? 'border-amber-500 text-gray-900 dark:text-white font-semibold'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                General
                            </button>

                            <!-- Auth Tab -->
                            <button
                                type="button"
                                @click="activeTab = 'auth'"
                                :class="activeTab === 'auth'
                                    ? 'border-amber-500 text-gray-900 dark:text-white font-semibold'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                Auth
                            </button>

                            <!-- Payment Tab -->
                            <button
                                type="button"
                                @click="activeTab = 'payment'"
                                :class="activeTab === 'payment'
                                    ? 'border-amber-500 text-gray-900 dark:text-white font-semibold'
                                    : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gray-300 dark:hover:border-gray-500'"
                                class="whitespace-nowrap pb-4 px-1 border-b-2 font-medium text-sm transition duration-150 ease-in-out"
                            >
                                Payment
                            </button>

                        </nav>
                    </div>

                    <!-- Tab Content -->
                    <div class="space-y-6 min-h-[200px]">

                        <!-- GENERAL TAB -->
                        <div
                            x-show="activeTab === 'general'"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 transform translate-y-1"
                            x-transition:enter-end="opacity-100 transform translate-y-0"
                            class="grid grid-cols-1 gap-6"
                        >

                            <!-- App Name -->
                            <div>
                                <x-input-label
                                    for="app_name"
                                    :value="__('App Name')"
                                    class="dark:text-gray-300"
                                />

                                <x-text-input
                                    id="app_name"
                                    name="app_name"
                                    type="text"
                                    class="mt-1 block w-full md:w-2/3 bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                    :value="old('app_name', $settings->app_name ?? config('app.name'))"
                                    required
                                    autofocus
                                />

                                <x-input-error
                                    class="mt-2"
                                    :messages="$errors->get('app_name')"
                                />
                            </div>

                            <!-- App Description -->
                            <div>
                                <x-input-label
                                    for="app_description"
                                    :value="__('App Description')"
                                    class="dark:text-gray-300"
                                />

                                <textarea
                                    id="app_description"
                                    name="app_description"
                                    rows="4"
                                    maxlength="500"
                                    class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-400 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    placeholder="Enter application description..."
                                >{{ old('app_description', $settings->app_description ?? '') }}</textarea>

                                <x-input-error
                                    class="mt-2"
                                    :messages="$errors->get('app_description')"
                                />
                            </div>

                        </div>

                        <!-- AUTH TAB -->
                        <div
                            x-show="activeTab === 'auth'"
                            x-cloak
                            class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600"
                        >
                            Authentication security threshold protocols configurations will be placed here.
                        </div>

                        <!-- PAYMENT TAB -->
                        <div
                            x-show="activeTab === 'payment'"
                            x-cloak
                            class="text-sm text-gray-500 dark:text-gray-400 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-dashed border-gray-300 dark:border-gray-600"
                        >
                            Payment gateway API merchant tokens integration rules will be placed here.
                        </div>

                    </div>

                    <!-- Footer Actions -->
                    <div class="mt-8 pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center justify-end gap-3">
                        <x-secondary-button
                            type="button"
                            onclick="window.history.back()"
                        >
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
