<?php

namespace Jdkweb\Rdw\Filament;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class RdwServiceProvider extends ServiceProvider
{
    final public function boot():void
    {
//        // php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="config"
//        $this->publishes([
//            dirname(__DIR__).'/config/rdw-api.php' => config_path('rdw-api.php'),
//        ], 'config');
//
//        // php artisan vendor:publish --provider="Jdkweb\Rdw\RdwServiceProvider" --tag="lang"
//        $this->publishes([
//            dirname(__DIR__).'/lang' =>  lang_path('vendor/rdw-api'),
//        ], 'lang');
//
//        // Load lang
//        $this->loadTranslationsFrom(dirname(__DIR__).'/lang/', 'rdw-api');
//
//        // When not published Load config
//        if(is_null(config('rdw-api.rdw_api_use'))) {
//            $this->mergeConfigFrom(dirname(__DIR__).'/config/rdw-api.php', 'rdw-api');
//        }

        // Demo route on and local
        if(config('rdw-api.rdw_api_demo') && env('APP_ENV') === 'local' ) {
            $this->loadRoutesFrom(dirname(__DIR__).'/routes/demo.php');
            // Manually register components
            Livewire::component('rdw-api-demo', \Jdkweb\Rdw\Filament\Demo\Livewire\RdwApiDemo::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    final public function register():void
    {
//        $this->app->singleton(Rdw::class, function ($app) {
//            return new Rdw();
//        });
//
//        // rdw as alias for "Jdkweb\Rdw\Rdw"
//        $this->app->alias(Rdw::class, 'rdw');
    }
}
