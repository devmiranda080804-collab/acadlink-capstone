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
            return back()->with('success', 'Naipadala na ang password reset link sa iyong email. Pakitingnan ang iyong inbox.');
        }

        return back()->withErrors([
            'email' => 'Walang account na may ganitong email address.'
        ]);
    }
}