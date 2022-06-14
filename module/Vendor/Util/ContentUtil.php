<?php


namespace Module\Vendor\Util;


use ModStart\Core\Exception\BizException;

class ContentUtil
{
    public static function parseEditorPlusContent($content)
    {
        if (is_string($content)) {
            $content = trim($content);
        }
        if (is_string($content) && starts_with($content, '[')) {
            $content = @json_decode($content, true);
        }
        $filter = [];
        foreach ($content as $one) {
            BizException::throwsIf('内容格式错误', empty($one['type']));
            switch ($one['type']) {
                case 'text':
                    BizException::throwsIf('内容格式错误', empty($one['data']['content']));
                    BizException::throwsIf('内容格式错误', !is_string($one['data']['content']));
                    $filter[] = [
                        'type' => $one['type'],
                        'data' => [
                            'content' => $one['data']['content']
                        ]
                    ];
                    break;
                case 'image':
                    BizException::throwsIf('内容格式错误', empty($one['data']['image']));
                    BizException::throwsIf('内容格式错误', !is_string($one['data']['image']));
                    $filter[] = [
                        'type' => $one['type'],
                        'data' => [
                            'image' => $one['data']['image']
                        ],
                    ];
                    break;
                case 'images':
                    BizException::throwsIf('内容格式错误', empty($one['data']['images']));
                    BizException::throwsIf('内容格式错误', !is_array($one['data']['images']));
                    $images = [];
                    foreach ($one['data']['images'] as $image) {
                        BizException::throwsIf('内容格式错误', !is_string($image));
                        $images[] = $image;
                    }
                    $filter[] = [
                        'type' => $one['type'],
                        'data' => [
                            'images' => $images
                        ],
                    ];
                    break;
            }
        }
        return $filter;
    }
}