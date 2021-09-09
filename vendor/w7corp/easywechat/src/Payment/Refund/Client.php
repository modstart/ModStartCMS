<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\Payment\Refund;

use EasyWeChat\Payment\Kernel\BaseClient;
class Client extends BaseClient
{
    /**
     * Refund by out trade number.
     *
     * @param $number
     * @param $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function byOutTradeNumber($number, $refundNumber, $totalFee, $refundFee, array $optional = [])
    {
        return $this->refund($refundNumber, $totalFee, $refundFee, array_merge($optional, ['out_trade_no' => $number]));
    }
    /**
     * Refund by transaction id.
     *
     * @param $transactionId
     * @param $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function byTransactionId($transactionId, $refundNumber, $totalFee, $refundFee, array $optional = [])
    {
        return $this->refund($refundNumber, $totalFee, $refundFee, array_merge($optional, ['transaction_id' => $transactionId]));
    }
    /**
     * Query refund by transaction id.
     *
     * @param $transactionId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function queryByTransactionId($transactionId)
    {
        return $this->query($transactionId, 'transaction_id');
    }
    /**
     * Query refund by out trade number.
     *
     * @param $outTradeNumber
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function queryByOutTradeNumber($outTradeNumber)
    {
        return $this->query($outTradeNumber, 'out_trade_no');
    }
    /**
     * Query refund by out refund number.
     *
     * @param $outRefundNumber
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function queryByOutRefundNumber($outRefundNumber)
    {
        return $this->query($outRefundNumber, 'out_refund_no');
    }
    /**
     * Query refund by refund id.
     *
     * @param $refundId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     */
    public function queryByRefundId($refundId)
    {
        return $this->query($refundId, 'refund_id');
    }
    /**
     * Refund.
     *
     * @param $refundNumber
     * @param int    $totalFee
     * @param int    $refundFee
     * @param array  $optional
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function refund($refundNumber, $totalFee, $refundFee, $optional = [])
    {
        $params = array_merge(['out_refund_no' => $refundNumber, 'total_fee' => $totalFee, 'refund_fee' => $refundFee, 'appid' => $this->app['config']->app_id], $optional);
        return $this->safeRequest($this->wrap($this->app->inSandbox() ? 'pay/refund' : 'secapi/pay/refund'), $params);
    }
    /**
     * Query refund.
     *
     * @param $number
     * @param $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function query($number, $type)
    {
        $params = ['appid' => $this->app['config']->app_id, $type => $number];
        return $this->request($this->wrap('pay/refundquery'), $params);
    }
}