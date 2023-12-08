<?php

namespace ModStart\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class ModStart
 * @method static string cacheKey($key)
 */
class ModStart extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \ModStart\ModStart::class;
    }
}
