<?php


namespace Module\AigcBase\Type;


use ModStart\Core\Type\BaseType;

class AigcKeyPoolStatus implements BaseType
{
    const ONLINE = 1;
    const OFFLINE = 2;
    const OFFLINE_FAIL = 3;

    public static function getList()
    {
        return [
            self::ONLINE => '已上线',
            self::OFFLINE => '未上线',
            self::OFFLINE_FAIL => '已下线',
        ];
    }

    public static function editList()
    {
        return [
            self::ONLINE => '已上线',
            self::OFFLINE => '未上线',
        ];
    }


}
