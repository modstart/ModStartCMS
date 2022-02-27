<?php


namespace Module\Vendor\Provider\LBS;


use Module\Vendor\Provider\ProviderTrait;

class IpProvider
{
    use ProviderTrait;

    /**
     * @return AbstractIpProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractIpProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
