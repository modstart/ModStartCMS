<?php

namespace ModStart\Core\Util;

use Intervention\Image\ImageManagerStatic as Image;
use ModStart\Core\Exception\BizException;

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
        try {
            $changed = false;
            if (ends_with(strtolower($path), '.webp')) {
                return;
            }
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
     * @param $image
     * @param $type
     * @param $content
     * @param array $option
     * @return mixed|void
     * @throws BizException
     */
    public static function watermark($image, $type, $content, $option = [])
    {
        switch ($type) {
            case 'text':
            case 'image':
                break;
            default:
                BizException::throws('Unknown watermark type');
        }
        if (!isset($option['return'])) {
            $option['return'] = false;
        }
        if (empty($option['position'])) {
            $option['position'] = 'bottom-right';
        }
        BizException::throwsIf('Unknown water position', !in_array($option['position'], [
            'center',
            'bottom-right', 'top-right', 'bottom-left', 'top-left',
            'left', 'right', 'top', 'bottom'
        ]));
        $changed = false;
        $img = Image::make($image);
        $width = $img->width();
        $height = $img->height();
        if ($width < 100 || $height < 100) {
            return;
        }
        $gap = intval(min($width, $height) / 50);
        switch ($type) {
            case 'text':
                if (empty($content)) {
                    return;
                }
                $img->text($content, $width - $gap, $height - $gap,
                    function ($font) use ($width, $height) {
                        $fontSize = max(min($width, $height) / 30, 10);
                        $font->file(base_path('vendor/modstart/modstart/resources/font/SourceHanSansCN-Medium.ttf'));
                        $font->size($fontSize);
                        $font->color('rgba(255, 255, 255, 0.5)');
                        $font->align('right');
                        $font->valign('bottom');
                    });
                $changed = true;
                break;
            case 'image':
                $localWater = FileUtil::savePathToLocalTemp($content);
                if (empty($localWater) || !file_exists($localWater)) {
                    return;
                }
                $watermark = Image::make($localWater);
                $limit = max(min($width, $height) / 10, 10);
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
                $watermark->opacity(50);
                $img->insert($watermark, $option['position'], $gap, $gap);
                $changed = true;
                break;
        }
        if ($option['return']) {
            return $img->response('png');
        }
        if ($changed) {
            $img->save($image);
        }
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

}
