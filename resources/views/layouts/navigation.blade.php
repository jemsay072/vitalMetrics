<nav x-data="{ open: false }" @click.outside="open = false" class="relative text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                {{-- <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div> --}}
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-gray-700 bg-white hover:bg-gray-100 focus:outline-none transition ease-in-out duration-150 shadow-sm">
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
                        </div>
                        <div class="py-1">
                            <x-dropdown-link :href="route('profile.edit')" icon="fa-regular fa-user">
                                {{ __('Profile') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('profile.edit')" icon="fa-solid fa-gear">
                                {{ __('Settings') }}
                            </x-dropdown-link>
                        </div>
                        <div class="py-1 border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault(); this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </div>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
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

    <!-- Responsive Navigation Menu -->
    <div x-cloak x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="sm:hidden absolute top-full right-4 mt-2 z-20 w-[calc(100vw-2rem)] max-w-xs rounded-[30px] bg-white shadow-2xl ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
            <p class="text-xs text-gray-500">{{ Auth::user()->email }}</p>
        </div>
        <div class="space-y-1 px-2 py-3">
            <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                <span class="inline-flex items-center gap-2">
                    <i class="fa-regular fa-user text-gray-500"></i>
                    <span>{{ __('Profile') }}</span>
                </span>
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('profile.edit')" @click="open = false">
                <span class="inline-flex items-center gap-2">
                    <i class="fa-solid fa-gear text-gray-500"></i>
                    <span>{{ __('Settings') }}</span>
                </span>
            </x-responsive-nav-link>
        </div>
        <div class="border-t border-gray-100 px-2 py-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link :href="route('logout')" class="text-red-600 hover:bg-red-50" onclick="event.preventDefault(); this.closest('form').submit(); open = false;">
                    <span class="inline-flex items-center gap-2">
                        <i class="fa-solid fa-right-from-bracket text-red-500"></i>
                        <span>{{ __('Log Out') }}</span>
                    </span>
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
