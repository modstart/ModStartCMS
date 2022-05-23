<?php


namespace ModStart\Core\Monitor;


use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

class StatisticMonitor
{
    protected static $timeMap = array();
    protected static $client = null;
    private static $inited = false;
    private static $reportAddress = null;

    public static function isEnable()
    {
        if (!defined('LARAVEL_START')) {
            return false;
        }
        if (!self::$inited) {
            self::$reportAddress = config('modstart.statisticServer', null);
            self::$inited = true;
        }
        return !empty(self::$reportAddress);
    }

    public static function init()
    {
        if (!self::isEnable()) return;
        $eventName = 'kernel.handled';
        if (class_exists('Illuminate\\Foundation\\Http\\Events\\RequestHandled')) {
            $eventName = 'Illuminate\\Foundation\\Http\\Events\\RequestHandled';
        }
        Event::listen($eventName, function ($eventOrRequest = null, $response = null) use ($eventName) {
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
            $routeAction = Route::currentRouteAction();
            $domain = \ModStart\Core\Input\Request::domain();
            self::tick($domain, "$method." . $routeAction, $time);
        });
    }

    public static function tickStart($module, $group)
    {
        if (!self::isEnable()) return;
        return self::$timeMap[$module][$group] = microtime(true);
    }

    public static function tickEnd($module, $group, $success = true, $code = 0, $msg = null)
    {
        if (!self::isEnable()) return;
        if (isset(self::$timeMap[$module][$group]) && self::$timeMap[$module][$group] > 0) {
            $timeStart = self::$timeMap[$module][$group];
            self::$timeMap[$module][$group] = 0;
        } else {
            $timeStart = microtime(true);
        }
        self::send($module, $group, round((microtime(true) - $timeStart) * 1000, 2), $success, $code, $msg);
    }

    public static function tick($module, $group, $costMS, $success = true, $code = 0, $msg = null)
    {
        if (!self::isEnable()) return;
        self::send($module, $group, round($costMS, 2), $success, $code, $msg);
    }

    private static function send($module, $group, $cost, $success, $code, $msg)
    {
        try {
            $binData = self::encode($module, $group, $cost, $success, $code, $msg);
            if (extension_loaded('swoole')) {
                if (!self::$client || !self::$client->isConnected()) {
                    self::$client = new \swoole_client(SWOOLE_UDP);
                    list($ip, $port) = explode(':', self::$reportAddress);
                    self::$client->connect($ip, $port);
                }
                self::$client->send($binData);
                self::$client->close();
                self::$client = null;
            } else {
                $timeout = null;
                $socket = stream_socket_client('udp://' . self::$reportAddress, $errno, $errmsg, $timeout);
                if (!$socket) {
                    return;
                }
                stream_set_timeout($socket, $timeout);
                stream_socket_sendto($socket, $binData);
            }
        } catch (\Exception $e) {
            Log::error('StatisticMonitor.send.error -> ' . $e->getMessage());
        }
    }

    private static function encode($module, $group, $cost, $success, $code = 0, $msg = '')
    {
        $data = array(
            'module' => $module,
            'group' => $group,
            'cost' => $cost,
            'success' => $success,
            'time' => time(),
            'code' => $code,
            'msg' => $msg
        );
        $string = json_encode($data);
        $packData = pack('N', strlen($string)) . $string;
        return $packData;
    }
}
