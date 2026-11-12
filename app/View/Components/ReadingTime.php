<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ReadingTime extends Component
{
    public function __construct(
        public string $name = 'reading_time',
        public string $id = 'reading_time',
        public string $label = 'Reading Time / Timestamp',
        public bool $required = true,
        public ?string $value = null,
        public bool $listenForUpdates = false,
    ) {
        //
    }

    public function render(): View|Closure|string
    {
        return view('components.reading-time');
    }
}
