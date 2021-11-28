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
        $this->registerServices();

        $this->registerRepositories();
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
            Contracts\Repositories\PermGroupRepository::class,
            Contracts\Repositories\PermissionRepository::class,
        ];
    }

    private function registerServices()
    {
        $this->app->bind(Contracts\Services\UserService::class, function () {
            return new Services\UserService();
        });
    }

    public function registerRepositories()
    {
        $this->app->bind(Contracts\Repositories\UserRepository::class, function () {
            return new Repositories\UserRepository(new Models\User());
        });

        $this->app->bind(Contracts\Repositories\PermGroupRepository::class, function () {
            return new Repositories\PermGroupRepository(new Models\PermGroup());
        });

        $this->app->bind(Contracts\Repositories\PermissionRepository::class, function () {
            return new Repositories\PermissionRepository(new Models\Permission());
        });
    }
}
