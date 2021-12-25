<?php


namespace Module\Vendor\Provider\IDManager;


use Illuminate\Support\Facades\Cache;

abstract class AbstractDbCacheIDManager extends AbstractIDManager
{
    abstract public function dbCacheAll();

    private function clearCache()
    {
        Cache::forget($this->name() . '_All');
    }

    public function all()
    {
        return Cache::rememberForever($this->name() . '_All', function () {
            return $this->dbCacheAll();
        });
    }

    public function add($ids)
    {
        $this->clearCache();
    }

    public function remove($ids)
    {
        $this->clearCache();
    }

    public function total()
    {
        return count($this->all());
    }

    private function idsPaginate(&$ids, $page, $pageSize)
    {
        $page = max($page, 1);
        $offset = max($page - 1, 0) * $pageSize;
        if (!isset($ids[$offset])) {
            return [];
        }
        $results = [];
        for ($i = $offset; $i < $offset + $pageSize; $i++) {
            if (!isset($ids[$i])) {
                break;
            }
            $results[] = $ids[$i];
        }
        return $results;
    }

    public function paginate($page, $pageSize)
    {
        $ids = $this->all();
        return $this->idsPaginate($ids, $page, $pageSize);
    }

    public function paginateRandom($page, $pageSize, $cacheKey = 'all', $cacheMinutes = 60)
    {
        $ids = Cache::remember($this->name() . '_Random_' . $cacheKey, $cacheMinutes, function () {
            $ids = $this->all();
            shuffle($ids);
            return $ids;
        });
        return $this->idsPaginate($ids, $page, $pageSize);
    }

    public function forgetRandom($cacheKey = 'all')
    {
        Cache::forget($this->name() . '_Random_' . $cacheKey);
    }


}