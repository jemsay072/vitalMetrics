<div class="flex flex-col">
    <div class="flex p-4 text-white bg-red-400 items-center justify-between">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('admin.dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
        </div>
        <!-- Mobile close button -->
        <button x-on:click="$dispatch('close-sidebar')"
                class="lg:hidden text-white hover:text-gray-300 focus:outline-none focus:text-gray-300">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <nav class="flex justify-center flex-col text-white">
        @if (auth()->user()->hasPermission('dashboard'))
            <x-nav-link :href="route('admin.dashboard')" variant="sidebar" icon="fa-solid fa-chart-column">
                {{__('Dashboard')}}
            </x-nav-link>
        @endif
        @if (auth()->user()->hasPermission('users'))
            <x-nav-link :href="route('admin.users')" variant="sidebar" icon="fa-solid fa-users">
                {{__('Users Management')}}
            </x-nav-link>
        @endif
        @if (auth()->user()->hasPermission('settings'))
            <x-nav-link :href="route('admin.settings')" variant="sidebar" icon="fa-solid fa-user-gear">
                {{__('Admin Settings')}}
            </x-nav-link>
        @endif
        @if (auth()->user()->hasPermission('roles'))
           <x-nav-link :href="route('admin.role')" variant="sidebar" icon="fa-solid fa-shield">
                {{__('Role Management')}}
            </x-nav-link>
        @endif

    </nav>
</div>
<a href="{{route('profile.edit')}}" class="profile flex items-center gap-2.5 px-2">
    <div class="flex items-center justify-center shrink-0">
        @if (auth()->check() && auth()->user()->profile?->avatar)
            <div class="rounded-full bg-amber-500 p-0.5">
                <img src="{{ asset('storage/' . auth()->user()->profile->avatar) }}" alt="avatar" class="w-10 h-10 rounded-full object-cover">
            </div>
        @else
            <div class="w-10 h-10 rounded-full text-white font-bold flex items-center justify-center tracking-wider text-sm shadow-sm select-none border {{ auth()->check() ? auth()->user()->avatar_color : 'bg-gray-500 border-gray-600' }}">
                {{ auth()->check() ? auth()->user()->initials : '??' }}
            </div>

        @endif
    </div>
    <div class="prof-info flex flex-col font-medium text-heading">
        <p class="text-white">{{ auth()->check() ? auth()->user()->name : '??' }}</p>
        <span class="text-xs font-bold text-body text-emerald-600">Online</span>
    </div>
</a>
