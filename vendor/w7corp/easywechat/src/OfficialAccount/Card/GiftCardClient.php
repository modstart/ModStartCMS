<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OfficialAccount\Card;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class GiftCardClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class GiftCardClient extends BaseClient
{
    /**
     * 申请微信支付礼品卡权限接口.
     *
     * @param $subMchId
     *
     * @return mixed
     */
    public function add($subMchId)
    {
        $params = ['sub_mch_id' => $subMchId];
        return $this->httpPostJson('card/giftcard/pay/whitelist/add', $params);
    }
    /**
     * 绑定商户号到礼品卡小程序接口(商户号必须为公众号申请的商户号，否则报错).
     *
     * @param $subMchId
     * @param $wxaAppid
     *
     * @return mixed
     */
    public function bind($subMchId, $wxaAppid)
    {
        $params = ['sub_mch_id' => $subMchId, 'wxa_appid' => $wxaAppid];
        return $this->httpPostJson('card/giftcard/pay/submch/bind', $params);
    }
    /**
     * 上传小程序代码.
     *
     * @param $wxaAppid
     * @param $pageId
     *
     * @return mixed
     */
    public function set($wxaAppid, $pageId)
    {
        $params = ['wxa_appid' => $wxaAppid, 'page_id' => $pageId];
        return $this->httpPostJson('card/giftcard/wxa/set', $params);
    }
}