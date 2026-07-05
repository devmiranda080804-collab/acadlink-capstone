<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function show()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = Auth::user();

        $user->update([
            'password'             => Hash::make($request->password),
            'must_change_password' => false,
        ]);

        return redirect($this->dashboardFor($user->role))
            ->with('success', 'Password updated successfully.');
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