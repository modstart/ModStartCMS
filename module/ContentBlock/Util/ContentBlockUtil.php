<?php


namespace Module\ContentBlock\Util;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Dao\ModelUtil;
use Module\ContentBlock\Model\ContentBlock;
use Module\Vendor\Util\CacheUtil;

class ContentBlockUtil
{
    const CACHE_KEY_PREFIX = 'ContentBlock:';

    public static function clearCache()
    {
        $records = ContentBlock::all(['id', 'name']);
        foreach ($records as $record) {
            CacheUtil::forget(self::CACHE_KEY_PREFIX . 'Id:' . $record->id);
            CacheUtil::forget(self::CACHE_KEY_PREFIX . 'Name:' . $record->name);
            CacheUtil::forget(self::CACHE_KEY_PREFIX . 'Name:' . $record->name . ':All');
        }
    }

    private static function getBy($where)
    {
        $m = ContentBlock::where($where)
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
        $m = $m->toArray();
        ModelUtil::decodeRecordJson($m, ['basicImages', 'basicTexts']);
        if ($m['image']) {
            $m['image'] = AssetsUtil::fixFull($m['image']);
        }
        if ($m['basicImages']) {
            $m['basicImages'] = AssetsUtil::fixFull($m['basicImages'], false);
        }
        return $m;
    }

    private static function allBy($where, $limit)
    {
        $query = ContentBlock::where($where)
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
        ModelUtil::decodeRecordsJson($ms, ['basicImages', 'basicTexts']);
        foreach ($ms as $mIndex => $m) {
            $ms[$mIndex]['basicImages'] = AssetsUtil::fixFull($m['basicImages'], false);
        }
        return $ms;
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

    public static function all($name, $limit = 0)
    {
        return self::allBy([
            'name' => $name,
            'enable' => true,
        ], $limit);
    }

    public static function getCached($name)
    {
        return CacheUtil::remember(
            self::CACHE_KEY_PREFIX . "Name:" . $name,
            3600,
            function () use ($name) {
                return self::get($name);
            });
    }

    public static function getByIdCached($id)
    {
        return CacheUtil::remember(self::CACHE_KEY_PREFIX . "Id:" . $id,
            3600, function () use ($id) {
                return self::getById($id);
            });
    }

    public static function allCached($name, $limit = 0)
    {
        $records = CacheUtil::remember(self::CACHE_KEY_PREFIX . "Name:" . $name . ':All',
            3600,
            function () use ($name) {
                return self::all($name);
            });
        if ($limit > 0) {
            $records = array_slice($records, 0, $limit);
        }
        return $records;
    }
}
