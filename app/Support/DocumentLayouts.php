<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

// System CMS → Document Layouts: one column per generated document (TOS, Exam, Answer Key)
// with everything the Admin can change in it — header, contents and footer. Anything left
// unchanged follows Site Content or the wording the document has always had, so the files
// look the same until the Admin edits them. Saved as settings "layout.{document}.{field}".
class DocumentLayouts
{
    const DOCUMENTS = [
        'tos'        => ['title' => 'TOS', 'hint' => 'The Table of Specification downloaded from the Assessment Generator.'],
        'exam'       => ['title' => 'Exam', 'hint' => 'The student copy of the exam downloaded from the Exam Builder.'],
        'answer_key' => ['title' => 'Answer Key', 'hint' => 'The answer key downloaded with the exam.'],
        'memorandum'     => ['title' => 'Memorandum', 'hint' => 'A CBMA memorandum. Download the template, then write the message in Word.', 'template' => true],
        'request_letter' => ['title' => 'Request Letter', 'hint' => 'A letter of request to a university office. Download the template, then write the letter in Word.', 'template' => true],
        'advice_slip'    => ['title' => 'Advice Slip', 'hint' => 'The consultation form for students, two slips on one page to cut in half.', 'template' => true],
    ];

    // Common fields a document doesn't use (the Advice Slip has no footer).
    const EXCLUDE = [
        'advice_slip' => ['footer.tagline', 'footer.contact'],
    ];

    // Common fields whose starting value differs for one document.
    const OVERRIDES = [
        'request_letter' => [
            'footer.contact' => ['default' => "(075) 529 - 5223\nSan Vicente West, Urdaneta City, Pangasinan, Philippines\nucu.edu.ph  |  officeofthepresident@ucu.edu.ph"],
        ],
    ];

    const SECTIONS = ['header' => 'Header', 'contents' => 'Contents', 'footer' => 'Footer', 'print' => 'Print defaults'];

    // type: text | textarea | image | bool | select. default: a value, or "site:key" for Site Content.
    const COMMON = [
        'header.logo_left'       => ['section' => 'header', 'type' => 'image', 'label' => 'Left logo', 'default' => 'site:letterhead_logo', 'hint' => 'PNG, max 5MB.'],
        'header.logo_right'      => ['section' => 'header', 'type' => 'image', 'label' => 'Right logo', 'default' => 'site:letterhead_seal', 'hint' => 'PNG, max 5MB. For example the CBMA seal.'],
        'header.university_name' => ['section' => 'header', 'type' => 'text', 'label' => 'University name', 'default' => 'site:university_name'],
        'header.university_note' => ['section' => 'header', 'type' => 'text', 'label' => 'Line under the university name', 'default' => 'site:university_note'],
        'header.college_name'    => ['section' => 'header', 'type' => 'text', 'label' => 'College name', 'default' => 'site:college_name'],
        'footer.tagline'         => ['section' => 'footer', 'type' => 'text', 'label' => 'Tagline', 'default' => 'Bright future starts here', 'hint' => 'The first word prints in blue, the rest in red.'],
        'footer.contact'         => ['section' => 'footer', 'type' => 'textarea', 'label' => 'Contact details', 'default' => 'site:contact_details', 'hint' => 'One item per line.'],
    ];

    const FIELDS = [
        'tos' => [
            'contents.instructor_label' => ['section' => 'contents', 'type' => 'text', 'label' => 'Label under the instructor', 'default' => 'Printed Name & Signature of Instructor'],
            'contents.checked_label'    => ['section' => 'contents', 'type' => 'text', 'label' => 'Label for the Program Head', 'default' => 'Checked & Reviewed:'],
            'contents.approved_label'   => ['section' => 'contents', 'type' => 'text', 'label' => 'Label for the Dean', 'default' => 'Approved:'],
        ],
        'exam' => [
            'contents.title_lines'    => ['section' => 'contents', 'type' => 'bool', 'label' => 'Show university, college and term above the title', 'default' => '1'],
            'contents.name_label'     => ['section' => 'contents', 'type' => 'text', 'label' => 'Name line', 'default' => 'Name'],
            'contents.date_label'     => ['section' => 'contents', 'type' => 'text', 'label' => 'Date line', 'default' => 'Date'],
            'contents.section_label'  => ['section' => 'contents', 'type' => 'text', 'label' => 'Course & section line', 'default' => 'Course & Section'],
            'contents.score_label'    => ['section' => 'contents', 'type' => 'text', 'label' => 'Score line', 'default' => 'Score'],
            'contents.answer_sheet_note' => ['section' => 'contents', 'type' => 'text', 'label' => 'Note for the "With Answer Sheet" layout', 'default' => 'Write all your answers on the separate answer sheet.'],
            'contents.directions.mc-single'       => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: Multiple Choice', 'default' => 'Choose the letter of the best answer.'],
            'contents.directions.true-false'      => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: True or False', 'default' => 'Write TRUE if the statement is correct and FALSE if it is not.'],
            'contents.directions.identification'  => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: Identification', 'default' => 'Identify the term or concept being described.'],
            'contents.directions.enumeration'     => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: Enumeration', 'default' => 'List what is being asked.'],
            'contents.directions.short-answer'    => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: Short Answer', 'default' => 'Answer the following questions briefly but completely.'],
            'contents.directions.problem-solving' => ['section' => 'contents', 'type' => 'text', 'label' => 'Directions: Problem Solving', 'default' => 'Solve the following problems. Show your complete solution.'],
            'footer.page_numbers'     => ['section' => 'footer', 'type' => 'bool', 'label' => 'Show "Page 1 of 2"', 'default' => '1'],
            'print.font'   => ['section' => 'print', 'type' => 'select', 'label' => 'Default font', 'options' => 'fonts', 'default' => 'Arial'],
            'print.size'   => ['section' => 'print', 'type' => 'select', 'label' => 'Default font size', 'options' => 'sizes', 'default' => '12'],
            'print.paper'  => ['section' => 'print', 'type' => 'select', 'label' => 'Default paper', 'options' => 'papers', 'default' => 'long'],
            'print.layout' => ['section' => 'print', 'type' => 'select', 'label' => 'Default layout', 'options' => 'layouts', 'default' => 'standard', 'hint' => 'Faculty can still change these per exam in Print Settings.'],
        ],
        'answer_key' => [
            'contents.title_suffix' => ['section' => 'contents', 'type' => 'text', 'label' => 'Added after the exam title', 'default' => '— ANSWER KEY'],
            'contents.note'         => ['section' => 'contents', 'type' => 'text', 'optional' => true, 'label' => 'Note under the title', 'default' => 'For faculty reference only — do not distribute to students.'],
            'footer.page_numbers'   => ['section' => 'footer', 'type' => 'bool', 'label' => 'Show "Page 1 of 2"', 'default' => '1'],
        ],
        'memorandum' => [
            'contents.number'         => ['section' => 'contents', 'type' => 'text', 'label' => 'Memorandum number line', 'default' => 'CBMA Memorandum No. ___ s. {year}', 'hint' => '{year} becomes the current year.'],
            'contents.to_label'       => ['section' => 'contents', 'type' => 'text', 'label' => '"To" label', 'default' => 'To:'],
            'contents.subject_label'  => ['section' => 'contents', 'type' => 'text', 'label' => '"Subject" label', 'default' => 'Subject:'],
            'contents.closing'        => ['section' => 'contents', 'type' => 'text', 'label' => 'Closing line', 'default' => 'Thank you.'],
            'contents.signatory_name' => ['section' => 'contents', 'type' => 'text', 'label' => 'Signed by', 'default' => 'dean:name', 'hint' => 'Follows the Dean in Settings → Document Signatories until changed.'],
            'contents.signatory_title' => ['section' => 'contents', 'type' => 'text', 'label' => 'Title of the signer', 'default' => 'dean:title'],
            'contents.noted_name'     => ['section' => 'contents', 'type' => 'text', 'optional' => true, 'label' => '"Noted" by', 'default' => 'DR. JOSEPHINE S. LAMBINICIO', 'hint' => 'Leave blank to leave out the "Noted" part.'],
            'contents.noted_title'    => ['section' => 'contents', 'type' => 'text', 'label' => 'Title of the "Noted" signer', 'default' => 'Vice President for Academic Affairs'],
        ],
        'request_letter' => [
            'header.right_logo'   => ['section' => 'header', 'type' => 'bool', 'label' => 'Show the right logo and college name', 'default' => '0'],
            'contents.recipient'  => ['section' => 'contents', 'type' => 'textarea', 'label' => 'Addressed to', 'default' => "ATTY. AMIHAN APRIL C. MELLA-ALCAZAR, PhD\nActing University President\nUrdaneta City University\nSan Vicente West, Urdaneta City", 'hint' => 'One line each: name, position, office, address.'],
            'contents.thru'       => ['section' => 'contents', 'type' => 'textarea', 'optional' => true, 'label' => '"THRU" (optional)', 'default' => "Atty. Dar A. Diga\nChief Operating Officer", 'hint' => 'Name, then position. Leave blank to leave it out.'],
            'contents.salutation' => ['section' => 'contents', 'type' => 'text', 'label' => 'Greeting', 'default' => 'Dear Sir/Madam:'],
            'contents.closing'    => ['section' => 'contents', 'type' => 'text', 'label' => 'Closing', 'default' => 'Kind regards,'],
            'contents.noted'      => ['section' => 'contents', 'type' => 'textarea', 'optional' => true, 'label' => '"Noted" signers (optional)', 'default' => "dean:name | dean:title", 'hint' => 'One per line: Name | Position. The Dean in Settings is filled in until changed.'],
        ],
        'advice_slip' => [
            'contents.title'          => ['section' => 'contents', 'type' => 'text', 'label' => 'Title', 'default' => 'ADVICE SLIP'],
            'contents.info_rows'      => ['section' => 'contents', 'type' => 'textarea', 'label' => 'Student details rows', 'default' => "NAME\nCOURSE\nYEAR LEVEL\nMOBILE", 'hint' => 'One row per line.'],
            'contents.details_label'  => ['section' => 'contents', 'type' => 'text', 'label' => 'Box title', 'default' => 'Details of Advice:'],
            'contents.details_hint'   => ['section' => 'contents', 'type' => 'text', 'optional' => true, 'label' => 'Box instruction', 'default' => '(Please indicate the concern or issue and the corresponding advice or recommendation.)'],
            'contents.action_label'   => ['section' => 'contents', 'type' => 'textarea', 'label' => 'Action heading', 'default' => "Action Required / Recommendation:\nPlease report to the Dean or Program Head"],
            'contents.actions'        => ['section' => 'contents', 'type' => 'textarea', 'label' => 'Checkbox choices', 'default' => "For your information\nFor immediate action\nFor future reference\nOthers: ______________________________", 'hint' => 'One choice per line.'],
            'contents.evaluator_label' => ['section' => 'contents', 'type' => 'text', 'label' => 'Signature line', 'default' => 'Signature of Evaluator'],
            'contents.date_label'     => ['section' => 'contents', 'type' => 'text', 'label' => 'Date line', 'default' => 'Date'],
            'contents.two_per_page'   => ['section' => 'contents', 'type' => 'bool', 'label' => 'Two slips on one page (with a cut line)', 'default' => '1'],
        ],
    ];

    // Saved for an optional field the Admin emptied on purpose, so it prints nothing.
    const NONE = '__none__';

    protected static ?array $saved = null;

    public static function fields(string $document): array
    {
        $common = array_diff_key(self::COMMON, array_flip(self::EXCLUDE[$document] ?? []));
        foreach (self::OVERRIDES[$document] ?? [] as $field => $changes) {
            $common[$field] = $changes + $common[$field];
        }

        return $common + (self::FIELDS[$document] ?? []);
    }

    public static function hasFooter(string $document): bool
    {
        return isset(self::fields($document)['footer.contact']);
    }

    public static function flush(): void
    {
        self::$saved = null;
    }

    // What the document prints: the Admin's value, or the default.
    public static function text(string $document, string $field): string
    {
        $saved = self::saved($document, $field);
        if ($saved !== null) {
            return $saved === self::NONE ? '' : $saved;
        }

        return self::defaultValue($document, $field);
    }

    public static function enabled(string $document, string $field): bool
    {
        return self::text($document, $field) === '1';
    }

    public static function lines(string $document, string $field): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', self::text($document, $field)))));
    }

    // The text split over lines the way the letterhead prints it.
    public static function wrapped(string $document, string $field, int $width): array
    {
        return explode("\n", wordwrap(mb_strtoupper(self::text($document, $field)), $width));
    }

    public static function defaultValue(string $document, string $field): string
    {
        $default = (string) (self::fields($document)[$field]['default'] ?? '');
        if (str_starts_with($default, 'site:')) {
            return SiteContent::text(substr($default, 5));
        }

        // "dean:name" / "dean:title" (also inside text) follow Settings → Document Signatories.
        if (str_contains($default, 'dean:')) {
            $dean = Signatories::dean();
            $default = str_replace(['dean:name', 'dean:title'], [mb_strtoupper($dean['name'] ?? '') ?: '___________________________', $dean['title']], $default);
        }

        return $default;
    }

    public static function isChanged(string $document, string $field): bool
    {
        return self::saved($document, $field) !== null;
    }

    // The image file for a generated document: this document's own upload, else Site Content's.
    public static function imagePath(string $document, string $field): string
    {
        $own = self::uploadedImage($document, $field);
        if ($own) {
            return Storage::disk('public')->path($own);
        }

        return SiteContent::imagePath(substr(self::fields($document)[$field]['default'], 5));
    }

    // Only a replacement the Admin made (here or in Site Content) — the TOS template already
    // holds the original images, so it is left alone otherwise.
    public static function replacedImagePath(string $document, string $field): ?string
    {
        $own = self::uploadedImage($document, $field);
        if ($own) {
            return Storage::disk('public')->path($own);
        }
        $siteKey = substr(self::fields($document)[$field]['default'], 5);

        return SiteContent::uploadedImage($siteKey) ? SiteContent::imagePath($siteKey) : null;
    }

    public static function uploadedImage(string $document, string $field): ?string
    {
        $path = self::saved($document, $field);

        return $path && Storage::disk('public')->exists($path) ? $path : null;
    }

    public static function imageUrl(string $document, string $field): string
    {
        $own = self::uploadedImage($document, $field);

        return $own
            ? url('/admin/system-cms/layouts/image/' . $document . '/' . str_replace('header.logo_', '', $field) . '?v=' . Storage::disk('public')->lastModified($own))
            : SiteContent::imageUrl(substr(self::fields($document)[$field]['default'], 5));
    }

    // Default directions of a question type, as set in the Exam column.
    public static function directions(?string $type): ?string
    {
        $field = 'contents.directions.' . $type;

        return isset(self::FIELDS['exam'][$field]) ? self::text('exam', $field) : null;
    }

    public static function key(string $document, string $field): string
    {
        return 'layout.' . $document . '.' . $field;
    }

    protected static function saved(string $document, string $field): ?string
    {
        if (self::$saved === null) {
            try {
                self::$saved = Setting::where('key', 'like', 'layout.%')->pluck('value', 'key')->all();
            } catch (\Throwable) {
                self::$saved = [];
            }
        }
        $value = self::$saved[self::key($document, $field)] ?? null;

        return $value !== null && $value !== '' ? $value : null;
    }
}
