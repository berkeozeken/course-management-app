<?php
declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__ . '/../vendor/autoload.php';

try {
    // Normal Laravel boot + request handling
    $app = require __DIR__ . '/../bootstrap/app.php';
    /** @var \Illuminate\Contracts\Http\Kernel $kernel */
    $kernel = $app->make(Kernel::class);

    $response = $kernel->handle(
        $request = Request::capture()
    );

    $response->send();
    $kernel->terminate($request, $response);

} catch (Throwable $e) {
    // ---- DEBUG MODU: Supervisor logları bozduğu için ekrana ve DOSYAYA yazdırıyoruz ----
    $msg = "BOOT ERROR: " . $e->getMessage() . "\n\n" . $e->getTraceAsString() . "\n";

    // 1) Dosyaya yaz (public'ten okunabilir)
    @file_put_contents(__DIR__ . '/__boot_error.txt', $msg);

    // 2) Render gövdeyi değiştirmesin diye 200 OK dön ve düz metin yaz
    http_response_code(200);
    header('Content-Type: text/plain; charset=UTF-8');
    echo $msg;

    // 3) Yine de stderr'e bas
    error_log($msg);

    exit(0);
}
