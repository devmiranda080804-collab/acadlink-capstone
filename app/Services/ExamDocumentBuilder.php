<?php

namespace App\Services;

use App\Models\Exam;
use App\Support\AcademicTerm;
use App\Support\ExamPrintOptions;
use App\Support\DocumentLayouts;
use App\Support\SiteContent;
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
// Options (see ExamPrintOptions). The student copy also follows the chosen layout:
// standard, with a separate answer sheet, or in two columns.
class ExamDocumentBuilder
{
    protected const ROMAN = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];

    // Letterhead colors from the CBMA template.
    protected const RED = 'C12035';
    protected const BLUE = '273F89';

    // Space between the two columns, in twips.
    protected const COLUMN_GAP = 360;

    protected array $options;

    // Which Document Layouts column the file follows: "exam" or "answer_key".
    protected string $document = 'exam';

    // Applies to the student copy only; the answer key always has one column.
    protected string $layout = 'standard';

    public function __construct(?array $options = null)
    {
        $this->options = ($options ?? []) + ExamPrintOptions::defaults();
    }

    protected function isAnswerSheet(): bool
    {
        return $this->layout === 'answer_sheet';
    }

    protected function isTwoColumn(): bool
    {
        return $this->layout === 'two_column';
    }

    public function build(Exam $exam): PhpWord
    {
        $this->document = 'exam';
        $this->layout = $this->options['layout'];
        [$phpWord, $section] = $this->newDocument();
        $size = $this->options['size'];

        $this->addTitleBlock($section, $exam, strtoupper($exam->title));

        $totalPoints = $exam->sections->flatMap(fn($s) => $s->questions)->sum('points');
        $meta = 'Grading Period: ' . $exam->grading_period
            . ($exam->duration_minutes ? '      Duration: ' . $exam->duration_minutes . ' minutes' : '')
            . '      Total Points: ' . $totalPoints;
        $section->addText($meta, ['size' => $size - 1]);
        if ($this->isAnswerSheet()) {
            $section->addText(DocumentLayouts::text('exam', 'contents.answer_sheet_note'), ['italic' => true, 'bold' => true, 'size' => $size - 1]);
        } else {
            $this->addStudentLines($section);
        }
        $section->addTextBreak(1, ['size' => $size - 2]);

        // The title stays across the page; the questions flow into two columns below it.
        if ($this->isTwoColumn()) {
            $section = $phpWord->addSection($this->pageStyle() + [
                'breakType' => 'continuous',
                'colsNum'   => 2,
                'colsSpace' => self::COLUMN_GAP,
            ]);
        }

        $questionNumber = 0;

        foreach ($exam->sections as $si => $examSection) {
            $section->addText($this->sectionHeading($examSection->title, $si), ['bold' => true, 'size' => $size], ['spaceAfter' => 60, 'keepNext' => true]);
            if ($examSection->instructions) {
                $directions = $section->addTextRun(['spaceAfter' => 120, 'keepNext' => true]);
                $directions->addText('Directions: ', ['bold' => true, 'italic' => true, 'size' => $size - 1]);
                $directions->addText($examSection->instructions, ['italic' => true, 'size' => $size - 1]);
            }

            foreach ($examSection->questions as $question) {
                $questionNumber++;
                $this->addQuestion($section, $question, $questionNumber);
            }

            $section->addTextBreak(1, ['size' => $size - 2]);
        }

        if ($this->isAnswerSheet()) {
            $this->addAnswerSheet($section, $exam);
        }

        return $phpWord;
    }

    protected function addStudentLines(Section $section): void
    {
        $size = $this->options['size'];

        $label = fn ($field) => DocumentLayouts::text('exam', 'contents.' . $field);
        $section->addText($label('name_label') . ': ____________________________________________     ' . $label('date_label') . ': ________________', ['size' => $size - 1], ['spaceAfter' => 60]);
        $section->addText($label('section_label') . ': _________________________________     ' . $label('score_label') . ': _______________', ['size' => $size - 1]);
    }

    // The last page(s) of the "With Answer Sheet" layout, numbered like the questions.
    protected function addAnswerSheet(Section $section, Exam $exam): void
    {
        $size = $this->options['size'];

        $section->addPageBreak();
        $section->addText('ANSWER SHEET', ['bold' => true, 'size' => $size + 2], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        $section->addText(mb_strtoupper($exam->title), ['bold' => true, 'size' => $size], ['alignment' => Jc::CENTER, 'spaceAfter' => 160]);
        $this->addStudentLines($section);
        $section->addTextBreak(1, ['size' => $size - 2]);

        $questionNumber = 0;

        foreach ($exam->sections as $si => $examSection) {
            $section->addText($this->sectionHeading($examSection->title, $si), ['bold' => true, 'size' => $size], ['spaceAfter' => 80, 'keepNext' => true]);

            $short = [];
            foreach ($examSection->questions as $question) {
                $questionNumber++;

                if (in_array($question->type, ['mc-single', 'true-false', 'identification'], true) && $question->children->isEmpty()) {
                    $short[] = $questionNumber;
                    continue;
                }

                $this->addShortAnswerGrid($section, $short);
                $short = [];
                $this->addLongAnswer($section, $question, $questionNumber);
            }
            $this->addShortAnswerGrid($section, $short);

            $section->addTextBreak(1, ['size' => $size - 2]);
        }
    }

    // Three answers per row for items that only need a word or a letter.
    protected function addShortAnswerGrid(Section $section, array $numbers): void
    {
        if (!$numbers) {
            return;
        }

        $size = $this->options['size'];
        $cell = (int) floor($this->textWidth() / 3);
        $table = $section->addTable(['cellMargin' => 40, 'width' => $this->textWidth(), 'unit' => 'dxa']);

        foreach (array_chunk($numbers, 3) as $row) {
            $table->addRow(420);
            for ($i = 0; $i < 3; $i++) {
                $number = $row[$i] ?? null;
                $table->addCell($cell, ['valign' => 'bottom'])->addText(
                    $number ? $number . '. ' . $this->blankFor($cell - 160, strlen((string) $number) + 2) : '',
                    ['size' => $size],
                    ['spaceAfter' => 0]
                );
            }
        }

        $section->addText('', ['size' => 4], ['spaceAfter' => 0]);
    }

    protected function addLongAnswer(Section $section, $question, int $number): void
    {
        $size = $this->options['size'];
        $line = ['size' => $size];
        $indent = ['indentation' => ['left' => 400], 'spaceAfter' => 60, 'keepNext' => true];
        $width = $this->textWidth() - 400;

        $section->addText($number . '.', ['bold' => true, 'size' => $size], ['spaceAfter' => 40, 'keepNext' => true]);

        if ($question->type === 'enumeration') {
            $count = max(count($question->options['answers'] ?? ['']), 1);
            for ($i = 0; $i < $count; $i++) {
                $section->addText(($i + 1) . '. ' . $this->blankFor($width, strlen((string) ($i + 1)) + 2), $line, $indent);
            }
        } elseif ($question->children->isEmpty()) {
            $lines = $question->type === 'short-answer' ? 8 : 6;
            for ($i = 0; $i < $lines; $i++) {
                $section->addText($this->blankFor($width), $line, $indent);
            }
        }

        foreach ($question->children as $ci => $child) {
            $section->addText(chr(97 + $ci) . '. ' . $this->blankFor($width, 3), $line, $indent);
        }

        $section->addTextBreak(1, ['size' => max(6, $size - 4)]);
    }

    // A writing line that fits the given width (twips), leaving room for its label. The
    // widest of the available fonts draws an underscore at about 0.56 of the font size.
    protected function blankFor(int $width, int $labelChars = 0): string
    {
        $characters = (int) floor($width / 20 / ($this->options['size'] * 0.56)) - $labelChars;

        return str_repeat('_', max($characters, 6));
    }

    // Writing lines keep their usual length unless they'd run past a column.
    protected function blank(int $length, int $labelChars = 0): string
    {
        if (!$this->isTwoColumn()) {
            return str_repeat('_', $length);
        }

        $column = (int) (($this->textWidth() - self::COLUMN_GAP) / 2) - 400;

        return substr($this->blankFor($column, $labelChars), 0, $length);
    }

    protected function paper(): array
    {
        return ExamPrintOptions::PAPERS[$this->options['paper']] ?? ExamPrintOptions::PAPERS['long'];
    }

    // The width between the page margins, in twips.
    protected function textWidth(): int
    {
        return $this->paper()['width'] - 2160;
    }

    protected function pageStyle(): array
    {
        $paper = $this->paper();

        return [
            'pageSizeW'    => $paper['width'],
            'pageSizeH'    => $paper['height'],
            'marginLeft'   => 1080,
            'marginRight'  => 1080,
            'marginTop'    => 1080,
            'marginBottom' => 1000,
            'headerHeight' => 400,
            'footerHeight' => 360,
        ];
    }

    // A standalone document — same letterhead, same continuous numbering as the student
    // copy above — listing only the correct answers. Kept out of build() on purpose: it's
    // downloaded separately (ExamGeneratorController::answerKey()) so printing the exam
    // for students never includes it.
    public function buildAnswerKey(Exam $exam): PhpWord
    {
        $this->document = 'answer_key';
        $this->layout = 'standard';
        [$phpWord, $section] = $this->newDocument();
        $size = $this->options['size'];

        $this->addTitleBlock($section, $exam, trim(strtoupper($exam->title) . ' ' . DocumentLayouts::text('answer_key', 'contents.title_suffix')));
        $note = DocumentLayouts::text('answer_key', 'contents.note');
        if ($note !== '') {
            $section->addText($note, ['italic' => true, 'size' => $size - 1], ['alignment' => Jc::CENTER]);
        }
        $section->addTextBreak(1, ['size' => $size - 2]);

        $questionNumber = 0;

        foreach ($exam->sections as $si => $examSection) {
            $section->addText($this->sectionHeading($examSection->title, $si), ['bold' => true, 'size' => $size], ['spaceAfter' => 60, 'keepNext' => true]);

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

        $section = $phpWord->addSection($this->pageStyle());

        $width = $this->textWidth();
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
        $brand->addCell(760, ['valign' => 'center'])->addImage(DocumentLayouts::imagePath($this->document, 'header.logo_left'), ['height' => 42]);
        $words = $brand->addCell(4600, ['valign' => 'center']);
        foreach (DocumentLayouts::wrapped($this->document, 'header.university_name', 14) as $line) {
            $words->addText($line, ['name' => 'Calibri', 'bold' => true, 'size' => 17, 'color' => self::RED], ['spaceAfter' => 0, 'spacing' => 0, 'lineHeight' => 0.9]);
        }
        $words->addText(DocumentLayouts::text($this->document, 'header.university_note'), ['name' => 'Calibri', 'size' => 6.5, 'color' => self::RED], ['spaceAfter' => 0]);

        // CBMA seal + college name on the right.
        $right = $table->addCell((int) ($width * 0.38), ['valign' => 'bottom']);
        $right->addImage(DocumentLayouts::imagePath($this->document, 'header.logo_right'), ['height' => 30, 'alignment' => Jc::RIGHT]);
        foreach (DocumentLayouts::wrapped($this->document, 'header.college_name', 26) as $line) {
            $right->addText($line, ['name' => 'Tahoma', 'size' => 8], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        }

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
        [$first, $rest] = array_pad(explode(' ', DocumentLayouts::text($this->document, 'footer.tagline'), 2), 2, '');
        $tagline->addText($first, ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::BLUE]);
        if ($rest !== '') {
            $tagline->addText(' ' . $rest, ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::RED]);
        }

        $middle = $table->addCell((int) ($width * 0.26), ['valign' => 'bottom']);
        if (DocumentLayouts::enabled($this->document, 'footer.page_numbers')) {
            $middle->addPreserveText('Page {PAGE} of {NUMPAGES}', ['name' => 'Calibri', 'size' => 8, 'color' => '555555'], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        }

        $right = $table->addCell((int) ($width * 0.40), ['valign' => 'bottom']);
        foreach (DocumentLayouts::lines($this->document, 'footer.contact') as $line) {
            $right->addText($line, ['name' => 'Calibri', 'size' => 8, 'color' => self::RED], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        }
    }

    // Same opening lines as the official TOS: university, college, term, then the title.
    protected function addTitleBlock(Section $section, Exam $exam, string $title): void
    {
        $size = $this->options['size'];
        $center = ['alignment' => Jc::CENTER, 'spaceAfter' => 0];
        $course = $exam->programAssignment->course;

        // The Exam column can leave these lines out; the answer key always has them.
        if ($this->document !== 'exam' || DocumentLayouts::enabled('exam', 'contents.title_lines')) {
            $section->addText(mb_strtoupper(DocumentLayouts::text($this->document, 'header.university_name')), ['bold' => true, 'size' => $size], $center);
            $section->addText(mb_strtoupper(DocumentLayouts::text($this->document, 'header.college_name')), ['bold' => true, 'size' => $size], $center);
            $section->addText(mb_strtoupper(AcademicTerm::currentSemester()) . ', A.Y. ' . AcademicTerm::currentSchoolYear(), ['bold' => true, 'size' => $size], $center);
            $section->addTextBreak(1, ['size' => $size - 4]);
        }
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
                if ($this->isAnswerSheet()) {
                    $section->addText('(True / False)', ['size' => $size], $indent);
                } else {
                    $section->addText('Answer: ________ (True / False)', ['size' => $size], $indent);
                }
                break;

            case 'identification':
                if (!$this->isAnswerSheet()) {
                    $section->addText('Answer: ' . $this->blank(31, 8), ['size' => $size], $indent);
                }
                break;

            case 'enumeration':
                if (!$this->isAnswerSheet()) {
                    $count = count($options['answers'] ?? ['']);
                    for ($i = 0; $i < max($count, 1); $i++) {
                        $section->addText(($i + 1) . '. ' . $this->blank(31, strlen((string) ($i + 1)) + 2), ['size' => $size], $indent);
                    }
                }
                break;

            case 'problem-solving':
                $section->addText('Show your solution' . ($this->isAnswerSheet() ? ' on the answer sheet.' : ':'), ['size' => $size - 1, 'italic' => true], $indent);
                if (!$this->isAnswerSheet()) {
                    for ($i = 0; $i < 4; $i++) {
                        $section->addText($this->blank(55), ['size' => $size], $indent);
                    }
                }
                break;

            case 'short-answer': // labeled "Essay" in the UI — open-ended, graded against a rubric
                if (!$this->isAnswerSheet()) {
                    for ($i = 0; $i < 6; $i++) {
                        $section->addText($this->blank(55), ['size' => $size], $indent);
                    }
                }
                break;

            default:
                // Unreachable via the current "Choose Question Type" modal (kept only so an
                // older exam with a since-removed type still exports instead of erroring).
                if (!$this->isAnswerSheet()) {
                    $section->addText($this->blank(55), ['size' => $size], $indent);
                }
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
