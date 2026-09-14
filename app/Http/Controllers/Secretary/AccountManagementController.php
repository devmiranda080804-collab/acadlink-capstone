<?php

namespace App\Http\Controllers\Secretary;

use App\Http\Controllers\Controller;
use App\Mail\NewAccountCredentials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->where('role', 'faculty');

        if ($request->get('status') === 'archived') {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $accounts = $query->latest()->paginate(6)->withQueryString();

        return view('secretary.sec-account-management', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'program'       => 'required|in:BSA,BSMA,BSOA',
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ]);

        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name'                 => $request->first_name . ' ' . $request->last_name,
            'email'                => $request->email,
            'google_email'         => $request->google_email,
            'password'             => Hash::make($temporaryPassword),
            'role'                 => 'faculty',
            'program'              => $request->program,
            'academic_year'        => $request->academic_year,
            'must_change_password' => true,
        ]);
        AuditLog::record('Account Created', "Created faculty account for {$user->name} ({$user->email})");

        Mail::to($user->email)->send(new NewAccountCredentials($user, $temporaryPassword));

        return redirect()->back()
            ->with('success', 'Faculty account created successfully. The login credentials have been sent to their email.');
    }

    // No email field here — this is not editable after creation, it's only set during account creation
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'program'       => 'required|in:BSA,BSMA,BSOA',
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ]);

        $user->update([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'google_email'  => $request->google_email,
            'program'       => $request->program,
            'academic_year' => $request->academic_year,
        ]);

        return back()->with('success', 'Account updated successfully.');
    }

    public function archive(User $user)
    {
        abort_unless($user->role === 'faculty', 403);

        $user->update([
            'is_active'   => false,
            'archived_at' => now(),
        ]);

        return back()->with('success', 'Faculty account archived successfully.');
    }

    public function unarchive(User $user)
    {
        abort_unless($user->role === 'faculty', 403);

        $user->update([
            'is_active'   => true,
            'archived_at' => null,
        ]);

        return back()->with('success', 'Faculty account restored successfully.');
    }
}