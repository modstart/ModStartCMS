<?php

namespace ModStart;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Exception\BizException;
use ModStart\Form\Form;
use ModStart\Support\Manager\FieldManager;
use ModStart\Support\Manager\WidgetManager;

class ModStart
{
    public static $version = '1.0.0';

    public static $script = [];
    public static $style = [];
    public static $css = [];
    public static $js = [];

    
    public static function clearCache()
    {
        Cache::forget('ModStartServiceProviders');
        Cache::forget('ModStartAdminRoutes');
        Cache::forget('ModStartApiRoutes');
        Cache::forget('ModStartOpenApiRoutes');
        Cache::forget('ModStartWebRoutes');
    }


    public static function scriptFile($scriptFile)
    {
        if (strpos($scriptFile, '/') !== 0) {
            $scriptFile = base_path($scriptFile);
        }
        try {
            return self::script(file_get_contents($scriptFile));
        } catch (\Exception $e) {
            BizException::throws('FileNotFound -> ' . $scriptFile);
        }
    }

    
    public static function script($script = '')
    {
        $script = trim($script);
        if (!empty($script)) {
            self::$script = array_merge(self::$script, (array)$script);
            return;
        }
        return view('modstart::part.script', ['script' => array_unique(self::$script)]);
    }

    public static function styleFile($styleFile)
    {
        if (strpos($styleFile, '/') !== 0) {
            $styleFile = base_path($styleFile);
        }
        try {
            return self::style(file_get_contents($styleFile));
        } catch (\Exception $e) {
            BizException::throws('FileNotFound -> ' . $styleFile);
        }
    }

    
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
}
