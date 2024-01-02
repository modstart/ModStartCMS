<?php


namespace Module\Vendor\Provider\LBS;


abstract class AbstractAddressParseProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $content string 地址原始内容
     * @param $param array 额外参数
     * @return AddressParseResponse
     */
    abstract public function parse($content, $param = []);

}
