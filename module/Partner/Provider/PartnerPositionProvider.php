<?php


namespace Module\Partner\Provider;


use Module\Vendor\Provider\ProviderTrait;

/**
 * Class PartnerPositionProvider
 * @package Module\Partner\Provider
 * @deprecated delete at 2023-10-01
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
