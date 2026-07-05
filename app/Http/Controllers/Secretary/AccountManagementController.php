<?php

namespace App\Http\Controllers\Secretary;

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
                  ->orWhere('employee_id', 'like', "%{$search}%")
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
            'program'       => 'required|in:FMAD,OFD,BAD',
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
            'role'                 => 'faculty',
            'program'              => $request->program,
            'academic_year'        => $request->academic_year,
            'must_change_password' => true,
        ]);

        Mail::to($user->email)->send(new NewAccountCredentials($user, $temporaryPassword));

        return redirect()->back()
            ->with('success', 'Faculty account created successfully. Naipadala na ang login credentials sa email niya.')
            ->with('employee_id', $employeeId)
            ->with('temp_password', $temporaryPassword);
    }

    // Walang email dito — hindi na ito ide-edit, ginawa lang noong create
    public function update(Request $request, User $user)
    {
        $request->validate([
            'full_name'     => 'required|string|max:255',
            'role'          => 'required|in:program_head,secretary',
            'program'       => 'required_if:role,program_head|nullable|in:FMAD,OFD,BAD',
            'academic_year' => 'required',
        ]);

        $user->update([
            'name'          => $request->full_name,
            'role'          => $request->role,
            'program'       => $request->role === 'program_head' ? $request->program : null,
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