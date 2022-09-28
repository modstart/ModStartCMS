<?php


namespace Module\Vendor\Provider\ImageCompress;


abstract class AbstractImageCompressProvider
{
    abstract public function name();

    abstract public function title();

    abstract public function process($format, $imageData, $param = []);
}
