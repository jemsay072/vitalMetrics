<x-app-layout>

    <div>
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="Create New User"
                description="Provision a new admin or regular user account into VitalMetrics system."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ['label' => 'User', 'url' => '/admin/users'],
                    ['label' => 'Create'],
                ]"
            >
            </x-page-header>
        </div>

        {{-- Main Content Area --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                {{-- Two-Column Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    {{-- Left Column: Account Preview --}}
                    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-6 shadow-sm sm:rounded-lg flex flex-col items-center text-center">

                        <div class="relative mb-4 flex justify-center w-fit mx-auto">
                            <div class="w-28 h-28 aspect-square rounded-full text-white font-extrabold flex items-center justify-center tracking-wider text-3xl shadow-inner uppercase bg-slate-300 dark:bg-gray-600 border-4 border-white dark:border-gray-700 ring-4 ring-offset-2 ring-slate-200 dark:ring-gray-600 dark:ring-offset-gray-800 select-none">
                                ?
                            </div>
                        </div>

                        <h2 class="text-xl font-bold text-gray-400 dark:text-gray-300">
                            {{ __('New User Account') }}
                        </h2>

                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                            {{ __('Profile overview badges will generate automatically after store completion parameters.') }}
                        </p>
                    </div>

                    {{-- Right Column: Form --}}
                    <div class="lg:col-span-2 space-y-6">

                        {{-- Block 1: Account Credentials --}}
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">

                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                                Account Credentials
                            </h2>

                            <div
                                x-data="{
                                    password: '',
                                    confirmPassword: '',
                                    showPassword: false,
                                    copied: false,
                                    generatePassword() {
                                        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%';
                                        let result = '';
                                        for (let i = 0; i < 12; i++) {
                                            result += chars.charAt(Math.floor(Math.random() * chars.length));
                                        }
                                        this.password = result;
                                        this.confirmPassword = result;
                                        this.showPassword = true;
                                    },
                                    copyPassword() {
                                        if (!this.password) return;
                                        navigator.clipboard.writeText(this.password);
                                        this.copied = true;
                                        setTimeout(() => this.copied = false, 2000);
                                    }
                                }"
                                class="grid grid-cols-1 md:grid-cols-2 gap-4"
                            >

                                <div>
                                    <x-input-label for="user_name" :value="__('Name')" class="dark:text-gray-300" />
                                    <x-text-input id="user_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>

                                <div>
                                    <x-input-label for="user_email" :value="__('Email')" class="dark:text-gray-300" />
                                    <x-text-input id="user_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- Password Field -->
                                <div>
                                    <div class="flex items-center justify-between">
                                        <x-input-label for="password" :value="__('Password')" class="dark:text-gray-300" />

                                        <div class="flex items-center gap-3">
                                            <button
                                                type="button"
                                                @click="generatePassword()"
                                                class="text-xs font-medium text-teal-600 hover:text-teal-500 dark:text-teal-400 flex items-center gap-1"
                                            >
                                                <i class="fa-solid fa-wand-magic-sparkles"></i> Auto-Generate
                                            </button>

                                            <button
                                                type="button"
                                                x-show="password.length > 0"
                                                @click="copyPassword()"
                                                class="text-xs font-medium text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex items-center gap-1"
                                            >
                                                <i class="fa-solid" :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"></i>
                                                <span x-text="copied ? 'Copied!' : 'Copy'"></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="relative mt-1">
                                        <x-text-input
                                            id="password"
                                            name="password"
                                            x-bind:type="showPassword ? 'text' : 'password'"
                                            x-model="password"
                                            class="block w-full pr-10"
                                            required
                                        />

                                        <!-- Eye Toggle Button -->
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            title="Show/Hide Password"
                                        >
                                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>

                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>

                                <!-- Confirm Password Field -->
                                 <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="dark:text-gray-300" />

                                    <div class="relative mt-1">
                                        <x-text-input
                                            id="password_confirmation"
                                            name="password_confirmation"
                                            x-bind:type="showPassword ? 'text' : 'password'"
                                            x-model="confirmPassword"
                                            class="block w-full pr-10"
                                            required
                                        />

                                        <!-- Eye Toggle Button -->
                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                            title="Show/Hide Password"
                                        >
                                            <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_role" :value="__('Role')" class="dark:text-gray-300" />

                                    <select
                                        id="user_role"
                                        name="role_id"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    >
                                        <option value="">Select Account Security Role Designation</option>

                                        @foreach ($roles as $role)
                                            <option
                                                value="{{ $role->id }}"
                                                {{ old('role_id') == $role->id ? 'selected' : '' }}
                                            >
                                                {{ $role->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                                </div>

                                <div>
                                    <x-input-label for="user_status" :value="__('Status')" class="dark:text-gray-300" />

                                    <select
                                        id="user_status"
                                        name="status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    >
                                        <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>

                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>

                            </div>
                        </div>

                        {{-- Block 2: Profile Information --}}
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">

                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                                Profile Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <x-input-label for="user_phone" :value="__('Phone')" class="dark:text-gray-300" />
                                    <x-text-input id="user_phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" autocomplete="tel" />
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>

                                <div>
                                    <x-input-label
                                        for="user_birth_date"
                                        :value="__('Birth Date')"
                                        class="dark:text-gray-300"
                                    />

                                    <x-text-input
                                        id="user_birth_date"
                                        name="birth_date"
                                        type="date"
                                        class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:[&::-webkit-calendar-picker-indicator]:invert"
                                        :value="old('birth_date')"
                                    />

                                    <x-input-error
                                        class="mt-2"
                                        :messages="$errors->get('birth_date')"
                                    />
                                </div>

                                <div>
                                    <x-input-label for="user_sex" :value="__('Sex')" class="dark:text-gray-300" />

                                    <select
                                        id="user_sex"
                                        name="sex"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    >
                                        <option value="">Select Sex</option>
                                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>

                                    <x-input-error class="mt-2" :messages="$errors->get('sex')" />
                                </div>

                                <div>
                                    <x-input-label for="user_marital_status" :value="__('Marital Status')" class="dark:text-gray-300" />

                                    <select
                                        id="user_marital_status"
                                        name="marital_status"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                    >
                                        <option value="">Select Status</option>
                                        <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="separated" {{ old('marital_status') == 'separated' ? 'selected' : '' }}>Separated</option>
                                    </select>

                                    <x-input-error class="mt-2" :messages="$errors->get('marital_status')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="user_address" :value="__('Address')" class="dark:text-gray-300" />
                                    <x-text-input id="user_address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>

                                <div>
                                    <x-input-label for="user_city" :value="__('City')" class="dark:text-gray-300" />
                                    <x-text-input id="user_city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>

                                <div>
                                    <x-input-label for="user_province" :value="__('Province')" class="dark:text-gray-300" />
                                    <x-text-input id="user_province" name="province" type="text" class="mt-1 block w-full" :value="old('province')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="user_zip_code" :value="__('Zip Code')" class="dark:text-gray-300" />
                                    <x-text-input id="user_zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code')" />
                                    <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                                </div>

                            </div>
                        </div>

                        {{-- Block 3: Actions --}}
                        <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg flex items-center justify-end gap-3">

                            <x-secondary-button
                                type="button"
                                onclick="window.history.back()"
                                class="dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600"
                            >
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button type="submit">
                                {{ __('Save Changes') }}
                            </x-primary-button>

                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

</x-app-layout>
