<?php

/*
 * This file is part of the overtrue/wechat.
 *
 * (c) overtrue <i@overtrue.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace EasyWeChat\Kernel\Messages;

/**
 * Class Music.
 *
 * @property $url
 * @property $hq_url
 * @property $title
 * @property $description
 * @property $thumb_media_id
 * @property $format
 */
class Music extends Message
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'music';
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['title', 'description', 'url', 'hq_url', 'thumb_media_id', 'format'];
    /**
     * Aliases of attribute.
     *
     * @var array
     */
    protected $jsonAliases = ['musicurl' => 'url', 'hqmusicurl' => 'hq_url'];
    public function toXmlArray()
    {
        $music = ['Music' => ['Title' => $this->get('title'), 'Description' => $this->get('description'), 'MusicUrl' => $this->get('url'), 'HQMusicUrl' => $this->get('hq_url')]];
        if ($thumbMediaId = $this->get('thumb_media_id')) {
            $music['Music']['ThumbMediaId'] = $thumbMediaId;
        }
        return $music;
    }
}