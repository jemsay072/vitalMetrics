<x-app-layout>
    <div>
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                    title="Blood Pressure"
                    description="Blood Pressure Description"
                    :breadcrumbs="[
                        ['label' => 'Home', 'url' => '/'],
                    ]"
                >
                {{-- <x-slot name="actions">
                    <button
                        x-data
                        x-on:click="$dispatch('open-modal', 'bp-form')"
                        type="button"
                        class="text-slate-500 border border-blue-500 hover:bg-blue-500 hover:text-white focus:ring-4 focus:ring-brand-medium font-medium rounded text-sm px-4 py-2.5"
                    >
                        + Blood Pressure
                    </button>
                </x-slot> --}}
            </x-page-header>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            test only
        </div>
    </div>
</x-app-layout>
