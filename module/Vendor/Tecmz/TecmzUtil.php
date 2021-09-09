<?php


namespace Module\Vendor\Tecmz;

use ModStart\Core\Input\InputPackage;

class TecmzUtil
{
    public static function url()
    {
        return 'https://api.tecmz.com';
    }

    
    private static function instance($configPrefix)
    {
        $config = modstart_config();
        return Tecmz::instance($config->getWithEnv("${configPrefix}AppId"), $config->getWithEnv("${configPrefix}AppSecret"));
    }

    public static function audioConvert($from, $to, $contentBin)
    {
        $config = modstart_config();
        $appId = $config->getWithEnv('softApiAudioConvertAppId');
        $appSecret = $config->getWithEnv('softApiAudioConvertAppSecret');
        if (empty($appId)) {
            $appId = $config->getWithEnv('softApiDefaultAppId');
            $appSecret = $config->getWithEnv('softApiDefaultAppSecret');
        }
        $softApi = Tecmz::instance($appId, $appSecret);
        $ret = $softApi->audioConvert($from, $to, base64_encode($contentBin));
        if ($ret['code']) {
            return null;
        }
        return base64_decode($ret['data']['content']);
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

    public static function captchaIsEnable()
    {
        return !!modstart_config('softApiCaptchaEnable', false);
    }

    public static function captchaScript()
    {
        return '<script src="https://api.tecmz.com/lib/captcha/base-1.0.0.js?20200410"></script>';
    }

    public static function captchaAppId()
    {
        return modstart_config('softApiCaptchaAppId');
    }

    public static function captchaVerify()
    {
        $input = InputPackage::buildFromInput();
        return self::instance('softApiCaptcha')->captchaVerify(
            $input->getTrimString('action'),
            $input->getTrimString('key'),
            $input->getTrimString('data'),
            $input->getTrimString('runtime'),
            $input->getTrimString('types')
        );
    }

    public static function captchaValidate($captchaKey)
    {
        $ret = self::instance('softApiCaptcha')->captchaValidate($captchaKey);
        if ($ret['code']) {
            return false;
        }
        return true;
    }

}
