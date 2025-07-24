<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register application services if needed.
    }

    public function boot(): void
    {
        // Tier 1: Limit 5 attempts per minute per identity + IP
        RateLimiter::for('login-tier1', function (Request $request) {
            return Limit::perMinute(5)->by($request->input('identity') . '|' . $request->ip());
        });

        // Tier 2: Limit 20 attempts per hour per IP
        RateLimiter::for('login-tier2', function (Request $request) {
            return Limit::perHour(20)->by($request->ip());
        });

        // Tier 3: Limit 50 attempts per day per IP
        RateLimiter::for('login-tier3', function (Request $request) {
            return Limit::perDay(50)->by($request->ip());
        });
    }
}
