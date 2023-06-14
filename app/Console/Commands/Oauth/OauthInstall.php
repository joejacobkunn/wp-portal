<?php

namespace App\Console\Commands\Oauth;

use Illuminate\Console\Command;

class OauthInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:install {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup oauth server';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->generateKeys();
    }

    public function generateKeys(): void
    {
        if (! $this->option('force') &&
            file_exists(storage_path(config('auth.oauth.rsa.private')))
            && file_exists(storage_path(config('auth.oauth.rsa.public')))) {
            $this->comment('OAuth Keys already exist!');

            return;
        }

        $privateKey = openssl_pkey_new();
        openssl_pkey_export($privateKey, $privateKeyString);
        $publicKey = openssl_pkey_get_details($privateKey)['key'];

        file_put_contents(storage_path(config('auth.oauth.rsa.private')), $privateKeyString);
        file_put_contents(storage_path(config('auth.oauth.rsa.public')), $publicKey);

        $this->comment('OAuth Keys generated');
    }
}
