<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\NewAccountCredentials;
use App\Models\User;
use App\Support\AccountNotifier;
use App\Support\AcademicTerm;
use App\Support\Programs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\AuditLog;

class AccountManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()->whereIn('role', ['program_head', 'secretary', 'faculty']);

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

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('program')) {
            $query->inProgram($request->program);
        }

        if ($request->filled('academic_year')) {
            $query->where('academic_year', $request->academic_year);
        }

        $accounts = $query->latest()->paginate(6)->withQueryString();

        $schoolYears = AcademicTerm::schoolYearOptions();
        $currentSchoolYear = AcademicTerm::currentSchoolYear();

        return view('admin.account-management', compact('accounts', 'schoolYears', 'currentSchoolYear'));
    }

    // Faculty can teach in several programs; a Program Head leads one; a Secretary serves all.
    protected function programsFor(Request $request): array
    {
        return match ($request->role) {
            'faculty'      => array_values(array_unique((array) $request->programs)),
            'program_head' => [$request->program],
            default        => [],
        };
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|in:program_head,secretary,faculty',
            'program'       => 'required_if:role,program_head|nullable|in:' . implode(',', Programs::codes()),
            'programs'      => 'required_if:role,faculty|nullable|array',
            'programs.*'    => 'in:' . implode(',', Programs::codes()),
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ], [], ['programs' => 'program']);

        $programs = $this->programsFor($request);
        $temporaryPassword = Str::password(12);

        $user = User::create([
            'name'                 => trim($request->first_name . ' ' . $request->last_name),
            'first_name'           => $request->first_name,
            'last_name'            => $request->last_name,
            'email'                => $request->email,
            'google_email'         => $request->google_email,
            'password'             => Hash::make($temporaryPassword),
            'role'                 => $request->role,
            'program'              => $programs[0] ?? null,
            'academic_year'        => $request->academic_year,
            'must_change_password' => true,
        ]);
        $user->syncPrograms($programs);

        AuditLog::record('Account Created', "Created {$user->role} account for {$user->name} ({$user->email})");

        Mail::to($user->email)->send(new NewAccountCredentials($user, $temporaryPassword));

        return redirect()->back()
            ->with('success', 'Account created successfully. The login credentials have been sent to their email.');
    }

    // Email is not included here — this should no longer be editable, per the instructor's instructions
    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'role'          => 'required|in:program_head,secretary,faculty',
            'program'       => 'required_if:role,program_head|nullable|in:' . implode(',', Programs::codes()),
            'programs'      => 'required_if:role,faculty|nullable|array',
            'programs.*'    => 'in:' . implode(',', Programs::codes()),
            'academic_year' => 'required',
            'google_email'  => 'nullable|email|max:255',
        ], [], ['programs' => 'program']);

        $roleChanged = $user->role !== $request->role;

        $user->update([
            'name'          => trim($request->first_name . ' ' . $request->last_name),
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'google_email'  => $request->google_email,
            'role'          => $request->role,
            'academic_year' => $request->academic_year,
        ]);
        $programChange = $user->syncPrograms($this->programsFor($request));

        AccountNotifier::sendDetailChanges($user, $programChange);

        if ($roleChanged) {
            \DB::table('sessions')->where('user_id', $user->id)->delete();
        }

        return back()->with('success', 'Account updated successfully.' . ($roleChanged ? ' User will be redirected to their new dashboard on next login.' : ''));
    }

    public function archive(User $user)
    {
        abort_unless(in_array($user->role, ['program_head', 'secretary', 'faculty']), 403);

        $user->update([
            'is_active'   => false,
            'archived_at' => now(),
        ]);

        AuditLog::record('Account Archived', "Archived account: {$user->name} ({$user->email})");

        AccountNotifier::send($user, 'archived');

        return back()->with('success', 'Account archived successfully.');
    }

    public function unarchive(User $user)
    {
        abort_unless(in_array($user->role, ['program_head', 'secretary', 'faculty']), 403);

        $user->update([
            'is_active'   => true,
            'archived_at' => null,
        ]);

        AccountNotifier::send($user, 'restored');

        return back()->with('success', 'Account restored successfully.');
    }
}