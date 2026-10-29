<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        RateLimiter::for('login', function (Request $request): Limit {
            $key = Str::transliterate(Str::lower($request->string('email')->toString()).'|'.$request->ip());

            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('client-lookup', function (Request $request): Limit {
            return Limit::perMinute(60)->by((string) ($request->user()?->id ?: $request->ip()));
        });
    }
}
