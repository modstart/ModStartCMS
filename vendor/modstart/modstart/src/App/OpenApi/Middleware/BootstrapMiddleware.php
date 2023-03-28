<?php

namespace ModStart\App\OpenApi\Middleware;

use Illuminate\Http\Request;
use ModStart\App\Core\AccessGate;
use ModStart\App\Core\CurrentApp;
use ModStart\Core\Input\Response;
use ModStart\Support\Manager\FieldManager;
use ModStart\Support\Manager\WidgetManager;

class BootstrapMiddleware
{
    /**
     * @var AccessGate[]
     */
    private static $gates = [];

    public static function addGate($cls)
    {
        self::$gates[] = $cls;
    }

    public function handle(Request $request, \Closure $next)
    {
        if (method_exists(CurrentApp::class, 'set')) {
            CurrentApp::set(CurrentApp::OPEN_API);
        }

        foreach (self::$gates as $item) {
            /** @var AccessGate $instance */
            $instance = app($item);
            $ret = $instance->check($request);
            if (Response::isError($ret)) {
                return $ret;
            }
        }

        FieldManager::registerBuiltinFields();
        WidgetManager::registerBuiltinWidgets();
        if (file_exists($bootstrap = modstart_open_api_path('bootstrap.php'))) {
            require $bootstrap;
        }
        return $next($request);
    }
}
