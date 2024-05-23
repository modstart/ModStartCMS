<?php

namespace ModStart\Core\Util;

use Intervention\Image\ImageManagerStatic as Image;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Provider\FontProvider;

/**
 * @Util 图片工具类
 */
class ImageUtil
{
    public static function base64Src($imageContent, $type = 'png')
    {
        if (!in_array($type, ['png', 'gif', 'jpg', 'jpeg'])) {
            return null;
        }
        return 'data:' . FileUtil::mime($type) . ';base64,' . base64_encode($imageContent);
    }

    public static function limitSizeAndDetectOrientation($path, $maxWidth = 1000, $maxHeight = 1000)
    {
        $extensionPermit = [
            'jpg', 'jpeg', 'png', 'gif',
        ];
        $ext = FileUtil::extension($path);
        if (!in_array($ext, $extensionPermit)) {
            return;
        }
        try {
            $changed = false;
            $exif = @exif_read_data($path);
            $image = Image::make($path);
            if (!empty($exif['Orientation'])) {
                switch (intval($exif['Orientation'])) {
                    case 2:
                        $image->flip();
                        $changed = true;
                        break;
                    case 3:
                        $image->rotate(180);
                        $changed = true;
                        break;
                    case 4:
                        $image->rotate(180);
                        $image->flip();
                        $changed = true;
                        break;
                    case 5:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 6:
                        $image->rotate(-90);
                        $changed = true;
                        break;
                    case 7:
                        $image->rotate(90);
                        $image->flip();
                        $changed = true;
                        break;
                    case 8:
                        $image->rotate(90);
                        $changed = true;
                        break;
                }
            }

            $width = $image->width();
            $height = $image->height();
            if ($width > $maxWidth || $height > $maxHeight) {
                $changed = true;
                if ($width > $maxWidth) {
                    $image->resize($maxWidth, intval($maxWidth * $height / $width));
                }
                if ($height > $maxHeight) {
                    $image->resize(intval($maxHeight * $width / $height), $maxHeight);
                }
            }

            if ($changed) {
                $image->save($path);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * 为图片加水印
     * 单位尺寸：min( 宽度, 高度 ) / 100
     * @param $image string 图片路径，绝对路径
     * @param $type string 水印类型 image|text
     * @param $content string 水印内容 image 为图片路径，text 为文字内容
     * @param $option array 水印配置
     * @return array
     * @example
     * 文字单行 $option = [ 'mode' => 'single', ],
     * 文字多行 $option = [ 'mode' => 'repeat', ],
     * 图片单行 $option = [ 'mode' => 'single', 'imageSize' => 20, ],
     * 图片多行 $option = [ 'mode' => 'repeat', 'imageSize' => 20, 'gapX' => 50, 'gapY' => 30, ],
     */
    public static function watermark($image, $type, $content, $option = [])
    {
        $option = array_merge([
            'return' => false,                                  // 是否返回图片内容
            'mode' => 'single',                                 // 重复模式 单个 single 重复 repeat
            'rotate' => 'horizontal',                           // 旋转角度 水平 horizontal 倾斜 oblique
            'gapX' => 50,                                       // 重复模式下，水平间隔
            'gapY' => 30,                                       // 重复模式下，垂直间隔
            'minSizePx' => 100,                                 // 最小加水印尺寸（单位像素），宽和高必须都大于此值

            'textColor' => '#FFFFFF',                           // 文字颜色
            'textOpacity' => 40,                                // 文字透明度
            'textSize' => 5,                                    // 文字大小
            'textFont' => FontProvider::firstLocalPathOrFail(), // 文字字体

            'imageSize' => 20,                                  // 图片大小
            'imageOpacity' => 40,                               // 图片透明度
        ], $option);
        try {
            BizException::throwsIf('Image not exists', !file_exists($image));
            BizException::throwsIf('watermark type error', !in_array($type, ['image', 'text']));
            BizException::throwsIf('watermark content empty', empty($content));
            BizException::throwsIf('watermark text color error', !preg_match('/^#[0-9a-fA-F]{6}$/', $option['textColor']));
        } catch (BizException $e) {
            return Response::generateError($e->getMessage());
        }

        $changed = false;

        $data = [
            'processed' => false,
            'success' => false,
            'message' => '',
            'content' => null,
        ];

        $img = Image::make($image);
        $width = $img->width();
        $height = $img->height();
        if ($width < $option['minSizePx'] || $height < $option['minSizePx']) {
            $img->destroy();
            $data['message'] = '图片尺寸过小，不加水印';
            return Response::generateSuccessData($data);
        }

        $info = self::calcWatermarkPositionInfo($width, $height, $option);
        $normalPx = $info['normalPx'];
        $points = $info['points'];
        $option = $info['option'];

        switch ($type) {
            case 'text':
                $option['_textFont'] = FileUtil::savePathToLocalTemp($option['textFont']);
                $textColor = $option['textColor'] . sprintf('%02x', intval($option['textOpacity'] * 255 / 100));
                $option['_textColor'] = ColorUtil::hexToRgba($textColor);
                $option['_textSize'] = intval($option['textSize'] * $normalPx);
                foreach ($points as $point) {
                    $img->text($content, $point['x'], $point['y'],
                        function ($font) use ($option) {
                            $font->file($option['_textFont']);
                            $font->size($option['_textSize']);
                            $font->color($option['_textColor']);
                            $font->align('center');
                            $font->valign('center');
                            if ('oblique' == $option['rotate']) {
                                $font->angle(45);
                            }
                        });
                }
                $changed = true;
                break;
            case 'image':
                $localWater = FileUtil::savePathToLocalTemp($content);
                BizException::throwsIf('watermark image not exists', !file_exists($localWater));
                $watermark = Image::make($localWater);
                $limit = intval($option['imageSize'] * $normalPx);
                $waterWidth = $watermark->width();
                $waterHeight = $watermark->height();
                if ($waterWidth > $waterHeight) {
                    $waterHeight = intval($limit * $waterHeight / $waterWidth);
                    $waterWidth = $limit;
                } else {
                    $waterWidth = intval($limit * $waterWidth / $waterHeight);
                    $waterHeight = $limit;
                }
                $watermark->resize($waterWidth, $waterHeight);
                $watermark->opacity($option['imageOpacity']);
                if ('oblique' == $option['rotate']) {
                    $watermark->rotate(45);
                }
                foreach ($points as $point) {
                    $img->insert($watermark, 'top-left',
                        intval($point['x'] - $waterWidth / 2),
                        intval($point['y'] - $waterHeight / 2)
                    );
                }
                $changed = true;
                break;
        }
        $data['processed'] = true;
        if ($option['return']) {
            $data['content'] = $img->response('png');
            $img->destroy();
            $data['success'] = true;
            return Response::generateSuccessData($data);
        }
        if ($changed) {
            $data['success'] = true;
            $img->save($image);
        }
        $img->destroy();
        return Response::generateSuccessData($data);
    }

    public static function info($file)
    {
        $img = Image::make($file);
        return [
            'width' => $img->width(),
            'height' => $img->height(),
            'size' => $img->filesize(),
        ];
    }

    /**
     * @param $width integer 图片宽度
     * @param $height integer 图片高度
     * @param $option array 水印参数
     * @return array
     */
    public static function calcWatermarkPositionInfo($width, $height, array $option)
    {
        $normalPx = min($width, $height) / 100;

        $points = [];
        switch ($option['mode']) {
            case 'single':
                $points[] = [
                    'x' => intval($width / 2),
                    'y' => intval($height / 2)
                ];
                break;
            case 'repeat':
                $option['_gapX'] = intval($option['gapX'] * $normalPx);
                $option['_gapY'] = intval($option['gapY'] * $normalPx);
                $xs = [];
                for ($d = 0, $start = intval($width / 2); $start - $d > 0 && $start + $d < $width; $d += $option['_gapX']) {
                    $xs[] = $start + $d;
                    if ($d > 0) {
                        $xs[] = $start - $d;
                    }
                }
                for ($d = 0, $start = intval($height / 2); $start - $d > 0 && $start + $d < $height; $d += $option['_gapY']) {
                    foreach ($xs as $x) {
                        $points[] = ['x' => $x, 'y' => $start + $d];
                        if ($d > 0) {
                            $points[] = ['x' => $x, 'y' => $start - $d];
                        }
                    }
                }
                break;
        }
        return [
            'normalPx' => $normalPx,
            'points' => $points,
            'option' => $option,
        ];
    }

}
