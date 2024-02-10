<?php


namespace Module\Vendor\QuickRun\ImageDesign;


use Intervention\Image\Facades\Image;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Provider\FontProvider;
use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\QrcodeUtil;
use ModStart\Core\Util\SerializeUtil;

class ImageDesignUtil
{
    const LINE_BREAK = '[BR]';

    public static function renderBase64DataString($imageConfig, $variables = [])
    {
        $image = self::render($imageConfig, $variables);
        return 'data:image/png;base64,' . base64_encode($image);
    }

    public static function renderResponse($imageConfig, $variables = [])
    {
        $image = self::render($imageConfig, $variables);
        return Response::raw($image, [
            'Content-Type' => 'image/png'
        ]);
    }

    private static function rectRadius($fillColor, $width, $height, $radius)
    {
        if (!class_exists('\ImagickDraw') || !class_exists('\Imagick')) {
            return null;
        }
        $draw = new \ImagickDraw();
        // $draw->setStrokeColor('#FF0000');
        $draw->setFillColor($fillColor);
        $draw->setStrokeWidth(0);
        $draw->roundRectangle(0, 0, $width - 1, $height, $radius, $radius);

        $imagick = new \Imagick();
        $imagick->newImage($width, $height, 'transparent');
        $imagick->setImageFormat('png');
        $imagick->drawImage($draw);
        $out = $imagick->getImageBlob();
        $imagick->clear();
        $imagick->destroy();
        return $out;
    }

    public static function textLineCount($text)
    {
        $pcs = explode(self::LINE_BREAK, $text);
        return count($pcs);
    }

    public static function render($imageConfig, $variables = [])
    {
        BizException::throwsIfEmpty('imageConfig 为空', $imageConfig);
        $configParam = [];
        foreach ($variables as $k => $v) {
            $configParam['${' . $k . '}'] = $v;
        }
        $imageConfig = SerializeUtil::jsonEncode($imageConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $imageConfig = str_replace(array_keys($configParam), array_values($configParam), $imageConfig);
        $imageConfig = json_decode($imageConfig, true);

        BizException::throwsIf('width empty', empty($imageConfig['width']));
        BizException::throwsIf('height empty', empty($imageConfig['height']));
        BizException::throwsIf('backgroundImage 和 backgroundColor 为空', empty($imageConfig['backgroundImage']) && empty($imageConfig['backgroundColor']));
        BizException::throwsIf('blocks empty', !isset($imageConfig['blocks']));

        if (empty($imageConfig['font'])) {
            $fontPath = FontProvider::firstLocalPathOrFail();
        } else {
            $fontPath = FileUtil::savePathToLocalTemp($imageConfig['font'], 'ttf', true);
        }

        if (!empty($imageConfig['backgroundImage'])) {
            $backgroundImage = FileUtil::savePathToLocalTemp($imageConfig['backgroundImage']);
            $image = Image::make($backgroundImage);
        } else {
            $image = Image::canvas($imageConfig['width'], $imageConfig['height'], $imageConfig['backgroundColor']);
        }

        foreach ($imageConfig['blocks'] as $item) {
            $item['x'] = intval($item['x']);
            $item['y'] = intval($item['y']);
            switch ($item['type']) {
                case 'text':
                    $lineHeight = isset($item['data']['lineHeight']) ? $item['data']['lineHeight'] : 1.2;
                    $lines = explode(self::LINE_BREAK, $item['data']['text']);
                    $offsets = [];
                    if (!empty($item['data']['shadowOffset'])) {
                        if (empty($item['data']['shadowColor'])) {
                            $item['data']['shadowColor'] = '#000000';
                        }
                        $offsets[] = [
                            'x' => $item['data']['shadowOffset'],
                            'y' => $item['data']['shadowOffset'],
                            'color' => $item['data']['shadowColor']
                        ];
                    }
                    if (!empty($item['data']['shadowBorder'])) {
                        if (empty($item['data']['shadowColor'])) {
                            $item['data']['shadowColor'] = '#000000';
                        }
                        foreach ([-$item['data']['shadowBorder'], 0, $item['data']['shadowBorder']] as $x) {
                            foreach ([-$item['data']['shadowBorder'], 0, $item['data']['shadowBorder']] as $y) {
                                $offsets[] = [
                                    'x' => $x,
                                    'y' => $y,
                                    'color' => $item['data']['shadowColor']
                                ];
                            }
                        }
                    }
                    $offsets[] = [
                        'x' => 0,
                        'y' => 0,
                        'color' => $item['data']['color']
                    ];
                    foreach ($offsets as $offset) {
                        $y = $item['y'];
                        foreach ($lines as $line) {
                            $line = trim($line);
                            if (empty($line)) {
                                continue;
                            }
                            $image->text($line, $item['x'] + $offset['x'], $y + $offset['y'], function ($font) use ($item, $offset, $fontPath) {
                                $font->file($fontPath);
                                $font->size($item['data']['size']);
                                $font->color($offset['color']);
                                $font->align($item['data']['align']);
                                $font->valign('top');
                            });
                            $y += $item['data']['size'] * $lineHeight;
                        }
                    }
                    break;
                case 'rect':
                    $x = $item['x'];
                    $y = $item['y'];
                    $isDraw = false;
                    if (isset($item['data']['radius'])) {
                        $radiusRect = self::rectRadius($item['data']['backgroundColor'], $item['data']['width'], $item['data']['height'], $item['data']['radius']);
                        if ($radiusRect) {
                            $radiusRect = Image::make($radiusRect);
                            $image->insert($radiusRect, 'top-left', $x, $y);
                            $isDraw = true;
                        }
                    }
                    if (!$isDraw) {
                        $image->rectangle($x, $y, $x + $item['data']['width'], $y + $item['data']['height'], function ($draw) use ($item) {
                            $draw->background($item['data']['backgroundColor']);
                        });
                    }
                    break;
                case 'image':
                    $itemImagePath = FileUtil::savePathToLocalTemp($item['data']['image']);
                    $itemImage = Image::make($itemImagePath);
                    if (!empty($item['data']['opacity'])) {
                        $itemImage->opacity($item['data']['opacity']);
                    }
                    if (isset($item['data']['width']) && isset($item['data']['height'])) {
                        $itemImage->resize($item['data']['width'], $item['data']['height']);
                    }
                    $image->insert($itemImage, 'top-left', $item['x'], $item['y']);
                    break;
                case 'qrcode':
                    $qrcode = QrcodeUtil::png($item['data']['text'], $item['data']['width']);
                    $qrcode = Image::make($qrcode);
                    $image->insert($qrcode, 'top-left', $item['x'], $item['y']);
                    break;
                case 'maskColor':
                    $color = ColorUtil::hexToRgbaArray($item['data']['color']);
                    $itemImagePath = FileUtil::savePathToLocalTemp($item['data']['image']);
                    $im = imagecreatefrompng($itemImagePath);
                    imagesavealpha($im, true);
                    imagefilter($im, IMG_FILTER_COLORIZE, $color['r'], $color['g'], $color['b']);
                    $tempImage = FileUtil::generateLocalTempPath('png');
                    imagepng($im, $tempImage);
                    imagedestroy($im);
                    $itemImage = Image::make($tempImage);
                    $image->insert($itemImage, 'top-left', $item['x'], $item['y']);
                    FileUtil::savePathToLocalTemp($tempImage);
                    break;
            }
        }
        return $image->encode('png');
    }
}
