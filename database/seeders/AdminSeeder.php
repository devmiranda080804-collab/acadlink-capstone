<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@cbma.edu.ph',
            'password' => Hash::make('admin123'),

            'employee_id' => 'EMP-000',

            'role' => 'admin',

            'program' => 'ADMIN',

            'academic_year' => '2025-2026'
        ]);
    }
}