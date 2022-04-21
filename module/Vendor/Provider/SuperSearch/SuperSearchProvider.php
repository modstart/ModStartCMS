<?php


namespace Module\Vendor\Provider\SuperSearch;


use Module\Vendor\Provider\ProviderTrait;

class SuperSearchProvider
{
    use ProviderTrait;

    /**
     * @return AbstractSuperSearchProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractSuperSearchProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}
