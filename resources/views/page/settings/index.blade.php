<x-app-layout>
    <div x-data="{ currentTab: '{{ session('active_tab', 'general') }}' }">

        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <x-page-header
                    title="Settings"
                    description="Manage your application preferences and localization setups."
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/dashboard'],
                        ['label' => 'Settings', 'url' => '#']
                    ]"
                />
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <form action="{{ route('settings.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- Tab Navigation Header -->
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <!-- General Tab Trigger -->
                        <button
                            type="button"
                            @click="currentTab = 'general'"
                            :class="currentTab === 'general' ? 'border-gray-700 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-all duration-200 focus:outline-none">
                            General Settings
                        </button>

                        <!-- Localization Tab Trigger -->
                        <button
                            type="button"
                            @click="currentTab = 'localization'"
                            :class="currentTab === 'localization' ? 'border-gray-700 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-all duration-200 focus:outline-none">
                            Localization
                        </button>

                        <!-- Notifications Tab Trigger -->
                        <button
                            type="button"
                            @click="currentTab = 'notifications'"
                            :class="currentTab === 'notifications' ? 'border-gray-700 text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 text-sm transition-all duration-200 focus:outline-none">
                            Notifications
                        </button>
                    </nav>
                </div>

                <!-- Tab Contents Container -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 shadow-sm min-h-[250px]">

                    <!-- 1. GENERAL SETTINGS TAB -->
                    <div x-show="currentTab === 'general'" x-transition:enter="transition ease-out duration-200" class="space-y-6">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">Application Theme</h3>
                            <p class="text-xs text-gray-500 mb-3">Choose how VitalMetrics looks on your device.</p>
                            <select name="theme" class="max-w-md w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring focus:ring-gray-200 text-sm">
                                <option value="light" {{ ($preferences->theme ?? 'light') == 'light' ? 'selected' : '' }}>Light Mode (Recommended)</option>
                                <option value="dark" {{ ($preferences->theme ?? '') == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-1">Dashboard Landing</h3>
                            <p class="text-xs text-gray-500 mb-3">Select the default page you want to see right after logging in.</p>
                            <select name="dashboard_landing" class="max-w-md w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring focus:ring-gray-200 text-sm">
                                <option value="dashboard" {{ ($preferences->dashboard_landing ?? 'dashboard') == 'dashboard' ? 'selected' : '' }}>Main Dashboard</option>
                                <option value="analytics" {{ ($preferences->dashboard_landing ?? '') == 'analytics' ? 'selected' : '' }}>Detailed Analytics</option>
                            </select>
                        </div>
                    </div>

                    <!-- 2. LOCALIZATION TAB -->
                    <div x-show="currentTab === 'localization'" x-transition:enter="transition ease-out duration-200" class="space-y-6" style="display: none;">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">Weight Unit</h3>
                            <p class="text-xs text-gray-500 mb-3">Set your preferred unit for logging weight entries.</p>
                            <select name="weight_unit" class="max-w-md w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring focus:ring-gray-200 text-sm">
                                <option value="kg" {{ ($preferences->weight_unit ?? 'kg') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                                <option value="lbs" {{ ($preferences->weight_unit ?? '') == 'lbs' ? 'selected' : '' }}>Pounds (lbs)</option>
                            </select>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <h3 class="text-base font-semibold text-gray-900 mb-1">Date Format</h3>
                            <p class="text-xs text-gray-500 mb-3">Choose how system dates are structured and displayed.</p>
                            <select name="date_format" class="max-w-md w-full rounded-md border-gray-300 shadow-sm focus:border-gray-500 focus:ring focus:ring-gray-200 text-sm">
                                <option value="M d, Y" {{ ($preferences->date_format ?? 'M d, Y') == 'M d, Y' ? 'selected' : '' }}>Oct 30, 2026 (M d, Y)</option>
                                <option value="Y-m-d" {{ ($preferences->date_format ?? '') == 'Y-m-d' ? 'selected' : '' }}>2026-10-30 (Y-m-d)</option>
                                <option value="d/m/Y" {{ ($preferences->date_format ?? '') == 'd/m/Y' ? 'selected' : '' }}>30/10/2026 (d/m/Y)</option>
                            </select>
                        </div>
                    </div>

                    <!-- 3. NOTIFICATIONS TAB -->
                    <div x-show="currentTab === 'notifications'" x-transition:enter="transition ease-out duration-200" class="space-y-4" style="display: none;">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-1">System Alerts & Rules</h3>
                            <p class="text-xs text-gray-500 mb-4">Toggle how you want to be reminded or alerted by VitalMetrics.</p>
                        </div>

                        <!-- In-App Notification Toggle -->
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="notifications_enabled" name="notifications_enabled" type="checkbox" value="1" {{ ($preferences->notifications_enabled ?? true) ? 'checked' : '' }}
                                    class="focus:ring-gray-500 h-4 w-4 text-gray-800 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="notifications_enabled" class="font-medium text-gray-700">Enable System Notifications</label>
                                <p class="text-xs text-gray-500">Show immediate warnings or push metrics alerts on the header bar.</p>
                            </div>
                        </div>

                        <!-- Email Notification Toggle -->
                        <div class="flex items-start pt-3 border-t border-gray-100">
                            <div class="flex items-center h-5">
                                <input id="email_notifications" name="email_notifications" type="checkbox" value="1" {{ ($preferences->email_notifications ?? false) ? 'checked' : '' }}
                                    class="focus:ring-gray-500 h-4 w-4 text-gray-800 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="email_notifications" class="font-medium text-gray-700">Email Updates</label>
                                <p class="text-xs text-gray-500">Receive periodic health overview summaries or critical system notifications via email.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Global Action Button Footer -->
                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex justify-center py-2 px-5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 transition duration-150 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                        Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
