<?php

namespace ModStart\Admin\Middleware;

use Illuminate\Http\Request;
use ModStart\App\Core\CurrentApp;
use ModStart\Support\Manager\FieldManager;
use ModStart\Support\Manager\WidgetManager;

class BootstrapMiddleware
{
    public function handle(Request $request, \Closure $next)
    {
        if(method_exists(CurrentApp::class,'set')){
            CurrentApp::set(CurrentApp::ADMIN);
        }

        FieldManager::registerBuiltinFields();
        WidgetManager::registerBuiltinWidgets();
        if (file_exists($bootstrap = modstart_admin_path('bootstrap.php'))) {
            require $bootstrap;
        }
        return $next($request);
    }
}
