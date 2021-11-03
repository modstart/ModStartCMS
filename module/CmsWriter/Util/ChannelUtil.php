<?php


namespace Module\CmsWriter\Util;


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Dao\ModelUtil;
use ModStart\Core\Util\TreeUtil;

class ChannelUtil
{
    public static function clearCache()
    {
        Cache::forget('CmsChannels');
        Cache::forget('CmsChannelTree');
    }

    public static function tree()
    {
        return Cache::remember('CmsChannelTree', 3600, function () {
            return TreeUtil::modelToTree('cms_channel', [
                'alias' => 'alias',
                'title' => 'title',
                'cover' => 'cover',
                'description' => 'description',
            ]);
        });
    }

    public static function getByAlias($alias)
    {
        foreach (self::all() as $item) {
            if ($alias == $item['alias']) {
                return $item;
            }
        }
        return null;
    }

    public static function mapById()
    {
        return array_build(self::all(), function ($k, $v) {
            return [$v['id'], $v];
        });
    }

    public static function all()
    {
        return Cache::remember('CmsChannels', 3600, function () {
            return ModelUtil::all('cms_channel');
        });
    }
}
