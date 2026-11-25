<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->isActive()) {
            abort(403, 'Your account or assigned role is inactive.');
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }

}
