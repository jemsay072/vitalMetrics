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

        <div class="card-group">
            <x-dashboard.summary-cards name="Blood Pressure" icon="fas fa-heartbeat">
                Card
            </x-dashboard.summary-cards>
        </div>
    </div>
</x-app-layout>
