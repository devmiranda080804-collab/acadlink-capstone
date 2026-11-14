<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

// System-wide wording and images the Admin manages in the System CMS. Until something is
// changed, each one falls back to the value it has always had.
class SiteContent
{
    const TEXTS = [
        'system_name' => ['label' => 'System name', 'default' => 'CBMA System', 'hint' => 'Shown on the login page.'],
        'system_tagline' => ['label' => 'Tagline', 'default' => 'Academic Coordination and Instructional Management', 'hint' => 'Shown under the system name on the login page.'],
        'sidebar_name' => ['label' => 'Sidebar name', 'default' => 'CBMA', 'hint' => 'Under the logo in the sidebar of every page.'],
        'sidebar_subtitle' => ['label' => 'Sidebar subtitle', 'default' => 'Academic Coordination', 'hint' => 'The small line under the sidebar name.'],
        'university_name' => ['label' => 'University name', 'default' => 'Urdaneta City University', 'hint' => 'Used in the letterhead and title of downloaded exams and the TOS.'],
        'university_note' => ['label' => 'University note', 'default' => 'Owned and operated by the City Government of Urdaneta', 'hint' => 'The small line under the university name in the letterhead.'],
        'college_name' => ['label' => 'College name', 'default' => 'College of Business Management and Accountancy', 'hint' => 'Used in the letterhead and title of downloaded exams and the TOS.'],
        'contact_details' => ['label' => 'Contact details', 'default' => "(075) 600 - 1507\nSan Vicente West, Urdaneta City, Pangasinan\nucu.edu.ph  |  univpresidentofficial@gmail.com", 'hint' => 'The footer of downloaded exams and the TOS. One item per line.', 'multiline' => true],
    ];

    // source: where the original image lives ("public" folder or the document branding folder).
    const IMAGES = [
        'login_background' => ['label' => 'Login background', 'source' => 'public', 'file' => 'images/bg-cbma.svg', 'hint' => 'Behind the login and password pages. Without a photo it shows the CBMA design (navy, the CBMA seal and the college name). Upload a CBMA photo or design to show it behind, with the CBMA colors over it.'],
        'login_logo' => ['label' => 'System logo', 'source' => 'public', 'file' => 'images/cbma-logo.png', 'hint' => 'On the login and password pages, and in the sidebar of every page.'],
        'letterhead_logo' => ['label' => 'Letterhead logo', 'source' => 'resource', 'file' => 'templates/branding/ucu-logo.png', 'hint' => 'Left side of the letterhead on downloaded exams and the TOS. PNG only.', 'png_only' => true],
        'letterhead_seal' => ['label' => 'Letterhead seal', 'source' => 'resource', 'file' => 'templates/branding/cbma-seal.png', 'hint' => 'Right side of the letterhead on downloaded exams and the TOS. PNG only.', 'png_only' => true],
    ];

    // Loaded once per request, because the sidebar reads these on every page.
    protected static ?array $saved = null;

    public static function flush(): void
    {
        self::$saved = null;
    }

    protected static function saved(string $key): ?string
    {
        self::$saved ??= Setting::where('key', 'like', 'site.%')->pluck('value', 'key')->all();

        return (self::$saved[$key] ?? '') !== '' ? self::$saved[$key] : null;
    }

    public static function text(string $key): string
    {
        return self::saved('site.' . $key) ?? self::TEXTS[$key]['default'];
    }

    // The text split over lines the way the letterhead prints it.
    public static function wrapped(string $key, int $width): array
    {
        return explode("\n", wordwrap(mb_strtoupper(self::text($key)), $width));
    }

    // The contact details as separate lines.
    public static function lines(string $key): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', self::text($key)))));
    }

    public static function uploadedImage(string $key): ?string
    {
        $path = self::saved('site.image.' . $key);

        return $path && Storage::disk('public')->exists($path) ? $path : null;
    }

    // For a page: the uploaded image's link, or the original image.
    public static function imageUrl(string $key): string
    {
        $uploaded = self::uploadedImage($key);

        return $uploaded ? url('/site-content/' . $key . '?v=' . Storage::disk('public')->lastModified($uploaded)) : self::originalUrl($key);
    }

    public static function originalUrl(string $key): string
    {
        return self::IMAGES[$key]['source'] === 'public'
            ? asset(self::IMAGES[$key]['file'])
            : url('/site-content/' . $key . '?original=1');
    }

    // For a generated document: the image file on disk.
    public static function imagePath(string $key): string
    {
        $uploaded = self::uploadedImage($key);

        return $uploaded ? Storage::disk('public')->path($uploaded) : self::originalPath($key);
    }

    public static function originalPath(string $key): string
    {
        $image = self::IMAGES[$key];

        return $image['source'] === 'public' ? public_path($image['file']) : resource_path($image['file']);
    }
}
