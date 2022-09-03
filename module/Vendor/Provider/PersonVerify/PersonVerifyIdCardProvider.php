<?php


namespace Module\Vendor\Provider\PersonVerify;


use Module\Vendor\Provider\ProviderTrait;

class PersonVerifyIdCardProvider
{
    use ProviderTrait;

    /**
     * @return AbstractPersonVerifyIdCardProvider[]
     */
    public static function all()
    {
        return self::listAll();
    }

    /**
     * @param $name
     * @return AbstractPersonVerifyIdCardProvider
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

    public static function firstResponse($name, $idCardNumber, $param = [])
    {
        $provider = self::first();
        if (!$provider) {
            return null;
        }
        return $provider->verify($name, $idCardNumber, $param);
    }
}
