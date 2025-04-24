<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // In Laravel 12, broadcasting is configured in bootstrap/app.php
        // This provider is kept for compatibility
    }
}
