<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

// A Program Head can lead several programs and works in one at a time. The Program Head
// pages read the user's program, so for this request it is the one chosen in the switcher.
class UseActiveProgram
{
    public const SESSION_KEY = 'active_program';

    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->role === 'program_head') {
            $codes = $user->programCodes();
            $chosen = $request->session()->get(self::SESSION_KEY);
            $active = in_array($chosen, $codes, true) ? $chosen : ($codes[0] ?? null);

            // In memory only: marked as unchanged so it is never saved by accident.
            $user->forceFill(['program' => $active])->syncOriginalAttribute('program');
        }

        return $next($request);
    }
}
