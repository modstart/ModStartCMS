<?php

namespace Module\Vendor\QuickRun\KeyPool;

use ModStart\Core\Type\BaseType;

class KeyPoolItemStatus implements BaseType
{
    const USING = 1;
    const BAN = 2;

    public static function getList()
    {
        return [
            self::USING => 'USING',
            self::BAN => 'BAN',
        ];
    }
}
