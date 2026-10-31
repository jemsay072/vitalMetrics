<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Redirect the page if dashboard is not exist
     */
    public function getRedirectRoute($user): string{

        if($user->hasPermission('dashboard')) return 'dashboard';

        if($user->hasPermission('trackers')) return 'bp';

        if($user->hasPermission('profile')) return 'profile.edit';

        return 'no-access';
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if($user->isAdmin()){
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->route(
            $this->getRedirectRoute($user)
        );

        // return redirect()->intended(route('dashboard'));
        // return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
