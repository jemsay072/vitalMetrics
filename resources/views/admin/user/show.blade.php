<x-app-layout>
    <div
        x-data="{
            isEditing: new URLSearchParams(window.location.search).get('edit') === '1',
            cancelEdit() {
                this.isEditing = false;
                window.location.reload();
            }
        }"
    >
        <!-- Page Header -->
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="User Details"
                description="View and edit the complete user profile information."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ['label' => 'User', 'url' => '/admin/users'],
                    ['label' => 'Details'],
                ]"
            />
        </div>

        <!-- Main Body -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form
                id="user-details"
                method="POST"
                action="{{ route('admin.users.update', $user) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- LEFT COLUMN: Profile Card -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg flex flex-col items-center text-center border border-gray-100 dark:border-gray-700">

                        <div class="relative mb-4 flex justify-center w-fit mx-auto">
                            <button
                                type="button"
                                x-bind:disabled="!isEditing"
                                x-on:click="$dispatch('open-modal','users-avatar')"
                                x-bind:class="isEditing
                                 ? 'cursor-pointer hover:scale-105 focus:outline-none focus:ring-4 focus:ring-amber-500'
                                 : 'cursor-default'"
                                class="group relative w-28 h-28 aspect-square rounded-full transition duration-150 ease-in-out"
                            >
                                @if($user->profile?->avatar)
                                    <div class="w-full h-full aspect-square rounded-full ring-4 ring-offset-2 ring-amber-500 overflow-hidden bg-white dark:bg-gray-700">
                                        <img
                                            src="{{ asset('storage/' . $user->profile->avatar) }}"
                                            alt="avatar"
                                            class="w-full h-full rounded-full object-cover group-hover:blur-[1px] transition duration-200"
                                        >
                                    </div>
                                @else
                                    <div class="w-full h-full aspect-square rounded-full text-white font-extrabold flex items-center justify-center tracking-wider text-3xl shadow-inner uppercase select-none border-4 border-white dark:border-gray-700 ring-4 ring-offset-2 ring-gray-200 dark:ring-gray-600 group-hover:blur-[1px] transition duration-200 {{ auth()->check() ? $user->avatar_color : 'bg-amber-500' }}">
                                        {{ auth()->check() ? $user->initials : '??' }}
                                    </div>
                                @endif

                                <!-- Hover Overlay -->
                                <div x-show="isEditing" x-cloak class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-full opacity-0 group-hover:opacity-100 transition duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>
                            </button>

                            <!-- Status Indicator -->
                            <span class="absolute bottom-2 right-2 block h-5 w-5 rounded-full border-4 border-white dark:border-gray-800
                                {{ $user->status === 'inactive' ? 'bg-amber-500' : '' }}
                                {{ $user->status === 'active' || !$user->status ? 'bg-emerald-500' : '' }}">
                            </span>
                        </div>

                        <!-- User Identity -->
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                            {{ $user->name }}
                        </h2>

                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                            {{ $user->email }}
                        </p>

                        <!-- Role and Status -->
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role?->name ?? 'User' }}
                            </span>

                            <span class="px-3 py-1 rounded-full text-xs font-bold capitalize
                                {{ $user->status === 'active' || !$user->status ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $user->status === 'inactive' ? 'bg-amber-100 text-amber-700' : '' }}">
                                {{ $user->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Account Information -->
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-100 dark:border-gray-700">

                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                                Account Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <x-input-label for="user_name" :value="__('Name')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->name ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input
                                            id="user_name"
                                            name="name"
                                            type="text"
                                            class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600"
                                            :value="old('name', $user->name)"
                                            required
                                            autofocus
                                            autocomplete="name"
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_email" :value="__('Email')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->email ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input
                                            id="user_email"
                                            name="email"
                                            type="email"
                                            class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed dark:bg-gray-600 dark:text-gray-300 dark:border-gray-600"
                                            :value="old('email', $user->email)"
                                            required
                                            autocomplete="email"
                                            readonly
                                        />
                                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_role" :value="__('Role')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->role_id ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <select
                                            id="user_role"
                                            name="role_id"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        >
                                            <option value="" {{ old('role_id', $user->role_id) == '' ? 'selected' : '' }}>Select Role</option>

                                            @foreach ($roles as $role)
                                                <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                                    {{ $role->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_status" :value="__('Status')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->status ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <select
                                            id="user_status"
                                            name="status"
                                            class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm"
                                        >
                                            <option value="" {{ old('status', $user->status) == '' ? 'selected' : '' }}>Select Status</option>
                                            <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="p-4 sm:p-8 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-100 dark:border-gray-700">

                            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-6 border-b border-gray-100 dark:border-gray-700 pb-2">
                                Profile Information
                            </h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                <div>
                                    <x-input-label for="user_phone" :value="__('Phone')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->phone ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_phone" name="phone" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600" :value="old('phone', $user->profile?->phone)" autocomplete="tel"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_birth_date" :value="__('Birth Date')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->birth_date ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_birth_date" name="birth_date" type="date" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600 dark:[&::-webkit-calendar-picker-indicator]:invert" :value="old('birth_date', $user->profile?->birth_date)"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_sex" :value="__('Sex')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->sex ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <select id="user_sex" name="sex" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                            <option value="" {{ old('sex', $user->profile?->sex) == '' ? 'selected' : '' }}>Select Sex</option>
                                            <option value="male" {{ old('sex', $user->profile?->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('sex', $user->profile?->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('sex')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_marital_status" :value="__('Marital Status')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->marital_status ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <select id="user_marital_status" name="marital_status" class="mt-1 block w-full border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                            <option value="" {{ old('marital_status', $user->profile?->marital_status) == '' ? 'selected' : '' }}>Select Marital Status</option>
                                            <option value="single" {{ old('marital_status', $user->profile?->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married" {{ old('marital_status', $user->profile?->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="widowed" {{ old('marital_status', $user->profile?->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                            <option value="divorced" {{ old('marital_status', $user->profile?->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="separated" {{ old('marital_status', $user->profile?->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                        </select>
                                        <x-input-error class="mt-2" :messages="$errors->get('marital_status')" />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="user_address" :value="__('Address')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->address ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_address" name="address" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600" :value="old('address', $user->profile?->address)"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_city" :value="__('City')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{$user->profile?->city ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_city" name="city" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600" :value="old('city', $user->profile?->city)"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                    </div>
                                </div>

                                <div>
                                    <x-input-label for="user_province" :value="__('Province')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->province ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_province" name="province" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600" :value="old('province', $user->profile?->province)"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <x-input-label for="user_zip_code" :value="__('Zip Code')" class="dark:text-gray-300" />
                                    <p x-show="!isEditing" class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                        {{ $user->profile?->zip_code ?: '—' }}
                                    </p>
                                    {{-- Edit Mode --}}
                                    <div x-show="isEditing" x-cloak>
                                        <x-text-input id="user_zip_code" name="zip_code" type="text" class="mt-1 block w-full bg-white text-gray-900 dark:bg-gray-700 dark:text-white dark:border-gray-600" :value="old('zip_code', $user->profile?->zip_code)"/>
                                        <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="p-4 sm:p-6 bg-white dark:bg-gray-800 shadow sm:rounded-lg border border-gray-100 dark:border-gray-700 flex items-center justify-end gap-3">

                            {{-- View Mode --}}
                            <div x-show="!isEditing" class="flex items-center gap-3">

                                <a
                                    href="{{ route('admin.users') }}"
                                    class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 transition"
                                >
                                    Back to Users
                                </a>

                                <x-primary-button
                                    type="button"
                                    x-on:click="isEditing = true"
                                >
                                    <i class="fa-solid fa-pen mr-2"></i>
                                    Edit User
                                </x-primary-button>

                            </div>

                            {{-- Edit Mode --}}
                            <div x-show="isEditing" class="flex items-center gap-3">

                                <x-secondary-button
                                    type="button"
                                    x-on:click="cancelEdit()"
                                >
                                    Cancel
                                </x-secondary-button>

                                <x-primary-button type="submit">
                                    Save Changes
                                </x-primary-button>

                            </div>

                        </div>

                    </div>
                </div>
            </form>

            <!-- Avatar Modal -->
            <x-modal name="users-avatar" :show="false" maxWidth="md">
                <div
                    x-data="{
                        imagePreview: '{{ $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : '' }}',
                        hasExistingAvatar: {{ $user->profile?->avatar ? 'true' : 'false' }},
                        isDeleted: false,
                        showDeleteConfirm: false,
                        triggerFileSelect() { this.$refs.fileInput.click(); }
                    }"
                    class="p-6 text-slate-800 dark:text-gray-200 bg-white dark:bg-gray-800 rounded-lg"
                >
                    <!-- Modal Header -->
                    <div class="border-b border-gray-100 dark:border-gray-700 pb-3 mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ __('Update User Avatar') }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Choose a high-quality profile photo (JPEG, PNG, or WebP) as your account identity identifier.
                        </p>
                    </div>

                    <form action="{{ route('admin.users.avatar.update', $user) }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                        @csrf

                        <input type="hidden" name="delete_avatar" :value="isDeleted ? '1' : '0'">

                        <!-- Avatar Preview -->
                        <div x-show="!showDeleteConfirm" class="flex flex-col items-center justify-center my-8 transition duration-200">
                            <div class="relative group w-32 h-32 rounded-full border-2 border-dashed border-gray-300 dark:border-gray-600 p-1 flex items-center justify-center bg-gray-50 dark:bg-gray-700 overflow-hidden">

                                <template x-if="imagePreview && !isDeleted">
                                    <img :src="imagePreview" class="w-full h-full rounded-full object-cover">
                                </template>

                                <template x-if="!imagePreview || isDeleted">
                                    <div class="w-full h-full rounded-full bg-slate-200 dark:bg-gray-600 text-slate-500 dark:text-gray-300 flex flex-col items-center justify-center select-none text-center p-2">
                                        <i class="fa-solid fa-image text-2xl mb-1 text-slate-400"></i>
                                        <span class="text-[10px] font-semibold text-slate-400">No Image</span>
                                    </div>
                                </template>

                                <template x-if="imagePreview && !isDeleted">
                                    <div class="absolute inset-0 bg-neutral-900/60 flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-full text-white">
                                        <button type="button" @click="triggerFileSelect()" class="p-2 bg-blue-600 rounded-full hover:bg-blue-500 transition shadow-md" title="Replace profile image">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>

                                        <button
                                            type="button"
                                            @click="if(hasExistingAvatar) { showDeleteConfirm = true; } else { imagePreview = ''; isDeleted = true; $refs.fileInput.value = ''; }"
                                            class="p-2 bg-red-600 rounded-full hover:bg-red-500 transition shadow-md"
                                            title="Remove photo"
                                        >
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    </div>
                                </template>

                                <template x-if="!imagePreview || isDeleted">
                                    <button type="button" @click="triggerFileSelect()" class="absolute inset-0 w-full h-full rounded-full focus:outline-none opacity-0 group-hover:opacity-100 bg-neutral-900/20 transition-opacity flex items-center justify-center text-slate-700">
                                        <span class="text-xs font-bold bg-white px-2 py-1 rounded shadow-sm">Select File</span>
                                    </button>
                                </template>
                            </div>

                            <input
                                x-ref="fileInput"
                                type="file"
                                name="avatar"
                                accept="image/jpeg,image/png,image/webp"
                                class="hidden"
                                @change="
                                    const file = $event.target.files[0];
                                    if (file) {
                                        isDeleted = false;
                                        imagePreview = URL.createObjectURL(file);
                                    }
                                "
                            />

                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                                Accepts JPG, PNG or WEBP. Max 2MB.
                            </p>
                        </div>

                        <!-- Delete Confirmation -->
                        <div x-show="showDeleteConfirm" x-transition class="bg-red-50 dark:bg-red-950/40 p-5 rounded-xl border border-red-200 dark:border-red-900 text-center space-y-4 my-6">
                            <div class="flex items-center justify-center text-red-600 mx-auto w-12 h-12 bg-red-100 dark:bg-red-900/50 rounded-full">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </div>

                            <div>
                                <h4 class="text-sm font-bold text-red-800 dark:text-red-300">
                                    Are you sure you want to remove this avatar?
                                </h4>
                                <p class="text-xs text-red-600 dark:text-red-400 mt-1">
                                    This operation will permanently delete the physical image file stored in our systems.
                                </p>
                            </div>

                            <div class="flex justify-center space-x-2 pt-1">
                                <button
                                    type="button"
                                    class="bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-3 py-2 rounded-md text-xs font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition shadow-sm"
                                    @click="showDeleteConfirm = false"
                                >
                                    No, Keep It
                                </button>

                                <button
                                    type="button"
                                    class="bg-red-600 text-white px-3 py-2 rounded-md text-xs font-semibold hover:bg-red-700 shadow transition"
                                    @click="
                                        isDeleted = true;
                                        showDeleteConfirm = false;
                                        imagePreview = '';
                                        $refs.fileInput.value = '';
                                    "
                                >
                                    Yes, Remove Image
                                </button>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <x-secondary-button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'users-avatar'); showDeleteConfirm = false;"
                                class="dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600"
                            >
                                {{ __('Cancel') }}
                            </x-secondary-button>

                            <x-primary-button
                                type="submit"
                                ::disabled="showDeleteConfirm"
                                ::class="{ 'opacity-50 cursor-not-allowed': showDeleteConfirm }"
                            >
                                {{ __('Save Changes') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
    </div>
</x-app-layout>
