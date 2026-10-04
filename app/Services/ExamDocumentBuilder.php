<?php

namespace App\Services;

use App\Models\Exam;
use App\Support\BloomLevels;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\Font;

// Builds a downloadable, printable .docx for a finalized exam — the student-facing
// paper (no answers revealed) followed by an Answer Key page for the faculty member's
// own grading/reference. Mirrors TosDocumentBuilder's institutional letterhead format.
class ExamDocumentBuilder
{
    protected const ROMAN = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

    public function build(Exam $exam): PhpWord
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection(['marginLeft' => 1000, 'marginRight' => 1000]);
        $center = ['alignment' => Jc::CENTER];
        $course = $exam->programAssignment->course;

        $section->addText('URDANETA CITY UNIVERSITY', ['bold' => true, 'size' => 13], $center);
        $section->addText('College of Business Management and Accountancy', ['size' => 10], $center);
        $section->addTextBreak(1);
        $section->addText(strtoupper($exam->title), ['bold' => true, 'size' => 13], $center);
        $section->addText($course->code . ' — ' . $course->title, ['size' => 10], $center);
        $section->addTextBreak(1);

        $totalPoints = $exam->sections->flatMap(fn($s) => $s->questions)->sum('points');
        $meta = 'Grading Period: ' . $exam->grading_period
            . ($exam->duration_minutes ? '    Duration: ' . $exam->duration_minutes . ' mins' : '')
            . '    Total Points: ' . $totalPoints;
        $section->addText($meta, ['size' => 9]);
        $section->addText('Name: ___________________________________    Date: _____________    Score: _______', ['size' => 10]);
        $section->addTextBreak(1);

        $questionNumber = 0;
        $answerKey = [];

        foreach ($exam->sections as $si => $examSection) {
            $roman = self::ROMAN[$si + 1] ?? (string) ($si + 1);
            $section->addText('Test ' . $roman . ' — ' . $examSection->title, ['bold' => true, 'size' => 11]);
            if ($examSection->instructions) {
                $section->addText($examSection->instructions, ['italic' => true, 'size' => 9, 'color' => '555555']);
            }
            $section->addTextBreak(1);

            foreach ($examSection->questions as $question) {
                $questionNumber++;
                $this->addQuestion($section, $question, $questionNumber, $answerKey);
            }

            $section->addTextBreak(1);
        }

        if (!empty($answerKey)) {
            $section->addPageBreak();
            $section->addText('ANSWER KEY', ['bold' => true, 'size' => 13], $center);
            $section->addText('(For faculty reference only — not included in the student copy above)', ['italic' => true, 'size' => 8, 'color' => '888888'], $center);
            $section->addTextBreak(1);
            foreach ($answerKey as $entry) {
                $section->addText($entry['number'] . '. ' . $entry['answer'], ['size' => 9.5]);
            }
        }

        return $phpWord;
    }

    protected function addQuestion(\PhpOffice\PhpWord\Element\Section $section, $question, int $number, array &$answerKey): void
    {
        $points = $question->points ? ' (' . $question->points . ' pt' . ($question->points == 1 ? '' : 's') . ')' : '';
        $section->addText($number . '. ' . $question->question_text . $points, ['size' => 10.5]);

        $options = $question->options ?? [];

        switch ($question->type) {
            case 'mc-single':
                foreach (($options['choices'] ?? []) as $i => $choice) {
                    $section->addText('    ' . chr(97 + $i) . '. ' . $choice, ['size' => 10]);
                }
                $correctIndex = $options['correct'] ?? null;
                $correctText = is_int($correctIndex) ? (chr(97 + $correctIndex) . '. ' . ($options['choices'][$correctIndex] ?? '')) : '—';
                $answerKey[] = ['number' => $number, 'answer' => $correctText];
                break;

            case 'true-false':
                $section->addText('    Answer: ________ (True / False)', ['size' => 10]);
                $answerKey[] = ['number' => $number, 'answer' => !empty($options['answer']) ? 'True' : 'False'];
                break;

            case 'identification':
                $section->addText('    Answer: _______________________________', ['size' => 10]);
                $answerKey[] = ['number' => $number, 'answer' => $options['answer'] ?? '—'];
                break;

            case 'enumeration':
                $count = count($options['answers'] ?? ['']);
                for ($i = 0; $i < max($count, 1); $i++) {
                    $section->addText('    ' . ($i + 1) . '. _______________________________', ['size' => 10]);
                }
                $answerKey[] = ['number' => $number, 'answer' => implode(', ', array_filter($options['answers'] ?? []))];
                break;

            case 'problem-solving':
                $section->addText('    Show your solution:', ['size' => 9, 'italic' => true, 'color' => '666666']);
                for ($i = 0; $i < 4; $i++) {
                    $section->addText('    _______________________________________________', ['size' => 10]);
                }
                $answerKey[] = ['number' => $number, 'answer' => ($options['answer'] ?? '—') . (($options['solution_steps'] ?? '') ? ' — ' . $options['solution_steps'] : '')];
                break;

            case 'short-answer': // labeled "Essay" in the UI — open-ended, graded against a rubric
                for ($i = 0; $i < 6; $i++) {
                    $section->addText('    _______________________________________________', ['size' => 10]);
                }
                $answerKey[] = ['number' => $number, 'answer' => 'Grading rubric — ' . ($options['rubric'] ?? 'none provided')];
                break;

            default:
                // Unreachable via the current "Choose Question Type" modal (kept only so an
                // older exam with a since-removed type still exports instead of erroring).
                $section->addText('    _______________________________________________', ['size' => 10]);
                $answerKey[] = ['number' => $number, 'answer' => '—'];
        }

        // Legacy sub-questions (e.g. a since-removed Case Analysis scenario format) —
        // walked generically so an older exam that still has them exports cleanly.
        foreach ($question->children as $ci => $child) {
            $childPoints = $child->points ? ' (' . $child->points . ' pt' . ($child->points == 1 ? '' : 's') . ')' : '';
            $section->addText('    ' . chr(97 + $ci) . '. ' . $child->question_text . $childPoints, ['size' => 10]);
        }

        $section->addTextBreak(1);
    }
}
