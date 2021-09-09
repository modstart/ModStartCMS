<?php

namespace Module\Partner\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use Module\Partner\Type\PartnerPosition;

class PartnerUtil
{
    const CACHE_KEY_PREFIX = 'partner:';

    
    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('partner')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    
    public static function listByPositionWithCache($position = 'home', $minutes = 60)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public static function clearCache()
    {
        foreach (PartnerPosition::getList() as $k => $_) {
            Cache::forget(self::CACHE_KEY_PREFIX . $k);
        }
    }
}