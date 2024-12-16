<?php

namespace Module\Vendor\SDK;

use ModStart\Core\Exception\BizException;

class WechatMiniProgramSdk extends HttpRequest
{
    use CacheTrait;

    private $appId;
    private $appSecret;

    public static function create($appId, $appSecret)
    {
        static $pool = [];
        if (isset($pool[$appId])) {
            return $pool[$appId];
        }
        $key = $appId;
        $ins = new static();
        $ins->appId = $appId;
        $ins->appSecret = $appSecret;
        $ins->setBaseUrl('https://api.weixin.qq.com');
        $pool[$key] = $ins;
        return $ins;
    }

    public function request($url, $param = [])
    {
        $url = $url . '?' . http_build_query([
                'access_token' => $this->getAccessToken()
            ]);
        $option = [
            'header' => [
                'Content-Type' => 'application/json',
            ]
        ];
        return $this->postJSON($url, json_encode($param), $option);
    }

    public function getAccessToken()
    {
        $cacheKey = "WechatMiniProgramSdk:AccessToken:{$this->appId}";
        $token = $this->cacheGet($cacheKey);
        if (empty($token)) {
            $ret = $this->getJSON('/cgi-bin/token', [
                'grant_type' => 'client_credential',
                'appid' => $this->appId,
                'secret' => $this->appSecret,
            ]);
            BizException::throwsIfResponseError($ret);
            BizException::throwsIf('获取access_token失败', empty($ret['data']['access_token']));
            $token = $ret['data']['access_token'];
            $this->cachePut($cacheKey, $token, $ret['data']['expires_in'] - 60);
        }
        return $token;
    }


}
