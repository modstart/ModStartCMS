<?php


namespace ModStart\Core\Monitor;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ModStart\Core\Util\ArrayUtil;

class HttpMonitor
{
    public static function init()
    {
        if (!config('modstart.trackPerformance', false)) {
            return;
        }
        $eventName = 'kernel.handled';
        if (class_exists('Illuminate\\Foundation\\Http\\Events\\RequestHandled')) {
            $eventName = 'Illuminate\\Foundation\\Http\\Events\\RequestHandled';
        }
        Event::listen($eventName, function ($eventOrRequest = null, $response = null) use ($eventName) {
            if (!defined('LARAVEL_START')) {
                return;
            }
            /** @var Request $request */
            /** @var Response $request */
            $request = $eventOrRequest;
            if ($eventName == 'kernel.handled') {
            } else {
                /** @var \Illuminate\Foundation\Http\Events\RequestHandled $eventOrRequest */
                $request = $eventOrRequest->request;
                $response = $eventOrRequest->response;
            }
            $time = round((microtime(true) - LARAVEL_START) * 1000, 2);
            $url = $request->url();
            $method = $request->method();
            $queryCountPerRequest = DatabaseMonitor::getQueryCountPerRequest();
            $param = [];
            if ($time > 1000 || $queryCountPerRequest > 10) {
                $param = ArrayUtil::serializeForLog($request->input());
            }
            if ($time > 1000) {
                Log::warning("LONG_REQUEST $method [$url] ${time}ms $param");
            }
            if ($queryCountPerRequest > 10) {
                Log::warning("MASS_REQUEST_SQL $method [$url] $queryCountPerRequest $param -> "
                    . json_encode(DatabaseMonitor::getQueryCountPerRequestSqls(), JSON_UNESCAPED_UNICODE));
            }
        });
    }

}
