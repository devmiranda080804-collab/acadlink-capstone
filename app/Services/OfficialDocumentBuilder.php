<?php

namespace App\Services;

use App\Support\DocumentLayouts;
use PhpOffice\PhpWord\Element\Cell;
use PhpOffice\PhpWord\Element\Section;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

// Word templates of the college's official documents (Memorandum, Request Letter, Advice
// Slip), built from System CMS → Document Layouts. Bracketed parts are for the writer to
// replace in Word; everything else comes from the document's column.
class OfficialDocumentBuilder
{
    protected const RED = 'C12035';
    protected const BLUE = '273F89';

    // Letter size, in twips (1 inch = 1440).
    protected const PAGE_W = 12240;
    protected const PAGE_H = 15840;
    protected const MARGIN = 1300;

    protected string $doc;

    public function build(string $document): PhpWord
    {
        abort_unless(!empty(DocumentLayouts::DOCUMENTS[$document]['template']), 404);
        $this->doc = $document;

        return match ($document) {
            'memorandum'     => $this->memorandum(),
            'request_letter' => $this->requestLetter(),
            'advice_slip'    => $this->adviceSlip(),
        };
    }

    public static function fileName(string $document): string
    {
        return 'CBMA-' . str_replace(' ', '-', DocumentLayouts::DOCUMENTS[$document]['title']) . '-Template.docx';
    }

    protected function text(string $field): string
    {
        return DocumentLayouts::text($this->doc, $field);
    }

    protected function lines(string $field): array
    {
        return DocumentLayouts::lines($this->doc, $field);
    }

    protected function page(PhpWord $word, string $font, int $size, bool $letterhead = true): Section
    {
        $word->setDefaultFontName($font);
        $word->setDefaultFontSize($size);
        $section = $word->addSection([
            'pageSizeW' => self::PAGE_W, 'pageSizeH' => self::PAGE_H,
            'marginTop' => $letterhead ? 1900 : 900, 'marginBottom' => $letterhead ? 1500 : 900,
            'marginLeft' => self::MARGIN, 'marginRight' => self::MARGIN,
            'headerHeight' => 400, 'footerHeight' => 360,
        ]);
        if ($letterhead) {
            $this->letterhead($section, $this->doc !== 'request_letter' || DocumentLayouts::enabled($this->doc, 'header.right_logo'));
            $this->footer($section);
        }

        return $section;
    }

    protected function width(): int
    {
        return self::PAGE_W - 2 * self::MARGIN;
    }

    // Same letterhead as the exam: university logo and name on the left, college on the right.
    protected function letterhead(Section $section, bool $withRight): void
    {
        $header = $section->addHeader();
        $width = $this->width();
        $table = $header->addTable(['cellMargin' => 0, 'width' => $width, 'unit' => 'dxa']);
        $table->addRow();
        $this->brand($table->addCell((int) ($width * 0.62), ['valign' => 'bottom']));
        $right = $table->addCell((int) ($width * 0.38), ['valign' => 'bottom']);
        if ($withRight) {
            $this->college($right, Jc::RIGHT);
        }
        $header->addText('', ['size' => 2], ['borderBottomSize' => 24, 'borderBottomColor' => self::BLUE, 'spaceAfter' => 0]);
    }

    protected function brand(Cell $cell): void
    {
        $brand = $cell->addTable(['cellMargin' => 0]);
        $brand->addRow();
        $brand->addCell(760, ['valign' => 'center'])->addImage(DocumentLayouts::imagePath($this->doc, 'header.logo_left'), ['height' => 42]);
        $words = $brand->addCell(4200, ['valign' => 'center']);
        foreach (DocumentLayouts::wrapped($this->doc, 'header.university_name', 14) as $line) {
            $words->addText($line, ['name' => 'Calibri', 'bold' => true, 'size' => 17, 'color' => self::RED], ['spaceAfter' => 0, 'spacing' => 0, 'lineHeight' => 0.9]);
        }
        $words->addText($this->text('header.university_note'), ['name' => 'Calibri', 'size' => 6.5, 'color' => self::RED], ['spaceAfter' => 0]);
    }

    protected function college(Cell $cell, string $align): void
    {
        $cell->addImage(DocumentLayouts::imagePath($this->doc, 'header.logo_right'), ['height' => 30, 'alignment' => $align]);
        foreach (DocumentLayouts::wrapped($this->doc, 'header.college_name', 26) as $line) {
            $cell->addText($line, ['name' => 'Tahoma', 'size' => 8], ['alignment' => $align, 'spaceAfter' => 0]);
        }
    }

    protected function footer(Section $section): void
    {
        if (!DocumentLayouts::hasFooter($this->doc)) {
            return;
        }
        $footer = $section->addFooter();
        $width = $this->width();
        $table = $footer->addTable(['cellMargin' => 0, 'width' => $width, 'unit' => 'dxa']);
        $table->addRow();
        $tagline = $table->addCell((int) ($width * 0.45), ['valign' => 'bottom'])->addTextRun(['spaceAfter' => 0]);
        [$first, $rest] = array_pad(explode(' ', $this->text('footer.tagline'), 2), 2, '');
        $tagline->addText($first, ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::BLUE]);
        if ($rest !== '') {
            $tagline->addText(' ' . $rest, ['name' => 'Calibri', 'bold' => true, 'size' => 8, 'color' => self::RED]);
        }
        $right = $table->addCell((int) ($width * 0.55), ['valign' => 'bottom']);
        foreach ($this->lines('footer.contact') as $line) {
            $right->addText($line, ['name' => 'Calibri', 'size' => 8, 'italic' => true, 'color' => self::RED], ['alignment' => Jc::RIGHT, 'spaceAfter' => 0]);
        }
    }

    protected function memorandum(): PhpWord
    {
        $word = new PhpWord();
        $s = $this->page($word, 'Bookman Old Style', 12);
        $tight = ['spaceAfter' => 0];

        $s->addText(str_replace('{year}', now()->format('Y'), $this->text('contents.number')), [], $tight);
        $s->addText(now()->format('F j, Y'), [], ['spaceAfter' => 240]);

        $to = $s->addTextRun(['spaceAfter' => 120]);
        $to->addText($this->text('contents.to_label') . "\t");
        $to->addText('[RECIPIENTS, e.g. ALL CBMA FACULTY MEMBERS]', ['bold' => true]);
        $subject = $s->addTextRun(['spaceAfter' => 160, 'borderBottomSize' => 12, 'borderBottomColor' => '000000']);
        $subject->addText($this->text('contents.subject_label') . '  ');
        $subject->addText('[SUBJECT]', ['bold' => true]);

        $s->addText('[Write the message here: what, when and where, and what the reader should do.]', [], ['alignment' => Jc::BOTH, 'spaceAfter' => 160]);
        $s->addListItem('[Agenda item]', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
        $s->addListItem('[Agenda item]', 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_BULLET_FILLED]);
        $s->addTextBreak();
        $s->addText($this->text('contents.closing'), [], ['spaceAfter' => 900]);

        $s->addText($this->text('contents.signatory_name'), ['bold' => true], $tight);
        $s->addText($this->text('contents.signatory_title'), [], ['spaceAfter' => 240]);

        if ($this->text('contents.noted_name') !== '') {
            $s->addText('Noted:', [], ['spaceAfter' => 700]);
            $s->addText($this->text('contents.noted_name'), ['bold' => true], $tight);
            $s->addText($this->text('contents.noted_title'), [], $tight);
        }

        return $word;
    }

    protected function requestLetter(): PhpWord
    {
        $word = new PhpWord();
        $s = $this->page($word, 'Century Gothic', 11);
        $tight = ['spaceAfter' => 0];
        $justify = ['alignment' => Jc::BOTH, 'spaceAfter' => 160];

        $s->addText(now()->format('F j, Y'), [], ['spaceAfter' => 240]);
        foreach ($this->lines('contents.recipient') as $i => $line) {
            $s->addText($line, $i === 0 ? ['bold' => true] : [], $tight);
        }
        $s->addTextBreak();

        $thru = $this->lines('contents.thru');
        if ($thru) {
            // Indented like the sample: "THRU:" then the name and position under each other.
            $table = $s->addTable(['cellMargin' => 0, 'indent' => new \PhpOffice\PhpWord\ComplexType\TblWidth(1440)]);
            foreach ($thru as $i => $line) {
                $table->addRow();
                $table->addCell(1300)->addText($i === 0 ? 'THRU:' : '', ['bold' => true], $tight);
                $table->addCell(5000)->addText($line, [], $tight);
            }
            $s->addTextBreak();
        }

        $re = $s->addTextRun(['spaceAfter' => 160]);
        $re->addText('RE: ', ['bold' => true]);
        $re->addText('[What the letter is requesting]');
        $s->addText($this->text('contents.salutation'), [], ['spaceAfter' => 160]);
        $s->addText('Greetings!', [], ['spaceAfter' => 160]);
        $s->addText('[Write the purpose of the request: what, when, where and why.]', [], $justify);
        $s->addText('[Add the details or how it supports the college\'s goals.]', [], $justify);
        $s->addText('[Close with the request for approval and thank the reader.]', [], $justify);
        $s->addText($this->text('contents.closing'), [], ['spaceAfter' => 700]);

        $s->addText('[NAME OF SENDER]', ['bold' => true, 'name' => 'Times New Roman', 'size' => 12], $tight);
        $s->addText('[Position]', ['name' => 'Times New Roman', 'size' => 12], ['spaceAfter' => 240]);

        $noted = $this->lines('contents.noted');
        if ($noted) {
            $s->addText('Noted:', [], ['spaceAfter' => 600]);
            foreach ($noted as $line) {
                [$name, $title] = array_pad(array_map('trim', explode('|', $line, 2)), 2, '');
                $s->addText($name, ['bold' => true, 'name' => 'Times New Roman', 'size' => 12], $tight);
                $s->addText($title, ['name' => 'Times New Roman', 'size' => 12], ['spaceAfter' => 480]);
            }
        }

        return $word;
    }

    protected function adviceSlip(): PhpWord
    {
        $word = new PhpWord();
        $s = $this->page($word, 'Bookman Old Style', 11, false);
        $twice = DocumentLayouts::enabled('advice_slip', 'contents.two_per_page');

        $this->slip($s);
        if ($twice) {
            $s->addText('', ['size' => 4], ['borderBottomSize' => 6, 'borderBottomColor' => '999999', 'borderBottomStyle' => 'dashed', 'spaceBefore' => 120, 'spaceAfter' => 240]);
            $this->slip($s);
        }

        return $word;
    }

    protected function slip(Section $s): void
    {
        $width = $this->width();
        $tight = ['spaceAfter' => 0];
        $border = ['borderSize' => 6, 'borderColor' => '444444'];

        // Logo and university, title, college and seal on one line.
        $top = $s->addTable(['cellMargin' => 0, 'width' => $width, 'unit' => 'dxa']);
        $top->addRow();
        $this->brand($top->addCell((int) ($width * 0.36), ['valign' => 'center']));
        $top->addCell((int) ($width * 0.34), ['valign' => 'center'])
            ->addText($this->text('contents.title'), ['name' => 'Arial', 'bold' => true, 'size' => 24, 'color' => '333333'], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
        $this->college($top->addCell((int) ($width * 0.30), ['valign' => 'center']), Jc::RIGHT);
        $s->addText('', ['size' => 4], ['spaceAfter' => 60]);

        // Student details.
        $info = $s->addTable($border + ['cellMargin' => 60, 'width' => $width, 'unit' => 'dxa']);
        foreach ($this->lines('contents.info_rows') as $row) {
            $info->addRow(340);
            $info->addCell((int) ($width * 0.18))->addText($row, ['size' => 10], $tight);
            $info->addCell(260)->addText(':', ['size' => 10], ['alignment' => Jc::CENTER, 'spaceAfter' => 0]);
            $info->addCell($width - (int) ($width * 0.18) - 260);
        }
        $s->addText('', ['size' => 4], ['spaceAfter' => 60]);

        // The advice box.
        $box = $s->addTable($border + ['cellMargin' => 80, 'width' => $width, 'unit' => 'dxa']);
        $box->addRow(2300);
        $cell = $box->addCell($width);
        $cell->addText($this->text('contents.details_label'), ['bold' => true], $tight);
        $cell->addText($this->text('contents.details_hint'), ['size' => 8.5], $tight);
        $s->addText('', ['size' => 4], ['spaceAfter' => 60]);

        // Action and signature.
        $bottom = $s->addTable($border + ['cellMargin' => 80, 'width' => $width, 'unit' => 'dxa']);
        $bottom->addRow();
        $left = $bottom->addCell((int) ($width / 2));
        foreach ($this->lines('contents.action_label') as $line) {
            $left->addText($line, ['size' => 9.5], ['spaceAfter' => 40]);
        }
        foreach ($this->lines('contents.actions') as $choice) {
            $left->addText('☐  ' . $choice, ['size' => 9.5], ['spaceAfter' => 40]);
        }
        $right = $bottom->addCell((int) ($width / 2), ['valign' => 'center']);
        $right->addText($this->text('contents.evaluator_label') . ': ________________________', ['size' => 9.5], ['spaceAfter' => 240]);
        $right->addText($this->text('contents.date_label') . ': ________________________', ['size' => 9.5], $tight);
    }
}
