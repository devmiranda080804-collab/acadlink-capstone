<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;
use App\Support\Signatories;
use App\Support\SiteContent;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

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
        $this->applySiteContent($path);

        return $path;
    }

    // Puts the letterhead and footer from the System CMS into the finished file. The template
    // holds the college's original wording, so nothing changes until the Admin changes it.
    protected function applySiteContent(string $path): void
    {
        $university = SiteContent::wrapped('university_name', 14);
        $contact = SiteContent::lines('contact_details');

        $replacements = [
            'URDANETA CITY UNIVERSITY' => mb_strtoupper(SiteContent::text('university_name')),
            'COLLEGE OF BUSINESS MANAGEMENT AND ACCOUNTANCY' => mb_strtoupper(SiteContent::text('college_name')),
            'URDANETA CITY' => $university[0],
            'UNIVERSITY' => implode(' ', array_slice($university, 1)),
            'Owned and operated by the City Government of Urdaneta' => SiteContent::text('university_note'),
            '(075) 600 - 1507' => $contact[0] ?? '',
            'San Vicente West, Urdaneta City, Pangasinan' => $contact[1] ?? '',
            'ucu.edu.ph  |' => implode('  |  ', array_slice($contact, 2)),
            '  univpresidentofficial@gmail.com' => '',
        ];
        // The template's contact line already reads like this, split over two pieces of text.
        if (($contact[2] ?? null) === 'ucu.edu.ph  |  univpresidentofficial@gmail.com' && count($contact) === 3) {
            unset($replacements['ucu.edu.ph  |'], $replacements['  univpresidentofficial@gmail.com']);
        }

        $images = array_filter([
            'word/media/image1.png' => SiteContent::uploadedImage('letterhead_seal') ? SiteContent::imagePath('letterhead_seal') : null,
            'word/media/image2.png' => SiteContent::uploadedImage('letterhead_logo') ? SiteContent::imagePath('letterhead_logo') : null,
        ]);

        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return;
        }

        foreach (['word/document.xml', 'word/header1.xml', 'word/footer1.xml'] as $part) {
            $xml = $zip->getFromName($part);
            if ($xml === false) {
                continue;
            }

            $updated = $xml;
            foreach ($replacements as $old => $new) {
                $updated = str_replace('>' . $old . '</w:t>', '>' . htmlspecialchars($new, ENT_XML1) . '</w:t>', $updated);
            }
            if ($updated !== $xml) {
                $zip->addFromString($part, $updated);
            }
        }

        foreach ($images as $name => $file) {
            $zip->addFile($file, $name);
        }

        $zip->close();
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
            // TemplateProcessor blanks a plain "0", and a level with no items should still show it.
            $doc->setValue("tot_{$key}", $totals[$level] === 0 ? '0 ' : (string) $totals[$level]);
        }
        $doc->setValue('tot_hours', (string) $breakdown['total_hours']);
        $doc->setValue('tot_items', (string) $breakdown['total_items']);
    }

    // "1-3" -> "1, 2, 3" like the college's sample; with several tests the "II." style
    // prefix is kept. Long runs stay as a range, and anything else is printed as-is.
    protected function itemNumbers(?string $range, string $level): string
    {
        if ($range && str_contains($range, ',')) {
            return implode(', ', array_map(fn ($part) => $this->itemNumbers(trim($part), $level), explode(',', $range)));
        }

        if (!$range || !preg_match('/^(?:(VI|IV|V|I{1,3})\.)?(\d+)(?:-(\d+))?$/', trim($range), $m)) {
            return (string) $range;
        }

        $start = (int) $m[2];
        $end = isset($m[3]) && $m[3] !== '' ? (int) $m[3] : $start;
        $numbers = $end - $start >= 8 ? "{$start}-{$end}" : implode(', ', range($start, max($start, $end)));

        return $m[1] !== '' ? $m[1] . '. ' . $numbers : $numbers;
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
