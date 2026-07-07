<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RolesPermissionsController extends Controller
{
    // Lahat ng modules per role — order nito = order ng sidebar
    protected array $roleModules = [
        'program_head' => [
            'dashboard'          => 'Dashboard',
            'template-review'    => 'Template Review',
            'course-oversight'   => 'Course Oversight',
            'account-management' => 'Account Management',
            'announcements'      => 'Announcements',
            'calendar'           => 'Calendar of Activities',
        ],
        'secretary' => [
            'dashboard'              => 'Dashboard',
            'document-repository'    => 'Document Repository',
            'template-distribution'  => 'Template Distribution',
            'course-filing'          => 'Course Filing',
            'account-management'     => 'Account Management',
            'announcements'          => 'Announcements',
            'calendar'               => 'Calendar of Activities',
        ],
        'faculty' => [
            'dashboard'           => 'Dashboard',
            'my-template'         => 'My Template',
            'exam-generator'      => 'Exam Generator',
            'shared-library'      => 'Shared Library',
            'course-coordination' => 'Course Coordination',
            'analytics'           => 'Analytics',
            'calendar'            => 'Calendar of Activities',
            'announcements'       => 'Announcements',
            'submissions'         => 'Submissions and Deadline',
            'cms'                 => 'CMS',
        ],
    ];

    public function index()
    {
        return redirect('/admin/roles-permissions/program_head');
    }

    public function show(string $role)
    {
        abort_unless(array_key_exists($role, $this->roleModules), 404);

        $modules = $this->roleModules[$role];

        // Kunin yung current enabled/disabled state mula sa DB
        $saved = DB::table('role_permissions')
            ->where('role', $role)
            ->pluck('is_enabled', 'module');

        // Kung wala pa sa DB (bagong module), default = true
        $permissions = [];
        foreach ($modules as $slug => $label) {
            $permissions[$slug] = $saved[$slug] ?? true;
        }

        return view('admin.roles-permissions', [
            'selectedRole' => $role,
            'modules'      => $modules,
            'permissions'  => $permissions,
            'roleModules'  => $this->roleModules,
        ]);
    }

    public function update(Request $request, string $role)
    {
        abort_unless(array_key_exists($role, $this->roleModules), 404);

        $modules = array_keys($this->roleModules[$role]);

        foreach ($modules as $module) {
            // Dashboard ay laging enabled — hindi pwedeng i-disable
            if ($module === 'dashboard') {
                $isEnabled = true;
            } else {
                $isEnabled = $request->has("permissions.{$module}");
            }

            DB::table('role_permissions')->updateOrInsert(
                ['role' => $role, 'module' => $module],
                ['is_enabled' => $isEnabled, 'updated_at' => now()]
            );
        }

        return back()->with('success', 'Permissions updated successfully.');
    }
}