<?php

namespace App\Support;

// URLs for the shared file viewer (FileController). Every "View" opens the viewer in the
// same tab, with a Back button to the page it came from, and every "Download" downloads,
// so the two never get mixed up.
class FileLinks
{
    public static function view(string $source, int $id): string
    {
        return url("/files/{$source}/{$id}");
    }

    public static function download(string $source, int $id): string
    {
        return url("/files/{$source}/{$id}/download");
    }
}
