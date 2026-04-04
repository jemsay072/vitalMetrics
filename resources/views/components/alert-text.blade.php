@if (session('success') || session('info') || session('warning') || session('error') )
    @php
        $type = session('success') ? 'success' : 
            (session('error') ? 'error' :
            (session('warning') ? 'warning' : 'info')
        );

        $message = session($type);

        $classes = [
            'success' => 'text-green-800 bg-green-50',
            'error' => 'text-red-800 bg-red-50',
            'warning' => 'text-yellow-800 bg-yellow-50',
            'info' => 'text-blue-800 bg-blue-50',
        ]
    @endphp

    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 5000)"
        class="fixed bottom-5 right-5 z-50 flex items-center p-4 mb-4 rounded-lg shadow {{ $classes[$type] }}"
    >
        <span class="text-sm font-medium">
            {{$message}}
        </span>
    </div>
@endif