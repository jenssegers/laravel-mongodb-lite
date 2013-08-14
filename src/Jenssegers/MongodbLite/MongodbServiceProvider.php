<?php namespace Jenssegers\MongodbLite;

use Jenssegers\MongodbLite\Model;
use Jenssegers\MongodbLite\DatabaseManager;
use Illuminate\Support\ServiceProvider;

class MongodbServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        Model::setConnectionResolver($this->app['mongodb']);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // The database manager is used to resolve various connections, since multiple
        // connections might be managed. It also implements the connection resolver
        // interface which may be used by other components requiring connections.
        $this->app['mongodblite'] = $this->app->share(function($app)
        {
            return new DatabaseManager($app);
        });
    }

}