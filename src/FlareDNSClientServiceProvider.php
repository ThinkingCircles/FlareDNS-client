<?php

namespace ThinkingCircles\FlareDNSClient;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use ThinkingCircles\FlareDNSClient\Console\Commands\FlareDNSClientSyncCommand;


class FlareDNSClientServiceProvider extends ServiceProvider
{
    protected $packageName = 'flaredns-client';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        // Publish config
        $this->publishes([
            __DIR__.'/config/config.php' => config_path($this->packageName.'.php'),
        ], 'config');

        // Register Command
        $this->commands([
            FlareDNSClientSyncCommand::class
        ]);

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom( __DIR__.'/config/config.php', $this->packageName);


    }
   

}