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
 * Class Raw.
 */
class Raw extends Message
{
    /**
     * @var string
     */
    protected $type = 'raw';
    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = ['content'];
    /**
     * Constructor.
     *
     * @param $content
     */
    public function __construct($content)
    {
        parent::__construct(['content' => strval($content)]);
    }
    /**
     * @param array $appends
     * @param  $withType
     *
     * @return array
     */
    public function transformForJsonRequest(array $appends = [], $withType = true)
    {
        return !empty(json_decode($this->content, true)) ? json_decode($this->content, true) : [];
    }
    public function __toString()
    {
        return !empty($this->get('content')) ? $this->get('content') : '';
    }
}