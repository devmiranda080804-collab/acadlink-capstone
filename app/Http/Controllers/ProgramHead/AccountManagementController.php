<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Mail\NewAccountCredentials;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class AccountManagementController extends Controller
{
        public function index(Request $request)
    {
        $myProgram = auth()->user()->program;

        $query = User::query()
            ->where('role', 'faculty')
            ->where('program', $myProgram);

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

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $accounts = $query->latest()->paginate(6)->withQueryString();

        return view('program-head.ph-account-management', compact('accounts', 'myProgram'));
    }

    public function store(Request $request)
    {
        $myProgram = auth()->user()->program;

        abort_if(empty($myProgram), 403, 'No program is assigned to your account. Please contact the Admin.');

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ]);

        $temporaryPassword = Str::password(12);

       $user = User::create([
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'google_email'  => $request->google_email,
            'password'      => Hash::make($temporaryPassword),
            'role'          => 'faculty',
            'program'       => $myProgram, // server-derived, not from the form
            'academic_year' => $request->academic_year,
        ]);
        AuditLog::record('Account Created', "Created faculty account for {$user->name} ({$user->email})");

        Mail::to($user->email)->send(new NewAccountCredentials($user, $temporaryPassword));

        return redirect()->back()
            ->with('success', 'Faculty account created successfully. The login credentials have been sent to their email.');
    }

    public function update(Request $request, User $user)
    {
        abort_unless($user->role === 'faculty' && $user->program === auth()->user()->program, 403);

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ]);

        $user->update([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'google_email'  => $request->google_email,
            'academic_year' => $request->academic_year,
        ]);

        return back()->with('success', 'Account updated successfully.');
    }

    public function archive(User $user)
    {
        abort_unless($user->role === 'faculty' && $user->program === auth()->user()->program, 403);

        $user->update([
            'is_active'   => false,
            'archived_at' => now(),
        ]);

        return back()->with('success', 'Faculty account archived successfully.');
    }

    public function unarchive(User $user)
    {
        abort_unless($user->role === 'faculty' && $user->program === auth()->user()->program, 403);

        $user->update([
            'is_active'   => true,
            'archived_at' => null,
        ]);

        return back()->with('success', 'Faculty account restored successfully.');
    }
}