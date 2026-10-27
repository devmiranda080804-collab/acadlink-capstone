<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'The password reset link has been sent to your email. Please check your inbox.');
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => 'A reset link was just sent. Please wait a minute before asking for another one, and check your spam folder.'
            ])->onlyInput('email');
        }

        return back()->withErrors([
            'email' => 'No account found with that email address.'
        ])->onlyInput('email');
    }
}