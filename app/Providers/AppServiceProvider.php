<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // HTTPS (Render)
        if (app()->environment('production')) {
            URL::forceScheme('https');
            if (config('app.url')) {
                URL::forceRootUrl(config('app.url'));
            }
        }

        // === Build sırasında DB probunu atla ===
        // Docker build'te php artisan package:discover çağrılır; env yokken DB'ye dokunma.
        if (env('SKIP_DB_PROBE', false)) {
            return;
        }

        // === DB ROTASI: internal -> external (5 dk cache) ===
        $chosen = Cache::remember('db_route_choice', 300, function () {
            try {
                DB::purge('pgsql_internal');
                DB::connection('pgsql_internal')->getPdo();
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
