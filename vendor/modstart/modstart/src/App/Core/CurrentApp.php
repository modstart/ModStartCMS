<?php


namespace ModStart\App\Core;


use Illuminate\Support\Facades\Session;
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

    public static function set($app)
    {
        Session::flash('_currentApp', $app);
    }

    public static function get()
    {
        return Session::get('_currentApp');
    }

    public static function is($app)
    {
        return self::get() == $app;
    }

    public static function isAdmin()
    {
        return self::is(self::ADMIN);
    }

    public static function isWeb()
    {
        return self::is(self::WEB);
    }

    public static function isOpenApi()
    {
        return self::is(self::OPEN_API);
    }

    public static function isApi()
    {
        return self::is(self::API);
    }

}
