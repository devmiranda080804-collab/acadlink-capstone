<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (! Auth::attempt([
            'email'    => $request->email,
            'password' => $request->password,
        ])) {
            return back()->withErrors([
                'login' => 'Invalid email or password.',
            ])->onlyInput('email');
        }

        $user = Auth::user();

        if (! $user->is_active) {
            Auth::logout();
            return back()->withErrors([
                'login' => 'Your account has been deactivated. Please contact the admin.',
            ]);
        }

        $request->session()->regenerate();

        if ($user->must_change_password) {
            return redirect('/change-password');
        }

        return redirect($this->dashboardFor($user->role));
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    protected function dashboardFor(string $role): string
    {
        return match ($role) {
            'admin'        => '/admin/dashboard',
            'program_head' => '/program-head/dashboard',
            'secretary'    => '/secretary/dashboard',
            'faculty'      => '/faculty/dashboard',
            default        => '/login',
        };
    }
}