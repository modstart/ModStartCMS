<?php


namespace Module\Cms\Provider\Theme;

use Module\Vendor\Provider\ProviderTrait;

class CmsThemeProvider
{
    use ProviderTrait;

    /**
     * @return AbstractThemeProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractThemeProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }
}