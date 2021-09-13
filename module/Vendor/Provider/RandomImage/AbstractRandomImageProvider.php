<?php


namespace Module\Vendor\Provider\RandomImage;


abstract class AbstractRandomImageProvider
{
    abstract public function get($param = []);
}
