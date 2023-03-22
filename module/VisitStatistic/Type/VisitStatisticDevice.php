<?php


namespace Module\VisitStatistic\Type;


use ModStart\Core\Type\BaseType;
use ModStart\Core\Util\AgentUtil;

class VisitStatisticDevice implements BaseType
{
    const DESKTOP = 1;
    const MOBILE = 2;

    public static function getList()
    {
        return [
            self::DESKTOP => 'PC',
            self::MOBILE => '手机',
        ];
    }

    public static function current()
    {
        switch (AgentUtil::device()) {
            case 'pc':
                return self::DESKTOP;
            case 'mobile':
                return self::MOBILE;
        }
        return 0;
    }
}
