<?php

namespace App\Services;

use PhpOffice\PhpWord\TemplateProcessor;

// The TOS template, with room for more than one Program Head. The template has one
// signature (a name line beside the Dean's, then a title line); each extra head gets a
// copy of those two lines underneath, without the Dean's part.
class TosTemplate extends TemplateProcessor
{
    // Adds signature lines for heads 2..$count, filled as ${program_head_N} / ${program_head_title_N}.
    public function addProgramHeadLines(int $count): void
    {
        if ($count < 2) {
            return;
        }

        $xml = $this->tempDocumentMainPart;
        $name = $this->paragraphAround($xml, '${program_head}');
        $title = $this->paragraphAround($xml, '${program_head_title}');
        if (!$name || !$title || $title[0] < $name[1]) {
            return;
        }

        $nameXml = substr($xml, $name[0], $name[1] - $name[0]);
        $titleXml = substr($xml, $title[0], $title[1] - $title[0]);
        // The name line also anchors the page's footer text box; copies leave it out.
        $nameXml = preg_replace('#<mc:AlternateContent>.*?</mc:AlternateContent>|<w:drawing>.*?</w:drawing>|<w:pict>.*?</w:pict>#s', '', $nameXml);
        $space = '<w:p><w:pPr><w:spacing w:after="0" w:line="240" w:lineRule="auto"/></w:pPr></w:p>';

        $extra = '';
        for ($n = 2; $n <= $count; $n++) {
            $extra .= $space . $space
                . str_replace(['${program_head}', '${dean}'], ['${program_head_' . $n . '}', ''], $nameXml)
                . str_replace(['${program_head_title}', '${dean_title}'], ['${program_head_title_' . $n . '}', ''], $titleXml);
        }

        $this->tempDocumentMainPart = substr($xml, 0, $title[1]) . $extra . substr($xml, $title[1]);
    }

    // [start, end] of the <w:p> that holds $text, end just after its </w:p>.
    protected function paragraphAround(string $xml, string $text): ?array
    {
        $at = strpos($xml, $text);
        if ($at === false) {
            return null;
        }
        // Walk back to the paragraph that is open at $text; paragraphs inside a text box
        // (opened and closed before it) are skipped.
        preg_match_all('#<w:p[ >]|</w:p>#', substr($xml, 0, $at), $tags, PREG_OFFSET_CAPTURE);
        $depth = 0;
        $start = null;
        foreach (array_reverse($tags[0]) as [$tag, $offset]) {
            if ($tag === '</w:p>') {
                $depth++;
            } elseif ($depth === 0) {
                $start = $offset;
                break;
            } else {
                $depth--;
            }
        }
        $end = strpos($xml, '</w:p>', $at);

        return $start !== null && $end !== false ? [$start, $end + strlen('</w:p>')] : null;
    }
}
