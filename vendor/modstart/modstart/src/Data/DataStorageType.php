<?php


namespace ModStart\Data;


use Illuminate\Support\Str;
use ModStart\Core\Type\BaseType;

class DataStorageType implements BaseType
{
    private static $list = [
        'DataFile' => '本地存储',
    ];

    public static function register($name, $title)
    {
        self::$list[$name] = $title;
    }

    public static function getList()
    {
        return self::$list;
    }

    public static function toDriverName($name)
    {
        if (!Str::startsWith($name, 'Data')) {
            $name = 'Data' . $name;
        }
        return 'DataStorage_' . $name;
    }
}
