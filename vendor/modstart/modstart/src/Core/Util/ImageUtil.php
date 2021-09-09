<?php

namespace ModStart\Core\Util;

use Intervention\Image\ImageManagerStatic as Image;

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

}
