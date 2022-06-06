<?php

namespace ModStart\Core\Util;

use ModStart\Core\Exception\BizException;
use ModStart\ModStart;

/**
 * Class QrcodeUtil
 * @package ModStart\Core\Util
 * @Util 二维码
 */
class QrcodeUtil
{
    /**
     * @Util 生成二维码
     * @desc 生成PNG格式的二维码图片
     * @param $content string 二维码内容
     * @param $size int 大小，默认200
     * @return string 图片二进制串
     */
    public static function png($content, $size = 200)
    {
        if (class_exists(\BaconQrCode\Renderer\Image\Png::class)) {
            $renderer = new \BaconQrCode\Renderer\Image\Png();
            $renderer->setMargin(0);
            $renderer->setHeight($size);
            $renderer->setWidth($size);
        } else {
            BizException::throwsIf('Please Install imagick extension', !extension_loaded('imagick'));
            $renderer = new \BaconQrCode\Renderer\ImageRenderer(
                new \BaconQrCode\Renderer\RendererStyle\RendererStyle(400),
                new \BaconQrCode\Renderer\Image\ImagickImageBackEnd()
            );
        }
        $writer = new \BaconQrCode\Writer($renderer);
        return $writer->writeString($content);
    }

    /**
     * @Util 生成二维码
     * @desc 生成二维码Base64串
     * @param $content string 二维码内容
     * @param $size int 大小，默认200
     * @return string 二维码Base64字符串
     * @example
     * // 返回 data:image/png;base64,xxxxxxxx
     * QrcodeUtil::pngBase64String('http://www.xxx.com')
     */
    public static function pngBase64String($content, $size = 200)
    {
        return 'data:image/png;base64,' . base64_encode(self::png($content, $size));
    }
}
