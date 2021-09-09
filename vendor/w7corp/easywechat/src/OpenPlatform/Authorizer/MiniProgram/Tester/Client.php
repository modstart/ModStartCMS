<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OpenPlatform\Authorizer\MiniProgram\Tester;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class Client.
 *
 * @author caikeal <caiyuezhang@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 绑定小程序体验者.
     *
     * @param $wechatId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bind($wechatId)
    {
        return $this->httpPostJson('wxa/bind_tester', ['wechatid' => $wechatId]);
    }
    /**
     * 解绑小程序体验者.
     *
     * @param $wechatId
     * @param $userStr
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unbind($wechatId = null, $userStr = null)
    {
        return $this->httpPostJson('wxa/unbind_tester', [$userStr ? 'userstr' : 'wechatid' => isset($userStr) ? $userStr : $wechatId]);
    }
    public function unbindWithUserStr($userStr)
    {
        return $this->httpPostJson('wxa/unbind_tester', ['userstr' => $userStr]);
    }
    /**
     * 获取体验者列表.
     *
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('wxa/memberauth', ['action' => 'get_experiencer']);
    }
}