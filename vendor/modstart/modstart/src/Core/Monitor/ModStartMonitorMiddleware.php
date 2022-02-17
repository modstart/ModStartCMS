<?php


namespace ModStart\Core\Monitor;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class ModStartMonitorMiddleware
 * @package ModStart\Core\Monitor
 * @deprecated
 * @delete after 20220417
 */
class ModStartMonitorMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        $response = $next($request);
        if (defined('LARAVEL_START')) {
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $input = $request->input();
            $url = $request->url();
            $method = $request->method();
            if ($time > 1000) {
                $param = json_encode($input, JSON_UNESCAPED_UNICODE);
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            $queryCountPerRequest = DatabaseMonitor::getQueryCountPerRequest();
            if ($queryCountPerRequest > 10) {
                $param = json_encode($input, JSON_UNESCAPED_UNICODE);
                Log::warning("MASS_REQUEST_SQL $queryCountPerRequest $method [$url] $param -> "
                    . json_encode(DatabaseMonitor::getQueryCountPerRequestSqls()));
            }
        }
        return $response;
    }
}
