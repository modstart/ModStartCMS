<?php

namespace ModStart\App\OpenApi\Middleware;

use Illuminate\Http\Request;
use ModStart\Support\Manager\FieldManager;
use ModStart\Support\Manager\WidgetManager;

class BootstrapMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        FieldManager::registerBuiltinFields();
        WidgetManager::registerBuiltinWidgets();
        if (file_exists($bootstrap = modstart_open_api_path('bootstrap.php'))) {
            require $bootstrap;
        }
        return $next($request);
    }
}
