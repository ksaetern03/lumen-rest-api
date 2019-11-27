<?php

namespace App\Repositories\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoriesServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(\App\Repositories\Contracts\UserInterface::class, function (){
            return new \App\Repositories\Eloquents\UserRepository(new \App\Models\User());
        });
    }

    /**
     * Get the services provided by the provider.
     * 
     * @return array
     */
    public function provides()
    {
        return [
            \App\Repositories\Contracts\UserInterface::class,
        ];
    }
}