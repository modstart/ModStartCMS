<?php

namespace Module\Ad\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\ArrayUtil;
use ModStart\Module\ModuleManager;
use Module\Ad\Type\AdPosition;

class AdUtil
{
    const CACHE_KEY_PREFIX = 'ad:';

    
    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('ad')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    
    public static function randomByPositionWithCache($position = 'home')
    {
        $ads = self::listByPositionWithCache($position);
        return ArrayUtil::random($ads);
    }

    
    public static function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public static function clearCache()
    {
        foreach (AdPosition::getList() as $k => $_) {
            Cache::forget(self::CACHE_KEY_PREFIX . $k);
        }
    }
}
