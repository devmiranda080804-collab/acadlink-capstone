<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use RuntimeException;
use ZipArchive;

// Reads and rewrites only the wording of a .docx's paragraphs. Everything else in
// the file — layout, styles, images/logos, headers and footers — is left exactly as
// uploaded, so an edited Word file still looks like the original.
class DocxTextEditor
{
    private const W_NS = 'http://schemas.openxmlformats.org/wordprocessingml/2006/main';
    private const XML_NS = 'http://www.w3.org/XML/1998/namespace';
    private const PART = 'word/document.xml';

    // [paragraph index => text] for every non-empty paragraph in the document body.
    // The index is the paragraph's position in document order, which is what
    // applyParagraphs() uses to find it again.
    public function paragraphs(string $path): array
    {
        $result = [];

        foreach ($this->paragraphNodes($this->loadDocument($path)) as $index => $paragraph) {
            $text = $this->paragraphText($paragraph);

            if ($text !== '') {
                $result[$index] = $text;
            }
        }

        return $result;
    }

    // $edits is [paragraph index => new text]. Only the w:t text nodes of each edited
    // paragraph change — runs, formatting, images and layout are not touched.
    public function applyParagraphs(string $path, array $edits): void
    {
        $dom = $this->loadDocument($path);
        $nodes = $this->paragraphNodes($dom);

        foreach ($edits as $index => $text) {
            $paragraph = $nodes->item((int) $index);

            if ($paragraph instanceof DOMElement) {
                $this->setParagraphText($paragraph, (string) $text);
            }
        }

        $this->writeDocument($path, $dom->saveXML());
    }

    protected function loadDocument(string $path): DOMDocument
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException("Could not open docx: {$path}");
        }

        $xml = $zip->getFromName(self::PART);
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException('docx is missing ' . self::PART);
        }

        $dom = new DOMDocument();
        $dom->loadXML($xml, LIBXML_NONET);

        return $dom;
    }

    protected function paragraphNodes(DOMDocument $dom): DOMNodeList
    {
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('w', self::W_NS);

        return $xpath->query('//w:body//w:p');
    }

    // Only the text belonging to this paragraph itself — a paragraph inside a
    // text box is its own entry in paragraphNodes(), so its text must not be
    // counted (or overwritten) as part of the outer paragraph.
    protected function ownTextNodes(DOMElement $paragraph): array
    {
        $nodes = [];

        foreach ($paragraph->getElementsByTagNameNS(self::W_NS, 't') as $t) {
            $parent = $t->parentNode;

            while ($parent !== null && !($parent instanceof DOMElement && $parent->localName === 'p' && $parent->namespaceURI === self::W_NS)) {
                $parent = $parent->parentNode;
            }

            if ($parent === $paragraph) {
                $nodes[] = $t;
            }
        }

        return $nodes;
    }

    protected function paragraphText(DOMElement $paragraph): string
    {
        $text = '';

        foreach ($this->ownTextNodes($paragraph) as $t) {
            $text .= $t->textContent;
        }

        return trim($text);
    }

    protected function setParagraphText(DOMElement $paragraph, string $text): void
    {
        $nodes = $this->ownTextNodes($paragraph);

        if ($nodes === []) {
            if ($text === '') {
                return;
            }

            $document = $paragraph->ownerDocument;
            $run = $document->createElementNS(self::W_NS, 'w:r');
            $t = $document->createElementNS(self::W_NS, 'w:t');
            $run->appendChild($t);
            $paragraph->appendChild($run);
            $nodes = [$t];
        }

        // Everything goes into the first text node, which keeps that run's
        // formatting; the others are emptied. Mixed formatting inside a single
        // paragraph collapses to its first run's style after an edit.
        foreach ($nodes as $i => $t) {
            $t->nodeValue = $i === 0 ? $text : '';
            $t->setAttributeNS(self::XML_NS, 'xml:space', 'preserve');
        }
    }

    protected function writeDocument(string $path, string $xml): void
    {
        $zip = new ZipArchive();

        if ($zip->open($path) !== true) {
            throw new RuntimeException("Could not open docx for writing: {$path}");
        }

        $zip->deleteName(self::PART);
        $zip->addFromString(self::PART, $xml);
        $zip->close();
    }
}
