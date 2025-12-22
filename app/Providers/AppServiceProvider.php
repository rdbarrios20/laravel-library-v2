<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Carbon\CarbonInterval;

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
        Schema::defaultStringLength(191);
        //Passport load key from storage path
        Passport::loadKeysFrom(storage_path());
        //Passport duration token configuration 
        Passport::tokensExpireIn(CarbonInterval::days(2));
        Passport::refreshTokensExpireIn(carbonInterval::days(30));
        Passport::personalAccessTokensExpireIn(CarbonInterval::months(2));
    }
}
