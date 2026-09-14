<?php

namespace App\Services;

use Google\Client;
use Google\Service\Docs;
use Google\Service\Docs\Document as GoogleDocument;
use Google\Service\Drive;
use Google\Service\Drive\Permission;
use RuntimeException;

// Wraps the Google Docs + Drive APIs behind a single "system" Google account
// (a personal Gmail authorized once via `php artisan google:authorize`).
// Documents are created under that account and shared out to each real
// user's own Gmail address — no per-user OAuth login is needed.
class GoogleDocsService
{
    protected Client $client;

    public function __construct()
    {
        $clientId     = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $refreshToken = config('services.google.refresh_token');

        if (! $clientId || ! $clientSecret || ! $refreshToken) {
            throw new RuntimeException(
                'Google Docs is not configured yet. Set GOOGLE_CLIENT_ID, GOOGLE_CLIENT_SECRET, '
                . 'and GOOGLE_REFRESH_TOKEN in .env (run `php artisan google:authorize` to get the refresh token).'
            );
        }

        $this->client = new Client();
        $this->client->setClientId($clientId);
        $this->client->setClientSecret($clientSecret);
        $this->client->setAccessType('offline');
        $this->client->addScope(Docs::DOCUMENTS);
        $this->client->addScope(Drive::DRIVE_FILE);
        $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
    }

    public function createDocument(string $title): string
    {
        $docs = new Docs($this->client);
        $document = $docs->documents->create(new GoogleDocument(['title' => $title]));

        return $document->getDocumentId();
    }

    // Grants access to one real user's own Gmail address.
    // role: 'writer' (can edit) or 'reader' (view only — used for the
    // protected master copy of a distributed template)
    public function shareWithEmail(string $fileId, string $email, string $role = 'writer'): void
    {
        $drive = new Drive($this->client);

        $permission = new Permission([
            'type'         => 'user',
            'role'         => $role,
            'emailAddress' => $email,
        ]);

        $drive->permissions->create($fileId, $permission, ['sendNotificationEmail' => false]);
    }

    // Duplicates a document — used so a faculty member gets their own fully
    // editable copy of an official (protected) template
    public function copyDocument(string $fileId, string $newTitle): string
    {
        $drive = new Drive($this->client);

        $copy = $drive->files->copy($fileId, new \Google\Service\Drive\DriveFile(['name' => $newTitle]));

        return $copy->getId();
    }

    public function deleteDocument(string $fileId): void
    {
        $drive = new Drive($this->client);
        $drive->files->delete($fileId);
    }

    public function editUrl(string $fileId): string
    {
        return "https://docs.google.com/document/d/{$fileId}/edit";
    }
}
