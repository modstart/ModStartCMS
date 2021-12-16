<?php

namespace Module\Article\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use Module\Article\Type\ArticlePosition;

class ArticleUtil
{
    const CACHE_KEY_PREFIX = 'article:';

    public static function get($id)
    {
        return ModelUtil::get('article', $id);
    }

    public static function getByAlias($alias)
    {
        return ModelUtil::get('article', ['alias' => $alias]);
    }

    /**
     * 根据位置获取
     *
     * @param string $position
     * @return mixed
     */
    public static function listByPosition($position = 'home')
    {
        return ModelUtil::model('article')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
    }

    /**
     * 根据位置获取，有缓存
     *
     * @param string $position
     * @param int $minutes
     * @return mixed
     */
    public static function listByPositionWithCache($position = 'home', $minutes = 600)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $position, $minutes, function () use ($position) {
            return self::listByPosition($position);
        });
    }

    public static function clearCache()
    {
        foreach (ArticlePosition::getList() as $k => $_) {
            Cache::forget(self::CACHE_KEY_PREFIX . $k);
        }
    }
}