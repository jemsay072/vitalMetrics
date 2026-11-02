<nav x-data="{ open: false, dropdownOpen: false }" @click.outside="open = false; dropdownOpen = false" class="relative text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links Placeholder -->
            </div>

            <!-- OPTION 1: FLOATING MINIMALIST CARD PROFILE SECTION -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 relative">
                <!-- TRIGGER BUTTON: Tinanggal ang puting box at border para natural na lumutang sa dark bar -->
                <button
                    @click="dropdownOpen = !dropdownOpen"
                    class="inline-flex items-center gap-2 px-3 py-2 text-sm font-semibold text-gray-200 hover:text-white focus:outline-none transition ease-in-out duration-150 cursor-pointer"
                >
                    <span>{{ Auth::user()->name }}</span>
                    <svg
                        class="h-3.5 w-3.5 text-gray-400 transition-transform duration-200"
                        :class="{'rotate-180 text-white': dropdownOpen}"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>

                <!-- PURE TAILWIND DROPDOWN CARD: Floating look, rounder corners, and deep premium shadow -->
                <div
                    x-cloak
                    x-show="dropdownOpen"
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 scale-95 translate-y-1"
                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                    x-transition:leave-end="opacity-0 scale-95 translate-y-1"
                    class="absolute right-0 top-full mt-1.5 w-56 rounded-2xl border border-gray-100 bg-white p-1.5 shadow-2xl ring-1 ring-black ring-opacity-5 z-50 overflow-hidden"
                >
                    <!-- Profile User Profile Information Panel Header -->
                    <div class="px-3.5 py-3 border-b border-gray-50 mb-1.5 bg-gray-50/50 rounded-t-xl">
                        <p class="text-sm font-bold text-gray-800 tracking-wide truncate">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] font-semibold text-gray-400 truncate mt-0.5">{{ Auth::user()->email }}</p>
                    </div>

                    <!-- Action Links Container Area -->
                    <div class="space-y-0.5">
                        @if (auth()->user()->hasPermission('profile'))
                            <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition duration-150">
                                <i class="fa-regular fa-user text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                                <span>Profile</span>
                            </a>
                        @endif

                        <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition duration-150">
                            <i class="fa-solid fa-gear text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                            <span>Settings</span>
                        </a>

                        <!-- FIXED ACTION GUARD LAYER: Hindi lumalabas kapag ang role_id ay Admin (1) -->
                        @if (auth()->user()->role_id !== 1)
                            <a href="{{ route('dashboard.activities') }}" class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition duration-150">
                                <i class="fa-solid fa-clock-rotate-left text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                                <span>Activity Logs</span>
                            </a>
                        @endif
                    </div>

                    <!-- System Log Out Action Container Node -->
                    <div class="mt-1.5 pt-1.5 border-t border-gray-100">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button
                                type="submit"
                                class="group flex w-full items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-black text-red-600 hover:bg-red-50 hover:text-red-700 transition duration-150 text-left cursor-pointer"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                            >
                                <i class="fa-solid fa-arrow-right-from-bracket text-red-400 group-hover:text-red-500 w-4 text-center text-sm transition"></i>
                                <span>Log Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Hamburger Button for Mobile Layout Controls -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu Mobile Viewports Layout Drawer -->
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="sm:hidden absolute top-full right-4 mt-2 z-20 w-[calc(100vw-2rem)] max-w-xs rounded-2xl bg-white shadow-2xl ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
            <p class="text-sm font-bold text-gray-800 tracking-wide">{{ Auth::user()->name }}</p>
            <p class="text-[11px] font-semibold text-gray-400 mt-0.5">{{ Auth::user()->email }}</p>
        </div>

        <div class="space-y-0.5 px-2 py-2">
            @if (auth()->user()->hasPermission('profile'))
                <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                    <span class="inline-flex items-center gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-regular fa-user text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                        <span>{{ __('Profile') }}</span>
                    </span>
                </x-responsive-nav-link>
            @endif

            <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                <span class="inline-flex items-center gap-3 text-xs font-bold text-slate-600">
                    <i class="fa-solid fa-gear text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                    <span>{{ __('Settings') }}</span>
                </span>
            </x-responsive-nav-link>

            @if (auth()->user()->role_id !== 1)
                <x-responsive-nav-link :href="route('dashboard.activities')" @click="open = false">
                    <span class="inline-flex items-center gap-3 text-xs font-bold text-slate-600">
                        <i class="fa-solid fa-clock-rotate-left text-gray-400 group-hover:text-slate-500 w-4 text-center text-sm transition"></i>
                        <span>{{ __('Activity Logs') }}</span>
                    </span>
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="border-t border-gray-100 px-2 py-2">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" class="hover:bg-red-50 rounded-xl" onclick="event.preventDefault(); this.closest('form').submit(); open = false;">
                    <span class="inline-flex items-center gap-3 text-xs font-black text-red-600">
                        <i class="fa-solid fa-arrow-right-from-bracket text-red-400 w-4 text-center text-sm"></i>
                        <span>{{ __('Log Out') }}</span>
                    </span>
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
