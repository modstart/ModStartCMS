<?php

namespace Module\CmsWriter\Type;

use ModStart\Core\Type\BaseType;

class PostStatus implements BaseType
{
    const WAIT_VERIFY = 1;
    const VERIFY_PASS = 2;
    const VERIFY_REJECT = 3;

    public static function getList()
    {
        return [
            self::WAIT_VERIFY => '等待审核',
            self::VERIFY_PASS => '审核成功',
            self::VERIFY_REJECT => '审核拒绝',
        ];
    }

}
