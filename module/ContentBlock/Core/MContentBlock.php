<?php


use Illuminate\Support\Facades\Cache;
use ModStart\Core\Assets\AssetsUtil;

/**
 * @Util 内容区块
 */
class MContentBlock
{
    const CACHE_KEY_PREFIX = 'ContentBlock:';

    /**
     * @Util 根据name获取内容区块，60分钟缓存
     * @param $name string name
     * @return array|null
     */
    public static function getCached($name)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . "Name:" . $name, 60, function () use ($name) {
            return self::get($name);
        });
    }

    /**
     * @Util 根据id获取内容区块，60分钟缓存
     * @param $id integer ID
     * @return array|null
     */
    public static function getByIdCached($id)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . "Id:" . $id, 60, function () use ($id) {
            return self::getById($id);
        });
    }

    private static function getBy($where)
    {
        $m = \Module\ContentBlock\Model\ContentBlock::where($where)
            ->where(function ($query) {
                $query->whereNull('startTime')
                    ->orWhere('startTime', '<=', date('Y-m-d H:i:s'));
            })
            ->where(function ($query) {
                $query->whereNull('endTime')
                    ->orWhere('endTime', '>=', date('Y-m-d H:i:s'));
            })
            ->orderBy('sort', 'asc')
            ->first();
        if (empty($m)) {
            return null;
        }
        if ($m['image']) {
            $m['image'] = AssetsUtil::fixFull($m['image']);
        }
        return $m->toArray();
    }

    public static function getById($id)
    {
        return self::getBy([
            'id' => $id,
            'enable' => true,
        ]);
    }

    public static function get($name)
    {
        return self::getBy([
            'name' => $name,
            'enable' => true,
        ]);
    }

    /**
     * @Util 根据name获取内容区块列表，60分钟缓存
     * @param $name string 名称
     * @param $limit int 最多返回多少个
     * @return array
     */
    public static function allCached($name, $limit = 0)
    {
        return Cache::remember(self::CACHE_KEY_PREFIX . $name . ':' . $limit, 60, function () use ($name, $limit) {
            return self::all($name, $limit);
        });
    }

    private static function allBy($where, $limit)
    {
        $query = \Module\ContentBlock\Model\ContentBlock::where($where)
            ->where(function ($query) {
                $query->whereNull('startTime')
                    ->orWhere('startTime', '<=', date('Y-m-d H:i:s'));
            })
            ->where(function ($query) {
                $query->whereNull('endTime')
                    ->orWhere('endTime', '>=', date('Y-m-d H:i:s'));
            })
            ->orderBy('sort', 'asc');
        if ($limit > 0) {
            $query = $query->limit($limit);
        }
        $ms = $query->get()->toArray();
        AssetsUtil::recordsFixFullOrDefault($ms, ['image']);
        return $ms;
    }

    public static function all($name, $limit = 0)
    {
        return self::allBy([
            'name' => $name,
            'enable' => true,
        ], $limit);
    }
}
