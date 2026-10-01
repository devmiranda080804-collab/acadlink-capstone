<?php

namespace App\Services;

use App\Models\Course;
use App\Models\User;
use App\Support\AcademicTerm;
use App\Support\BloomLevels;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

// Builds a downloadable, editable .docx replicating the official Table of Specification
// format (sample provided by the client) — faculty download this to fill in the schedule,
// sign, or adjust wording; nothing here round-trips back into the app.
class TosDocumentBuilder
{
    public function build(Course $course, string $gradingPeriod, array $breakdown, User $faculty): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection(['orientation' => 'landscape', 'marginLeft' => 700, 'marginRight' => 700]);

        $center = ['alignment' => Jc::CENTER];
        $section->addText('URDANETA CITY UNIVERSITY', ['bold' => true, 'size' => 13], $center);
        $section->addText('College of Business Management and Accountancy', ['size' => 10], $center);
        $section->addTextBreak(1);
        $section->addText('TABLE OF SPECIFICATION', ['bold' => true, 'size' => 14], $center);
        $section->addText(AcademicTerm::currentSemester() . ' AY ' . AcademicTerm::currentSchoolYear(), ['size' => 10], $center);
        $section->addText(strtoupper($gradingPeriod), ['bold' => true, 'size' => 10], $center);
        $section->addTextBreak(1);

        $section->addText(
            'Subject: ' . $course->code . '    Course Title: ' . $course->title . '    Day/s and Time: ___________________',
            ['size' => 10]
        );
        $section->addTextBreak(1);

        $this->addTable($section, $breakdown);

        $section->addTextBreak(1);
        $section->addText('Date Submitted: ___________________', ['size' => 10]);
        $section->addTextBreak(1);

        $this->addSignOffBlock($section, $faculty);

        return $phpWord;
    }

    protected function addTable(\PhpOffice\PhpWord\Element\Section $section, array $breakdown): void
    {
        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => '999999',
            'cellMargin' => 60,
        ]);

        $headerStyle = ['bgColor' => 'F0F0F0'];
        $headerFont = ['bold' => true, 'size' => 8];
        $bodyFont = ['size' => 8];
        $center = ['alignment' => Jc::CENTER];

        $widths = array_merge([2600], array_fill(0, 6, 1000), [700, 500, 900]);
        $headers = array_merge(['Topics'], BloomLevels::LEVELS, ['No. of Hours', '%', 'No. of Items']);

        $table->addRow();
        foreach ($headers as $i => $h) {
            $table->addCell($widths[$i], $headerStyle)->addText($h, $headerFont, $center);
        }

        foreach ($breakdown['topics'] as $topic) {
            $table->addRow();
            $table->addCell($widths[0])->addText($topic['topic'], $bodyFont);

            foreach (BloomLevels::LEVELS as $i => $level) {
                $cell = $table->addCell($widths[$i + 1]);
                $data = $topic['levels'][$level];
                if ($data['count'] > 0) {
                    $cell->addText($data['range'], $bodyFont, $center);
                    $cell->addText('(' . $data['count'] . ')', $bodyFont, $center);
                } else {
                    $cell->addText('', $bodyFont, $center);
                }
            }

            $table->addCell($widths[7])->addText((string) $topic['hours'], $bodyFont, $center);
            $table->addCell($widths[8])->addText($topic['weight_percent'] . '%', $bodyFont, $center);
            $table->addCell($widths[9])->addText((string) $topic['target_items'], array_merge($bodyFont, ['bold' => true]), $center);
        }

        // TOTAL row
        $totalsByLevel = array_fill_keys(BloomLevels::LEVELS, 0);
        foreach ($breakdown['topics'] as $topic) {
            foreach (BloomLevels::LEVELS as $level) {
                $totalsByLevel[$level] += $topic['levels'][$level]['count'];
            }
        }

        $table->addRow();
        $table->addCell($widths[0], $headerStyle)->addText('TOTAL', $headerFont);
        foreach (BloomLevels::LEVELS as $i => $level) {
            $table->addCell($widths[$i + 1], $headerStyle)->addText((string) $totalsByLevel[$level], $headerFont, $center);
        }
        $table->addCell($widths[7], $headerStyle)->addText((string) $breakdown['total_hours'], $headerFont, $center);
        $table->addCell($widths[8], $headerStyle)->addText('100%', $headerFont, $center);
        $table->addCell($widths[9], $headerStyle)->addText((string) $breakdown['total_items'], $headerFont, $center);
    }

    protected function addSignOffBlock(\PhpOffice\PhpWord\Element\Section $section, User $faculty): void
    {
        $table = $section->addTable();
        $table->addRow();

        $table->addCell(4500)->addText('Prepared by:', ['size' => 10]);
        $table->addCell(4500)->addText('Checked and Verified:', ['size' => 10]);

        $table->addRow();
        $table->addCell(4500)->addText($faculty->name, ['bold' => true, 'underline' => Font::UNDERLINE_SINGLE, 'size' => 10]);
        $table->addCell(4500)->addText('___________________________', ['size' => 10]);

        $table->addRow();
        $table->addCell(4500)->addText('Faculty In-Charge', ['italic' => true, 'size' => 9]);
        $table->addCell(4500)->addText('Program Head', ['italic' => true, 'size' => 9]);

        $section->addTextBreak(1);
        $section->addText('Approved:', ['size' => 10]);
        $section->addTextBreak(1);
        $section->addText('___________________________', ['size' => 10]);
        $section->addText('Dean', ['italic' => true, 'size' => 9]);
    }
}
