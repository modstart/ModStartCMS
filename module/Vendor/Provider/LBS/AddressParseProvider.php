<?php


namespace Module\Vendor\Provider\LBS;


use Module\Vendor\Provider\ProviderTrait;

/**
 * @method static AbstractAddressParseProvider[] listAll()
 */
class AddressParseProvider
{
    use ProviderTrait;

    /**
     * @param $content string 待解析的地址
     * @return AddressParseResponse|null
     */
    public static function firstResponse($content)
    {
        $provider = self::first();
        if (!$provider) {
            return null;
        }
        return $provider->parse($content);
    }
}
