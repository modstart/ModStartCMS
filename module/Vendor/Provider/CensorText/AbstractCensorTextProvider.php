<?php


namespace Module\Vendor\Provider\CensorText;


abstract class AbstractCensorTextProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function verify($content, $param = []);
}