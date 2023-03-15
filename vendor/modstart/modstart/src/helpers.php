<?php

use ModStart\Admin\Config\AdminConfig;
use ModStart\Core\Input\Request;
use ModStart\ModStart;
use ModStart\Module\ModuleManager;

/**
 * @Util MSCore版本
 * @desc 获取MSCore版本
 * @return string 版本号
 */
function modstart_version()
{
    return ModStart::$version;
}

/**
 * 管理绝对路径
 * @desc 生成Admin的文件绝对路径
 * @param string $path
 * @return string
 */
function modstart_admin_path($path = '')
{
    return ucfirst(config('modstart.admin.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * @Util Admin路径
 * @desc 生成Admin的路径，自动加前缀
 * @param string $url 路径
 * @param array $param 参数
 * @return string
 * @example
 * // 返回 /admin/aaa/bbb
 * modstart_admin_url('aaa/bbb')
 * // 返回 /admin/aaa/bbb?x=y
 * modstart_admin_url('aaa/bbb',['x'=>'y'])
 */
function modstart_admin_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.admin.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    if ('/' != $prefix) {
        $prefix .= '/';
    }
    return $prefix . $url;
}

/**
 * 生成Web的文件绝对路径
 * @param string $path
 * @return string
 */
function modstart_web_path($path = '')
{
    return ucfirst(config('modstart.web.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * @Util 生成完整的Web路径
 * @param string $url 路径
 * @param array $param 参数
 * @return string
 * @example
 * // 返回 http://www.example.com/aaa/bbb
 * modstart_web_url('aaa/bbb')
 * // 返回 http://www.example.com/aaa/bbb?x=y
 * modstart_web_url('aaa/bbb',['x'=>'y'])
 */
function modstart_web_full_url($url = '', $param = [])
{
    return Request::domainUrl() . modstart_web_url($url, $param);
}

/**
 * @Util Web路径
 * @desc 生成Web的路径，自动加前缀
 * @param string $url 路径
 * @param array $param 参数
 * @return string
 * @example
 * // 返回 /aaa/bbb
 * modstart_web_url('aaa/bbb')
 * // 返回 /aaa/bbb?x=y
 * modstart_web_url('aaa/bbb',['x'=>'y'])
 */
function modstart_web_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.web.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}

/**
 * 生成Api文件绝对路径
 * @param string $path
 * @return string
 */
function modstart_api_path($path = '')
{
    return ucfirst(config('modstart.api.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}


/**
 * @Util Api路径
 * @desc 生成Api的路径，自动加前缀
 * @param string $url 路径
 * @param array $param 参数
 * @return string
 * @example
 * // 返回 /api/aaa/bbb
 * modstart_api_url('aaa/bbb')
 * // 返回 /api/aaa/bbb?x=y
 * modstart_api_url('aaa/bbb',['x'=>'y'])
 */
function modstart_api_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.api.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . '/' . $url;
}

/**
 * 生成OpenApi的文件绝对路径
 * @param string $path
 * @return string
 */
function modstart_open_api_path($path = '')
{
    return ucfirst(config('modstart.openApi.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * OpenApi路径
 * @desc 生成Api的路径，自动加前缀
 * @param string $url 路径
 * @return string
 * @example
 * // 返回 /open_api/aaa/bbb
 * modstart_open_api_url('aaa/bbb')
 * // 返回 /open_api/aaa/bbb?x=y
 * modstart_open_api_url('aaa/bbb',['x'=>'y'])
 */
function modstart_open_api_url($url = '')
{
    $prefix = config('modstart.openApi.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}

function modstart_admin_config($key = null, $default = null)
{
    return AdminConfig::get($key, $default);
}

function modstart_base_path()
{
    return Request::basePath();
}

function modstart_baseurl_active($match, $output = 'active')
{
    $pass = false;
    $url = Request::basePathWithQueries();
    if (is_string($match)) {
        if (!starts_with($match, '/')) {
            $match = modstart_web_url($match);
        }
        if (\ModStart\Core\Util\ReUtil::isWildMatch($match, $url)) {
            $pass = true;
        }
    } else if (is_array($match)) {
        foreach ($match as $item) {
            if (!starts_with($item, '/')) {
                $item = modstart_web_url($item);
            }
            if (\ModStart\Core\Util\ReUtil::isWildMatch($item, $url)) {
                $pass = true;
                break;
            }
        }
    }
    if ($pass) {
        return $output;
    }
    return '';
}

/**
 * 生成安全的路由地址
 * @param $name
 * @return string|null
 */
function modstart_action($name, $parameters = [])
{
    try {
        return action($name, $parameters);
    } catch (Exception $e) {
        return null;
    }
}

/**
 * @Util 获取配置
 * @desc 用于获取表 config 中的配置选项
 * @param $key string 配置名称
 * @param $default any|string 默认值
 * @param $useCache bool 启用缓存，默认为true
 * @return any|string|\ModStart\Core\Config\MConfig 返回配置值或配置对象
 * @example
 * // 网站名称
 * modstart_config('siteName');
 * // 获取一个配置数组，数组需存储成 json 格式
 * modstart_config()->getArray('xxx')
 * // 设置配置项
 * modstart_config()->set('xxx','aaa')
 */
function modstart_config($key = null, $default = '', $useCache = true)
{
    try {
        if (is_null($key)) {
            return app('modstartConfig');
        }
        $v = app('modstartConfig')->get($key, $default, $useCache);
        if (true === $default || false === $default) {
            return boolval($v);
        }
        if (0 === $default) {
            return intval($v);
        }
        if (is_array($default)) {
            $v = @json_decode($v, true);
            if (null === $v) {
                return $default;
            }
        }
        return $v;
    } catch (Exception $e) {
        return $default;
    }
}

/**
 * @Util 模块判断
 * @desc 判断模块是否已安装并启用
 * @param $module string 模块名称，如 Member
 * @param $version string 模块版本要求，如 1.0.0， >=1.0.0
 * @return bool 模块是否安装并启用
 * @example
 * // 模块Member是否安装并启用
 * modstart_module_enabled('Member')
 * // 模块Member是否安装了 >=1.2.0 的版本
 * modstart_module_enabled('Member','>=1.2.0')
 */
function modstart_module_enabled($module, $version = null)
{
    if (null === $version) {
        return ModuleManager::isModuleEnabled($module);
    } else {
        return ModuleManager::isModuleEnableMatch($module, $version);
    }
}

function LM($module, $name, ...$params)
{
    static $sessionLocale = null;
    static $locale = null;
    static $fallbackLocale = null;
    if (null === $locale) {
        $sessionLocale = \Illuminate\Support\Facades\Session::get('_locale', null);
        $locale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');
    }
    static $langs = [];
    if (!isset($langs[$module])) {
        $langs[$module] = [];
        if ($sessionLocale && file_exists($file = ModuleManager::path($module, "Lang/$sessionLocale.php"))) {
            $langs[$module] = (require $file);
        } else if (file_exists($file = ModuleManager::path($module, "Lang/$locale.php"))) {
            $langs[$module] = (require $file);
        } else if (file_exists($file = ModuleManager::path($module, "Lang/$fallbackLocale.php"))) {
            $langs[$module] = (require $file);
        }
    }
    if (isset($langs[$module][$name])) {
        return $langs[$module][$name];
    }
    return L($name, ...$params);
}

/**
 * @Util 多语言
 * @desc 获取多语言翻译
 * @param $name string 多语言
 * @param ...$params string|int 多语言参数
 * @return string 多语言翻译
 * @example
 * // 返回 消息
 * L('Message');
 * // 返回 文件最大为10M
 * L('File Size Limit %s','10M');
 */
function L($name, ...$params)
{
    static $sessionLocale = null;
    static $locale = null;
    static $fallbackLocale;
    static $langTrans = [];
    static $trackMissing = false;
    static $trackMissingData = null;
    if (null === $locale) {
        $sessionLocale = \Illuminate\Support\Facades\Session::get('_locale', null);
        $locale = config('app.locale');
        $fallbackLocale = config('app.fallback_locale');
        $trackMissing = config('modstart.lang.track_missing', false);
        if (ModuleManager::isModuleInstalled('I18n') && \ModStart\Core\Dao\ModelManageUtil::hasTable('lang_trans')) {
            $langTrans = \Module\I18n\Util\LangTransUtil::map();
        }
    }
    if ($trackMissing && null === $trackMissingData) {
        $trackMissingData = [];
        if (file_exists($file = storage_path('cache/lang_missing.php'))) {
            $trackMissingData = (require $file);
        }
        register_shutdown_function(function () use (&$trackMissingData, $file) {
            ksort($trackMissingData);
            file_put_contents($file, '<?php return ' . var_export($trackMissingData, true) . ';');
        });
    }
    if ($sessionLocale && isset($langTrans[$sessionLocale][$name])) {
        if ($trackMissing && isset($trackMissingData[$name])) unset($trackMissingData[$name]);
        if (!empty($params)) {
            return call_user_func_array('sprintf', array_merge([$langTrans[$sessionLocale][$name]], $params));
        }
        return $langTrans[$sessionLocale][$name];
    } else if (isset($langTrans[$locale][$name])) {
        if ($trackMissing && isset($trackMissingData[$name])) unset($trackMissingData[$name]);
        if (!empty($params)) {
            return call_user_func_array('sprintf', array_merge([$langTrans[$locale][$name]], $params));
        }
        return $langTrans[$locale][$name];
    } else if (isset($langTrans[$fallbackLocale][$name])) {
        if ($trackMissing && isset($trackMissingData[$name])) unset($trackMissingData[$name]);
        if (!empty($params)) {
            return call_user_func_array('sprintf', array_merge([$langTrans[$fallbackLocale][$name]], $params));
        }
        return $langTrans[$fallbackLocale][$name];
    }
    $ids = [
        'base.' . $name,
        'modstart::base.' . $name,
    ];
    if (strpos($name, '.') !== false) {
        array_unshift($ids, $name);
    }
    foreach ($ids as $id) {
        $trans = trans($id);
        if ($trans !== $id) {
            if ($trackMissing && isset($trackMissingData[$name])) unset($trackMissingData[$name]);
            if (!empty($params)) {
                return call_user_func_array('sprintf', array_merge([$trans], $params));
            }
            return $trans;
        }
    }
    if ($trackMissing) $trackMissingData[$name] = $name;
    if (!empty($params)) {
        return call_user_func_array('sprintf', array_merge([$name], $params));
    }
    return $name;
}

if (!function_exists('array_build')) {
    function array_build($array, callable $callback)
    {
        $results = [];

        foreach ($array as $key => $value) {
            list($innerKey, $innerValue) = call_user_func($callback, $key, $value);

            $results[$innerKey] = $innerValue;
        }

        return $results;
    }
}

if (!function_exists('starts_with')) {
    function starts_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) === 0) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('array_get')) {
    function array_get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return value($default);
            }

            $array = $array[$segment];
        }

        return $array;
    }
}


if (!function_exists('array_has')) {
    function array_has($array, $key)
    {
        if (empty($array) || is_null($key)) {
            return false;
        }

        if (array_key_exists($key, $array)) {
            return true;
        }

        foreach (explode('.', $key) as $segment) {
            if (!is_array($array) || !array_key_exists($segment, $array)) {
                return false;
            }

            $array = $array[$segment];
        }

        return true;
    }
}

if (!function_exists('array_except')) {
    function array_except($array, $keys)
    {
        array_forget($array, $keys);

        return $array;
    }
}

if (!function_exists('array_forget')) {
    function array_forget(&$array, $keys)
    {
        $original = &$array;

        $keys = (array)$keys;

        if (count($keys) === 0) {
            return;
        }

        foreach ($keys as $key) {
            $parts = explode('.', $key);

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($array[$part]) && is_array($array[$part])) {
                    $array = &$array[$part];
                } else {
                    $parts = [];
                }
            }

            unset($array[array_shift($parts)]);

            // clean up after each pass
            $array = &$original;
        }
    }
}
if (!function_exists('ends_with')) {
    function ends_with($haystack, $needles)
    {
        foreach ((array)$needles as $needle) {
            if ((string)$needle === mb_substr($haystack, -mb_strlen($needle))) {
                return true;
            }
        }
        return false;
    }
}

if (PHP_VERSION_ID >= 80000) {
    require_once __DIR__ . '/Misc/Laravel/Input.php';
}

