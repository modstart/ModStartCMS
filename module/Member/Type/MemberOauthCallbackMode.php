<?php


namespace Module\Member\Type;


use ModStart\Core\Type\BaseType;

class MemberOauthCallbackMode implements BaseType
{
    const View = 'View';
    const AutoBind = 'AutoBind';

    public static function getList()
    {
        return [
            self::View => '查看',
            self::AutoBind => '绑定',
        ];
    }
}
