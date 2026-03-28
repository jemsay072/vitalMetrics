{{-- <x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> --}}
<x-app-layout>
    <x-page-header 
        title="Dashboard" 
        description="Welcome back, user." 
        :breadcrumbs="[
            ['label' => 'Home', 'url' => '/'],
            ['label' => 'Dashboard', 'url' => '/dashboard']
        ]" 
    />
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        Dashboard is here
    </div>
</x-app-layout>