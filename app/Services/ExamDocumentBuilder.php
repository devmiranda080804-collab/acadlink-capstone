<?php

namespace App\Services;

use App\Models\Exam;
use App\Support\AcademicTerm;
use App\Support\ExamPrintOptions;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

// Builds the downloadable, printable .docx for a finalized exam — the student-facing
// paper, with no answers revealed anywhere in it. The Answer Key is a SEPARATE document
// (buildAnswerKey() below) so a faculty member who prints/shares this file directly with
// students never risks handing out the answers on a trailing page.
//
// Both carry the CBMA letterhead (UCU and CBMA logos, contact details) taken from the
// college's own TOS template, and use the font, size and paper chosen in Download
// Options (see ExamPrintOptions).
class ExamDocumentBuilder
{
    protected const ROMAN = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

    // Letterhead colors from the CBMA template.
    protected const RED = 'C12035';
    protected const BLUE = '273F89';

    protected array $options;

    public function __construct(?array $options = null)
    {
        $this->options = $options ?? ExamPrintOptions::DEFAULTS;
    }

    public function build(Exam $exam): PhpWord
    {
        [$phpWord, $section] = $this->newDocument();
        $size = $this->options['size'];

        $this->addTitleBlock($section, $exam, strtoupper($exam->title));

        $totalPoints = $exam->sections->flatMap(fn($s) => $s->questions)->sum('points');
        $meta = 'Grading Period: ' . $exam->grading_period
            . ($exam->duration_minutes ? '      Duration: ' . $exam->duration_minutes . ' minutes' : '')
            . '      Total Points: ' . $totalPoints;
        $section->addText($meta, ['size' => $size - 1]);
        $section->addText('Name: ____________________________________________     Date: ________________', ['size' => $size - 1], ['spaceAfter' => 60]);
        $section->addText('Course & Section: _________________________________     Score: _______________', ['size' => $size - 1]);
        $section->addTextBreak(1, ['size' => $size - 2]);

        $questionNumber = 0;

        foreach ($exam->sections as $si => $examSection) {
            $section->addText($this->sectionHeading($examSection->title, $si), ['bold' => true, 'size' => $size], ['spaceAfter' => 60]);
            if ($examSection->instructions) {
                $directions = $section->addTextRun(['spaceAfter' => 120]);
                $directions->addText('Directions: ', ['bold' => true, 'italic' => true, 'size' => $size - 1]);
                $directions->addText($examSection->instructions, ['italic' => true, 'size' => $size - 1]);
            }

            foreach ($examSection->questions as $question) {
                $questionNumber++;
                $this->addQuestion($section, $question, $questionNumber);
            }

            $section->addTextBreak(1, ['size' => $size - 2]);
        }

        return $phpWord;
    }

    // A standalone document — same letterhead, same continuous numbering as the student
    // copy above — listing only the correct answers. Kept out of build() on purpose: it's
    // downloaded separately (ExamGeneratorController::answerKey()) so printing the exam
    // for students never includes it.
    public function buildAnswerKey(Exam $exam): PhpWord
    {
        [$phpWord, $section] = $this->newDocument();
        $size = $this->options['size'];

        $this->addTitleBlock($section, $exam, strtoupper($exam->title) . ' — ANSWER KEY');
        $section->addText('For faculty reference only — do not distribute to students.', ['italic' => true, 'size' => $size - 1], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1, ['size' => $size - 2]);

        $questionNumber = 0;

        foreach ($exam->sections as $si => $examSection) {
            $section->addText($this->sectionHeading($examSection->title, $si), ['bold' => true, 'size' => $size], ['spaceAfter' => 60]);

            foreach ($examSection->questions as $question) {
                $questionNumber++;
                $section->addText($questionNumber . '. ' . $this->answerFor($question), ['size' => $size], ['spaceAfter' => 40]);
            }

            $section->addTextBreak(1, ['size' => $size - 2]);
        }

        return $phpWord;
    }

    // A document with the chosen font and paper, and the CBMA letterhead on every page.
    protected function newDocument(): array
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName($this->options['font']);
        $phpWord->setDefaultFontSize($this->options['size']);

        $paper = ExamPrintOptions::PAPERS[$this->options['paper']] ?? ExamPrintOptions::PAPERS['long'];
        $section = $phpWord->addSection([
            'pageSizeW'    => $paper['width'],
            'pageSizeH'    => $paper['height'],
            'marginLeft'   => 1080,
            'marginRight'  => 1080,
            'marginTop'    => 1080,
            'marginBottom' => 1000,
            'headerHeight' => 400,
            'footerHeight' => 360,
        ]);

        $width = $paper['width'] - 2160;
        $this->addLetterhead($section, $width);
        $this->addFooter($section, $width);

        return [$phpWord, $section];
    }

    protected function addLetterhead(Section $section, int $width): void
    {
        $header = $section->addHeader();
        $table = $header->addTable(['cellMargin' => 0, 'width' => $width, 'unit' => 'dxa']);
        $table->addRow();

        // UCU logo + wordmark on the left.
        $left = $table->addCell((int) ($width * 0.62), ['valign' => 'bottom']);
        $brand = $left->addTable(['cellMargin' => 0]);
        $brand->addRow();
        $brand->addCell(760, ['valign' => 'center'])->addImage(resource_path('templates/branding/ucu-logo.png'), ['height' => 42]);
        $words = $brand->addCell(4600, ['valign' => 'center']);
        $words->addText('URDANETA CITY', ['name' => 'Calibri', 'bold' => true, 'size' => 17, 'color' => self::RED], ['spaceAfter' => 0, 'spacing' => 0, 'lineHeight' => 0.9]);
        $words->addText('UNIVERSITY', ['name' => 'Calibri', 'bold' => true, 'size' => 17, 'color' => self::RED], ['spaceAfter' => 0, 'spacing' => 0, 'lineHeight' => 0.9]);
        $words->addText('Owned and operated by the City Government of Urdaneta', ['name' => 'Calibri', 'size' => 6.5, 'color' => self::RED], ['spaceAfter' => 0]);

        // CBMA seal + college name on the right.
        $right = $table->addCell((int) ($width * 0.38), ['valign' => 'bottom']);
        $right->addImage(resource_path('templates/branding/cbma-seal.png'), ['height' => 30, 'alignment' => Jc::RIGHT]);
        $right->addText('COLLEGE OF BUSINESS', ['name' => 'Tahoma', 'size' => 8], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        $right->addText('MANAGEMENT AND ACCOUNTANCY', ['name' => 'Tahoma', 'size' => 8], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);

        // The blue rule under the letterhead.
        $header->addText('', ['size' => 2], ['borderBottomSize' => 24, 'borderBottomColor' => self::BLUE, 'spaceAfter' => 0]);
    }

    protected function addFooter(Section $section, int $width): void
    {
        $footer = $section->addFooter();
        $table = $footer->addTable(['cellMargin' => 0, 'width' => $width, 'unit' => 'dxa']);
        $table->addRow();

        $left = $table->addCell((int) ($width * 0.34), ['valign' => 'bottom']);
        $tagline = $left->addTextRun(['spaceAfter' => 0]);
        $tagline->addText('Bright', ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::BLUE]);
        $tagline->addText(' future starts here', ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::RED]);

        $middle = $table->addCell((int) ($width * 0.26), ['valign' => 'bottom']);
        $middle->addPreserveText('Page {PAGE} of {NUMPAGES}', ['name' => 'Calibri', 'size' => 8, 'color' => '555555'], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);

        $right = $table->addCell((int) ($width * 0.40), ['valign' => 'bottom']);
        foreach (['(075) 600 - 1507', 'San Vicente West, Urdaneta City, Pangasinan', 'ucu.edu.ph  |  univpresidentofficial@gmail.com'] as $line) {
            $right->addText($line, ['name' => 'Calibri', 'size' => 8, 'color' => self::RED], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        }
    }

    // Same opening lines as the official TOS: university, college, term, then the title.
    protected function addTitleBlock(Section $section, Exam $exam, string $title): void
    {
        $size = $this->options['size'];
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];
        $course = $exam->programAssignment->course;

        $section->addText('URDANETA CITY UNIVERSITY', ['bold' => true, 'size' => $size], $center);
        $section->addText('COLLEGE OF BUSINESS MANAGEMENT AND ACCOUNTANCY', ['bold' => true, 'size' => $size], $center);
        $section->addText(mb_strtoupper(AcademicTerm::currentSemester()) . ', A.Y. ' . AcademicTerm::currentSchoolYear(), ['bold' => true, 'size' => $size], $center);
        $section->addTextBreak(1, ['size' => $size - 4]);
        $section->addText($title, ['bold' => true, 'size' => $size + 2], $center);
        $section->addText($course->code . ' — ' . $course->title, ['size' => $size], ['alignment' => Jc::CENTER, 'spaceAfter' => 160]);
    }

    // Sections made from the TOS are already titled "Test I – Multiple Choice"; others
    // get their "Test I —" prefix here.
    protected function sectionHeading(string $title, int $index): string
    {
        $roman = self::ROMAN[$index + 1] ?? (string) ($index + 1);

        return preg_match('/^test\s+[ivx]+\b/i', trim($title))
            ? mb_strtoupper($title)
            : mb_strtoupper('Test ' . $roman . ' — ' . $title);
    }

    protected function addQuestion(Section $section, $question, int $number): void
    {
        $size = $this->options['size'];
        // keepNext holds a question and its choices/answer lines together on one page.
        $indent = ['indentation' => ['left' => 400], 'spaceAfter' => 0, 'keepNext' => true, 'keepLines' => true];
        $points = $question->points ? ' (' . $question->points . ' pt' . ($question->points == 1 ? '' : 's') . ')' : '';

        $section->addText($number . '. ' . $question->question_text . $points, ['size' => $size], ['spaceAfter' => 40, 'keepNext' => true, 'keepLines' => true, 'indentation' => ['left' => 360, 'hanging' => 360]]);

        $options = $question->options ?? [];

        switch ($question->type) {
            case 'mc-single':
                foreach (($options['choices'] ?? []) as $i => $choice) {
                    $section->addText(chr(97 + $i) . '. ' . $choice, ['size' => $size], $indent);
                }
                break;

            case 'true-false':
                $section->addText('Answer: ________ (True / False)', ['size' => $size], $indent);
                break;

            case 'identification':
                $section->addText('Answer: _______________________________', ['size' => $size], $indent);
                break;

            case 'enumeration':
                $count = count($options['answers'] ?? ['']);
                for ($i = 0; $i < max($count, 1); $i++) {
                    $section->addText(($i + 1) . '. _______________________________', ['size' => $size], $indent);
                }
                break;

            case 'problem-solving':
                $section->addText('Show your solution:', ['size' => $size - 1, 'italic' => true], $indent);
                for ($i = 0; $i < 4; $i++) {
                    $section->addText('_______________________________________________________', ['size' => $size], $indent);
                }
                break;

            case 'short-answer': // labeled "Essay" in the UI — open-ended, graded against a rubric
                for ($i = 0; $i < 6; $i++) {
                    $section->addText('_______________________________________________________', ['size' => $size], $indent);
                }
                break;

            default:
                // Unreachable via the current "Choose Question Type" modal (kept only so an
                // older exam with a since-removed type still exports instead of erroring).
                $section->addText('_______________________________________________________', ['size' => $size], $indent);
        }

        // Legacy sub-questions (e.g. a since-removed Case Analysis scenario format) —
        // walked generically so an older exam that still has them exports cleanly.
        foreach ($question->children as $ci => $child) {
            $childPoints = $child->points ? ' (' . $child->points . ' pt' . ($child->points == 1 ? '' : 's') . ')' : '';
            $section->addText(chr(97 + $ci) . '. ' . $child->question_text . $childPoints, ['size' => $size], $indent);
        }

        $section->addTextBreak(1, ['size' => max(6, $size - 4)]);
    }

    protected function answerFor($question): string
    {
        $options = $question->options ?? [];

        switch ($question->type) {
            case 'mc-single':
                $correctIndex = $options['correct'] ?? null;
                return is_int($correctIndex) ? (chr(97 + $correctIndex) . '. ' . ($options['choices'][$correctIndex] ?? '')) : '—';

            case 'true-false':
                return !empty($options['answer']) ? 'True' : 'False';

            case 'identification':
                return $options['answer'] ?? '—';

            case 'enumeration':
                return implode(', ', array_filter($options['answers'] ?? []));

            case 'problem-solving':
                return ($options['answer'] ?? '—') . (($options['solution_steps'] ?? '') ? ' — ' . $options['solution_steps'] : '');

            case 'short-answer':
                return 'Grading rubric — ' . ($options['rubric'] ?? 'none provided');

            default:
                return '—';
        }
    }
}
