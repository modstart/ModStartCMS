<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\Work\Media;

use EasyWeChat\Kernel\BaseClient;
use EasyWeChat\Kernel\Http\StreamResponse;
/**
 * Class Client.
 *
 * @author mingyoung <mingyoungcheung@gmail.com>
 */
class Client extends BaseClient
{
    /**
     * Get media.
     *
     * @param $mediaId
     *
     * @return array|\EasyWeChat\Kernel\Http\Response|\EasyWeChat\Kernel\Support\Collection|object|\Psr\Http\Message\ResponseInterface|string
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get($mediaId)
    {
        $response = $this->requestRaw('cgi-bin/media/get', 'GET', ['query' => ['media_id' => $mediaId]]);
        if (false !== stripos($response->getHeaderLine('Content-Type'), 'text/plain')) {
            return $this->castResponseToType($response, $this->app['config']->get('response_type'));
        }
        return StreamResponse::buildFromPsrResponse($response);
    }
    /**
     * Upload Image.
     *
     * @param $path
     *
     * @return mixed
     */
    public function uploadImage($path)
    {
        return $this->upload('image', $path);
    }
    /**
     * Upload Voice.
     *
     * @param $path
     *
     * @return mixed
     */
    public function uploadVoice($path)
    {
        return $this->upload('voice', $path);
    }
    /**
     * Upload Video.
     *
     * @param $path
     *
     * @return mixed
     */
    public function uploadVideo($path)
    {
        return $this->upload('video', $path);
    }
    /**
     * Upload File.
     *
     * @param $path
     *
     * @return mixed
     */
    public function uploadFile($path)
    {
        return $this->upload('file', $path);
    }
    /**
     * Upload media.
     *
     * @param $type
     * @param $path
     *
     * @return mixed
     *
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function upload($type, $path)
    {
        $files = ['media' => $path];
        return $this->httpUpload('cgi-bin/media/upload', $files, [], compact('type'));
    }
}