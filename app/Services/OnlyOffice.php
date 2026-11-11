<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

// The document editor inside AcadLink. ONLYOFFICE Docs runs as its own service: the browser
// loads the editor from it, it fetches the file from AcadLink through a signed link, and it
// sends the edited file back to the callback. Both sides sign their messages with a shared
// secret, so nobody else can open a file through it or overwrite one.
class OnlyOffice
{
    protected const DOCUMENT_TYPES = [
        'doc' => 'word', 'docx' => 'word', 'xlsx' => 'cell', 'pptx' => 'slide', 'pdf' => 'pdf',
    ];

    protected const COMMAND_OK = 0;
    protected const COMMAND_NOT_OPEN = 1;
    protected const COMMAND_NO_CHANGES = 4;

    public static function isConfigured(): bool
    {
        return filled(config('services.onlyoffice.url')) && filled(config('services.onlyoffice.secret'));
    }

    public static function scriptUrl(): string
    {
        return rtrim(config('services.onlyoffice.url'), '/') . '/web-apps/apps/api/documents/api.js';
    }

    // Settings for the editor page, signed so the editor knows they came from AcadLink.
    public function editorConfig(Model $document, User $user): array
    {
        $type = $document->extension();
        $canEdit = $document->canEdit($user);
        // A reviewer can leave comments without changing the text (e.g. the Dean on a template).
        $canComment = $canEdit || (method_exists($document, 'canComment') && $document->canComment($user));
        $route = ['source' => $document::ONLYOFFICE_SOURCE, 'id' => $document->id];

        $config = [
            'documentType' => self::DOCUMENT_TYPES[$type],
            'document' => [
                'fileType' => $type,
                'key' => $this->keyFor($document),
                'title' => $document->file_name,
                'url' => $this->appUrl(URL::temporarySignedRoute('onlyoffice.file', now()->addDay(), $route, absolute: false)),
                'permissions' => ['edit' => $canEdit, 'comment' => $canComment, 'download' => true, 'print' => true],
            ],
            'editorConfig' => [
                'mode' => $canComment ? 'edit' : 'view',
                'lang' => 'en',
                'callbackUrl' => $this->appUrl(route('onlyoffice.callback', $route, absolute: false)),
                'user' => ['id' => (string) $user->id, 'name' => $user->name],
                'customization' => ['autosave' => true, 'forcesave' => true, 'compactHeader' => true],
            ],
        ];

        $config['token'] = JWT::encode($config, config('services.onlyoffice.secret'), 'HS256');

        return $config;
    }

    // Asks an open editor to send its unsaved changes to the callback now. True means a save
    // is on its way; false means there is nothing to wait for (no open editor or no changes).
    public function forceSave(Model $document): bool
    {
        if (!self::isConfigured() || !$document->document_key) {
            return false;
        }

        $command = ['c' => 'forcesave', 'key' => $document->document_key];
        $command['token'] = JWT::encode($command, config('services.onlyoffice.secret'), 'HS256');

        try {
            $response = Http::timeout(10)->acceptJson()->post(rtrim(config('services.onlyoffice.url'), '/') . '/coauthoring/CommandService.ashx', $command);
        } catch (\Throwable $e) {
            report($e);

            return false;
        }

        $error = $response->json('error');
        if (!in_array($error, [self::COMMAND_OK, self::COMMAND_NOT_OPEN, self::COMMAND_NO_CHANGES], true)) {
            Log::warning('Document editor refused a save command', ['key' => $document->document_key, 'error' => $error, 'http' => $response->status()]);
        }

        return $error === self::COMMAND_OK;
    }

    // Starts or checks a conversion. The file link in $job is relative to AcadLink.
    public function convert(array $job): array
    {
        $job['url'] = $this->appUrl($job['url']);
        $job['token'] = JWT::encode($job, config('services.onlyoffice.secret'), 'HS256');

        $response = Http::timeout(30)->acceptJson()->post(rtrim(config('services.onlyoffice.url'), '/') . '/converter', $job);
        $data = $response->json();

        abort_if(!$response->successful() || !is_array($data) || !empty($data['error']), 422, 'This file could not be converted. It may be protected or damaged. Please try another file.');

        return $data;
    }

    // The message the editor sent, or null if it wasn't signed with our secret. The signed
    // copy is the one to trust: in the request body as "token", or in the Authorization header.
    public function verifiedCallback(?string $bodyToken, ?string $headerToken): ?array
    {
        $token = $bodyToken ?: $headerToken;
        if (!$token || !self::isConfigured()) {
            return null;
        }

        try {
            $decoded = JWT::decode($token, new Key(config('services.onlyoffice.secret'), 'HS256'));
        } catch (\Throwable) {
            return null;
        }

        return json_decode(json_encode($decoded->payload ?? $decoded), true);
    }

    public function keyFor(Model $document): string
    {
        if (!$document->document_key) {
            $document->forceFill(['document_key' => self::newKey($document)])->saveQuietly();
        }

        return $document->document_key;
    }

    // Unique across the kinds of record, since they all share one editor service.
    public static function newKey(Model $document): string
    {
        return $document::ONLYOFFICE_SOURCE . '-' . $document->id . '-' . Str::random(32);
    }

    // The address where the editor service reaches AcadLink, which can differ from the one
    // people use (for example from inside a container).
    protected function appUrl(string $path): string
    {
        return rtrim(config('services.onlyoffice.app_url') ?: config('app.url'), '/') . $path;
    }
}
