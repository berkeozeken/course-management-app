<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

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
        Vite::prefetch(concurrency: 3);

        // Render'da HTTPS zorunlu
        if (app()->environment('production')) {
            URL::forceScheme('https');

            if (config('app.url')) {
                URL::forceRootUrl(config('app.url'));
            }
        }

        // === DB ROUTE: internal -> external fallback (5 dk cache) ===
        $chosen = Cache::remember('db_route_choice', 300, function () {
            try {
                DB::purge('pgsql_internal');
                DB::connection('pgsql_internal')->getPdo(); // hızlı ping
                return 'pgsql_internal';
            } catch (\Throwable $e) {
                return 'pgsql_external';
            }
        });

        if (config('database.default') !== $chosen) {
            config(['database.default' => $chosen]);
            DB::purge($chosen);
        }
    }
}
