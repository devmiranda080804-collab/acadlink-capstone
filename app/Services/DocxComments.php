<?php

namespace App\Services;

use ZipArchive;

// Removes the review comments from a Word file, so a template the Dean approved reaches
// faculty clean. The text and formatting stay exactly as they are.
class DocxComments
{
    protected const COMMENT_PARTS = [
        'word/comments.xml',
        'word/commentsExtended.xml',
        'word/commentsIds.xml',
        'word/commentsExtensible.xml',
    ];

    public static function has(string $path): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return false;
        }
        $has = $zip->locateName('word/comments.xml') !== false;
        $zip->close();

        return $has;
    }

    // Returns whether anything was removed.
    public static function strip(string $path): bool
    {
        $zip = new ZipArchive();
        if ($zip->open($path) !== true) {
            return false;
        }

        $changed = false;
        foreach (self::COMMENT_PARTS as $part) {
            if ($zip->locateName($part) !== false) {
                $zip->deleteName($part);
                $changed = true;
            }
        }

        // The comment markers in the text, and the runs that only hold a comment reference.
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            if (!preg_match('#^word/(document|header\d*|footer\d*|footnotes|endnotes)\.xml$#', $name)) {
                continue;
            }
            $xml = $zip->getFromIndex($i);
            $clean = preg_replace([
                '#<w:commentRangeStart\b[^>]*/>#',
                '#<w:commentRangeEnd\b[^>]*/>#',
                '#<w:r\b(?:(?!</w:r>).)*?<w:commentReference\b[^>]*/>(?:(?!</w:r>).)*?</w:r>#s',
                '#<w:commentReference\b[^>]*/>#',
            ], '', $xml);
            if ($clean !== $xml) {
                $zip->addFromString($name, $clean);
                $changed = true;
            }
        }

        // Links to the removed parts.
        foreach (['word/_rels/document.xml.rels', '[Content_Types].xml'] as $part) {
            $xml = $zip->getFromName($part);
            if ($xml === false) {
                continue;
            }
            $clean = preg_replace([
                '#<Relationship\b[^>]*Target="(?:\./)?comments(?:Extended|Ids|Extensible)?\.xml"[^>]*/>#i',
                '#<Override\b[^>]*PartName="/word/comments(?:Extended|Ids|Extensible)?\.xml"[^>]*/>#i',
            ], '', $xml);
            if ($clean !== $xml) {
                $zip->addFromString($part, $clean);
                $changed = true;
            }
        }

        $zip->close();

        return $changed;
    }
}
