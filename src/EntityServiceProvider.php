<?php

namespace BurningCloudSystem\Entity;

use BurningCloudSystem\Entity\Console\MakeEntityCommand;
use Illuminate\Support\ServiceProvider;

class EntityServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Read config file.
        if ($this->app->runningInConsole())
        {
            $this->publishes([
                __DIR__.'/Console/stubs' => $this->app->basePath('stubs')
            ], 'stubs');
        }


    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->runningInConsole()) 
        {
            $this->registerRepository();
            $this->registerMakeEntityCommand();

            // $this->mergeConfigFrom(
            //     __DIR__.'/../config/entity.php', 'entity');    
        }
    }

    /**
     * Register the migration repository service.
     *
     * @return void
     */
    protected function registerRepository()
    {
        $this->app->singleton('entity.repository', function($app){
            return new DatabaseEntityRepository($app['db']);
        });
    }

    /**
     * Register the command.
     *
     * @return void
     */
    protected function registerMakeEntityCommand()
    {
        $command = 'command.make.entity';
        $this->app->singleton($command, function($app){
            return new MakeEntityCommand($app['entity.repository'], $app['files']);
        });
        $this->commands($command);
    }

}
