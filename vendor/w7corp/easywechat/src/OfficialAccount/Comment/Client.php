<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\OfficialAccount\Comment;

use EasyWeChat\Kernel\BaseClient;
/**
 * Class Client.
 *
 * @author overtrue <i@overtrue.me>
 */
class Client extends BaseClient
{
    /**
     * Open article comment.
     *
     * @param string   $msgId
     * @param int|null $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function open($msgId, $index = null)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index];
        return $this->httpPostJson('cgi-bin/comment/open', $params);
    }
    /**
     * Close comment.
     *
     * @param string   $msgId
     * @param int|null $index
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function close($msgId, $index = null)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index];
        return $this->httpPostJson('cgi-bin/comment/close', $params);
    }
    /**
     * Get article comments.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $begin
     * @param int    $count
     * @param int    $type
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list($msgId, $index, $begin, $count, $type = 0)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'begin' => $begin, 'count' => $count, 'type' => $type];
        return $this->httpPostJson('cgi-bin/comment/list', $params);
    }
    /**
     * Mark elect comment.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function markElect($msgId, $index, $commentId)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'user_comment_id' => $commentId];
        return $this->httpPostJson('cgi-bin/comment/markelect', $params);
    }
    /**
     * Unmark elect comment.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function unmarkElect($msgId, $index, $commentId)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'user_comment_id' => $commentId];
        return $this->httpPostJson('cgi-bin/comment/unmarkelect', $params);
    }
    /**
     * Delete comment.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete($msgId, $index, $commentId)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'user_comment_id' => $commentId];
        return $this->httpPostJson('cgi-bin/comment/delete', $params);
    }
    /**
     * Reply to a comment.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $commentId
     * @param $content
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function reply($msgId, $index, $commentId, $content)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'user_comment_id' => $commentId, 'content' => $content];
        return $this->httpPostJson('cgi-bin/comment/reply/add', $params);
    }
    /**
     * Delete a reply.
     *
     * @param $msgId
     * @param int    $index
     * @param int    $commentId
     *
     * @return \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function deleteReply($msgId, $index, $commentId)
    {
        $params = ['msg_data_id' => $msgId, 'index' => $index, 'user_comment_id' => $commentId];
        return $this->httpPostJson('cgi-bin/comment/reply/delete', $params);
    }
}