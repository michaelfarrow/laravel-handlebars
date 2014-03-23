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

        $app->extend('view.engine.resolver', function ($resolver, $app) {
            $resolver->register('handlebars', function () use ($app) {
                return $app->make('Twombolr\Handlebars\HandlebarsEngine');
            });

            return $resolver;
        });

        $app->extend('view', function ($env, $app) {
            $env->addExtension('handlebars', 'handlebars');

            return $env;
        });
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
