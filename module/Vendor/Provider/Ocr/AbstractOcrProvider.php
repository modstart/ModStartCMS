<?php


namespace Module\Vendor\Provider\Ocr;


abstract class AbstractOcrProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $imageUrl string 图片地址
     * @param $format string 图片格式
     * @param $param array 额外参数
     * @return array
     */
    abstract public function getText($imageUrl, $format = null, $param = []);

}
