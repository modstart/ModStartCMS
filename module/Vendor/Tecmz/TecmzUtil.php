<?php


namespace Module\Vendor\Tecmz;

use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;

class TecmzUtil
{
    public static function url($module = null)
    {
        return 'https://api.tecmz.com' . ($module ? '/m/' . $module : '');
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


    public static function callCloudModelSync($configPrefix, $type, $modelConfig = [], $option = [])
    {
        $api = TecmzUtil::instance($configPrefix);
        $option = array_merge([
            'timeout' => 60,
        ], $option);
        $timeout = 60;
        $queueRet = $api->callCloudModelQueue($type, $modelConfig, $option);
        BizException::throwsIfResponseError($queueRet);
        $endTime = time() + $timeout;
        sleep(3);
        while (time() < $endTime) {
            sleep(3);
            $retQuery = $api->callCloudModelQuery($type, $queueRet['data']['taskId']);
            BizException::throwsIfResponseError($retQuery);
            if (in_array($retQuery['data']['status'], ['QUEUE', 'PROCESS'])) {
                continue;
            }
            if (!in_array($retQuery['data']['status'], ['SUCCESS'])) {
                BizException::throws($retQuery['data']['statusRemark']);
            }
            return Response::generateSuccessData($retQuery['data']['result']);
        }
        BizException::throws('请求超时');
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
