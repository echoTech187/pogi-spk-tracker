<?php

namespace App\Providers;

use App\Domain\Contracts\DashboardRepositoryInterface;
use App\Infrastructure\Persistence\PogiUserProvider;
use App\Infrastructure\Persistence\Repositories\EloquentDashboardRepository;
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
        $this->app->bind(
            DashboardRepositoryInterface::class,
            EloquentDashboardRepository::class
        );
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
