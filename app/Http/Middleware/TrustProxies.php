<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request;

class TrustProxies extends Middleware
{
    // Render gibi reverse proxy arkasında tüm proxy’lere güven
    protected $proxies = '*';

    // X-Forwarded-* header’larını açıkça bit seviyesinde ayarla (sabit ad kullanmadan)
    protected $headers =
        Request::HEADER_X_FORWARDED_FOR   |
        Request::HEADER_X_FORWARDED_HOST  |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_PORT;
}
