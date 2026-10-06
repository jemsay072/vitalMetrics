<x-app-layout>
    <div>
        <!-- Page Header Area -->
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="User Details"
                description="Tingnan at i-edit ang kumpletong impormasyon ng user profile."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/admin/dashboard'],
                    ['label' => 'User', 'url' => '/admin/users'],
                ]"
            >
                {{-- Slot para sa actions kung sakaling gagamitin sa hinaharap --}}
            </x-page-header>
        </div>

        <!-- Main Body Area Layout -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
            <form
                id="user-details"
                method="POST"
                action="{{ route('admin.users.update', $user) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                <!-- Two-Column Grid System -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                    <!-- KALIWANG COLUMN: Profile Hero Card & Avatar Summary -->
                    <div class="bg-white p-6 shadow sm:rounded-lg flex flex-col items-center text-center">
                        <div class="relative mb-4 flex justify-center w-fit max-auto">
                            <button
                                type="button"
                                x-on:click="$dispatch('open-modal','users-avatar')"
                                class="group relative w-28 h-28 aspect-square rounded-full focus:outline-none focus:ring-4 focus:ring-amber-500 active:ring-4 active:ring-amber-500 transition duration-150 ease-in-out"
                            >
                                @if($user->profile?->avatar)
                                    <!-- Kapag may nakaupload na avatar image -->
                                    <div class="w-full h-full aspect-square rounded-full ring-4 ring-offset-2 ring-amber-500 overflow-hidden bg-white">
                                        <img src="{{ asset('storage/' . $user->profile->avatar) }}" alt="avatar" class="w-full h-full rounded-full object-cover group-hover:blur-[1px] transition duration-200">
                                    </div>
                                @else
                                    <!-- Kapag walang avatar, ipapakita ang initials gamit ang iyong dynamic avatar color model accessor -->
                                    <div class="w-full h-full aspect-square rounded-full text-white font-extrabold flex items-center justify-center tracking-wider text-3xl shadow-inner uppercase select-none border-4 border-white ring-4 ring-offset-2 ring-gray-200 group-hover:blur-[1px] transition duration-200 {{ auth()->check() ? $user->avatar_color : 'bg-amber-500' }}">
                                        {{ auth()->check() ? $user->initials : '??' }}
                                    </div>
                                @endif
                                <!-- Hover Overlay Icon Layer -->
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20 rounded-full opacity-0 group-hover:opacity-100 transition duration-200">
                                    <svg xmlns="http://w3.org" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </div>

                            </button>

                            <!-- Maliit na Dot Status Indicator base sa status ng piniling User -->
                            <span class="absolute bottom-2 right-2 block h-5 w-5 rounded-full border-4 border-white
                                {{ $user->status === 'inactive' ? 'bg-amber-500' : '' }}
                                {{-- {{ $user->status === 'away' ? 'bg-amber-500' : '' }}
                                {{ $user->status === 'busy' ? 'bg-red-500' : '' }}
                                {{ $user->status === 'invisible' ? 'bg-gray-400' : '' }} --}}
                                {{ $user->status === 'active' || !$user->status ? 'bg-emerald-500' : '' }}
                            ">
                            </span>
                        </div>

                        <!-- User Name at Email Header Summary -->
                        <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                        <p class="text-sm text-gray-500 mb-4">{{ $user->email }}</p>

                        <!-- Role at Status Badges -->
                        <div class="flex flex-wrap gap-2 justify-center">
                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ $user->isAdmin() ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700' }}">
                                {{ $user->role?->name ?? 'User' }}
                            </span>
                            <span class="px-3 py-1 rounded-full text-xs font-bold capitalize
                                {{ $user->status === 'active' || !$user->status ? 'bg-emerald-100 text-emerald-700' : '' }}
                                {{ $user->status === 'inactive' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{-- {{ $user->status === 'away' ? 'bg-amber-100 text-amber-700' : '' }}
                                {{ $user->status === 'busy' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $user->status === 'invisible' ? 'bg-gray-100 text-gray-700' : '' }} --}}
                            ">
                                {{ $user->status ?? 'Active' }}
                            </span>
                        </div>
                    </div>

                    <!-- KANANG COLUMN: Detailed Input Forms (Account & Profile) -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Block 1: Account Information -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900 mb-6 border-b border-gray-100 pb-2">Account Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="user_name" :value="__('Name')" />
                                    <x-text-input id="user_name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                                <div>
                                    <x-input-label for="user_email" :value="__('Email')" />
                                    <x-text-input id="user_email" name="email" type="email" class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed" :value="old('email', $user->email)" required autocomplete="email" readonly/>
                                    <x-input-error class="mt-2" :messages="$errors->get('email')" />
                                </div>
                                <div>
                                    <x-input-label for="user_role" :value="__('Role')" />
                                    <x-text-input id="user_role" type="text" class="mt-1 block w-full bg-gray-50 text-gray-500 cursor-not-allowed" :value="$user->role?->name" disabled />
                                    <x-input-error class="mt-2" :messages="$errors->get('role')" />
                                </div>
                                <div>
                                    <x-input-label for="user_status" :value="__('Status')" />
                                    <select id="user_status" name="status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="" {{ old('status', $user->status) == '' ? 'selected' : '' }}>Select Status</option>
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('status')" />
                                </div>
                            </div>
                        </div>

                        <!-- Block 2: Profile Information -->
                        <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                            <h2 class="text-lg font-medium text-gray-900 mb-6 border-b border-gray-100 pb-2">Profile Information</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="user_phone" :value="__('Phone')" />
                                    <x-text-input id="user_phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->profile?->phone)" autocomplete="tel"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                                </div>
                                <div>
                                    <x-input-label for="user_birth_date" :value="__('Birth Date')" />
                                    <x-text-input id="user_birth_date" name="birth_date" type="date" class="mt-1 block w-full" :value="old('birth_date', $user->profile?->birth_date)"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                                </div>
                                <div >
                                    <x-input-label for="user_sex" :value="__('Sex')" />
                                    <select id="user_sex" name="sex" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="" {{ old('sex', $user->profile?->sex) == '' ? 'selected' : '' }}>Select Sex</option>
                                        <option value="male" {{ old('sex', $user->profile?->sex) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('sex', $user->profile?->sex) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('sex')" />
                                </div>
                                <div>
                                    <x-input-label for="user_marital_status" :value="__('Marital Status')" />
                                    <select id="user_marital_status" name="marital_status" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm">
                                        <option value="" {{ old('marital_status', $user->profile?->marital_status) == '' ? 'selected' : '' }}>Select Marital Status</option>
                                        <option value="single" {{ old('marital_status', $user->profile?->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $user->profile?->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="widowed" {{ old('marital_status', $user->profile?->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="divorced" {{ old('marital_status', $user->profile?->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="separated" {{ old('marital_status', $user->profile?->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('marital_status')" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="user_address" :value="__('Address')" />
                                    <x-text-input id="user_address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->profile?->address)"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('address')" />
                                </div>
                                <div>
                                    <x-input-label for="user_city" :value="__('City')" />
                                    <x-text-input id="user_city" name="city" type="text" class="mt-1 block w-full" :value="old('city', $user->profile?->city)"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('city')" />
                                </div>
                                <div>
                                    <x-input-label for="user_province" :value="__('province')" />
                                    <x-text-input id="user_province" name="province" type="text" class="mt-1 block w-full" :value="old('province', $user->profile?->province)"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('province')" />
                                </div>
                                <div class="md:col-span-2">
                                    <x-input-label for="user_zip_code" :value="__('Zip Code')" />
                                    <x-text-input id="user_zip_code" name="zip_code" type="text" class="mt-1 block w-full" :value="old('zip_code', $user->profile?->zip_code)"/>
                                    <x-input-error class="mt-2" :messages="$errors->get('zip_code')" />
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons Section -->
                        <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg flex items-center justify-end gap-3">
                            <x-secondary-button type="button" onclick="window.history.back()">{{ __('Cancel') }}</x-secondary-button>
                            <x-primary-button type="submit">{{ __('Save Changes') }}</x-primary-button>
                        </div>

                    </div>
                </div>
            </form>
            <!-- Modal -->
            <x-modal name="users-avatar" :show="false" maxWidth="md">
                <div
                    x-data="{
                        imagePreview: '{{ $user->profile?->avatar ? asset('storage/' . $user->profile->avatar) : '' }}',
                        hasExistingAvatar: {{ $user->profile?->avatar ? 'true' : 'false' }},
                        isDeleted: false,
                        showDeleteConfirm: false,
                        triggerFileSelect() { this.$refs.fileInput.click(); }
                    }"
                    class="p-6 text-slate-800"
                >
                    <!-- Modal Title Area -->
                    <div class="border-b border-gray-100 pb-3 mb-6">
                        <h3 class="text-lg font-bold text-gray-900">{{ __('Update User Avatar') }}</h3>
                        <p class="text-xs text-gray-500">Choose a high-quality profile photo (JPEG, PNG, or WebP) as your account identity identifier.</p>
                    </div>

                    <form action="{{ route('admin.users.avatar.update', $user) }}" method="POST" enctype="multipart/form-data" id="avatar-form">
                        @csrf

                        <!-- Hidden Input for Database Delete State Trigger Condition Flag -->
                        <input type="hidden" name="delete_avatar" :value="isDeleted ? '1' : '0'">

                        <!-- 1. MAIN AVATAR WORKSPACE: Hidden only when deep delete confirmation warning is shown -->
                        <div x-show="!showDeleteConfirm" class="flex flex-col items-center justify-center my-8 transition duration-200">
                            <div class="relative group w-32 h-32 rounded-full border-2 border-dashed border-gray-300 p-1 flex items-center justify-center bg-gray-50 overflow-hidden">

                                <!-- [A] Render Preview Image Element State -->
                                <template x-if="imagePreview && !isDeleted">
                                    <img :src="imagePreview" class="w-full h-full rounded-full object-cover">
                                </template>

                                <!-- [B] Render Fallback Initials Placeholder Elements Layer State -->
                                <template x-if="!imagePreview || isDeleted">
                                    <div class="w-full h-full rounded-full bg-slate-200 text-slate-500 flex flex-col items-center justify-center select-none text-center p-2">
                                        <i class="fa-solid fa-image text-2xl mb-1 text-slate-400"></i>
                                        <span class="text-[10px] font-semibold text-slate-400">No Image</span>
                                    </div>
                                </template>

                                <!-- [C] Active Floating Action Buttons: Hover trigger when image preview exist -->
                                <template x-if="imagePreview && !isDeleted">
                                    <div class="absolute inset-0 bg-neutral-900/60 flex items-center justify-center gap-4 opacity-0 group-hover:opacity-100 transition-opacity duration-200 rounded-full text-white">
                                        <!-- Replace Profile Image Layer Trigger Button -->
                                        <button type="button" @click="triggerFileSelect()" class="p-2 bg-blue-600 rounded-full hover:bg-blue-500 transition shadow-md" title="Replace profile image">
                                            <i class="fa-solid fa-pen text-sm"></i>
                                        </button>
                                        <!-- Show Delete Warning Prompt Trigger Button -->
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

                                <!-- [D] Direct Click Surface Selection: Hover trigger when no active preview state image exists -->
                                <template x-if="!imagePreview || isDeleted">
                                    <button type="button" @click="triggerFileSelect()" class="absolute inset-0 w-full h-full rounded-full focus:outline-none opacity-0 group-hover:opacity-100 bg-neutral-900/20 transition-opacity flex items-center justify-center text-slate-700">
                                        <span class="text-xs font-bold bg-white px-2 py-1 rounded shadow-sm">Select File</span>
                                    </button>
                                </template>
                            </div>

                            <!-- Hidden Input Native File Selector Controller Component -->
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
                            <p class="text-xs text-gray-400 mt-3">Accepts JPG, PNG or WEBP. Max 2MB.</p>
                        </div>

                        <!-- 2. INLINE WARNING BOX WINDOW AREA PANEL: Displays only when delete confirmation request is active -->
                        <div x-show="showDeleteConfirm" x-transition class="bg-red-50 p-5 rounded-xl border border-red-200 text-center space-y-4 my-6">
                            <div class="flex items-center justify-center text-red-600 mx-auto w-12 h-12 bg-red-100 rounded-full">
                                <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-red-800">Are you sure you want to remove this avatar?</h4>
                                <p class="text-xs text-red-600 mt-1">This operation will permanently delete the physical image file stored in our systems.</p>
                            </div>
                            <div class="flex justify-center space-x-2 pt-1">
                                <button
                                    type="button"
                                    class="bg-white border border-gray-300 text-gray-700 px-3 py-2 rounded-md text-xs font-semibold hover:bg-gray-50 transition shadow-sm"
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

                        <!-- Bottom Modal Navigation Footer Control Button Area Layout Options -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                            <x-secondary-button
                                type="button"
                                x-on:click="$dispatch('close-modal', 'users-avatar'); showDeleteConfirm = false;"
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
