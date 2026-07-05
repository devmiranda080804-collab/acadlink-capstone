<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolePermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'admin' => [
                'dashboard',
                'account-management',
                'roles-permissions',
                'template-approvals',
                'audit-logs',
                'announcements',
                'calendar',
            ],
            'program_head' => [
                'dashboard',
                'template-review',
                'course-oversight',
                'account-management',
                'announcements',
                'calendar',
            ],
            'secretary' => [
                'dashboard',
                'document-repository',
                'template-distribution',
                'course-filing',
                'account-management',
                'announcements',
                'calendar',
            ],
            'faculty' => [
                'dashboard',
                'my-template',
                'exam-generator',
                'shared-library',
                'course-coordination',
                'analytics',
                'calendar',
                'announcements',
                'submissions',
                'cms',
            ],
        ];

        foreach ($permissions as $role => $modules) {
            foreach ($modules as $module) {
                DB::table('role_permissions')->insertOrIgnore([
                    'role'       => $role,
                    'module'     => $module,
                    'is_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}