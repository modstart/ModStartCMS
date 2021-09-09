<?php

namespace Module\Member\Type;

use ModStart\Core\Type\BaseType;

class MemberMessageStatus implements BaseType
{
    const UNREAD = 1;
    const READ = 2;

    public static function getList()
    {
        return [
            self::UNREAD => '未读',
            self::READ => '已读',
        ];
    }
}