<?php


namespace Module\Partner\Provider;


use Module\Vendor\Provider\ProviderTrait;

class PartnerPositionProvider
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
