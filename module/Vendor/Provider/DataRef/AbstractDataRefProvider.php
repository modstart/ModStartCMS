<?php


namespace Module\Vendor\Provider\DataRef;


abstract class AbstractDataRefProvider
{
    abstract public function name();

    abstract public function title();

    abstract function isUsing($category, $path, $data, $param = []);
}
