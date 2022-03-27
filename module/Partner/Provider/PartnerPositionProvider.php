<?php


namespace Module\Partner\Provider;


use Module\Vendor\Provider\ProviderTrait;

/**
 * Class PartnerPositionProvider
 * @package Module\Partner\Provider
 * @deprecated
 */
class PartnerPositionProvider
{
    use ProviderTrait;

    /**
     * @return AbstractPartnerPositionProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractPartnerPositionProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
