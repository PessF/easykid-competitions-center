<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL; 
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;
use Google\Client;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void { }

    public function boot(): void
    {
        
        if (app()->environment('production') || env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        Blade::component('layouts.setup', 'setup-layout');
        Blade::component('layouts.admin', 'admin-layout');
        Blade::component('layouts.user', 'user-layout');

        Storage::extend('google', function ($app, $config) {
            $client = new Client();
            $client->setClientId($config['clientId']);
            $client->setClientSecret($config['clientSecret']);
            $client->refreshToken($config['refreshToken']);

            $service = new \Google\Service\Drive($client);

            $options = [
                'sharedFolderId' => $config['folder']
            ];

            $adapter = new GoogleDriveAdapter($service, '/', $options);
            $driver = new Filesystem($adapter);

            return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
        });
    }
}