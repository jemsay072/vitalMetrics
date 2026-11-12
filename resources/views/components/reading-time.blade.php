@props([
    'name' => 'reading_time',
    'id' => 'reading_time',
    'label' => 'Reading Time / Timestamp',
    'required' => true,
    'value' => null,
    'listenForUpdates' => false,
])

@php
    $initialValue = old($name, $value);
@endphp

<div
    x-data="readingTime(
        @js($initialValue ?? ''),
        @js($listenForUpdates)
    )"
    class="relative"
    @click.outside="open = false"
>
    {{-- Label --}}
    <label
        for="{{ $id }}"
        class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1.5"
    >
        {{ $label }}
    </label>

    {{-- Hidden input: actual value submitted to Laravel --}}
    <input
        type="hidden"
        name="{{ $name }}"
        id="{{ $id }}"
        x-model="timeValue"
        @if($required) required @endif
    >

    {{-- Visible picker button --}}
    <button
        type="button"
        @click="open = !open"
        class="flex items-center justify-between w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-2.5 text-sm font-medium text-gray-900 dark:text-white bg-white dark:bg-gray-700 shadow-sm hover:border-emerald-400 dark:hover:border-emerald-500 focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition duration-150"
        :class="error ? 'border-red-500 dark:border-red-500' : ''"
    >
        <span
            x-text="displayTime"
            :class="timeValue ? 'text-gray-900 dark:text-white' : 'text-gray-400 dark:text-gray-500'"
        ></span>

        <svg
            class="w-4 h-4 text-gray-500 dark:text-gray-400"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <circle cx="12" cy="12" r="10" stroke-width="2"/>
            <path stroke-linecap="round" stroke-width="2" d="M12 6v6l4 2"/>
        </svg>
    </button>

    {{-- Validation message --}}
    <p
        x-show="error"
        x-cloak
        class="text-xs text-red-500 mt-1"
    >
        Please select a complete reading time.
    </p>

    {{-- Custom dropdown --}}
    <div
        x-show="open"
        x-cloak
        x-transition
        class="absolute z-[100] mt-2 left-0 w-full sm:w-64 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 shadow-xl p-3"
    >
        <div class="grid grid-cols-3 gap-2">

            {{-- Hour --}}
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase text-center mb-2">
                    Hour
                </p>

                <div class="h-40 overflow-y-auto space-y-1 pr-1 reading-time-scroll">
                    @foreach(range(1, 12) as $hour)
                        <button
                            type="button"
                            @click="selectHour('{{ sprintf('%02d', $hour) }}')"
                            class="w-full py-2 rounded-lg text-sm font-medium transition"
                            :class="hour === '{{ sprintf('%02d', $hour) }}'
                                ? 'bg-emerald-600 text-white'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700'"
                        >
                            {{ sprintf('%02d', $hour) }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Minute --}}
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase text-center mb-2">
                    Minute
                </p>

                <div class="h-40 overflow-y-auto space-y-1 pr-1 reading-time-scroll">
                    @foreach(range(0, 59) as $minute)
                        <button
                            type="button"
                            @click="selectMinute('{{ sprintf('%02d', $minute) }}')"
                            class="w-full py-2 rounded-lg text-sm font-medium transition"
                            :class="minute === '{{ sprintf('%02d', $minute) }}'
                                ? 'bg-emerald-600 text-white'
                                : 'text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700'"
                        >
                            {{ sprintf('%02d', $minute) }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- AM / PM --}}
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase text-center mb-2">
                    Period
                </p>

                <div class="space-y-1">
                    <button
                        type="button"
                        @click="selectPeriod('AM')"
                        class="w-full py-2 rounded-lg text-sm font-medium transition"
                        :class="period === 'AM'
                            ? 'bg-emerald-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700'"
                    >
                        AM
                    </button>

                    <button
                        type="button"
                        @click="selectPeriod('PM')"
                        class="w-full py-2 rounded-lg text-sm font-medium transition"
                        :class="period === 'PM'
                            ? 'bg-emerald-600 text-white'
                            : 'text-gray-700 dark:text-gray-300 hover:bg-emerald-50 dark:hover:bg-gray-700'"
                    >
                        PM
                    </button>
                </div>
            </div>
        </div>

        {{-- Selected time preview --}}
        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs text-gray-500 dark:text-gray-400">
                    Selected time
                </span>

                <span
                    class="text-sm font-bold text-emerald-600 dark:text-emerald-400"
                    x-text="displayTime"
                ></span>
            </div>

            <button
                type="button"
                @click="updateTime()"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg py-2 transition"
            >
                Done
            </button>
        </div>
    </div>
</div>
