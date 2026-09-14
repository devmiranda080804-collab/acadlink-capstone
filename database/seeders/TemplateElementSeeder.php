<?php

namespace Database\Seeders;

use App\Models\TemplateElement;
use Illuminate\Database\Seeder;

class TemplateElementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaults = [
            'syllabus' => [
                ['label' => 'Course Description', 'field_type' => 'rich_text', 'instructions' => 'A short overview of what the course covers.'],
                ['label' => 'Course Learning Outcomes', 'field_type' => 'rich_text', 'instructions' => 'What students should know or be able to do by the end of the course.'],
                ['label' => 'Course Content / Topics', 'field_type' => 'table', 'instructions' => 'Weekly or unit breakdown of topics covered.'],
                ['label' => 'Grading System', 'field_type' => 'table', 'instructions' => 'Breakdown of how the final grade is computed.'],
                ['label' => 'Course Policies', 'field_type' => 'rich_text', 'instructions' => 'Attendance, academic integrity, and other classroom policies.'],
                ['label' => 'References', 'field_type' => 'text', 'instructions' => 'Textbooks and other learning materials used.'],
            ],
            'lesson_plan' => [
                ['label' => 'Learning Objectives', 'field_type' => 'rich_text'],
                ['label' => 'Subject Matter / Topic', 'field_type' => 'text'],
                ['label' => 'Teaching Methods and Strategies', 'field_type' => 'rich_text'],
                ['label' => 'Learning Resources', 'field_type' => 'text', 'is_required' => false],
                ['label' => 'Assessment', 'field_type' => 'rich_text'],
            ],
            'course_guide' => [
                ['label' => 'Course Overview', 'field_type' => 'rich_text'],
                ['label' => 'Prerequisites', 'field_type' => 'text', 'is_required' => false],
                ['label' => 'Learning Outcomes', 'field_type' => 'rich_text'],
                ['label' => 'Grading Breakdown', 'field_type' => 'table'],
            ],
            'module' => [
                ['label' => 'Module Title', 'field_type' => 'text'],
                ['label' => 'Learning Objectives', 'field_type' => 'rich_text'],
                ['label' => 'Content', 'field_type' => 'rich_text'],
                ['label' => 'Activities / Exercises', 'field_type' => 'rich_text'],
                ['label' => 'Assessment Questions', 'field_type' => 'rich_text', 'is_required' => false],
            ],
        ];

        foreach ($defaults as $type => $elements) {
            foreach ($elements as $order => $element) {
                TemplateElement::updateOrCreate(
                    ['template_type' => $type, 'label' => $element['label']],
                    [
                        'instructions' => $element['instructions'] ?? null,
                        'field_type'   => $element['field_type'],
                        'is_required'  => $element['is_required'] ?? true,
                        'order'        => $order,
                        'is_active'    => true,
                    ]
                );
            }
        }
    }
}
