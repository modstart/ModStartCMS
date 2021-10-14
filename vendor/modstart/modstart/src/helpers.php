<?php

function modstart_version()
{
    return \ModStart\ModStart::$version;
}

function modstart_admin_path($path = '')
{
    return ucfirst(config('modstart.admin.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

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

function modstart_web_path($path = '')
{
    return ucfirst(config('modstart.web.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function modstart_web_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.web.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}

function modstart_api_path($path = '')
{
    return ucfirst(config('modstart.api.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function modstart_api_url($url = '', $param = [])
{
    if (!empty($param)) {
        $url = $url . '?' . http_build_query($param);
    }
    $prefix = config('modstart.api.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . '/' . $url;
}

function modstart_open_api_path($path = '')
{
    return ucfirst(config('modstart.openApi.directory')) . ($path ? DIRECTORY_SEPARATOR . $path : $path);
}

function modstart_open_api_url($url = '')
{
    $prefix = config('modstart.openApi.prefix');
    $prefix = config('modstart.subdir') . $prefix;
    return $prefix . $url;
}

function modstart_admin_config($key = null, $default = null)
{
    return \ModStart\Admin\Config\AdminConfig::get($key, $default);
}

function modstart_base_apth()
{
    return \ModStart\Core\Input\Request::basePath();
}

function modstart_baseurl_active($match, $output = 'active')
{
    $pass = false;
    $url = \ModStart\Core\Input\Request::basePathWithQueries();
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
 * @param null $key
 * @param string $default
 * @param bool $useCache
 * @return \Illuminate\Foundation\Application|mixed|string|\ModStart\Core\Config\MConfig
 */
function modstart_config($key = null, $default = '', $useCache = true)
{
    try {
        if (is_null($key)) {
            return app('modstartConfig');
        }
        return app('modstartConfig')->get($key, $default, $useCache);
    } catch (Exception $e) {
        return $default;
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
        if ($sessionLocale && file_exists($file = \ModStart\Module\ModuleManager::path($module, "Lang/$sessionLocale.php"))) {
            $langs[$module] = (require $file);
        } else if (file_exists($file = \ModStart\Module\ModuleManager::path($module, "Lang/$locale.php"))) {
            $langs[$module] = (require $file);
        } else if (file_exists($file = \ModStart\Module\ModuleManager::path($module, "Lang/$fallbackLocale.php"))) {
            $langs[$module] = (require $file);
        }
    }
    if (isset($langs[$module][$name])) {
        return $langs[$module][$name];
    }
    return L($name, ...$params);
}

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
        if (\ModStart\Module\ModuleManager::isModuleInstalled('I18n') && \ModStart\Core\Dao\ModelManageUtil::hasTable('lang_trans')) {
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
    foreach (['base.' . $name, 'modstart::base.' . $name] as $id) {
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
