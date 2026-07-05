<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewAccountCredentials;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['program_head', 'secretary']);

        if ($request->get('status') === 'archived') {
            $query->whereNotNull('archived_at');
        } else {
            $query->whereNull('archived_at');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('program')) {
            $query->where('program', $request->program);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $accounts = $query->latest()->paginate(6)->withQueryString();

        return view('admin.account-management', compact('accounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:program_head,secretary',
            'program'       => 'required_if:role,program_head|nullable|in:FMAD,OFD,BAD',
            'academic_year' => 'required',
        ]);

        $lastUser = User::latest('id')->first();
        $nextId = $lastUser ? $lastUser->id + 1 : 1;
        $employeeId = 'EMP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name'                 => $request->first_name . ' ' . $request->last_name,
            'email'                => $request->email,
            'password'             => Hash::make($temporaryPassword),
            'employee_id'          => $employeeId,
            'role'                 => $request->role,
            'program'              => $request->role === 'program_head' ? $request->program : null,
            'academic_year'        => $request->academic_year,
            'must_change_password' => true,
        ]);

        Mail::to($user->email)->send(new NewAccountCredentials($user, $temporaryPassword));

        return redirect()->back()
            ->with('success', 'Account created successfully. Naipadala na ang login credentials sa email niya.')
            ->with('employee_id', $employeeId)
            ->with('temp_password', $temporaryPassword);
    }

    // Email ay hindi na kasama dito — hindi na ito ide-edit base sa sabi ng instructor niyo
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'role'          => 'required|in:program_head,secretary',
            'program'       => 'required_if:role,program_head|nullable|in:FMAD,OFD,BAD',
            'academic_year' => 'required',
        ]);

        $roleChanged = $user->role !== $request->role;

        $user->update([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'role'          => $request->role,
            'program'       => $request->role === 'program_head' ? $request->program : null,
            'academic_year' => $request->academic_year,
        ]);

        if ($roleChanged) {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Account updated successfully.' . ($roleChanged ? ' User will be redirected to their new dashboard on next login.' : ''));
    }

    public function archive(User $user)
    {
        abort_unless(in_array($user->role, ['program_head', 'secretary']), 403);

        $user->update([
            'is_active'   => false,
            'archived_at' => now(),
        ]);

        return back()->with('success', 'Account archived successfully.');
    }

    public function unarchive(User $user)
    {
        abort_unless(in_array($user->role, ['program_head', 'secretary']), 403);

        $user->update([
            'is_active'   => true,
            'archived_at' => null,
        ]);

        return back()->with('success', 'Account restored successfully.');
    }
}