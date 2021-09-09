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
 * Class CoinClient.
 *
 * @author overtrue <i@overtrue.me>
 */
class CoinClient extends BaseClient
{
    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function activate()
    {
        return $this->httpGet('card/pay/activate');
    }
    /**
     * @param $cardId
     * @param int    $quantity
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPrice($cardId, $quantity)
    {
        return $this->httpPostJson('card/pay/getpayprice', ['card_id' => $cardId, 'quantity' => $quantity]);
    }
    /**
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function summary()
    {
        return $this->httpGet('card/pay/getcoinsinfo');
    }
    /**
     * @param int $count
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function recharge($count)
    {
        return $this->httpPostJson('card/pay/recharge', ['coin_count' => $count]);
    }
    /**
     * @param $orderId
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function order($orderId)
    {
        return $this->httpPostJson('card/pay/getorder', ['order_id' => $orderId]);
    }
    /**
     * @param array $filters
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function orders(array $filters)
    {
        return $this->httpPostJson('card/pay/getorderlist', $filters);
    }
    /**
     * @param $cardId
     * @param $orderId
     * @param int    $quantity
     *
     * @return array|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function confirm($cardId, $orderId, $quantity)
    {
        return $this->httpPostJson('card/pay/confirm', ['card_id' => $cardId, 'order_id' => $orderId, 'quantity' => $quantity]);
    }
}