<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            ['code' => 'CAE1', 'title' => 'Financial Accounting and Reporting', 'program' => 'FMAD'],
            ['code' => 'CAE2', 'title' => 'Marketing Management', 'program' => 'FMAD'],
            ['code' => 'CAE3', 'title' => 'Intermediate Accounting 1', 'program' => 'BAD'],
            ['code' => 'CAE4', 'title' => 'Cost Accounting and Control', 'program' => 'BAD'],
            ['code' => 'CAE5', 'title' => 'Business Laws and Regulations', 'program' => 'OFD'],
            ['code' => 'CS 101', 'title' => 'Introduction to Programming', 'program' => 'OFD'],
        ];

        foreach ($courses as $c) {
            Course::firstOrCreate(['code' => $c['code']], $c);
        }
    }
}