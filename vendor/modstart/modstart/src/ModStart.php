<?php

namespace ModStart;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use ModStart\Core\Exception\BizException;
use ModStart\Module\ModuleManager;
use ModStart\Support\Manager\FieldManager;
use ModStart\Support\Manager\WidgetManager;

/**
 * Class ModStart
 * @package ModStart
 */
class ModStart
{
    public static $version = '3.8.0';

    public static $script = [];
    public static $style = [];
    public static $css = [];
    public static $js = [];

    /**
     * 获取当前项目的缓存Key
     * 多个项目代码共用一个缓存时，需要根据路径来区分不同的项目
     * @param $key string 缓存键
     * @return string
     */
    public static function cacheKey($key)
    {
        static $hash = null;
        if (null === $hash) {
            $hash = md5(__DIR__);
        }
        return join(':', [$key, $hash]);
    }

    /**
     * 清除缓存
     */
    public static function clearCache()
    {
        Cache::forget(self::cacheKey('ModStartServiceProviders'));
        Cache::forget(self::cacheKey('ModStartAdminRoutes'));
        Cache::forget(self::cacheKey('ModStartApiRoutes'));
        Cache::forget(self::cacheKey('ModStartOpenApiRoutes'));
        Cache::forget(self::cacheKey('ModStartWebRoutes'));

        if (method_exists(ModuleManager::class, 'clearCache')) {
            ModuleManager::clearCache();
        }

        /**
         * 如果启用了Laravel优化，这些文件会缓存ServiceProvider
         * 会造成缓存清理不干净甚至服务崩溃的问题
         */
        self::safeCleanOptimizedFile('bootstrap/cache/compiled.php');
        self::safeCleanOptimizedFile('bootstrap/cache/services.json');
        self::safeCleanOptimizedFile('bootstrap/cache/config.php');

        if (method_exists(ModuleManager::class, 'hotReloadSystemConfig')) {
            ModuleManager::hotReloadSystemConfig();
        }
    }

    private static function safeCleanOptimizedFile($file)
    {
        if (file_exists($path = base_path($file))) {
            @unlink($path);
        }
    }


    /**
     * 载入一个JS文件内容
     * @param $scriptFile
     * @param bool $absolute
     * @return Factory|View|void
     * @throws BizException
     */
    public static function scriptFile($scriptFile, $absolute = false)
    {
        if (!$absolute) {
            $scriptFile = base_path($scriptFile);
        }
        try {
            return self::script(file_get_contents($scriptFile));
        } catch (\Exception $e) {
            BizException::throws('FileNotFound -> ' . $scriptFile);
        }
    }

    /**
     * JS 脚本代码，显示在body底部
     * @param string $script
     * @return Factory|View|void
     */
    public static function script($script = '')
    {
        $script = trim($script);
        if (!empty($script)) {
            self::$script = array_merge(self::$script, (array)$script);
            return;
        }
        return view('modstart::part.script', ['script' => array_unique(self::$script)]);
    }

    /**
     * 载入一个CSS样式文件的内容
     * @param $styleFile
     * @param $absolute
     * @return Factory|View|void
     * @throws BizException
     */
    public static function styleFile($styleFile, $absolute = false)
    {
        if (!$absolute) {
            $styleFile = base_path($styleFile);
        }
        try {
            return self::style(file_get_contents($styleFile));
        } catch (\Exception $e) {
            BizException::throws('FileNotFound -> ' . $styleFile);
        }
    }

    /**
     * CSS 样式代码，显示在head头部
     * @param string $style
     * @return Factory|View|void
     */
    public static function style($style = '')
    {
        $style = trim($style);
        if (!empty($style)) {
            self::$style = array_merge(self::$style, (array)$style);
            return;
        }
        static::$style = array_merge(
            static::$style,
            FieldManager::collectFieldAssets('style'),
            WidgetManager::collectWidgetAssets('style')
        );
        return view('modstart::part.style', ['style' => array_unique(self::$style)]);
    }

    /**
     * CSS 样式文件，显示在head头部
     * @param null $css
     * @return Factory|View|void
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            self::$css = array_merge(self::$css, (array)$css);
            return;
        }
        static::$css = array_merge(
            static::$css,
            FieldManager::collectFieldAssets('css')
        );
        return view('modstart::part.css', ['css' => array_unique(static::$css)]);
    }

    /**
     * JS 脚本文件，显示在body底部
     * @param null $js
     * @return Factory|View|void
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            self::$js = array_merge(self::$js, (array)$js);
            return;
        }
        static::$js = array_merge(
            static::$js,
            FieldManager::collectFieldAssets('js')
        );
        return view('modstart::part.js', ['js' => array_unique(static::$js)]);
    }


    /**
     * 获取当前运行环境
     * @return string laravel5|laravel9
     */
    public static function env()
    {
        static $env = null;
        if (null === $env) {
            $pcs = explode('.', \Illuminate\Foundation\Application::VERSION);
            $env = 'laravel' . $pcs[0];
        };
        return $env;
    }
}
