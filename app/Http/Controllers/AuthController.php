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
            'email' => 'required|email',
            'password' => 'required',
            'login_as' => 'required'
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ]))
        {
            $user = Auth::user();

            if ($user->role !== $request->login_as)
            {
                Auth::logout();

                return back()->withErrors([
                    'login' => 'Selected role does not match your account.'
                ]);
            }

            $request->session()->regenerate();

            switch ($user->role)
            {
                case 'admin':
                    return redirect('/admin/dashboard');

                case 'program_head':
                    return redirect('/program-head/dashboard');

                case 'secretary':
                    return redirect('/secretary/dashboard');

                case 'faculty':
                    return redirect('/faculty/dashboard');

                default:
                    Auth::logout();

                    return back()->withErrors([
                        'login' => 'Unauthorized role.'
                    ]);
            }
        }

        return back()->withErrors([
            'login' => 'Invalid email or password.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}