
@if (auth()->user()->isAdmin())
    <x-admin.side-bar />
@else
    <x-user.side-bar />
@endif
