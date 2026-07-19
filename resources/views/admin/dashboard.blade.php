<x-app-layout>
    <div class="p-6">
        <h1 class="text-3xl font-bold">
            Admin Dashboard
        </h1>

        <p class="mt-2 text-gray-600">
            Welcome back, {{ auth()->user()->name }}.
        </p>
    </div>
</x-app-layout>
