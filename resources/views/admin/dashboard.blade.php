<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- HEADLINE GREETINGS BAR -->
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Admin Dashboard
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Welcome back,
                    <span class="font-semibold text-slate-700 dark:text-gray-200">
                        {{ auth()->user()->name }}
                    </span>.
                    Here's what's happening today.
                </p>
            </div>

            <!-- THREE-COLUMN HIGHLIGHT COUNTER CARDS -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <!-- Card 1: Total Users -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Total Users
                        </p>
                        <h3 class="mt-1 text-3xl font-black text-slate-800 dark:text-white">
                            {{ $totalUsers }}
                        </h3>
                    </div>
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg">
                        <i class="fa-solid fa-users text-xl"></i>
                    </div>
                </div>

                <!-- Card 2: User Status Matrix -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            User Status Matrix
                        </p>
                        <div class="mt-1 flex items-baseline gap-2">
                            <span class="text-3xl font-black text-emerald-600 dark:text-emerald-400">
                                {{ $activeUsers }}
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Active</span>
                            </span>
                            <span class="text-xl font-bold text-red-500 dark:text-red-400">
                                / {{ $inactiveUsers }}
                                <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Inactive</span>
                            </span>
                        </div>
                    </div>
                    <div class="p-3 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-lg">
                        <i class="fa-solid fa-user-check text-xl"></i>
                    </div>
                </div>

                <!-- Card 3: Total Roles -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            Total Roles
                        </p>
                        <h3 class="mt-1 text-3xl font-black text-slate-800 dark:text-white">
                            {{ $totalRoles }}
                        </h3>
                    </div>
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-lg">
                        <i class="fa-solid fa-shield-halved text-xl"></i>
                    </div>
                </div>

            </div>

            <!-- RECENT USERS AND STATUS OVERVIEW -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Recent Users -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">

                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-900/50">
                        <h3 class="text-xs font-bold text-slate-800 dark:text-gray-200 uppercase tracking-wider">
                            Recent Registered Users
                        </h3>
                        <a href="/admin/users"
                           class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition">
                            View All →
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">

                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-900/70 text-gray-500 dark:text-gray-400 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200 dark:border-gray-700">
                                    <th class="px-5 py-3">User Profile</th>
                                    <th class="px-5 py-3">Email Address</th>
                                    <th class="px-5 py-3">Status</th>
                                    <th class="px-5 py-3">Date Joined</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-600 dark:text-gray-300">

                                @forelse ($recentUsers as $user)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">

                                        <td class="px-5 py-3.5 font-semibold text-slate-800 dark:text-gray-100">
                                            {{ $user->name }}
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400">
                                            {{ $user->email }}
                                        </td>

                                        <td class="px-5 py-3.5">
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-bold uppercase tracking-wide
                                                {{ $user->status === 'active'
                                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300'
                                                    : 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' }}">
                                                {{ $user->status ?? 'active' }}
                                            </span>
                                        </td>

                                        <td class="px-5 py-3.5 text-gray-400 dark:text-gray-500 text-xs">
                                            {{ $user->created_at->diffForHumans() }}
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-400 dark:text-gray-500 text-xs">
                                            No recent users detected within this timeline channel.
                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- User Status Overview -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-5 flex flex-col justify-between">

                    <div>
                        <h3 class="text-xs font-bold text-slate-800 dark:text-gray-200 uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">
                            User Status Overview
                        </h3>

                        <div class="space-y-4">

                            <!-- Active Accounts -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <span>Active Accounts Rate</span>
                                    <span class="text-emerald-600 dark:text-emerald-400">
                                        {{ $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 1) : 0 }}%
                                    </span>
                                </div>

                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-emerald-500 h-2 rounded-full transition-all duration-500"
                                         style="width: {{ $totalUsers > 0 ? ($activeUsers / $totalUsers) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                            <!-- Inactive Accounts -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-400 mb-1">
                                    <span>Deactivated Accounts Rate</span>
                                    <span class="text-red-500 dark:text-red-400">
                                        {{ $totalUsers > 0 ? round(($inactiveUsers / $totalUsers) * 100, 1) : 0 }}%
                                    </span>
                                </div>

                                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-2">
                                    <div class="bg-red-400 h-2 rounded-full transition-all duration-500"
                                         style="width: {{ $totalUsers > 0 ? ($inactiveUsers / $totalUsers) * 100 : 0 }}%">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 text-[11px] text-gray-400 dark:text-gray-500 flex items-start gap-2">
                        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                        <span>
                            System values automatically synchronized with account security filters block configurations.
                        </span>
                    </div>

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
