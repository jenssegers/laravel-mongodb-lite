<?php namespace Jenssegers\Mongodb\Lite;

use Jenssegers\Mongodb\Lite\Model;
use Jenssegers\Mongodb\Lite\Connection;
use Illuminate\Support\ServiceProvider;

class MongodbServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['db']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Add a mongodb extension to the original database manager
        $this->app['db']->extend('mongodb', function($config)
        {
            return new Connection($config);
        });
    }

}