<?php


namespace ModStart\App\OpenApi;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

class ModStartOpenApi
{
    private static function listModuleRoutes()
    {
        $modules = ModuleManager::listAllInstalledModulesInRequiredOrder(true);
        $files = [];
        foreach ($modules as $module) {
            if (file_exists($file = ModuleManager::path($module, 'OpenApi/routes.php'))) {
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
            /**
             * @deprecated delete at 2024-06-08
             */
            if (method_exists(ModStart::class, 'cacheKey')) {
                $routesFiles = Cache::rememberForever(ModStart::cacheKey('ModStartOpenApiRoutes'), function () {
                    return self::listModuleRoutes();
                });
            } else {
                $routesFiles = self::listModuleRoutes();
            }
        }
        foreach ($routesFiles as $module => $file) {
            Route::group([
                'prefix' => config('modstart.openApi.prefix'),
                'middleware' => ['openApi.bootstrap'],
                'namespace' => "\\Module\\$module\\OpenApi\\Controller",
            ], function ($router) use ($file) {
                if (file_exists($file)) {
                    require $file;
                }
            });
        }
        if (file_exists($routes = modstart_open_api_path('routes.php'))) {
            require $routes;
        }
    }
}
