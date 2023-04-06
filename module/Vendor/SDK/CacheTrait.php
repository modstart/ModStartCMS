<?php

namespace Module\Vendor\SDK;


use Module\Vendor\Util\CacheUtil;

trait CacheTrait
{
    protected function cacheRememberForever($key, $callback)
    {
        return CacheUtil::rememberForever('ThirdParty:' . $key, $callback);
    }

    protected function cacheRemember($key, $seconds, $callback)
    {
        return CacheUtil::remember('ThirdParty:' . $key, $seconds, $callback);
    }

    protected function cacheForget($key)
    {
        return CacheUtil::forget('ThirdParty:' . $key);
    }

    protected function cacheGet($key)
    {
        return CacheUtil::get('ThirdParty:' . $key);
    }

    protected function cachePut($key, $value, $seconds)
    {
        CacheUtil::put('ThirdParty:' . $key, $value, $seconds);
    }
}
