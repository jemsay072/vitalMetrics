<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if(! auth()->check()){
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (! $user->isAdmin()) {
            abort(403, 'Unauthorized.');
        }

        // Check if assigned Admin role is active
        // if($user->role?->status !== 'active' ){
        //     abort(403, 'Your assigned role is inactive');
        // }

        return $next($request);
    }
}
