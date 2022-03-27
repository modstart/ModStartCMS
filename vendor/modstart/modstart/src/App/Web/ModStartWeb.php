<?php


namespace ModStart\App\Web;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use ModStart\Module\ModuleManager;

class ModStartWeb
{
    private static function listModuleRoutes()
    {
        $modules = ModuleManager::listAllInstalledModulesInRequiredOrder(true);
        $files = [];
        foreach ($modules as $module) {
            if (file_exists($file = ModuleManager::path($module, 'Web/routes.php'))) {
                $files[$module] = $file;
            }
        }
        return $files;
    }

    public static function registerModuleRoutes()
    {
        if (config('env.APP_DEBUG')) {
            $routesFiles = self::listModuleRoutes();
        } else {
            $routesFiles = Cache::rememberForever('ModStartWebRoutes', function () {
                return self::listModuleRoutes();
            });
        }
        foreach ($routesFiles as $module => $file) {
            Route::group([
                'prefix' => config('modstart.web.prefix'),
                'middleware' => ['web.bootstrap'],
                'namespace' => "\\Module\\$module\\Web\\Controller",
            ], function ($router) use ($file) {
                if(file_exists($file)){
                    require $file;
                }
            });
        }
        if (file_exists($routes = modstart_web_path('routes.php'))) {
            require $routes;
        }
    }
}
