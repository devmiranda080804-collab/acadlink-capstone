<?php

namespace App\Services;

use Anthropic\Client;
use RuntimeException;

// Drafts exam questions from a topic's teaching notes using Claude (Haiku 4.5 — the
// cheapest current model, chosen deliberately after a cost discussion for this
// low-stakes, high-volume drafting task). Human-in-the-loop by design: everything
// returned here is a DRAFT only — nothing is persisted, and Bloom's Level is always
// re-assigned server-side from the question type in ExamGeneratorController::createQuestion(),
// never taken from the AI's output, matching Panel comment #11.
class QuestionGeneratorService
{
    protected Client $client;

    // One JSON schema per supported type, each producing {question_text, options}
    // in exactly the shape ExamQuestion.options already expects — no transform needed
    // between what the AI returns and what gets saved.
    const SUPPORTED_TYPES = ['mc-single', 'true-false', 'identification', 'enumeration', 'short-answer', 'problem-solving'];

    public function __construct()
    {
        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            throw new RuntimeException(
                'AI question generation is not configured yet. Set ANTHROPIC_API_KEY in .env '
                . '(get a key from console.anthropic.com).'
            );
        }

        $this->client = new Client(apiKey: $apiKey);
    }

    // Guidance for how a question should be worded/framed to genuinely exercise each
    // Bloom's Taxonomy level, not just carry its label — used so AI-generated batches
    // stay meaningfully aligned to the TOS's per-level breakdown, not just numerically.
    const LEVEL_PROMPT_HINTS = [
        'Remembering'   => 'requires recalling a fact, term, or definition directly stated in the content',
        'Understanding' => 'requires explaining or interpreting a concept in the student\'s own understanding, not just recalling it',
        'Applying'      => 'requires using a concept or procedure to solve a new problem or situation',
        'Analyzing'     => 'requires breaking a concept down into its parts or comparing/contrasting elements',
        'Evaluating'    => 'requires judging, critiquing, or justifying a position based on criteria',
        'Creating'      => 'requires proposing, designing, or synthesizing something new from the concepts covered',
    ];

    // $modulePdfAbsolutePath: when the topic has an uploaded module PDF, its content is fed
    // directly to Claude as a document block — no text extraction/conversion needed. $topicNotes
    // is still sent alongside as a short-hand summary/fallback when there's no module.
    // $bloomLevels: the Bloom's Taxonomy level(s) this batch must be written for (per the
    // TOS's own per-topic breakdown — see ExamGeneratorController::generateQuestions()) so
    // the questions are genuinely aligned to that cognitive demand, not just labeled as such.
    public function generate(string $type, string $topicName, string $topicNotes, int $count, ?string $modulePdfAbsolutePath = null, array $bloomLevels = []): array
    {
        $itemSchema = $this->itemSchemaFor($type);

        $levelHint = '';
        if ($bloomLevels) {
            $hints = collect($bloomLevels)
                ->map(fn($level) => ($level . ': ' . (self::LEVEL_PROMPT_HINTS[$level] ?? '')))
                ->implode(' / ');
            $levelHint = " Each question must be written at this Bloom's Taxonomy level: {$hints}.";
            if (count($bloomLevels) > 1) {
                $levelHint .= ' If writing more than one question, distribute them across these levels as evenly as possible.';
            }
        }

        $instruction = "Topic: {$topicName}\n\n"
            . ($topicNotes ? "Notes:\n{$topicNotes}\n\n" : '')
            . "Write exactly {$count} question(s) of this type based strictly on the content "
            . ($modulePdfAbsolutePath ? 'in the attached document' : 'above') . '.'
            . ($type === 'mc-single' ? ' Each question must have exactly 4 answer choices.' : '')
            . $levelHint;

        $content = [];
        if ($modulePdfAbsolutePath) {
            $content[] = [
                'type' => 'document',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'application/pdf',
                    'data' => base64_encode(file_get_contents($modulePdfAbsolutePath)),
                ],
            ];
        }
        $content[] = ['type' => 'text', 'text' => $instruction];

        $message = $this->client->messages->create(
            model: 'claude-haiku-4-5',
            maxTokens: 4096,
            system: 'You write exam questions for a Philippine college Accountancy/Business '
                . 'course, strictly grounded in the teaching content given to you. Do not invent '
                . 'facts not implied by that content. Return only the questions asked for.',
            messages: [[
                'role' => 'user',
                'content' => $content,
            ]],
            outputConfig: [
                'format' => [
                    'type' => 'json_schema',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'questions' => ['type' => 'array', 'items' => $itemSchema],
                        ],
                        'required' => ['questions'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        );

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                return json_decode($block->text, true)['questions'] ?? [];
            }
        }

        return [];
    }

    protected function itemSchemaFor(string $type): array
    {
        $optionsSchema = match ($type) {
            'mc-single' => [
                'type' => 'object',
                'properties' => [
                    // minItems/maxItems on arrays aren't supported by the structured-output
                    // API (only 0 or 1 are accepted) — the exact count of 4 is enforced via
                    // the description here and reinforced in the prompt text instead.
                    'choices' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => 'Exactly 4 answer choices'],
                    // 'minimum'/'maximum' aren't supported on integer types by the
                    // structured-output API either — enforced via description + prompt only.
                    'correct' => ['type' => 'integer', 'description' => 'Zero-based index (0, 1, 2, or 3) of the correct choice among the 4'],
                ],
                'required' => ['choices', 'correct'],
                'additionalProperties' => false,
            ],
            'true-false' => [
                'type' => 'object',
                'properties' => ['answer' => ['type' => 'boolean']],
                'required' => ['answer'],
                'additionalProperties' => false,
            ],
            'identification' => [
                'type' => 'object',
                'properties' => ['answer' => ['type' => 'string']],
                'required' => ['answer'],
                'additionalProperties' => false,
            ],
            'enumeration' => [
                'type' => 'object',
                'properties' => ['answers' => ['type' => 'array', 'items' => ['type' => 'string']]],
                'required' => ['answers'],
                'additionalProperties' => false,
            ],
            'short-answer' => [
                'type' => 'object',
                'properties' => ['rubric' => ['type' => 'string']],
                'required' => ['rubric'],
                'additionalProperties' => false,
            ],
            'problem-solving' => [
                'type' => 'object',
                'properties' => [
                    'answer' => ['type' => 'string'],
                    'solution_steps' => ['type' => 'string'],
                ],
                'required' => ['answer', 'solution_steps'],
                'additionalProperties' => false,
            ],
            default => throw new \InvalidArgumentException("Unsupported question type for AI generation: {$type}"),
        };

        return [
            'type' => 'object',
            'properties' => [
                'question_text' => ['type' => 'string'],
                'options' => $optionsSchema,
            ],
            'required' => ['question_text', 'options'],
            'additionalProperties' => false,
        ];
    }
}
