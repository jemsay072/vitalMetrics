<div class="flex flex-col">
    <div class="flex p-4 text-white bg-red-400 items-center justify-between">
        <!-- Logo -->
        <div class="shrink-0 flex items-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            </a>
        </div>
        <div class="flex hover:cursor-pointer">
            <i class="fa-solid fa-align-justify"></i>
            <span class="hidden">Toggle Here</span>
        </div>
    </div>
    <nav class="flex justify-center flex-col text-white">
        <x-nav-link :href="route('dashboard')" class="pb-1 px-3 !py-4 !text-md text-slate-300 border-b-0 hover:bg-[#4c4c4c] hover:text-slate-100 focus:text-slate-100 active:bg-[#4c4c4c] gap-3" icon="fa-solid fa-chart-column">
            {{__('Dashboard')}}
        </x-nav-link>
        <x-nav-link :href="route('dashboard')" class="pb-1 px-3 !py-4 !text-md text-slate-300 border-b-0 hover:bg-[#4c4c4c] hover:text-slate-100 focus:text-slate-100 active:bg-[#4c4c4c] gap-3" icon="fa-solid fa-fire">
            {{__('Calories Burn')}}
        </x-nav-link>
        <x-nav-link :href="route('dashboard')" class="pb-1 px-3 !py-4 !text-md text-slate-300 border-b-0 hover:bg-[#4c4c4c] hover:text-slate-100 focus:text-slate-100 active:bg-[#4c4c4c] gap-3" icon="fa-solid fa-map-location">
            {{__('Distance')}}
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