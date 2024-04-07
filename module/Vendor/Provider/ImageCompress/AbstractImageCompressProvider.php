<?php


namespace Module\Vendor\Provider\ImageCompress;


abstract class AbstractImageCompressProvider
{
    abstract public function name();

    abstract public function title();

    /**
     * 压缩图片
     * @param $format string 图片格式
     * @param $imageData binary 图片数据二进制
     * @param $param array 额外参数
     * @return array
     * @example
     * [
     *   'code'=>0,
     *   'msg'=>'',
     *   'data'=>[
     *      'data' => ...,
     *      'originalSize' => ...,
     *      'compressSize' => ...,
     *   ]
     * ]
     */
    abstract public function process($format, $imageData, $param = []);
}
