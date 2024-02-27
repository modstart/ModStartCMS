<?php


namespace ModStart\Admin\Type;


use ModStart\Core\Type\BaseType;

class UploadType implements BaseType
{
    /**
     * 用户文件，会出现在用户文件列表中
     */
    const USER = 1;
    /**
     * 系统文件，不会出现在用户文件列表中
     */
    const SYSTEM = 2;

    public static function getList()
    {
        return [
            self::USER => '用户文件',
            self::SYSTEM => '系统文件',
        ];
    }
}
