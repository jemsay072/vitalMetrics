<div class="flex flex-col">
    <div class="flex p-4 text-white bg-red-400 items-center justify-between">
        <span class="block">
            Vital Metrics
        </span>
        <div class="flex hover:cursor-pointer">
            <i class="fa-solid fa-align-justify"></i>
            <span class="hidden">Toggle Here</span>
        </div>
    </div>
    <nav class="flex justify-center flex-col text-white">
        <a href="{{route('dashboard')}}" class="px-2 py-3 hover:bg-[#4c4c4c] transition duration-300 ease-in-out">
            <span class="flex gap-2 items-center">
                <i class="fa-solid fa-chart-column"></i> Dashboard
            </span>
        </a>
        <a href="#" class="px-2 py-3 hover:bg-[#4c4c4c] transition duration-300 ease-in-out">
            <span class="flex gap-2 items-center">
                <i class="fa-solid fa-fire"></i> Calories Burn
            </span>
        </a>
        <a href="#" class="px-2 py-3 hover:bg-[#4c4c4c] transition duration-300 ease-in-out">
            <span class="flex gap-2 items-center">
                <i class="fa-solid fa-map-location"></i> Distance
            </span>
        </a>
    </nav>
</div>
<div class="profile flex items-center gap-2.5 px-2">
    <div class="flex items-center gap-2.5 rounded-full bg-amber-500 px-1">
        <img src="{{ asset('assets/img/avatar.png') }}" alt="avatar" class="w-10">
    </div>
    <div class="prof-info flex flex-col font-medium text-heading">
        <p class="text-white">John Doe Smith</p>
        <span class="text-xs font-bold text-body text-emerald-600">Online</span>
    </div>
</div>