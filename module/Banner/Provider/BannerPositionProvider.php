<?php


namespace Module\Banner\Provider;


use Module\Vendor\Provider\ProviderTrait;

class BannerPositionProvider
{
    use ProviderTrait;

    
    public static function all()
    {
        return self::listAll();
    }

    
    public static function get($name)
    {
        return self::getByName($name);
    }
}
