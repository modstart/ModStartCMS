<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OfficialAccount\User;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class UserClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class UserClient extends BaseClient
{
    /**
     * Fetch a user by open id.
     *
     * @param $openid
     * @param $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function get($openid, $lang = 'zh_CN')
    {
        $params = ['openid' => $openid, 'lang' => $lang];
        return $this->httpGet('cgi-bin/user/info', $params);
    }
    /**
     * Batch get users.
     *
     * @param array  $openids
     * @param $lang
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function select(array $openids, $lang = 'zh_CN')
    {
        return $this->httpPostJson('cgi-bin/user/info/batchget', ['user_list' => array_map(function ($openid) use($lang) {
            return ['openid' => $openid, 'lang' => $lang];
        }, $openids)]);
    }
    /**
     * List users.
     *
     * @param $nextOpenId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function lists($nextOpenId = null)
    {
        $params = ['next_openid' => $nextOpenId];
        return $this->httpGet('cgi-bin/user/get', $params);
    }
    /**
     * Set user remark.
     *
     * @param $openid
     * @param $remark
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function remark($openid, $remark)
    {
        $params = ['openid' => $openid, 'remark' => $remark];
        return $this->httpPostJson('cgi-bin/user/info/updateremark', $params);
    }
    /**
     * Get black list.
     *
     * @param string|null $beginOpenid
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function blacklist($beginOpenid = null)
    {
        $params = ['begin_openid' => $beginOpenid];
        return $this->httpPostJson('cgi-bin/tags/members/getblacklist', $params);
    }
    /**
     * Batch block user.
     *
     * @param array|$openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function block($openidList)
    {
        $params = ['openid_list' => (array) $openidList];
        return $this->httpPostJson('cgi-bin/tags/members/batchblacklist', $params);
    }
    /**
     * Batch unblock user.
     *
     * @param array $openidList
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unblock($openidList)
    {
        $params = ['openid_list' => (array) $openidList];
        return $this->httpPostJson('cgi-bin/tags/members/batchunblacklist', $params);
    }
    /**
     * @param $oldAppId
     * @param array  $openidList
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function changeOpenid($oldAppId, array $openidList)
    {
        $params = ['from_appid' => $oldAppId, 'openid_list' => $openidList];
        return $this->httpPostJson('cgi-bin/changeopenid', $params);
    }
}
