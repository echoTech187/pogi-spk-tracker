<?php

namespace App\Providers;

use App\Infrastructure\Persistence\PogiUserProvider;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

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
        // Daftarkan Provider User POGI yang tangguh (Zero-Crash Fallback)
        Auth::provider('pogi-user-provider', function ($app, array $config) {
            return new PogiUserProvider($app['hash'], $config['model'] ?? User::class);
        });
    }
}
