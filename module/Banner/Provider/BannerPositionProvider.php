<?php


namespace Module\Banner\Provider;


use Module\Vendor\Provider\ProviderTrait;

class BannerPositionProvider
{
    use ProviderTrait;

    /**
     * @return AbstractBannerPositionProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractBannerPositionProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
