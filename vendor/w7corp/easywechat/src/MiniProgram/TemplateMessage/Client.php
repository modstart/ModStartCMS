<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\MiniProgram\TemplateMessage;

use EasyWeChat\OfficialAccount\TemplateMessage\Client as BaseClient;
/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    const API_SEND = 'cgi-bin/message/wxopen/template/send';
    /**
     * {@inheritdoc}.
     */
    protected $message = ['touser' => '', 'template_id' => '', 'page' => '', 'form_id' => '', 'data' => [], 'emphasis_keyword' => ''];
    /**
     * {@inheritdoc}.
     */
    protected $required = ['touser', 'template_id', 'form_id'];
    /**
     * @param int $offset
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list($offset, $count)
    {
        return $this->httpPostJson('cgi-bin/wxopen/template/library/list', compact('offset', 'count'));
    }
    /**
     * @param $id
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($id)
    {
        return $this->httpPostJson('cgi-bin/wxopen/template/library/get', compact('id'));
    }
    /**
     * @param $id
     * @param array  $keyword
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function add($id, array $keyword)
    {
        return $this->httpPostJson('cgi-bin/wxopen/template/add', ['id' => $id, 'keyword_id_list' => $keyword]);
    }
    /**
     * @param $templateId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($templateId)
    {
        return $this->httpPostJson('cgi-bin/wxopen/template/del', ['template_id' => $templateId]);
    }
    /**
     * @param int $offset
     * @param int $count
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTemplates($offset, $count)
    {
        return $this->httpPostJson('cgi-bin/wxopen/template/list', compact('offset', 'count'));
    }
}