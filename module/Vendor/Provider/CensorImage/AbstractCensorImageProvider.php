<?php


namespace Module\Vendor\Provider\CensorImage;


abstract class AbstractCensorImageProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function verify($content, $param = []);
}