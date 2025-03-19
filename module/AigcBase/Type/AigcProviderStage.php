<?php

namespace Module\AigcBase\Type;

use ModStart\Core\Type\BaseType;

class AigcProviderStage implements BaseType
{
    const NORMAL = 'normal';
    const QUEUE = 'queue';
    const QUERY = 'query';

    public static function getList()
    {
        return [
            self::NORMAL => '普通',
            self::QUEUE => '队列',
            self::QUERY => '查询',
        ];
    }
}
