<?php

namespace App\Services;

use Anthropic\Client;
use RuntimeException;

// Reads a topic's module and suggests its key points. Only a suggestion: the
// instructor sees the list in the Add Topic form and can change it before saving.
class ModuleKeyPointsService
{
    const MAX_POINTS = 10;

    // Long Word modules are cut to this many characters before they are sent.
    const MAX_TEXT = 60000;

    protected Client $client;

    public function __construct()
    {
        $apiKey = config('services.anthropic.api_key');

        if (! $apiKey) {
            report(new RuntimeException('AI key points are not configured: ANTHROPIC_API_KEY is missing from .env.'));
            throw new RuntimeException('AI key points aren\'t available yet. You can type the key points yourself.');
        }

        $this->client = new Client(apiKey: $apiKey);
    }

    // Pass the PDF path for a PDF module, or the extracted text for a Word module.
    public function suggest(string $topic, ?string $pdfPath = null, ?string $text = null): array
    {
        $content = [];
        if ($pdfPath) {
            $content[] = [
                'type' => 'document',
                'source' => [
                    'type' => 'base64',
                    'media_type' => 'application/pdf',
                    'data' => base64_encode(file_get_contents($pdfPath)),
                ],
            ];
        } else {
            $content[] = ['type' => 'text', 'text' => "Module content:\n\n" . mb_substr((string) $text, 0, self::MAX_TEXT)];
        }

        $topicLine = trim($topic) !== '' ? 'The topic is "' . trim($topic) . '". ' : '';
        $content[] = ['type' => 'text', 'text' => $topicLine
            . 'List the key points a student must learn from this module, in the order the module teaches them. '
            . 'Give 4 to 8 points. Write each point as one short, clear sentence (at most 20 words) in English. '
            . 'Use only what the module says; do not add facts that are not in it. '
            . 'Do not number the points.'];

        $message = $this->client->messages->create(
            model: 'claude-haiku-4-5',
            maxTokens: 1024,
            system: 'You read teaching modules for a Philippine college Accountancy/Business course '
                . 'and pick out their key points for the instructor.',
            messages: [['role' => 'user', 'content' => $content]],
            outputConfig: [
                'format' => [
                    'type' => 'json_schema',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'key_points' => ['type' => 'array', 'items' => ['type' => 'string'], 'description' => '4 to 8 key points'],
                        ],
                        'required' => ['key_points'],
                        'additionalProperties' => false,
                    ],
                ],
            ],
        );

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                return self::clean(json_decode($block->text, true)['key_points'] ?? []);
            }
        }

        return [];
    }

    // Trims each point, drops bullets/numbers and blanks, removes repeats.
    public static function clean(array|string|null $points): array
    {
        if (is_string($points)) {
            $points = preg_split('/\r\n|\r|\n/', $points);
        }

        $clean = [];
        foreach ((array) $points as $point) {
            $point = trim(preg_replace('/^\s*(?:[-*•·]+|\d+[.)])\s*/u', '', (string) $point));
            $point = preg_replace('/\s+/u', ' ', $point);
            if ($point !== '' && ! in_array(mb_strtolower($point), array_map('mb_strtolower', $clean), true)) {
                $clean[] = mb_substr($point, 0, 300);
            }
        }

        return array_slice($clean, 0, self::MAX_POINTS);
    }
}
