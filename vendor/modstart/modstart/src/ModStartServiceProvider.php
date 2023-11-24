<?php


namespace ModStart;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ModStart\Admin\ModStartAdmin;
use ModStart\App\Api\ModStartApi;
use ModStart\App\OpenApi\ModStartOpenApi;
use ModStart\App\Web\ModStartWeb;
use ModStart\Core\Facades\ModStart;
use ModStart\Core\Input\Request;
use ModStart\Core\Monitor\DatabaseMonitor;
use ModStart\Core\Monitor\HttpMonitor;
use ModStart\Core\Monitor\StatisticMonitor;
use ModStart\Core\Util\ShellUtil;
use ModStart\Module\ModuleManager;

/**
 * Class ModStartServiceProvider
 * @package ModStart
 */
class ModStartServiceProvider extends ServiceProvider
{
    protected $commands = [
        \ModStart\Command\ModuleInstallCommand::class,
        \ModStart\Command\ModuleUninstallCommand::class,
        \ModStart\Command\ModuleEnableCommand::class,
        \ModStart\Command\ModuleDisableCommand::class,
        \ModStart\Command\ModuleInstallAllCommand::class,
        \ModStart\Command\ModuleRefreshAllCommand::class,
        \ModStart\Command\ModuleLinkAssetCommand::class,
    ];

    protected $routeMiddleware = [
        'admin.bootstrap' => \ModStart\Admin\Middleware\BootstrapMiddleware::class,
        'admin.auth' => \ModStart\Admin\Middleware\AuthMiddleware::class,
        'web.bootstrap' => \ModStart\App\Web\Middleware\BootstrapMiddleware::class,
        'api.bootstrap' => \ModStart\App\Api\Middleware\BootstrapMiddleware::class,
        'api.session' => \ModStart\App\Api\Middleware\SessionMiddleware::class,
        'openApi.bootstrap' => \ModStart\App\OpenApi\Middleware\BootstrapMiddleware::class,
    ];

    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'modstart');
        $this->loadViewsFrom(base_path('module'), 'module');
        $this->loadTranslationsFrom(__DIR__ . '/../lang/', 'modstart');


        $this->publishes([__DIR__ . '/../asset' => public_path('asset')], 'modstart');
        $this->publishes([__DIR__ . '/../resources/lang' => base_path('resources/lang')], 'modstart');

        $this->registerModuleServiceProviders();

        ModStartAdmin::registerAuthRoutes();
        ModStartAdmin::registerModuleRoutes();
        ModStartApi::registerModuleRoutes();
        ModStartOpenApi::registerModuleRoutes();
        ModStartWeb::registerModuleRoutes();
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/modstart.php', 'modstart');
        $this->mergeConfigFrom(__DIR__ . '/../config/env.php', 'env');
        $this->mergeConfigFrom(__DIR__ . '/../config/module.php', 'module');
        $this->mergeConfigFrom(__DIR__ . '/../config/data.php', 'data');

        $subdirUrl = config('modstart.subdirUrl');
        if (empty($subdirUrl)) {
            $subdirUrl = @getenv('SUBDIR_URL');
        }
        if ($subdirUrl) {
            URL::forceRootUrl($subdirUrl);
        }
        $forceScheme = config('modstart.forceSchema');
        if (empty($forceScheme)) {
            $forceScheme = @getenv('FORCE_SCHEMA');
        }
        if ($forceScheme) {
            if (\ModStart\ModStart::env() == 'laravel5') {
                URL::forceSchema($forceScheme);
            } else {
                URL::forceScheme($forceScheme);
            }
        }
        View::share('__msRoot', config('modstart.subdir'));

        $this->app->booting(function () {
            $loader = AliasLoader::getInstance();
            $loader->alias('ModStart', ModStart::class);
        });

        $this->app->singleton('modstartConfig', config('modstart.config.driver'));

        $this->registerRouteMiddleware();

        $this->commands($this->commands);

        $this->registerBladeDirectives();

        $this->registerRoutePattern();

        $this->setupMonitor();


        if (config('modstart.xForwardedHostVisitRedirect', true)) {
            if (!ShellUtil::isCli()) {
                $forwardedHost = Request::headerGet('x-forwarded-host');
                $domain = Request::domain();
                if ($forwardedHost && $domain && $forwardedHost != $domain) {
                    $localIgnores = [
                        'localhost',
                        '127.0.0.1'
                    ];
                    $isLocal = false;
                    foreach ($localIgnores as $li) {
                        if (Str::contains($forwardedHost, $li)) {
                            $isLocal = true;
                            break;
                        }
                    }
                    if (!$isLocal) {
                        $redirect = Request::domainUrl() . Request::basePathWithQueries();
                        Log::info('xForwardedHostVisitRedirect - ' . $forwardedHost . ' to ' . $redirect);
                        header("HTTP/1.1 301 Moved Permanently");
                        header("Location: " . $redirect);
                        exit();
                    }
                }
            }
        }
    }

    private function listModuleServiceProviders()
    {
        $records = [];
        $modules = ModuleManager::listAllEnabledModules();
        foreach ($modules as $module => $_) {
            $provider = "\\Module\\$module\\Core\\ModuleServiceProvider";
            if (class_exists($provider)) {
                $records[] = $provider;
            }
            $basic = ModuleManager::getModuleBasic($module);
            if (empty($basic['providers'])) {
                continue;
            }
            $records = array_merge($records, $basic['providers']);
        }
        foreach (['Core', 'Admin\\Core', 'Web\\Core', 'Api\\Core', 'OpenApi\\Core'] as $app) {
            $provider = "\\App\\$app\\ModuleServiceProvider";
            if (class_exists($provider)) {
                $records [] = $provider;
            }
        }
        return $records;
    }

    public function registerModuleServiceProviders()
    {
        if (config('env.APP_DEBUG')) {
            $providers = $this->listModuleServiceProviders();
        } else {
            $providers = Cache::rememberForever('ModStartServiceProviders', function () {
                return $this->listModuleServiceProviders();
            });
        }
        foreach ($providers as $provider) {
            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    private function registerRoutePattern()
    {
        Route::pattern('id', '[0-9]+');
        Route::pattern('alias', '[a-zA-Z0-9]+');
        Route::pattern('alias_url', '[a-zA-Z0-9_]+');
        Route::pattern('locale', '[a-z]{2}');
    }

    private function setupMonitor()
    {
        DatabaseMonitor::init();
        HttpMonitor::init();
        StatisticMonitor::init();
    }

    private function registerRouteMiddleware()
    {
        $router = app('router');
        foreach ($this->routeMiddleware as $key => $middleware) {
            if (PHP_VERSION_ID >= 80000) {
                $router->aliasMiddleware($key, $middleware);
            } else {
                $router->middleware($key, $middleware);
            }
        }
    }

    private function registerBladeDirectives()
    {
        $this->app->singleton('assetPathDriver', config('modstart.asset.driver'));

        Blade::directive('asset', function ($expression = '') use (&$assetBase) {
            if (empty($expression)) {
                return '';
            }
            if (PHP_VERSION_ID > 80000) {
                $regx = '/(.+)/i';
            } else {
                $regx = '/\\((.+)\\)/i';
            }
            if (preg_match($regx, $expression, $mat)) {
                $file = trim($mat[1], '\'" "');
                $driver = app('assetPathDriver');
                return $driver->getCDN($file) . $driver->getPathWithHash($file);
            } else {
                return '';
            }
        });
    }
}
