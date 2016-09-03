<?php

namespace App\Http\Middleware;

use App\Infrastructure\Logger;
use App\Infrastructure\Util\metrics\Metrics;
use Closure;
use Illuminate\Support\Facades\Log;

class LogAfterRequest
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }

    public function terminate($request, $response)
    {
        $punched = $request->header('PunchCache');
        if ($punched === '1'){
            Logger::info('Someone Is Punching Cache -> '.$request->fullUrl());
        } else {
            Logger::info('Someone Requested -> '.$request->fullUrl());
        }
    }
}