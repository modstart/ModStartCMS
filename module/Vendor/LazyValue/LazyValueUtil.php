<?php

namespace Module\Vendor\LazyValue;


use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Input\Response;
use Module\Vendor\Util\CacheUtil;

class LazyValueUtil
{
    public static function hash($key, $callback)
    {
        return CacheUtil::rememberForever("LazyValue.$key", function () use ($callback) {
            return md5(serialize($callback()));
        });
    }

    public static function notifyChange($key)
    {
        CacheUtil::forget("LazyValue.$key");
    }

    public static function status(&$value)
    {
        foreach ($value as $v) {
            if (null === $v) {
                return 'running';
            }
        }
        return 'finish';
    }

    public static function generateResponseWithStatus($value)
    {
        return Response::generate(0, 'ok', [
            'status' => LazyValueUtil::status($value),
            'value' => $value,
        ]);
    }

    public static function get($key, $param, $cacheSeconds, $expireLife = 86400)
    {
        $where = ['key' => $key, 'param' => json_encode($param)];
        $exists = ModelUtil::get('lazy_value', $where);
        if (empty($exists)) {
            ModelUtil::insert('lazy_value', [
                'key' => $key,
                'param' => json_encode($param),
                'expire' => time() + $cacheSeconds,
                'lifeExpire' => time() + $expireLife,
                'cacheSeconds' => $cacheSeconds,
                'value' => null,
            ]);
            LazyValueJob::create($key, $param, $cacheSeconds);
            return null;
        }
        if ($exists['expire'] < time()) {
            ModelUtil::update('lazy_value', $where, [
                'expire' => time() + $cacheSeconds,
                'lifeExpire' => time() + $expireLife,
                'cacheSeconds' => $cacheSeconds,
            ]);
            LazyValueJob::createRefresh($key, $param, $cacheSeconds);
        }
        return @json_decode($exists['value'], true);
    }

    // call minutely
    public static function watch()
    {
        ModelUtil::model('lazy_value')->where('lifeExpire', '<', time())->delete();
        $expires = ModelUtil::model('lazy_value')->where('expire', '<', time())->get(['key', 'param', 'cacheSeconds']);
        foreach ($expires as $expire) {
            LazyValueJob::createRefresh($expire->key, @json_decode($expire->param, true), $expire->cacheSeconds);
        }
    }
}
