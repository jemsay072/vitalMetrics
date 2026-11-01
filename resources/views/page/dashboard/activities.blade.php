<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <x-page-header
                title="All Activity Logs"
                description="Review your chronological health tracking updates and system activity trails."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Dashboard', 'url' => '/dashboard'],
                    ['label' => 'Activity Logs', 'url' => '#']
                ]"
            />
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- MAIN LOGS TABLE PANEL -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-100 text-gray-500 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200">
                                <th class="px-5 py-3">Activity Type</th>
                                <th class="px-5 py-3">Details / Remarks</th>
                                <th class="px-5 py-3">Status</th>
                                <th class="px-5 py-3">Logged Date Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-600">
                            @forelse ($activities as $activity)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-5 py-3.5 font-bold text-slate-800 uppercase text-xs tracking-wider">
                                        {{ $activity->type }}
                                    </td>
                                    <td class="px-5 py-3.5 text-gray-500">
                                        {{ $activity->description }}
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold uppercase tracking-wide
                                            @switch(strtolower($activity->status))
                                                @case('normal')
                                                @case('completed')
                                                    bg-green-100 text-green-700
                                                    @break

                                                @case('updated')
                                                @case('modified')
                                                    bg-amber-100 text-amber-700
                                                    @break

                                                @case('deleted')
                                                    bg-red-100 text-red-700
                                                    @break

                                                @case('low')
                                                @case('hypotension')
                                                    bg-blue-100 text-blue-700
                                                    @break

                                                @case('prehypertension')
                                                @case('warning')
                                                    bg-orange-100 text-orange-700
                                                    @break

                                                @case('high')
                                                @case('hypertension')
                                                @case('danger')
                                                    bg-red-200 text-red-800 font-extrabold animate-pulse
                                                    @break

                                                @default
                                                    bg-gray-100 text-gray-700
                                            @endswitch">
                                            {{ $activity->status ?? 'Completed' }}
                                        </span>


                                    </td>
                                    <td class="px-5 py-3.5 text-gray-400 text-xs">
                                        {{ $activity->created_at->format('M d, Y h:i A') }} ({{ $activity->created_at->diffForHumans() }})
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400 text-xs">No activity logs found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- PAGINATION LINKS NAV -->
                @if($activities->hasPages())
                    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50">
                        {{ $activities->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
