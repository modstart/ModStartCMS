<?php

namespace Module\Cms\Type;

use ModStart\Core\Type\BaseType;

class CmsModelContentStatus implements BaseType
{
    const SHOW = 1;
    const HIDE = 2;

    public static function getList()
    {
        return [
            self::SHOW => '显示',
            self::HIDE => '隐藏',
        ];
    }

}
