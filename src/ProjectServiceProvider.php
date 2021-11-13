<?php

namespace Retroace\WhereIsMyProjectClient;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;

class ProjectServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/project.php',
            'project'
        );
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
        $router = $this->app['router'];
        $router->pushMiddlewareToGroup('web', Retroace\WhereIsMyProjectClient\Middleware\AuthorizeProject::class);    

    }
}