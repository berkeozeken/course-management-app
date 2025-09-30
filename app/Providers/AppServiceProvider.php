<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
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
        if (env('SKIP_DB_PROBE', false)) {
            return;
        }

        // === DB default bağlantısını .env’den seçtir ===
        // Localde DB_CONNECTION=pgsql, Render’da DB_CONNECTION=pgsql_internal olacak.
        config(['database.default' => env('DB_CONNECTION', 'pgsql')]);

        // Bağlantıyı yenile
        DB::purge(config('database.default'));
    }
}
