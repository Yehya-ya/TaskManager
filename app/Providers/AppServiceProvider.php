<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->bootModelsDefaults();
        $this->bootRateLimiters();
    }

    private function bootModelsDefaults(): void
    {
        Model::unguard();
        Model::shouldBeStrict(! app()->isProduction());
    }

    private function bootRateLimiters(): void
    {
        RateLimiter::for('login', fn (Request $request): Limit => Limit::perMinute(5)
            ->by($request->string('email')->lower().'|'.$request->ip()));

        RateLimiter::for('register', fn (Request $request): Limit => Limit::perMinute(5)
            ->by($request->ip()));
    }
}
