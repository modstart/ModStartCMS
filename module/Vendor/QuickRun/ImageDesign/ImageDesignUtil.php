<?php


namespace Module\Vendor\QuickRun\ImageDesign;


use Intervention\Image\Facades\Image;
use ModStart\Core\Exception\BizException;
use ModStart\Core\Input\Response;
use ModStart\Core\Provider\FontProvider;
use ModStart\Core\Util\ColorUtil;
use ModStart\Core\Util\FileUtil;
use ModStart\Core\Util\LogUtil;
use ModStart\Core\Util\QrcodeUtil;

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

    public static function configSaveCheck($imageConfig)
    {
        BizException::throwsIfEmpty('imageConfig 为空', $imageConfig);
        BizException::throwsIfEmpty('宽度为空', $imageConfig['width']);
        BizException::throwsIfEmpty('高度为空', $imageConfig['height']);
        BizException::throwsIf('背景图和背景色同时为空', empty($imageConfig['backgroundImage']) && empty($imageConfig['backgroundColor']));
    }

    private static function getTextWidth($text, $fontPath, $fontSize)
    {
        $box = @imagettfbbox($fontSize, 0, $fontPath, $text);
        $width = abs($box[2] - $box[0]);
        return $width;
    }

    private static function ttfHasChar($fontFile, $char)
    {
        $fp = fopen($fontFile, 'rb');
        if (!$fp) return false;
        fseek($fp, 4);
        $numTables = unpack('n', fread($fp, 2))[1];
        fseek($fp, 12);
        $cmapOffset = null;
        for ($i = 0; $i < $numTables; $i++) {
            $record = unpack('a4tag/NcheckSum/Noffset/Nlength', fread($fp, 16));
            if ($record['tag'] === 'cmap') {
                $cmapOffset = $record['offset'];
                break;
            }
        }

        if ($cmapOffset === null) return false;

        // 读取 cmap 表头
        fseek($fp, $cmapOffset);
        $cmapHeader = unpack('nversion/nnumTables', fread($fp, 4));

        $unicodeOffset = null;
        for ($i = 0; $i < $cmapHeader['numTables']; $i++) {
            $entry = unpack('nplatformID/nencodingID/Noffset', fread($fp, 8));
            if ($entry['platformID'] == 3 && $entry['encodingID'] == 1) { // Windows Unicode BMP
                $unicodeOffset = $entry['offset'];
                break;
            }
        }

        if ($unicodeOffset === null) return false;
        fseek($fp, $cmapOffset + $unicodeOffset);
        $format = unpack('nformat', fread($fp, 2))['format'];

        if ($format != 4) {
            fclose($fp);
            return false;
        }

        // 解析 format 4
        fseek($fp, -2, SEEK_CUR);
        $fmt4 = unpack('nformat/nlength/nlanguage/nsegCountX2/nsearchRange/nentrySelector/nrangeShift', fread($fp, 14));
        $segCount = $fmt4['segCountX2'] / 2;

        $endCodes = unpack("n$segCount", fread($fp, $segCount * 2));
        fseek($fp, 2, SEEK_CUR); // skip reservedPad
        $startCodes = unpack("n$segCount", fread($fp, $segCount * 2));
        $idDeltas = unpack("n$segCount", fread($fp, $segCount * 2));

        fclose($fp);

        $code = mb_ord($char, 'UTF-8');
        for ($i = 1; $i <= $segCount; $i++) {
            if ($code >= $startCodes[$i] && $code <= $endCodes[$i]) {
                return true;
            }
        }
        return false;
    }

    private static function getTtfFont($fontFile, $fallbackFontFile, $text)
    {
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($chars as $char) {
            if (!self::ttfHasChar($fontFile, $char)) {
                return $fallbackFontFile;
            }
        }
        return $fontFile;
    }

    private static function replaceParam($data, $variables)
    {
        if (empty($variables)) {
            return $data;
        }
        if (is_array($data)) {
            $newData = [];
            foreach ($data as $k => $v) {
                $newData[$k] = self::replaceParam($v, $variables);
            }
            return $newData;
        }
        if (is_string($data)) {
            foreach ($variables as $k => $v) {
                $data = str_replace('${' . $k . '}', $v, $data);
            }
            return $data;
        }
        return $data;
    }

    public static function render($imageConfigJson, $variables = [])
    {
        BizException::throwsIfEmpty('imageConfig 为空', $imageConfigJson);
        $configParam = [];
        foreach ($variables as $k => $v) {
            $configParam['${' . $k . '}'] = $v;
        }
        //$imageConfig = SerializeUtil::jsonEncode($imageConfigJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        //$imageConfig = str_replace(array_keys($configParam), array_values($configParam), $imageConfig);
        //LogUtil::info('xxxxx', [
        //    '$imageConfigJson' => $imageConfigJson,
        //    '$imageConfig' => $imageConfig,
        //    '$imageConfigNew' => self::replaceParam($imageConfigJson, $variables)
        //]);
        //$imageConfig = json_decode($imageConfig, true);
        $imageConfig = self::replaceParam($imageConfigJson, $variables);

        BizException::throwsIf('width empty', empty($imageConfig['width']));
        BizException::throwsIf('height empty', empty($imageConfig['height']));
        BizException::throwsIf('backgroundImage 和 backgroundColor 为空', empty($imageConfig['backgroundImage']) && empty($imageConfig['backgroundColor']));
        BizException::throwsIf('blocks empty', !isset($imageConfig['blocks']));

        $systemFontPath = FontProvider::firstLocalPathOrFail();
        if (empty($imageConfig['font'])) {
            $fontPath = $systemFontPath;
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
                    $textFontPath = empty($item['data']['font']) ? null : $item['data']['font'];
                    if ($textFontPath) {
                        $textFontPath = FileUtil::savePathToLocalTemp($textFontPath, 'ttf', true);
                    }
                    if (empty($textFontPath)) {
                        $textFontPath = $fontPath;
                    }
                    $textFontPath = self::getTtfFont($textFontPath, $systemFontPath, $item['data']['text']);
                    $linesForBreak = explode(self::LINE_BREAK, $item['data']['text']);
                    $lines = [];
                    foreach ($linesForBreak as $line) {
                        $parts = explode("\n", $line);
                        foreach ($parts as $part) {
                            $lines[] = $part;
                        }
                    }
                    $lines = array_filter(array_map('trim', $lines));
                    if (!empty($item['data']['width'])) {
                        $newLines = [];
                        foreach ($lines as $text) {
                            $currentLine = '';
                            $words = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
                            foreach ($words as $char) {
                                $testLine = $currentLine . $char;
                                $lineWidth = self::getTextWidth($testLine, $textFontPath, $item['data']['size']);
                                if ($lineWidth > $item['data']['width'] && $currentLine !== '') {
                                    $newLines[] = $currentLine;
                                    $currentLine = $char;
                                } else {
                                    $currentLine = $testLine;
                                }
                            }
                            if ($currentLine !== '') {
                                $newLines[] = $currentLine;
                            }
                        }
                        $lines = $newLines;
                    }
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
                            //LogUtil::info('xxxx', [
                            //    '$line' => $line,
                            //    '$x' => $item['x'] + $offset['x'],
                            //    '$y' => $y + $offset['y'],
                            //    'align' => $item['data']['align'],
                            //]);
                            $image->text(
                                $line, $item['x'] + $offset['x'], $y + $offset['y'],
                                function ($font) use ($item, $offset, $textFontPath) {
                                    $font->file($textFontPath);
                                    $font->size($item['data']['size']);
                                    $font->color($offset['color']);
                                    $font->align($item['data']['align']);
                                    $font->valign('top');
                                }
                            );
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
                    if (isset($item['data']['width']) && isset($item['data']['height'])) {
                        $itemImage->resize($item['data']['width'], $item['data']['height']);
                    }
                    if (!empty($item['data']['opacity']) && $item['data']['opacity'] < 100 && $item['data']['opacity'] > 0) {
                        $itemImage->opacity($item['data']['opacity']);
                    }
                    $image->insert($itemImage, 'top-left', $item['x'], $item['y']);
                    break;
                case 'qrcode':
                    if (preg_match('/^data:image\/[a-zA-Z]+;base64,/', $item['data']['text'])) {
                        $imageData = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $item['data']['text']);
                        $imageData = base64_decode($imageData);
                        $qrcode = Image::make($imageData);
                        $qrcode->resize($item['data']['width'], $item['data']['width']);
                    } else {
                        $qrcode = QrcodeUtil::png($item['data']['text'], $item['data']['width']);
                        $qrcode = Image::make($qrcode);
                    }
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
