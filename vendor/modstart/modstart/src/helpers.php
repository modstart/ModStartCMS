<?php

use Illuminate\Support\Facades\View;
use ModStart\Admin\Config\AdminConfig;
use ModStart\Core\Input\Request;
use ModStart\Core\Util\SerializeUtil;
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
 * 判断是否为Tab
 * @return boolean
 */
function modstart_admin_is_tab()
{
    return boolval(View::shared('_isTab'));
}

/**
 * 生成Web的文件绝对路径
 * @param string $path
 * @return string 路径
 */
function modstart_web_path($path = '')
{
    return ucfirst(config('modstart.web.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

/**
 * @Util 生成完整的Web路径
 * @param string $url 路径
 * @param array $param 参数
 * @return string 地址
 * @example
 * // 返回 http://www.example.com/aaa/bbb
 * modstart_web_full_url('aaa/bbb')
 * // 返回 http://www.example.com/aaa/bbb?x=y
 * modstart_web_full_url('aaa/bbb',['x'=>'y'])
 */
function modstart_web_full_url($url = '', $param = [])
{
    $domainUrl = Request::domainUrl();
    if ('http://localhost' == $domainUrl) {
        $domainUrl = rtrim(modstart_config('siteUrl', 'http://localhost'), '/');
    }
    return $domainUrl . modstart_web_url($url, $param);
}

/**
 * @Util Web路径
 * @desc 生成Web的路径，自动加前缀
 * @param string $url 路径
 * @param array $param 参数
 * @return string 地址
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
 * 获取模块系统配置
 * @param $module string 模块名称
 * @param $key string 配置名称
 * @param $default string|array|boolean|integer 默认值
 */
function modstart_module_config($module, $key, $default = null)
{
    return ModuleManager::getModuleConfig($module, $key, $default);
}

/**
 * @Util 获取多个配置中第一个非空值
 * @param $keys array 多个配置名
 * @param $default string 默认值
 * @return array|bool|int|mixed|\ModStart\Core\Config\MConfig|string
 */
function modstart_configs($keys, $default = '')
{
    if (is_string($keys)) {
        $keys = explode(',', $keys);
    }
    foreach ($keys as $key) {
        $v = modstart_config($key);
        if ($v) {
            return $v;
        }
    }
    return $default;
}

/**
 * @Util 获取配置
 * @desc 用于获取表 config 中的配置选项
 * @param $key string 配置名称
 * @param $default string|array|boolean|integer 默认值，不能为 null
 * @param $useCache bool 启用缓存，默认为true
 * @return string|array|boolean|integer|\ModStart\Core\Config\MConfig 返回配置值或配置对象
 * @example
 * // 网站名称
 * modstart_config('siteName','[默认名称]');
 * // 获取一个配置数组，数组需存储成 json 格式
 * modstart_config()->getArray('xxx')
 * // 设置配置项
 * modstart_config()->set('xxx','aaa')
 */
function modstart_config($key = null, $default = '', $useCache = true)
{
    static $lastKey = null;
    static $lastValue = null;
    try {
        if ($key && $key === $lastKey) {
            return $lastValue;
        }
        if (is_null($key)) {
            $lastKey = null;
            $lastValue = null;
            return app('modstartConfig');
        }
        $lastKey = $key;
        $configDefault = $default;
        if (is_array($default)) {
            $configDefault = SerializeUtil::jsonEncode($default);
        }
        $v = app('modstartConfig')->get($key, $configDefault, $useCache);
        if (true === $default || false === $default) {
            $lastValue = boolval($v);
            return $lastValue;
        }
        if (is_int($default)) {
            $lastValue = intval($v);
            return $lastValue;
        }
        if (is_array($default)) {
            $v = @json_decode($v, true);
            if (null === $v) {
                $lastValue = $default;
                return $default;
            }
            $lastValue = $v;
            return $v;
        }
        $lastValue = $v;
        return $v;
    } catch (Exception $e) {
        $lastValue = $default;
        return $default;
    }
}

/**
 * @Util 获取配置资源路径
 * @param $key string 配置名称
 * @param $default string 默认值
 * @return string
 */
function modstart_config_asset_url($key, $default = '')
{
    $value = modstart_config($key, $default);
    return \ModStart\Core\Assets\AssetsUtil::fixFull($value);
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

function L_locale_title($locale = null)
{
    if (null === $locale) {
        $locale = L_locale();
    }
    $langs = config('modstart.i18n.langs', []);
    return isset($langs[$locale]) ? $langs[$locale] : $locale;
}

function L_locale($locale = null)
{
    static $useLocales = [
        'Admin' => null,
        'Web' => null,
    ];

    $app = \ModStart\App\Core\CurrentApp::WEB;
    $localeList = config('modstart.i18n.langs', []);
    if (\ModStart\App\Core\CurrentApp::is(\ModStart\App\Core\CurrentApp::ADMIN)) {
        $app = \ModStart\App\Core\CurrentApp::ADMIN;
        $localeList = config('modstart.admin.i18n.langs', []);
    }

    if (!array_key_exists($app, $useLocales)) {
        return 'zh';
    }

    $forceLocale = null;
    if (null !== $locale) {
        if (!isset($localeList[$locale])) {
            $forceLocale = $locale;
        }
    }

    if (null !== $forceLocale || null === $useLocales[$app]) {

        // forceLocale > routeLocale > sessionLocale > i18nLocale > locale > fallbackLocale

        $routeLocale = null;
        if ($app == \ModStart\App\Core\CurrentApp::WEB) {
            $routeLocale = \Illuminate\Support\Facades\Request::route('locale');
        }

        $sessionLocaleKey = '_locale';
        if ($app == \ModStart\App\Core\CurrentApp::ADMIN) {
            $sessionLocaleKey = '_adminLocale';
        }
        $sessionLocale = \Illuminate\Support\Facades\Session::get($sessionLocaleKey, null);

        $i18nLocale = null;
        if (!\ModStart\App\Core\CurrentApp::is(\ModStart\App\Core\CurrentApp::ADMIN)
            && ModuleManager::isModuleInstalled('I18n')) {
            $i18nLocale = \Module\I18n\Util\LangUtil::getDefault('shortName');
        }

        $locale = config('app.locale');

        $fallbackLocale = config('app.fallback_locale');

        //if (!empty($_GET['_DEBUG']) && !$routeLocale) {
        //    \Illuminate\Support\Facades\Log::info('$forceLocale - ' . json_encode([
        //            $app,
        //            $routeLocale,
        //            $sessionLocale,
        //            $locale,
        //            $fallbackLocale,
        //            debug_backtrace(),
        //        ]));
        //}

        $currentLocale = $forceLocale;
        if (empty($currentLocale)) {
            $currentLocale = $routeLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $sessionLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $i18nLocale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $locale;
        }
        if (empty($currentLocale)) {
            $currentLocale = $fallbackLocale;
        }
        \Illuminate\Support\Facades\Session::put($sessionLocaleKey, $currentLocale);
        $useLocales[$app] = $currentLocale;
    }
    return $useLocales[$app];
}

function L_format($name, $params)
{
    if (empty($params)) {
        return $name;
    }
    return sprintf($name, ...$params);
}

/**
 * @Util 多语言（模块）
 * @param $module string 模块名称
 * @param $name string 多语言
 * @param ...$params string|int 多语言参数
 * @return string 多语言翻译
 * @example
 * // 获取模块Member的多语言
 * LM('Member','Message')
 * // 获取模块Member的多语言，带参数
 * LM('Member','File Size Limit %s','10M')
 */
function LM($module, $name, ...$params)
{
    $useLocale = L_locale();
    if (empty($useLocale)) {
        return $name;
    }
    static $langs = [];
    if (!isset($langs[$module])) {
        $langs[$module] = [];
        $langFile = ModuleManager::path($module, "Lang/$useLocale.php");
        if (file_exists($langFile)) {
            $langs[$module] = (require $langFile);
        }
    }
    if (isset($langs[$module][$name])) {
        return L_format($langs[$module][$name], $params);
    }
    static $trackMissing = null;
    if (null === $trackMissing) {
        $trackMissing = config('modstart.trackMissingLang', false);
    }
    if ($trackMissing) {
        $langs[$module][$name] = $name;
        ksort($langs[$module]);
        $langFile = ModuleManager::path($module, "Lang/$useLocale.php");
        \ModStart\Core\Util\FileUtil::write($langFile, \ModStart\Core\Util\CodeUtil::phpVarExportReturnFile($langs[$module]));
    }
    return L_format($name, $params);
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
    $useLocale = L_locale();
    if (empty($useLocale)) {
        return $name;
    }
    static $lang = null;
    if (null === $lang) {
        $lang = [];
        $langFile = base_path("vendor/modstart/modstart/lang/" . $useLocale . "/base.php");
        if (file_exists($langFile)) {
            $lang = (require $langFile);
        }
        $langFile = base_path('resources/lang/' . $useLocale . '/base.php');
        if (file_exists($langFile)) {
            $langLocal = (require $langFile);
            $lang = array_merge($lang, $langLocal);
        }
        $validationLang = base_path("vendor/modstart/modstart/lang/" . $useLocale . "/validation.php");
        if (file_exists($validationLang)) {
            $validationLang = (require $validationLang);
            foreach ($validationLang as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $kk => $vv) {
                        $lang['validation.' . $k . '.' . $kk] = $vv;
                    }
                } else {
                    $lang['validation.' . $k] = $v;
                }
            }
        }
        if (ModuleManager::isModuleInstalled('I18n')) {
            $langTrans = \Module\I18n\Util\LangTransUtil::map();
            if (isset($langTrans[$useLocale])) {
                $lang = array_merge($lang, $langTrans[$useLocale]);
            }
        }
    }
    if (isset($lang[$name])) {
        return L_format($lang[$name], $params);
    }
    static $trackMissing = null;
    if (null === $trackMissing) {
        $trackMissing = config('modstart.trackMissingLang', false);
    }
    if ($trackMissing) {
        $lang[$name] = $name;
        $langFile = base_path('resources/lang/' . $useLocale . '/base.php');
        if (file_exists($langFile)) {
            $langFileData = (require $langFile);
        } else {
            $langFileData = [];
        }
        $langFileData[$name] = $name;
        ksort($langFileData);
        file_put_contents($langFile, \ModStart\Core\Util\CodeUtil::phpVarExportReturnFile($langFileData));
    }
    return L_format($name, $params);
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

