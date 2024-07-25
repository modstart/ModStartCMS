<?php


namespace Module\Vendor\Provider\RandomImage;


use ModStart\Core\Assets\AssetsUtil;
use ModStart\Core\Input\Response;
use ModStart\Core\Util\SerializeUtil;
use Module\Vendor\Provider\ProviderTrait;


/**
 * @method static AbstractRandomImageProvider first()
 * @method static AbstractRandomImageProvider[] listAll()
 */
class RandomImageProvider
{
    use ProviderTrait;

    public static function getImage($provider, $biz, $type = 'background', $param = [])
    {
        $ret = null;
        $error = null;
        if ($provider) {
            $ret = $provider->get($biz, $type, $param);
        } else {
            $error = 'NoProvider';
        }
        if (Response::isSuccess($ret)) {
            return $ret['data'];
        }
        if (isset($ret['msg'])) {
            $error = $ret['msg'];
        } else {
            $error = 'ERROR:' . SerializeUtil::jsonEncode($ret);
        }
        return [
            'url' => AssetsUtil::fixFull('asset/image/none.svg'),
            'error' => $error,
        ];
    }

    public static function getProviderImage($name, $biz, $type = 'background', $param = [])
    {
        $provider = self::getByName($name);
        return self::getImage($provider, $biz, $type, $param);
    }

    public static function getConfigProviderImage($configName, $biz, $type = 'background', $param = [])
    {
        $name = modstart_config($configName, '');
        if ($name) {
            $provider = self::getByName($name);
        } else {
            $provider = self::first();
        }
        return self::getImage($provider, $biz, $type, $param);
    }

    public static function getFirstImage($biz, $type = 'background', $param = [])
    {
        return self::getImage(self::first(), $biz, $type, $param);
    }

}
