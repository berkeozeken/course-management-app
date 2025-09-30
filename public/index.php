<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';

    $kernel = $app->make(Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    )->send();

    $kernel->terminate($request, $response);
} catch (Throwable $e) {
    // Hata hem loga (stderr) hem ekrana düz metin olarak gitsin
    error_log((string) $e);
    http_response_code(500);
    header('Content-Type: text/plain');
    echo "BOOT ERROR: " . $e->getMessage() . "\n";
}
