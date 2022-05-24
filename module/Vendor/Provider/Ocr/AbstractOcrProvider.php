<?php


namespace Module\Vendor\Provider\Ocr;


abstract class AbstractOcrProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * @param $imageData string
     * @param $format string
     * @param array $param
     * @return array
     */
    abstract public function getText($imageData, $format, $param = []);

}
