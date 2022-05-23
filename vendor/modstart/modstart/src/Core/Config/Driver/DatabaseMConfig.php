<?php


namespace ModStart\Core\Config\Driver;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Config\MConfig;
use ModStart\Core\Dao\ModelUtil;

class DatabaseMConfig extends MConfig
{
    const CACHE_PREFIX = 'config:';

    public function get($key, $defaultValue = '', $useCache = true)
    {
        $cacheFlag = self::CACHE_PREFIX . $key;
        $value = null;
        if ($useCache) {
            $value = Cache::get($cacheFlag);
            if (null !== $value) {
                if (null === $value || '' === $value) {
                    return $defaultValue;
                }
                return $value;
            }
        }
        if (null === $value) {
            $config = ModelUtil::get('config', ['key' => $key]);
            if ($config) {
                Cache::forever($cacheFlag, $config['value']);
                if (null === $config['value'] || '' === $config['value']) {
                    return $defaultValue;
                }
                return $config['value'];
            } else {
                Cache::forever($cacheFlag, $defaultValue);
            }
        }
        return $defaultValue;
    }

    public function set($key, $value)
    {
        $config = ModelUtil::get('config', ['key' => $key]);
        if ($config) {
            ModelUtil::update('config', ['id' => $config['id']], ['value' => $value]);
        } else {
            ModelUtil::insert('config', ['key' => $key, 'value' => $value]);
        }
        $cacheFlag = self::CACHE_PREFIX . $key;
        Cache::forget($cacheFlag);
    }

    public function remove($key)
    {
        $config = ModelUtil::get('config', ['key' => $key]);
        if ($config) {
            ModelUtil::delete('config', ['id' => $config['id']]);
        }
        $cacheFlag = self::CACHE_PREFIX . $key;
        Cache::forget($cacheFlag);
    }

}
