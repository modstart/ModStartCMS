<?php


namespace Module\Vendor\Provider\IDManager;


abstract class AbstractIDManager
{
    abstract public function name();

    abstract public function all();

    abstract public function add($ids);

    abstract public function remove($ids);

    abstract public function total();

    abstract public function paginate($page, $pageSize);

    abstract public function paginateRandom($page, $pageSize, $cacheKey = 'all', $cacheMinutes = 60);

    abstract public function forgetRandom($cacheKey = 'all');

}