<?php


namespace Module\Cms\Type;


use ModStart\Core\Type\BaseType;

class CmsContentVerifyStatus implements BaseType
{
    const VERIFYING = 1;
    const VERIFY_PASS = 2;
    const VERIFY_FAIL = 3;

    public static function getList()
    {
        return [
            self::VERIFYING => '审核中',
            self::VERIFY_PASS => '审核通过',
            self::VERIFY_FAIL => '审核拒绝',
        ];
    }
}