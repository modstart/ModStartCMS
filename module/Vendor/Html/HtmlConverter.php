<?php

namespace Module\Vendor\Html;

use ModStart\Core\Util\HtmlUtil;
use Module\Vendor\Markdown\MarkdownUtil;

/**
 * Class HtmlConverter
 * @package Module\Vendor\Html
 * @deprecated
 * @see \Module\Vendor\Provider\RichContent\RichContentProvider
 */
class HtmlConverter
{
    public static function convertToHtml($contentType,
                                         $content,
                                         $interceptors = null)
    {
        switch ($contentType) {
            case HtmlType::RICH_TEXT:
                $html = HtmlUtil::filter($content);
                break;
            case HtmlType::MARKDOWN:
                $html = MarkdownUtil::convertToHtml($content);
                $html = HtmlUtil::filter($html);
                break;
            case HtmlType::SIMPLE_TEXT:
                $html = HtmlUtil::text2html($content);
                break;
            default:
                throw new \Exception('HtmlConverter.convertToHtml contentType error');
        }
        if (!empty($interceptors)) {
            if (is_array($interceptors)) {
                foreach ($interceptors as $interceptor) {
                    $ins = new $interceptor();
                    $html = $ins->convert($html);
                }
            } else {
                $ins = new $interceptors();
                $html = $ins->convert($html);
            }

        }
        return $html;
    }
}
