<?php


namespace Module\Vendor\Provider\Ocr;


use Module\Vendor\Provider\ProviderTrait;

class OcrProvider
{
    use ProviderTrait;

    /**
     * @return AbstractOcrProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractOcrProvider
     */
    public static function get($name)
    {
        return self::getByName($name);
    }

    public static function first()
    {
        foreach (self::all() as $provider) {
            return $provider;
        }
        return null;
    }

}
