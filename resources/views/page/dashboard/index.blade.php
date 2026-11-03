<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto flex items-center justify-between">

            <!-- HEADLINE GREETINGS & PAGE HEADER -->
            <x-page-header
                title="Dashboard"
                description="Welcome back, user. Here is the real-time overview of your vital health metrics trackers."
                :breadcrumbs="[
                    ['label' => 'Home', 'url' => '/'],
                    ['label' => 'Dashboard', 'url' => '/dashboard']
                ]"
            />
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- THREE-COLUMN HIGHLIGHT COUNTER CARDS MATRIX (LATEST METRICS) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                <!-- Card Slot: Blood Pressure Tracking Profile -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-300">Latest Blood Pressure</p>
                        <h3 class="mt-1 text-2xl font-black text-slate-800 dark:text-white">
                            <span class="text-gray-600 dark:text-white">{{ $latestBP ? $latestBP->systolic . '/' . $latestBP->diastolic : '--/--' }}</span>
                            <span class="text-xs font-bold text-gray-400 dark:text-white block sm:inline sm:ml-1">mmHg</span>
                        </h3>
                        <p class="mt-1 text-[11px] font-semibold text-gray-400 dark:text-white">Total logs: {{ $totalBPCount }} readings</p>
                    </div>
                    <div class="p-3 bg-red-50 text-red-500 rounded-lg">
                        <i class="fa-solid fa-heartbeat text-xl"></i>
                    </div>
                </div>

                <!-- Card Slot: Weight Tracker Matrix -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-300">Latest Weight</p>
                        <h3 class="mt-1 text-2xl font-black text-slate-800">
                            <span class="text-gray-600 dark:text-white">{{ $latestWeight ? $latestWeight->value : '--' }}</span>
                            <span class="text-xs font-bold text-gray-400 dark:text-white block sm:inline sm:ml-1">
                                {{ auth()->user()->preferences?->weight_unit ?? 'kg' }}
                            </span>
                        </h3>
                        <p class="mt-1 text-[11px] font-semibold text-gray-400 dark:text-gray-300">Total logs: {{ $totalWeightCount }} inputs</p>
                    </div>
                    <div class="p-3 bg-blue-50 text-blue-500 rounded-lg">
                        <i class="fa-solid fa-weight-scale text-xl"></i>
                    </div>
                </div>

                <!-- Card Slot: Workout Summary Tracker -->
                <div class="p-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-300">Latest Workout Session</p>
                        <h3 class="mt-1 text-2xl font-black text-slate-800">
                            <span class="text-gray-600 dark:text-white">{{ $latestWorkout ? $latestWorkout->duration : '0' }}</span>
                            <span class="text-xs font-bold text-gray-400 dark:text-white block sm:inline sm:ml-1">mins</span>
                        </h3>
                        <p class="mt-1 text-[11px] font-semibold text-gray-400 dark:text-gray-300">Total logs: {{ $totalWorkoutCount }} sessions</p>
                    </div>
                    <div class="p-3 bg-amber-50 text-amber-500 rounded-lg">
                        <i class="fa-solid fa-dumbbell text-xl"></i>
                    </div>
                </div>

            </div>

            <!-- LOWER SECTIONS PANEL: RECENT ACTIVITIES LIST && SUMMARY SUMMARY GRAPH OVERVIEW -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Table Panel Component Space: Occupies 2 columns on wide layouts -->
                <div class="lg:col-span-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 flex items-center justify-between bg-gray-50 dark:bg-gray-700">
                        <h3 class="text-xs font-bold text-slate-800 uppercase tracking-wider dark:text-gray-400">Recent Personal Activities</h3>
                        <a href="/dashboard/activities" class="text-xs font-semibold text-blue-600 hover:text-blue-800 transition">View History →</a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-300 uppercase text-[10px] font-bold tracking-wider border-b border-gray-200 dark:border-gray-600">
                                    <th class="px-5 py-3">Activity Type</th>
                                    <th class="px-5 py-3">Details / Remarks</th>
                                    <th class="px-5 py-3">Status / Progress</th>
                                    <th class="px-5 py-3">Logged Date Time</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700 text-sm text-gray-600 dark:text-gray-300">
                                @forelse ($recentActivities as $activity)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        <td class="px-5 py-3.5 font-bold text-slate-800 dark:text-gray-200 uppercase text-xs tracking-wider">
                                            {{ $activity->type }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-xs">
                                            {{ $activity->description ?? '-' }}
                                        </td>
                                        <td class="px-5 py-3.5 text-gray-400 dark:text-gray-400 text-xs">
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
                                        <td class="px-5 py-3.5 text-gray-400 dark:text-gray-400 text-xs">
                                            {{ $activity->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-8 text-gray-400 text-xs">No recent physical health activities logged inside this channel timeline.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Side Tracker Bar: Health Summary Matrix Insights -->
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-sm p-5 transition-colors duration-300 flex flex-col justify-between">
                    <div>
                       <h3 class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-wider border-b border-gray-100 dark:border-gray-700 pb-3 mb-4">Weekly Progress Insights</h3>
                        <div class="space-y-4">
                            <!-- Progress Bar Row Element 1: Blood Pressure Logs Metric -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-300 mb-1">
                                    <span>BP Log Target Consistency</span>
                                    <span class="text-blue-600">{{ $totalBPCount > 0 ? min($totalBPCount * 10, 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full transition-all duration-500" style="width: {{ $totalBPCount > 0 ? min($totalBPCount * 10, 100) : 0 }}%"></div>
                                </div>
                            </div>

                            <!-- Progress Bar Row Element 2: Workout Logs Metric -->
                            <div>
                                <div class="flex items-center justify-between text-xs font-bold text-gray-500 dark:text-gray-300 mb-1">
                                    <span>Workout Target Completion</span>
                                    <span class="text-amber-600">{{ $totalWorkoutCount > 0 ? min($totalWorkoutCount * 20, 100) : 0 }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2">
                                    <div class="bg-amber-400 h-2 rounded-full transition-all duration-500" style="width: {{ $totalWorkoutCount > 0 ? min($totalWorkoutCount * 20, 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informational Footnote Tip Component -->
                    <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-700 text-[11px] text-gray-400 dark:text-gray-400 flex items-start gap-2">
                        <i class="fa-solid fa-heart-pulse text-blue-500 mt-0.5"></i>
                        <span>Keep logging your regular metrics data points to update your profile safety score rating charts automatically.</span>
                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>
