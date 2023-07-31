<?php


namespace Module\Vendor\QuickRun\ImageDesign;


use Intervention\Image\Facades\Image;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Provider\FontProvider;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\QrcodeUtil;

class ImageDesignUtil
{
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

    public static function render($imageConfig, $variables = [])
    {
        BizException::throwsIfEmpty('imageConfig 为空', $imageConfig);
        $configParam = [];
        foreach ($variables as $k => $v) {
            $configParam['${' . $k . '}'] = $v;
        }
        $imageConfig = json_encode($imageConfig, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $imageConfig = str_replace(array_keys($configParam), array_values($configParam), $imageConfig);
        $imageConfig = json_decode($imageConfig, true);

        BizException::throwsIf('width empty', empty($imageConfig['width']));
        BizException::throwsIf('height empty', empty($imageConfig['height']));
        BizException::throwsIf('backgroundImage 为空', empty($imageConfig['backgroundImage']));
        BizException::throwsIf('blocks empty', !isset($imageConfig['blocks']));

        if (empty($imageConfig['font'])) {
            $fontPath = FontProvider::firstLocalPathOrFail();
        } else {
            $fontPath = FileUtil::savePathToLocalTemp($imageConfig['font'], 'ttf', true);
        }

        $backgroundImage = FileUtil::savePathToLocalTemp($imageConfig['backgroundImage']);
        $image = Image::make($backgroundImage);

        foreach ($imageConfig['blocks'] as $item) {
            switch ($item['type']) {
                case 'text':
                    $lines = explode("[BR]", $item['data']['text']);
                    $y = $item['y'];
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) {
                            continue;
                        }
                        $image->text($line, $item['x'], $y, function ($font) use ($item, $fontPath) {
                            $font->file($fontPath);
                            $font->size($item['data']['size']);
                            $font->color($item['data']['color']);
                            $font->align($item['data']['align']);
                            $font->valign('top');
                        });
                        $y += $item['data']['size'] * 1.5;
                    }
                    break;
                case 'image':
                    $itemImagePath = FileUtil::savePathToLocalTemp($item['data']['image']);
                    $itemImage = Image::make($itemImagePath);
                    if (!empty($item['data']['opacity'])) {
                        $itemImage->opacity($item['data']['opacity']);
                    }
                    $image->insert($itemImage, 'top-left', $item['x'], $item['y']);
                    break;
                case 'qrcode':
                    $qrcode = QrcodeUtil::png($item['data']['text'], $item['data']['width']);
                    $qrcode = Image::make($qrcode);
                    $image->insert($qrcode, 'top-left', $item['x'], $item['y']);
                    break;
            }
        }
        return $image->encode('png');
    }
}
