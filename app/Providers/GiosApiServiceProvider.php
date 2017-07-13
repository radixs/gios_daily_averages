<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Helpers\GiosApiData;

class GiosApiServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('giosApiData', function($app) {
            return new GiosApiData($app->make('GuzzleHttp\Client'));
        });
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['giosApiData'];
    }
}
