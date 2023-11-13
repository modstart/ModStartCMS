<?php

namespace Module\Banner\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use Module\Banner\Type\BannerPosition;

class BannerUtil
{
    const CACHE_KEY_PREFIX = 'banner:';

    /**
     * 根据位置获取
     *
     * @param string $position
     * @return mixed
     */
    public static function listByPosition($position = 'home')
    {
        $records = ModelUtil::model('banner')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
        foreach ($records as $i => $v) {
            if ($v['image']) {
                $records[$i]['image'] = AssetsUtil::fixFull($v['image']);
            }
            if ($v['video']) {
                $records[$i]['video'] = AssetsUtil::fixFull($v['video']);
            }
        }
        return $records;
    }

    /**
     * 根据位置获取，有缓存
     *
     * @param string $position
     * @param int $minutes
     * @return mixed
     */
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
