<?php

namespace ModStart\Data;

use Illuminate\Support\Str;
use ModStart\Core\Provider\ProviderTrait;

/**
 * @method static AbstractDataStorageProvider[] listAll()
 */
class DataStorageProvider
{
    use ProviderTrait;

    static $list = [];

    public static function defaultEnabledDriver()
    {
        foreach (self::listAll() as $driver) {
            if ($driver->enable()) {
                $name = $driver->name();
                if (!Str::startsWith($name, 'Data')) {
                    $name = 'Data' . $name;
                }
                return 'DataStorage_' . $name;
            }
        }
        return null;
    }

    public static function uploadScript($param = [])
    {
        foreach (self::listAll() as $driver) {
            if ($driver->enable()) {
                return $driver->uploadScript($param);
            }
        }
        return '';
    }
}
