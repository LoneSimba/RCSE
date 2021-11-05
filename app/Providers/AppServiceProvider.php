<?php

namespace App\Providers;

use App\Models;
use App\Services;
use App\Contracts;
use App\Repositories;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class AppServiceProvider extends ServiceProvider implements DeferrableProvider
{
    protected bool $defer = true;

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Contracts\Services\UserService::class, function () {
            return new Services\UserService();
        });

        $this->app->bind(Contracts\Repositories\UserRepository::class, function () {
            return new Repositories\UserRepository(new Models\User());
        });

        $this->app->bind(Contracts\Repositories\PermissionRepository::class, function () {
            return new Repositories\PermissionRepository(new Models\Permission());
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function provides()
    {
        return [
            Contracts\Services\UserService::class,
            Contracts\Repositories\UserRepository::class,
        ];
    }
}
