<?php

namespace Jdkweb\Rdw\Filament;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class RdwServiceProvider extends ServiceProvider
{
    final public function boot():void
    {
        require __DIR__ ."/Helpers/RdwApiHelpers.php";


        // Demo route on and local
        if ((config('rdw-api.rdw_api_demo') || env('RDW_API_DEMO')) && env('APP_ENV') === 'local') {
            // Filament demo routes
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
    }
}
