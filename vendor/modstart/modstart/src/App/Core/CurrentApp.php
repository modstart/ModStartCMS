<?php


namespace ModStart\App\Core;


use ModStart\Core\Type\BaseType;

class CurrentApp implements BaseType
{
    const ADMIN = 'Admin';
    const WEB = 'Web';
    const OPEN_API = 'OpenApi';
    const API = 'Api';

    public static function getList()
    {
        return [
            self::ADMIN => 'Admin',
            self::WEB => 'Web',
            self::OPEN_API => 'OpenApi',
            self::API => 'Api',
        ];
    }

}
