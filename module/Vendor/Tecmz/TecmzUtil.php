<?php


namespace Module\Vendor\Tecmz;

class TecmzUtil
{
    public static function url()
    {
        return 'https://api.tecmz.com';
    }

    /**
     * @param $configPrefix
     * @return Tecmz
     */
    public static function instance($configPrefix)
    {
        $config = modstart_config();
        return Tecmz::instance($config->getWithEnv("${configPrefix}AppId"), $config->getWithEnv("${configPrefix}AppSecret"));
    }

    public static function asr($type, $contentBin)
    {
        $config = modstart_config();
        $appId = $config->getWithEnv('softApiAsrAppId');
        $appSecret = $config->getWithEnv('softApiAsrAppSecret');
        if (empty($appId)) {
            $appId = $config->getWithEnv('softApiDefaultAppId');
            $appSecret = $config->getWithEnv('softApiDefaultAppSecret');
        }
        $softApi = Tecmz::instance($appId, $appSecret);
        $ret = $softApi->asr($type, base64_encode($contentBin));
        if ($ret['code']) {
            return null;
        }
        return $ret['data']['text'];
    }

    public static function express($type, $no)
    {
        $config = modstart_config();
        $appId = $config->getWithEnv('softApiExpressAppId');
        $appSecret = $config->getWithEnv('softApiExpressAppSecret');
        if (empty($appId)) {
            $appId = $config->getWithEnv('softApiDefaultAppId');
            $appSecret = $config->getWithEnv('softApiDefaultAppSecret');
        }
        $softApi = Tecmz::instance($appId, $appSecret);
        $ret = $softApi->express($type, $no);
        if ($ret['code']) {
            return [];
        }
        return $ret['data']['list'];
    }

}
