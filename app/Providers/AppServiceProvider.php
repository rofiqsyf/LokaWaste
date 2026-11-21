<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
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
        // If running SQLite, register math functions for Haversine calculations
        if (config('database.default') === 'sqlite') {
            try {
                $pdo = DB::connection()->getPdo();
                if ($pdo instanceof \PDO && method_exists($pdo, 'sqliteCreateFunction')) {
                    $pdo->sqliteCreateFunction('radians', fn($deg) => deg2rad((float) $deg));
                    $pdo->sqliteCreateFunction('acos', fn($val) => acos((float) $val));
                    $pdo->sqliteCreateFunction('cos', fn($val) => cos((float) $val));
                    $pdo->sqliteCreateFunction('sin', fn($val) => sin((float) $val));
                    $pdo->sqliteCreateFunction('least', fn(...$args) => min($args));
                    $pdo->sqliteCreateFunction('greatest', fn(...$args) => max($args));
                }
            } catch (\Throwable $e) {
                // PDO might not be initialized during initial setup or artisan commands
            }
        }
    }
}
