<?php


namespace Module\Vendor\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Core\Util\AgentUtil;

class DeviceType implements BaseType
{
    const PC = 1;
    const MOBILE = 2;

    public static function getList()
    {
        return [
            self::PC => 'PC',
            self::MOBILE => '移动端',
        ];
    }

    public static function current()
    {
        if (AgentUtil::isMobile()) {
            return self::MOBILE;
        }
        return self::PC;
    }
}
