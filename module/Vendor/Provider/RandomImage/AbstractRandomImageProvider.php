<?php


namespace Module\Vendor\Provider\RandomImage;


abstract class AbstractRandomImageProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function get($biz, $type = 'background', $param = []);
}
