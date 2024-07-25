<?php

namespace Module\Vendor\Provider\RandomImage;

use ModStart\Core\Type\BaseType;

class RandomImageType implements BaseType
{
    const BACKGROUND = 'background';
    const COVER = 'cover';

    public static function getList()
    {
        return [
            self::BACKGROUND => '背景',
            self::COVER => '封面',
        ];
    }
}
