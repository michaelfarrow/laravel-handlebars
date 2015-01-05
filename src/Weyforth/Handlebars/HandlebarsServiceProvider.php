<?php
/**
 * Handlebars Service Provider.
 *
 * @author    Mike Farrow <contact@mikefarrow.co.uk>
 * @license   Proprietary/Closed Source
 * @copyright Mike Farrow
 */

namespace Weyforth\Handlebars;

use Illuminate\Support\ServiceProvider;
use Config;
use View;

class HandlebarsServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = false;


    /**
     * Register the service provider.
     *
     * Extend view class to provide our custom loader.
     *
     * @return void
     */
    public function register()
    {
        $app = $this->app;

        $app->instance('handlebars', new Handlebars);

        $app->extend('view.engine.resolver', function ($resolver, $app) {
            $resolver->register('handlebars', function () use ($app) {
                return $app->make('Weyforth\Handlebars\HandlebarsEngine');
            });

            return $resolver;
        });

        $app->extend('view', function ($env, $app) {
            $env->addExtension(Config::get('view.handlebars.extension', 'handlebars'), 'handlebars');

            return $env;
        });

        View::addNamespace('Handlebars', Config::get('view.handlebars.location'));
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('handlebars');
    }


}
