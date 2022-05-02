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

    public static function first()
    {
        foreach (self::all() as $provider) {
            return $provider;
        }
        return null;
    }

    public static function firstResponse($ip)
    {
        $provider = self::first();
        if (!$provider) {
            return null;
        }
        return $provider->getLocation($ip);
    }

    public static function firstResponseKey($ip, $keys = ['province'])
    {
        $res = self::firstResponse($ip);
        if (empty($res)) {
            return '';
        }
        $result = [];
        foreach ($keys as $key) {
            $result[] = $res->{$key};
        }
        return join('', $result);
    }
}
