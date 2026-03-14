<div class="pb-6 border-b border-gray-200 mb-6">
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

    <!-- Title and Description -->
    <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
    @if($desc)
        <p class="mt-2 text-sm text-gray-600">{{ $desc }}</p>
    @endif
</div>
