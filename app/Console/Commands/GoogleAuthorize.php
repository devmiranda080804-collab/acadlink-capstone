<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Docs;
use Google\Service\Drive;
use Illuminate\Console\Command;

// One-time setup command: authorizes ONE dedicated Google account (e.g. a
// Gmail created just for this system) so the app can create and share
// Google Docs on its behalf. Run this once, then paste the resulting
// refresh token into .env — no user ever needs to log into Google themselves.
class GoogleAuthorize extends Command
{
    protected $signature = 'google:authorize';

    protected $description = 'One-time authorization of the Google account used to create/share collaboration documents';

    public function handle()
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');

        if (! $clientId || ! $clientSecret) {
            $this->error('Set GOOGLE_CLIENT_ID and GOOGLE_CLIENT_SECRET in .env first (from your Google Cloud Console OAuth client), then run this again.');
            return self::FAILURE;
        }

        $client = new Client();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri('http://localhost');
        $client->setAccessType('offline');
        $client->setPrompt('consent'); // force a refresh_token even on repeat runs
        $client->addScope(Docs::DOCUMENTS);
        $client->addScope(Drive::DRIVE_FILE);

        $authUrl = $client->createAuthUrl();

        $this->info('1. Open this URL in your browser and log in with the DEDICATED Google account (not your personal one, unless that is the one you want to use):');
        $this->line('');
        $this->line($authUrl);
        $this->line('');
        $this->info('2. After you click "Allow", the browser will redirect to a broken localhost page — that is expected.');
        $this->info('3. Copy the value of the "code" parameter from that page\'s URL bar (everything after code= and before the next &).');
        $this->line('');

        $code = $this->ask('Paste the code here');

        if (! $code) {
            $this->error('No code entered. Run the command again.');
            return self::FAILURE;
        }

        $token = $client->fetchAccessTokenWithAuthCode(trim($code));

        if (isset($token['error'])) {
            $this->error('Google rejected the code: ' . ($token['error_description'] ?? $token['error']));
            return self::FAILURE;
        }

        if (empty($token['refresh_token'])) {
            $this->error('Google did not return a refresh token. This usually means the account already authorized this app before without "consent" prompt. Revoke access at https://myaccount.google.com/permissions and run this command again.');
            return self::FAILURE;
        }

        $this->line('');
        $this->info('Success! Add this line to your .env file:');
        $this->line('');
        $this->line('GOOGLE_REFRESH_TOKEN=' . $token['refresh_token']);
        $this->line('');
        $this->info('Then run: php artisan config:clear');

        return self::SUCCESS;
    }
}
