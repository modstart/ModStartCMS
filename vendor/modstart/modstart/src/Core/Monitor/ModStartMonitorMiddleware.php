<?php


namespace ModStart\Core\Monitor;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use ModStart\Core\Util\SerializeUtil;

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
                $param = SerializeUtil::jsonEncode($input);
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            $queryCountPerRequest = DatabaseMonitor::getQueryCountPerRequest();
            if ($queryCountPerRequest > 10) {
                $param = SerializeUtil::jsonEncode($input);
                Log::warning("MASS_REQUEST_SQL $queryCountPerRequest $method [$url] $param -> "
                    . SerializeUtil::jsonEncode(DatabaseMonitor::getQueryCountPerRequestSqls()));
            }
        }
        return $response;
    }
}
