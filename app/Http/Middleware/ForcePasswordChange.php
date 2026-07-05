<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->must_change_password && ! $request->is('change-password') && ! $request->is('logout')) {
            return redirect('/change-password');
        }

        return $next($request);
    }
}