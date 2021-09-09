<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OpenPlatform\Authorizer\OfficialAccount\MiniProgram;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class Client.
 *
 * @author Keal <caiyuezhang@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * 获取公众号关联的小程序.
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list()
    {
        return $this->httpPostJson('cgi-bin/wxopen/wxamplinkget');
    }
    /**
     * 关联小程序.
     *
     * @param $appId
     * @param   $notifyUsers
     * @param   $showProfile
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function link($appId, $notifyUsers = true, $showProfile = false)
    {
        $params = ['appid' => $appId, 'notify_users' => (string) $notifyUsers, 'show_profile' => (string) $showProfile];
        return $this->httpPostJson('cgi-bin/wxopen/wxamplink', $params);
    }
    /**
     * 解除已关联的小程序.
     *
     * @param $appId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unlink($appId)
    {
        $params = ['appid' => $appId];
        return $this->httpPostJson('cgi-bin/wxopen/wxampunlink', $params);
    }
}