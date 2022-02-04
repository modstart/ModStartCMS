<?php


namespace Module\Vendor\Provider\LiveStream;


abstract class AbstractLiveStreamProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function getPushUrl($appName, $streamName, $param = []);

    abstract public function getPlayUrl($appName, $streamName, $param = []);
}