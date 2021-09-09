<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\MicroMerchant\MerchantConfig;

use EasyWeChat\MicroMerchant\Kernel\BaseClient;
/**
 * Class Client.
 *
 * @author   liuml  <liumenglei0211@163.com>
 * @DateTime 2019-05-30  14:19
 */
class Client extends BaseClient
{
    /**
     * Service providers configure recommendation functions for small and micro businesses.
     *
     * @param $subAppId
     * @param $subscribeAppId
     * @param $receiptAppId
     * @param $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function setFollowConfig($subAppId, $subscribeAppId, $receiptAppId = '', $subMchId = '')
    {
        $params = ['sub_appid' => $subAppId, 'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id];
        if (!empty($subscribeAppId)) {
            $params['subscribe_appid'] = $subscribeAppId;
        } else {
            $params['receipt_appid'] = $receiptAppId;
        }
        return $this->safeRequest('secapi/mkt/addrecommendconf', array_merge($params, ['sign_type' => 'HMAC-SHA256', 'nonce_str' => uniqid('micro')]));
    }
    /**
     * Configure the new payment directory.
     *
     * @param $jsapiPath
     * @param $appId
     * @param $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function addPath($jsapiPath, $appId = '', $subMchId = '')
    {
        return $this->addConfig(['appid' => $appId ?: $this->app['config']->appid, 'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id, 'jsapi_path' => $jsapiPath]);
    }
    /**
     * bind appid.
     *
     * @param $subAppId
     * @param $appId
     * @param $subMchId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function bindAppId($subAppId, $appId = '', $subMchId = '')
    {
        return $this->addConfig(['appid' => $appId ?: $this->app['config']->appid, 'sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id, 'sub_appid' => $subAppId]);
    }
    /**
     * add sub dev config.
     *
     * @param array $params
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function addConfig(array $params)
    {
        return $this->safeRequest('secapi/mch/addsubdevconfig', $params);
    }
    /**
     * query Sub Dev Config.
     *
     * @param $subMchId
     * @param $appId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getConfig($subMchId = '', $appId = '')
    {
        return $this->safeRequest('secapi/mch/querysubdevconfig', ['sub_mch_id' => $subMchId ?: $this->app['config']->sub_mch_id, 'appid' => $appId ?: $this->app['config']->appid]);
    }
}