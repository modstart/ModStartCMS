<?php


namespace ModStart\Module;


use ModStart\Core\Type\BaseType;

class ModuleStatus implements BaseType
{
    const INSTALLED = 'installed';
    const NOT_INSTALLED = 'notInstalled';

    public static function getList()
    {
        return [
            self::INSTALLED => L('Installed'),
            self::NOT_INSTALLED => L('Not Installed'),
        ];
    }


}