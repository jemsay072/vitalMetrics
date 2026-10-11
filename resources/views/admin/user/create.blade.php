<x-app-layout>
    <div>
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="Create New User"
                description="Provision a new admin or regular user account into VitalMetrics system."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ['label' => 'User', 'url' => '/admin/users'],
                    ['label' => 'Create', 'url' => '#'],
                ]"
            >
            </x-page-header>
        </div>
        <!-- Main Content Area -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Two-Column Grid Setup System Mirroring the Edit Page  -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <div class="bg-white p-6 shadow sm:rounded-lg flex flex-col items-center text-center">
                        <div class="relative mb-4 flex justify-center w-fit mx-auto">
                            <!-- Static placeholder indicator shape representing empty avatar status layout -->
                            <div class="w-28 h-28 aspect-square rounded-full text-white font-extrabold flex items-center justify-center tracking-wider text-3xl shadow-inner uppercase bg-slate-300 border-4 border-white ring-4 ring-offset-2 ring-slate-200 select-none">
                                ?
                            </div>
                        </div>
                        <h2 class="text-xl font-bold text-gray-400">{{ __('New User Account') }}</h2>
                        <p class="text-xs text-gray-400 mt-1">{{ __('Profile overview badges will generate automatically after store completion parameters.') }}</p>
                    </div>

                    <!-- Right Column: Form Interactive Input Fields Partition Modules -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Block 1: Core Account Profile Metrics -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900 mb-6 border-b border-gray-100 pb-2">Account Credentials</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="user_name" :value="__('Name')" />
                                    <x-text-input id="user_name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                                <div>
                                    <x-input-label for="user_email" :value="__('Email')" />
                                    <x-text-input id="user_email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>

                                <!-- IMPORTANT: Password fields added directly into credential cards module grid layout alignment matrix -->
                                <div>
                                    <x-input-label for="password" :value="__('Password')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('password')" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                </div>

                                <div>
                                    <x-input-label for="user_role" :value="__('Role')" />
                                    <select id="user_role" name="role_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">Select Account Security Role Designation</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                                </div>
                                <div>
                                    <x-input-label for="user_status" :value="__('Status')" />
                                    <select id="user_status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>
                        </div>

                        <!-- Block 2: Complete Personal Data Segment Profiles Module -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900 mb-6 border-b border-gray-100 pb-2">Profile Information</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="user_phone" :value="__('Phone')" />
                                    <x-text-input id="user_phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" autocomplete="tel"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>
                                <div>
                                    <x-input-label for="user_birth_date" :value="__('Birth Date')" />
                                    <x-text-input id="user_birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date')"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                </div>
                                <div>
                                    <x-input-label for="user_sex" :value="__('Sex')" />
                                    <select id="user_sex" name="sex" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="">Select Sex</option>
                                        <option value="male" {{ old('sex') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex') == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('sex')" />
                                </div>
                                <div>
                                    <x-input-label for="user_marital_status" :value="__('Marital Status')" />
                                    <select id="user_marital_status" name="marital_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
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
                                    <x-input-label for="user_address" :value="__('Address')" />
                                    <x-text-input id="user_address" name="address" type="text" class="mt-1 block w-full" :value="old('address')"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>
                                <div>
                                    <x-input-label for="user_city" :value="__('City')" />
                                    <x-text-input id="user_city" name="city" type="text" class="mt-1 block w-full" :value="old('city')"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>
                                <div>
                                    <x-input-label for="user_provice" :value="__('Province')" />
                                    <x-text-input id="user_provice" name="province" type="text" class="mt-1 block w-full" :value="old('province')"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="user_zip_code" :value="__('Zip Code')" />
                                    <x-text-input id="user_zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code')"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                                </div>
                            </div>
                        </div>

                        <!-- Block 3: Buttons -->
                        <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg flex items-center justify-end gap-3">
                            <x-secondary-button type="button" onclick="window.history.back()">{{ __('Cancel') }}</x-secondary-button>
                            <x-primary-button type="submit">{{ __('Save Changes') }}</x-primary-button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
