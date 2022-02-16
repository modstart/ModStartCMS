<?php


namespace ModStart\Core\Monitor;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ModStartMonitorMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);
        if (defined('LARAVEL_START')) {
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $param = json_encode(\Illuminate\Support\Facades\Request::input());
            $url = $request->url();
            $method = $request->method();
            if ($time > 1000) {
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            $queryCountPerRequest = DatabaseMonitor::getQueryCountPerRequest();
            if ($queryCountPerRequest > 10) {
                Log::warning("MASS_REQUEST_SQL $queryCountPerRequest $method [$url] $param -> "
                    . json_encode(DatabaseMonitor::getQueryCountPerRequestSqls()));
            }
        }
        return $response;
    }
}
