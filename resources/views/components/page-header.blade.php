{{-- MANDATORY PROPERTY INITIALIZATION FRAMEWORK INJECTOR LAYER --}}
@props([
    'title',
    'description' => null,
    'desc' => null,
    'breadcrumbs' => []
])

<div class="max-w-7xl w-full mx-auto py-4 sm:py-5 px-4 sm:px-6 lg:px-8">

    {{-- Breadcrumbs Section --}}
    <nav
        class="flex text-[11px] font-semibold text-gray-400 dark:text-gray-400 mb-1 tracking-wide"
        aria-label="Breadcrumb"
    >
        @foreach ($breadcrumbs as $crumb)

            <a
                href="{{ $crumb['url'] }}"
                class="hover:text-blue-600 dark:hover:text-blue-400 transition duration-150"
            >
                {{ $crumb['label'] }}
            </a>

            @if (!$loop->last)
                <span class="mx-1.5 text-gray-300 dark:text-gray-600">/</span>
            @endif

        @endforeach
    </nav>

    {{-- Title + Description --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <div class="flex-1">

            {{-- Page Title --}}
            <h1 class="text-xl sm:text-2xl font-black text-gray-900 dark:text-white tracking-tight">
                {{ $title }}
            </h1>

            @php
                $headerDesc = $description ?? ($desc ?? null);
            @endphp

            @if(!empty($headerDesc))

                {{-- Page Description --}}
                <p class="mt-0.5 text-sm font-medium text-gray-500 dark:text-gray-400 leading-relaxed max-w-2xl">
                    {{ $headerDesc }}
                </p>

            @endif

        </div>

        {{-- Optional Actions Slot --}}
        <div class="flex-shrink-0 flex items-center gap-2">
            {{ $actions ?? '' }}
        </div>

    </div>

</div>
