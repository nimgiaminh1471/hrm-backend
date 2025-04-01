<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use App\Auth\CentralUserGuard;
use App\Models\User;
use App\Observers\UserObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Auth::extend('central', function ($app, $name, array $config) {
            return new CentralUserGuard(
                Auth::createUserProvider($config['provider']),
                $app->make('request')
            );
        });
        User::observe(UserObserver::class);
    }
}
