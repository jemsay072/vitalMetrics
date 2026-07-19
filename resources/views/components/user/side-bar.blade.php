<div class="flex flex-col">
    <div class="flex p-4 text-white bg-red-400 items-center justify-between">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}">
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
        <x-nav-link :href="route('dashboard')" variant="sidebar" icon="fa-solid fa-chart-column">
            {{__('Dashboard')}}
        </x-nav-link>
        <x-nav-link :href="route('workout-tracker')" variant="sidebar" icon="fa-solid fa-dumbbell">
            {{__('Workout')}}
        </x-nav-link>
        <x-nav-link :href="route('bp')" variant="sidebar" icon="fa-solid fa-fire">
            {{__('BP')}}
        </x-nav-link>
        <x-nav-link :href="route('weight')" variant="sidebar" icon="fa-solid fa-fire">
            {{__('Weight Tracker')}}
        </x-nav-link>
    </nav>
</div>
<a href="{{route('profile.edit')}}" class="profile flex items-center gap-2.5 px-2">
    <div class="flex items-center gap-2.5 rounded-full bg-amber-500 px-1">
        <img src="{{ asset('assets/img/avatar.png') }}" alt="avatar" class="w-10">
    </div>
    <div class="prof-info flex flex-col font-medium text-heading">
        <p class="text-white">John Doe Smith</p>
        <span class="text-xs font-bold text-body text-emerald-600">Online</span>
    </div>
</a>
