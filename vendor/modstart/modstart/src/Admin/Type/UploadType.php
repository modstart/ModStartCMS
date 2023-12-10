<?php


namespace ModStart\Admin\Type;


use ModStart\Core\Type\BaseType;

class UploadType implements BaseType
{
    const USER = 1;
    const SYSTEM = 2;

    public static function getList()
    {
        return [
            self::USER => '用户文件',
            self::SYSTEM => '系统文件',
        ];
    }
}
