<?php

namespace Module\Banner\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use Module\Banner\Type\BannerPosition;

class BannerUtil
{
    const CACHE_KEY_PREFIX = 'banner:';

    
    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('banner')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    
    public static function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public static function insertIfMissing($position, $data)
    {
        $data['position'] = $position;
        if (!ModelUtil::exists('banner', $data)) {
            ModelUtil::insert('banner', $data);
        }
    }

    public static function hasData($position = 'home')
    {
        return !empty(self::listByPositionWithCache($position));
    }

    public static function clearCache()
    {
        foreach (BannerPosition::getList() as $k => $_) {
            Cache::forget(self::CACHE_KEY_PREFIX . $k);
        }
    }
}
