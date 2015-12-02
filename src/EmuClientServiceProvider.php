<?php

namespace Imamuseum\EmuClient;

use Illuminate\Support\ServiceProvider;

class EmuClientServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if (config('emu-client.routes_enabled')) {
            include __DIR__.'/Http/routes.php';
        }

        $this->publishes([
            __DIR__.'/../config/emu-client.php' => config_path('emu-client.php'),
        ]);

        $this->commands([
            'Imamuseum\EmuClient\Console\Commands\EmuExportCommand',
        ]);
    }
}
