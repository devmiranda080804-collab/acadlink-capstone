<?php

// Plain-language validation messages for the whole system (panel feedback, Phase 1
// minor #18). Only the rules the app uses are listed; anything else falls back to
// Laravel's defaults. A form can still set its own message in its controller.
//
// :mb and :types are filled in by AppServiceProvider (the file size limit in MB, and
// the allowed file types written as "PDF, DOC or DOCX").

// Fields picked from a list rather than typed: "Please choose the course."
$choices = [
    'course_id', 'course_topic_id', 'program_assignment_id', 'exam_section_id', 'faculty_id',
    'role', 'program', 'programs', 'grading_period', 'category', 'type', 'doc_type',
    'semester', 'school_year', 'academic_year', 'term_mode',
];

$custom = [
    'password'   => ['min' => 'Your password must be at least :min characters long.'],
    'email'      => ['unique' => 'This email address is already used by another account.'],
    'expires_at' => ['after' => 'The expiry date must be in the future.'],
    'file'       => ['required' => 'Please choose a file to upload.', 'required_if' => 'Please choose a file to upload.'],
    'module'     => ['required' => 'Please choose the module file to upload.'],
];
foreach ($choices as $field) {
    $custom[$field] = ['required' => 'Please choose the :attribute.', 'required_if' => 'Please choose the :attribute.'];
}

return [
    'required'        => 'Please fill in the :attribute.',
    'required_if'     => 'Please fill in the :attribute.',
    'required_with'   => 'Please fill in the :attribute.',
    'present'         => 'Please fill in the :attribute.',
    'filled'          => 'Please fill in the :attribute.',

    'string'          => 'The :attribute must be text.',
    'integer'         => 'The :attribute must be a whole number, for example 3.',
    'numeric'         => 'The :attribute must be a number.',
    'boolean'         => 'The :attribute must be yes or no.',
    'array'           => 'Please choose at least one :attribute.',
    'email'           => 'Please enter a valid email address, for example juan.delacruz@gmail.com.',
    'in'              => 'Please choose a valid :attribute.',
    'exists'          => 'The selected :attribute could not be found. It may have been removed, so please refresh the page and choose again.',
    'unique'          => 'This :attribute is already in use. Please use a different one.',
    'confirmed'       => 'The two passwords don\'t match. Please type the same password in both fields.',
    'distinct'        => 'The :attribute has a duplicate value.',

    'date'            => 'Please enter a valid date for the :attribute.',
    'after'           => 'The :attribute must be later than :date.',
    'after_or_equal'  => 'The :attribute can\'t be earlier than :date.',
    'before'          => 'The :attribute must be earlier than :date.',
    'before_or_equal' => 'The :attribute can\'t be later than :date.',

    'gt'  => ['numeric' => 'The :attribute must be more than :value.'],
    'gte' => ['numeric' => 'The :attribute can\'t be less than :value.'],
    'lt'  => ['numeric' => 'The :attribute must be less than :value.'],
    'lte' => ['numeric' => 'The :attribute can\'t be more than :value.'],

    'min' => [
        'numeric' => 'The :attribute must be at least :min.',
        'string'  => 'The :attribute must be at least :min characters long.',
        'array'   => 'Please add at least :min :attribute.',
        'file'    => 'This file is too small.',
    ],
    'max' => [
        'numeric' => 'The :attribute can\'t be more than :max.',
        'string'  => 'The :attribute is too long. Please keep it under :max characters.',
        'array'   => 'You can add up to :max :attribute only.',
        'file'    => 'This file is too large. The limit is :mb MB.',
    ],
    'size' => [
        'numeric' => 'The :attribute must be :size.',
        'string'  => 'The :attribute must be exactly :size characters.',
        'array'   => 'The :attribute must have exactly :size items.',
        'file'    => 'This file must be :size kilobytes.',
    ],

    'file'      => 'Please choose a file to upload.',
    'uploaded'  => 'The file couldn\'t be uploaded. It may be too large, or your connection dropped. Please try again.',
    'image'     => 'Please upload an image (JPG, PNG or GIF).',
    'mimes'     => 'This file type isn\'t allowed here. Please upload :types.',
    'mimetypes' => 'This file type isn\'t allowed here. Please upload :types.',

    'custom' => $custom,

    // How each field is named in the messages above.
    'attributes' => [
        'title'                 => 'title',
        'description'           => 'description',
        'file'                  => 'file',
        'module'                => 'module file',
        'course_id'             => 'course',
        'course_topic_id'       => 'topic',
        'program_assignment_id' => 'subject',
        'exam_section_id'       => 'test',
        'faculty_id'            => 'faculty member',
        'first_name'            => 'first name',
        'last_name'             => 'last name',
        'email'                 => 'email address',
        'google_email'          => 'Google email',
        'password'              => 'password',
        'role'                  => 'role',
        'program'               => 'program',
        'programs'              => 'program',
        'programs.*'            => 'program',
        'academic_year'         => 'academic year',
        'school_year'           => 'school year',
        'semester'              => 'semester',
        'grading_period'        => 'grading period',
        'code'                  => 'code',
        'expires_at'            => 'expiry date',
        'body'                  => 'message',
        'content'               => 'content',
        'version'               => 'version',
        'topic'                 => 'topic',
        'hours'                 => 'hours',
        'week_from'             => 'From week',
        'week_to'               => 'To week',
        'target_items'          => 'number of items',
        'total_items'           => 'total number of items',
        'count'                 => 'number of items',
        'deadline'              => 'deadline',
        'activity_date'         => 'date',
        'location'              => 'location',
        'duration_minutes'      => 'duration',
        'notes'                 => 'notes',
        'category'              => 'category',
        'type'                  => 'type',
        'doc_type'              => 'document type',
        'reason'                => 'reason',
        'sample_activities'     => 'sample activities',
        'dean_name'             => 'Dean\'s name',
        'dean_title'            => 'Dean\'s title',
        'term_mode'             => 'term setting',
        'term_school_year'      => 'academic year',
        'term_semester'         => 'semester',
        'sections'              => 'test',
        'sections.*.title'      => 'test title',
        'sections.*.instructions' => 'test instructions',
        'sections.*.questions.*.question_text' => 'question',
        'sections.*.questions.*.points'        => 'points',
        'sections.*.questions.*.topic'         => 'topic',
        'parts'                 => 'test',
        'ids'                   => 'question',
        'ids.*'                 => 'question',
    ],
];
