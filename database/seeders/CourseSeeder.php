<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['code' => 'CAE1', 'title' => 'Financial Accounting and Reporting', 'program' => 'BSA'],
            ['code' => 'CAE2', 'title' => 'Marketing Management', 'program' => 'BSA'],
            ['code' => 'CAE3', 'title' => 'Intermediate Accounting 1', 'program' => 'BSOA'],
            ['code' => 'CAE4', 'title' => 'Cost Accounting and Control', 'program' => 'BSOA'],
            ['code' => 'CAE5', 'title' => 'Business Laws and Regulations', 'program' => 'BSMA'],
            ['code' => 'CS 101', 'title' => 'Introduction to Programming', 'program' => 'BSMA'],
        ];

        foreach ($courses as $c) {
            Course::firstOrCreate(['code' => $c['code']], $c);
        }
    }
}