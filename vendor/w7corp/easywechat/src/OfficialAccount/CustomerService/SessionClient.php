<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OfficialAccount\CustomerService;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class SessionClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class SessionClient extends BaseClient
{
    /**
     * List all sessions of $account.
     *
     * @param $account
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function list($account)
    {
        return $this->httpGet('customservice/kfsession/getsessionlist', ['kf_account' => $account]);
    }
    /**
     * List all the people waiting.
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function waiting()
    {
        return $this->httpGet('customservice/kfsession/getwaitcase');
    }
    /**
     * Create a session.
     *
     * @param $account
     * @param $openid
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create($account, $openid)
    {
        $params = ['kf_account' => $account, 'openid' => $openid];
        return $this->httpPostJson('customservice/kfsession/create', $params);
    }
    /**
     * Close a session.
     *
     * @param $account
     * @param $openid
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function close($account, $openid)
    {
        $params = ['kf_account' => $account, 'openid' => $openid];
        return $this->httpPostJson('customservice/kfsession/close', $params);
    }
    /**
     * Get a session.
     *
     * @param $openid
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get($openid)
    {
        return $this->httpGet('customservice/kfsession/getsession', ['openid' => $openid]);
    }
}