<?php

namespace App\Http\Controllers\ProgramHead;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $myProgram = auth()->user()->program;

        $query = User::query()
            ->where('role', 'faculty')
            ->where('program', $myProgram); // locked sa program ng PH

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_id', 'like', "%{$search}%")
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

        abort_if(empty($myProgram), 403, 'Walang program na nakatalaga sa account mo. Kontakin ang Admin.');

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'academic_year' => 'required',
        ]);

        $lastUser = User::latest('id')->first();
        $nextId = $lastUser ? $lastUser->id + 1 : 1;
        $employeeId = 'EMP-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $temporaryPassword = 'CBMA' . rand(1000, 9999);

        User::create([
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'password'      => Hash::make($temporaryPassword),
            'employee_id'   => $employeeId,
            'role'          => 'faculty',
            'program'       => $myProgram, // server-derived, hindi galing sa form
            'academic_year' => $request->academic_year,
        ]);

        return redirect()->back()
            ->with('success', 'Faculty account created successfully.')
            ->with('employee_id', $employeeId)
            ->with('temp_password', $temporaryPassword);
    }

    public function update(Request $request, User $user)
    {
        // Hindi makaka-edit ng faculty sa ibang program kahit galawin yung URL
        abort_unless($user->role === 'faculty' && $user->program === auth()->user()->program, 403);

        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'academic_year' => 'required',
        ]);

        $user->update([
            'name'          => $request->first_name . ' ' . $request->last_name,
            'email'         => $request->email,
            'academic_year' => $request->academic_year,
        ]);

        return back()->with('success', 'Faculty account updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless($user->role === 'faculty' && $user->program === auth()->user()->program, 403);

        $user->delete();

        return back()->with('success', 'Faculty account deleted successfully.');
    }
}