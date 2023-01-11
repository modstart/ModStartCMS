<?php

namespace Module\Article\Util;

use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use Module\Article\Type\ArticlePosition;

class ArticleUtil
{
    const CACHE_KEY_PREFIX = 'article:';

    public static function buildRecord($article)
    {
        if (empty($article)) {
            return $article;
        }
        $article['_url'] = self::url($article);
        return $article;
    }

    public static function url($article)
    {
        if ($article['alias']) {
            return modstart_web_url('article/' . $article['alias']);
        }
        return modstart_web_url('article/' . $article['id']);
    }

    public static function get($id)
    {
        $m = ModelUtil::get('article', $id);
        return self::buildRecord($m);
    }

    public static function getByAlias($alias)
    {
        return self::buildRecord(ModelUtil::get('article', ['alias' => $alias]));
    }

    /**
     * 根据位置获取
     *
     * @param string $position
     * @return mixed
     */
    public static function listByPosition($position = 'home')
    {
        $records = ModelUtil::model('article')->where(['position' => $position])->orderBy('sort', 'asc')->get()->toArray();
        return array_map(function ($item) {
            return self::buildRecord($item);
        }, $records);
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
