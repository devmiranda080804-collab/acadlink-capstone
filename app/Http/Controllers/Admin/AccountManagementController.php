<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['program_head', 'secretary']);

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
        'first_name'     => 'required|string|max:255',
        'last_name'      => 'required|string|max:255',
        'email'          => 'required|email|unique:users,email',
        'role'           => 'required|in:program_head,secretary',
        'program'        => 'required_if:role,program_head|nullable|in:FMAD,OFD,BAD',
        'academic_year'  => 'required',
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
        'role'          => $request->role,
        // Secretary = walang locked program (kaya niyang lahat); Program Head = locked sa pinili
        'program'       => $request->role === 'program_head' ? $request->program : null,
        'academic_year' => $request->academic_year,
    ]);

    return redirect()->back()
        ->with('success', 'Account created successfully.')
        ->with('employee_id', $employeeId)
        ->with('temp_password', $temporaryPassword);
}

    // FIXED: field names now match the form (first_name/last_name, not name)
    public function update(Request $request, User $user)
{
    $request->validate([
        'first_name'    => 'required|string|max:255',
        'last_name'     => 'required|string|max:255',
        'email'         => 'required|email|unique:users,email,' . $user->id,
        'role'          => 'required|in:program_head,secretary',
        'program'       => 'required_if:role,program_head|nullable|in:FMAD,OFD,BAD',
        'academic_year' => 'required',
    ]);

    $user->update([
        'name'          => $request->first_name . ' ' . $request->last_name,
        'email'         => $request->email,
        'role'          => $request->role,
        'program'       => $request->role === 'program_head' ? $request->program : null,
        'academic_year' => $request->academic_year,
    ]);

    return back()->with('success', 'Account updated successfully.');
}

    public function destroy(User $user)
    {
        $user->delete();

        return back()->with('success', 'Account deleted successfully.');
    }
}