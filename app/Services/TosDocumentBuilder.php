<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;
use App\Support\Signatories;
use PhpOffice\PhpWord\TemplateProcessor;

// Fills the official CBMA Table of Specification template (resources/templates/tos-template.docx,
// made from the college's own sample, with its UCU/CBMA header and footer) with the course,
// the computed breakdown and the signatories. Returns the path of a temporary .docx file.
class TosDocumentBuilder
{
    public const TEMPLATE = 'templates/tos-template.docx';

    protected const LEVEL_KEYS = [
        'Remembering'   => 'rem',
        'Understanding' => 'und',
        'Applying'      => 'app',
        'Analyzing'     => 'ana',
        'Evaluating'    => 'eva',
        'Creating'      => 'cre',
    ];

    protected const BLANK = '______________________';

    public function build(Course $course, string $gradingPeriod, array $breakdown, User $faculty): string
    {
        $doc = new TemplateProcessor(resource_path(self::TEMPLATE));

        $programHead = Signatories::programHead($course->program);
        $dean = Signatories::dean();

        $doc->setValues([
            'term'               => mb_strtoupper(AcademicTerm::currentSemester()) . ', A.Y. ' . AcademicTerm::currentSchoolYear(),
            'subject'            => $course->title,
            'course_code'        => $course->code,
            'program'            => $course->program ?: self::BLANK,
            'schedule'           => self::BLANK,
            'prelim_mark'        => $this->periodMark($gradingPeriod, 'Prelim'),
            'midterm_mark'       => $this->periodMark($gradingPeriod, 'Midterm'),
            'finals_mark'        => $this->periodMark($gradingPeriod, 'Final'),
            'instructor'         => mb_strtoupper($faculty->name),
            'date_submitted'     => now()->format('F j, Y'),
            'program_head'       => $programHead['name'] ? mb_strtoupper($programHead['name']) : self::BLANK,
            'program_head_title' => $programHead['title'],
            'dean'               => $dean['name'] ? mb_strtoupper($dean['name']) : self::BLANK,
            'dean_title'         => $dean['title'],
        ]);

        $this->fillTable($doc, $breakdown);

        $path = tempnam(sys_get_temp_dir(), 'tos');
        $doc->saveAs($path);

        return $path;
    }

    protected function fillTable(TemplateProcessor $doc, array $breakdown): void
    {
        $topics = $breakdown['topics'];
        $totals = array_fill_keys(BloomLevels::LEVELS, 0);

        $doc->cloneRow('t_topic', max(count($topics), 1));

        foreach (array_values($topics) as $i => $topic) {
            $n = $i + 1;
            $doc->setValue("t_topic#{$n}", $topic['topic']);

            foreach (self::LEVEL_KEYS as $level => $key) {
                $data = $topic['levels'][$level];
                $totals[$level] += (int) $data['count'];
                $doc->setValue("t_{$key}#{$n}", $data['count'] > 0 ? $this->itemNumbers($data['range'], $level) : '');
            }

            $doc->setValue("t_hours#{$n}", (string) $topic['hours']);
            $doc->setValue("t_pct#{$n}", $this->percent($topic['weight_percent']));
            $doc->setValue("t_items#{$n}", (string) $topic['target_items']);
        }

        if (empty($topics)) {
            foreach (['t_topic', 't_rem', 't_und', 't_app', 't_ana', 't_eva', 't_cre', 't_hours', 't_pct', 't_items'] as $name) {
                $doc->setValue("{$name}#1", '');
            }
        }

        foreach (self::LEVEL_KEYS as $level => $key) {
            $doc->setValue("tot_{$key}", (string) $totals[$level]);
        }
        $doc->setValue('tot_hours', (string) $breakdown['total_hours']);
        $doc->setValue('tot_items', (string) $breakdown['total_items']);
    }

    // "I.1-3" -> "1, 2, 3" like the college's sample; Creating items belong to Test II,
    // so they keep a "II." prefix. Long runs stay as a range, and anything the faculty
    // typed by hand in Edit mode is printed as-is.
    protected function itemNumbers(?string $range, string $level): string
    {
        if (!$range || !preg_match('/^(I{1,2})\.(\d+)(?:-(\d+))?$/', trim($range), $m)) {
            return (string) $range;
        }

        $start = (int) $m[2];
        $end = isset($m[3]) ? (int) $m[3] : $start;
        $numbers = $end - $start >= 8 ? "{$start}-{$end}" : implode(', ', range($start, max($start, $end)));

        return $m[1] === 'II' ? 'II. ' . $numbers : $numbers;
    }

    protected function percent($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 1, '.', ''), '0'), '.') . '%';
    }

    protected function periodMark(string $selected, string $period): string
    {
        return $selected === $period ? '__✓__' : '_____';
    }
}
