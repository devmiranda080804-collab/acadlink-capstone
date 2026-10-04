<?php

namespace App\Services;

use Anthropic\Client;
use PhpOffice\PhpWord\IOFactory;

// Checks whether an uploaded academic document's actual content plausibly
// matches the kind of document it's expected to be (e.g. a "TOS" submission
// requirement, or an "OBTL" course material, shouldn't accept a random
// unrelated file). Used by both the Submissions & Deadline feature and
// Course Coordination's official OBTL upload. Uses the same Claude
// integration already wired up for the Assessment Generator
// (QuestionGeneratorService) — PDFs are sent directly as a document block,
// DOCX text is extracted first.
//
// Deliberately fails OPEN: any technical trouble (unreadable file, missing
// API key, API error, unparseable response) returns valid=true. This is a
// content sanity-check meant to catch an obviously wrong file, not a strict
// gate that should ever block a real upload because of an infra hiccup.
class AcademicDocumentValidator
{
    protected ?Client $client;

    const TYPE_DESCRIPTIONS = [
        'syllabus'    => "an official course Syllabus — normally includes a course description, course outcomes, grading system, and a course outline/schedule of topics",
        'tos'         => "a Table of Specifications (TOS) — normally lists topics with their number of hours, number of items, and a breakdown across Bloom's Taxonomy cognitive levels (Remembering, Understanding, Applying, Analyzing, Evaluating, Creating)",
        'exam_bank'   => "an Exam / Test Question Bank — an actual set of test questions (e.g. multiple choice, identification, essay) used to assess students",
        'obtl'        => "an Outcomes-Based Teaching and Learning plan (OBTL) — normally lists the course's topics together with the number of weeks/hours allocated to each, aligned to course/program learning outcomes",
        'course_guide' => "a Course Guide — normally includes course objectives, an overview of the subject matter, required readings/materials, and how the course is structured across the term",
        'module'      => "an instructional Module / Learning Module — normally contains lesson content meant to teach students: explanations, examples, and activities or exercises for a specific topic",
        'research_template' => "a Research Template — a standardized outline/format for a research or capstone-style paper (e.g. sections for title, background, methodology, references)",
        'memorandum'  => "an official Memorandum — a formal internal communication with a header (To/From/Date/Subject) and a body conveying an announcement, directive, or information",
        'request_letter' => "a formal Request Letter — addressed to a specific recipient, stating a clear request/purpose and closing with a signature",
        'consultation_form' => "a Consultation Form — a structured form for recording a consultation session (names, date, concern/topic discussed, notes or action items)",
        'activity_proposal' => "an Activity Proposal — proposes an event or activity, normally including objectives, schedule, and budget/resources",
        'monitoring_sheet' => "a Monitoring Sheet — a tabular form for tracking the status or progress of tasks, submissions, or activities over time",
    ];

    public function __construct()
    {
        $apiKey = config('services.anthropic.api_key');
        $this->client = $apiKey ? new Client(apiKey: $apiKey) : null;
    }

    // $absolutePath must still exist on disk (validate before deleting any temp file).
    // Returns ['valid' => bool, 'reason' => ?string]. 'other' and unrecognized
    // requirement types are never checked — there's nothing to check them against.
    public function validate(string $absolutePath, string $extension, string $requirementType): array
    {
        $expected = self::TYPE_DESCRIPTIONS[$requirementType] ?? null;

        if (!$expected || !$this->client) {
            return ['valid' => true, 'reason' => null];
        }

        try {
            $block = $this->buildContentBlock($absolutePath, $extension);
            if ($block === null) {
                return ['valid' => true, 'reason' => null];
            }

            $message = $this->client->messages->create(
                model: 'claude-haiku-4-5',
                maxTokens: 300,
                system: 'You check whether a submitted academic document genuinely matches an '
                    . 'expected document type for a Philippine college. Judge the actual content, '
                    . 'not just the filename. Be lenient about formatting differences — only flag '
                    . 'it as not matching when the content is clearly a different kind of document '
                    . 'altogether (e.g. a blank page, an unrelated file, or a completely different '
                    . 'document type).',
                messages: [[
                    'role' => 'user',
                    'content' => [
                        $block,
                        ['type' => 'text', 'text' =>
                            "This file was submitted to satisfy a requirement expecting {$expected}.\n\n"
                            . 'Does this document\'s actual content genuinely match that expected type?',
                        ],
                    ],
                ]],
                outputConfig: [
                    'format' => [
                        'type' => 'json_schema',
                        'schema' => [
                            'type' => 'object',
                            'properties' => [
                                'matches' => ['type' => 'boolean'],
                                'reason'  => ['type' => 'string', 'description' => 'One short, plain-language sentence explaining the verdict, written for the faculty member who uploaded the file'],
                            ],
                            'required' => ['matches', 'reason'],
                            'additionalProperties' => false,
                        ],
                    ],
                ],
            );

            foreach ($message->content as $contentBlock) {
                if ($contentBlock->type === 'text') {
                    $json = json_decode($contentBlock->text, true);
                    if (is_array($json) && array_key_exists('matches', $json)) {
                        return ['valid' => (bool) $json['matches'], 'reason' => $json['reason'] ?? null];
                    }
                }
            }

            return ['valid' => true, 'reason' => null];
        } catch (\Throwable $e) {
            report($e);
            return ['valid' => true, 'reason' => null];
        }
    }

    protected function buildContentBlock(string $absolutePath, string $extension): ?array
    {
        $extension = strtolower($extension);

        if ($extension === 'pdf') {
            return [
                'type' => 'document',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'application/pdf',
                    'data' => base64_encode(file_get_contents($absolutePath)),
                ],
            ];
        }

        if ($extension === 'docx') {
            $text = trim($this->extractDocxText($absolutePath));
            return $text === '' ? null : ['type' => 'text', 'text' => "Document content:\n\n" . mb_substr($text, 0, 8000)];
        }

        // Legacy .doc (binary format) isn't reliably readable here — skip rather
        // than risk a false rejection from a garbled extraction.
        return null;
    }

    protected function extractDocxText(string $path): string
    {
        $phpWord = IOFactory::load($path);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $t = $element->getText();
                    $text .= (is_string($t) ? $t : '') . "\n";
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $child) {
                        if (method_exists($child, 'getText')) {
                            $childText = $child->getText();
                            $text .= is_string($childText) ? $childText : '';
                        }
                    }
                    $text .= "\n";
                }
            }
        }

        return $text;
    }
}
