<div class="max-w-7xl w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Breadcrumbs -->
    <nav class="flex text-sm text-gray-500 mb-2" aria-label="Breadcrumb">
        @foreach ($breadcrumbs as $crumb)
            <a href="{{ $crumb['url'] }}" class="hover:text-blue-600">
                {{ $crumb['label'] }}
            </a>
            @if (!$loop->last)
                <span class="mx-2">/</span>
            @endif
        @endforeach
    </nav>

    <!-- Title + Button Row -->
    <div class="flex items-center justify-between"">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
            @if($desc)
                <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
            @endif
        </div>
        <!-- Optional Button -->
        <div>{{ $actions ?? '' }}</div>
    </div>
</div>
