<?php


namespace Module\Banner\Provider;


use Module\Vendor\Provider\ProviderTrait;

/**
 * Class BannerPositionProvider
 * @package Module\Banner\Provider
 * @deprecated
 */
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
