<?php


namespace Module\Vendor\Provider\VideoStream;


abstract class AbstractVideoStreamProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function dialogUrl($scope);

    abstract public function getPlayUrl($param = []);
}
