<?php

namespace App\Support;

use App\Models\Exam;
use App\Models\Setting;
use Illuminate\Validation\Rule;

// Font and paper for the downloaded exam and answer key. The college default is set by
// the Admin (Settings); each exam keeps the faculty's own choice from Download Options.
class ExamPrintOptions
{
    public const FONTS = ['Arial', 'Times New Roman', 'Calibri', 'Tahoma'];

    public const SIZES = [10, 11, 12, 14];

    // Page size in twips (1 inch = 1440).
    public const PAPERS = [
        'short' => ['label' => 'Short (8.5 × 11 in)', 'width' => 12240, 'height' => 15840],
        'long'  => ['label' => 'Long (8.5 × 13 in)', 'width' => 12240, 'height' => 18720],
        'a4'    => ['label' => 'A4 (8.27 × 11.69 in)', 'width' => 11906, 'height' => 16838],
    ];

    public const DEFAULTS = ['font' => 'Arial', 'size' => 12, 'paper' => 'long'];

    public const SETTING_FONT = 'exam.font';
    public const SETTING_SIZE = 'exam.font_size';
    public const SETTING_PAPER = 'exam.paper';

    public static function collegeDefaults(): array
    {
        return self::clean([
            'font'  => Setting::get(self::SETTING_FONT),
            'size'  => Setting::get(self::SETTING_SIZE),
            'paper' => Setting::get(self::SETTING_PAPER),
        ], self::DEFAULTS);
    }

    public static function forExam(Exam $exam): array
    {
        return self::clean([
            'font'  => $exam->print_font,
            'size'  => $exam->print_font_size,
            'paper' => $exam->print_paper,
        ], self::collegeDefaults());
    }

    // Validation rules for the three fields under the given names.
    public static function rules(string $font = 'font', string $size = 'size', string $paper = 'paper', bool $required = false): array
    {
        $presence = $required ? 'required' : 'nullable';

        return [
            $font  => [$presence, Rule::in(self::FONTS)],
            $size  => [$presence, 'integer', Rule::in(self::SIZES)],
            $paper => [$presence, Rule::in(array_keys(self::PAPERS))],
        ];
    }

    // Keeps only valid values, falling back field by field.
    protected static function clean(array $values, array $fallback): array
    {
        return [
            'font'  => in_array($values['font'], self::FONTS, true) ? $values['font'] : $fallback['font'],
            'size'  => in_array((int) $values['size'], self::SIZES, true) ? (int) $values['size'] : $fallback['size'],
            'paper' => array_key_exists((string) $values['paper'], self::PAPERS) ? $values['paper'] : $fallback['paper'],
        ];
    }
}
